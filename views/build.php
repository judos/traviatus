<?php
if (!$execute) die('');
needed_login();
$stview=1;

Updater::dorf($login_dorf);

if (isset($_GET['gid']))
	$gid=$_GET['gid'];

if (isset($_GET['highest'])) {
	$highest=$login_dorf->highestGid();
	$gid=$highest[$_GET['highest']];

	//Palast statt Residenz suchen und umgekehrt
	if ($gid==0 and $_GET['highest']==25) $gid=$highest[26];
	if ($gid==0 and $_GET['highest']==26) $gid=$highest[25];

	if ($gid==0) unset($gid);
}

if (!isset($gid)) gotoP('dorf1');

//Gebäude ID und Grid ID und Stufe finden
if ($gid<19){
	$gebeude1typ=$login_dorf->gebeude1typ();
	$gebeude1=$login_dorf->gebeude1();
	$id=$gebeude1typ[$gid-1];
	$stufe=$gebeude1[$gid-1];
}
else {
	$gebeude2typ=$login_dorf->gebeude2typ();
	$gebeude2=$login_dorf->gebeude2();
	$id=$gebeude2typ[$gid-19];
	$stufe=$gebeude2[$gid-19];
}
if ($id>0) {
	$gebeude=GebeudeTyp::getById($id);
	$name=$gebeude->get('name');
}

$produktion=$login_dorf->produktion();
$lager=$login_dorf->lager();
$gebeudeAnzahl=$login_dorf->gebeudeAnzahl();
//Prüfen ob schon gebaut wird
$baumeisterFrei=$login_dorf->baumeisterFrei($gid);



//Neues Gebäude im Dorfzentrum bauen
if ($gid>18 and $id==0 and $stufe==0) {
	echo'<h1>Neues Gebäude errichten</h1><br>';
	$anz=0;
	$max=40;

	for ($i=5;$i<=$max;$i++) {
		$gebeude=GebeudeTyp::getById($i);
		if ($gebeude->neuBaubar($login_dorf,$gid)) {
			$anz=1;

			$kosten=$gebeude->bauKosten(1);
			$nr='';
			if ($gebeudeAnzahl[$i]>0)
				$nr=($gebeudeAnzahl[$i]+1).'. ';
			echo'<h2>'.$nr.$gebeude->get('name').'</h2>'.
				'<p class="f10">'.insert_div(t($gebeude->get('besch'))).'</p>'.
				'<p></p><table class="f10"><tbody><tr><td>';
			for ($j=0;$j<4;$j++)
				echo'<img class="res" src="img/un/r/'.($j+1).'.gif">'.
					$kosten[$j].' | ';
			echo'<img class="res" src="img/un/r/5.gif">'.
				$gebeude->get('arbeiter').' | '.
				'<img class="clock" src="img/un/a/clock.gif"> '.
				zeit_dauer($gebeude->bauzeit(1,$login_dorf)).
				'</td></tr></tbody></table>';

			//Genug Rohstoffe
			if ($login_dorf->genugRess($kosten)) {
				//Kein Nahrungsmangel
				if ($produktion[3]-$login_dorf->get('einwohner')
						-$gebeude->get('arbeiter')>2) {
					if ($baumeisterFrei) {

						echo'<a href="?page=build&do=build&id='.$i.
							'&gid='.$gid.'">Gebäude bauen</a>';
					}
					else
						echo'<span class="c">Es wird bereits gebaut</span>';
				}
				else
					echo'<span class="c">Nahrungsmangel: Erst eine
						Getreidefarm ausbauen</span>';
			}
			else	echo'<span class="c">Zu wenig Rohstoffe</span>';
			echo'<br><br>';
		}
	}
	if ($anz==0) {
		echo'<p class="c">Zur Zeit können keine neuen Gebäude
			errichtet werden.<br><br>
			Viele Gebäude benötigen bestimmte Voraussetzungen,
			<br> um gebaut werden zu können. Die
			Gebäudevoraussetzungen<br> kannst du in der Anleitung
			nachlesen.</p>';
	}
}


//Gebäude ausbauen
if (($gid<19) or ($gid>18 AND $id>0)) {
	$nachste_stufe=$stufe+1;
	$max_stufe=$gebeude->get('stufen');
	
	//TODO: remove if present when everything else finished
	//if ($build==-1) $nachste_stufe++;

	//Ausserhalb HD ist nur bis 10 baubar auf Land
	if ($gid<19 and $login_dorf->get('grosse')==0) {
		$smax=Diverses::get('max_ausbau_nicht_hd');
		if ($max_stufe>$smax) $max_stufe=$smax;
	}

	if ($nachste_stufe>$max_stufe)
	  $nachste_stufe=$max_stufe;

	$kosten=$gebeude->baukosten($nachste_stufe);

	//Name Stufe Beschreibung
	echo'<h1><b>'.$name.' Stufe '.$stufe.'</b></h1>
	<p class="f10">'.insert_div(t($gebeude->get('besch'))).'</p>';

	//Informationen zu Nutzen des Gebäudes
	if ($stufe>0 or ($stufe==0 and $id<=4)) {

		//Rohstoffgebäude
		if ($id<=4) {
			$produktion_all=explode(':',Diverses::get('produktion'));
			echo'<table class="f10" cellpadding="0"
				cellspacing="4" width="100%">
				<tbody><tr><td width="200">Aktuelle Produktion:</td>
				<td><b>'.$produktion_all[$stufe].'</b> pro Stunde</td>
				</tr><tr>
				<td width="200">Produktion bei Stufe '.
					$nachste_stufe.':</td>
				<td><b>'.$produktion_all[$nachste_stufe].
					'</b> pro Stunde</td></tr>
				</tbody></table>';
		}
		//Erweiterungen Sägewerk etc.
		elseif ($id>=5 and $id<=9) {
			echo'<table class="f10" cellpadding="0"
				cellspacing="4" width="100%">
				<tbody><tr>
				<td width="250">Aktuelle Produktionssteigerung:</td>
				<td><b>'.(5*$stufe).'</b> Prozent</td></tr><tr>
				<td width="250">Steigerung bei Stufe '.$nachste_stufe.':
				</td><td>
				<b>'.(5*$nachste_stufe).'</b> Prozent</td>
				</tr></tbody></table>';
		}
		//Lagergebäude
		elseif ($id==10 or $id==11 or $id==38 or $id==39) {
			$lager_allg=explode(':',Diverses::get('lager'));
			$f=1;
			if ($id>37) $f=3;
			if ($id==10 or $id==38) $ress='Rohstoffeinheiten';
			if ($id==11 or $id==39) $ress='Einheiten Getreide';
			echo'<table class="f10" cellpadding="0" cellspacing="4"
				width="100%">
				<tbody><tr><td width="250">Aktuelle Speicherkapazität:</td>
				<td><b>'.($lager_allg[$stufe-1]*100*$f).'</b> '.
					$ress.'</td>
				</tr><tr><td width="250">Speicherkapazität bei Stufe '.
					$nachste_stufe.':</td>
				<td><b>'.($lager_allg[$nachste_stufe-1]*100*$f).'</b> '.
					$ress.'</td>
				</tr></tbody></table>';
		}
		//Waffenschmid, Rüstungsschmid
		elseif ($id==12 or $id==13)	{
			require('geb/wr_schmiede.php');
		}
		//Turnierplatz
		elseif ($id==14) {
			echo'<table class="f10" cellpadding="0" cellspacing="4"
				width="100%"><tbody><tr>
				<td width="250">Aktuelle Geschwindigkeit</td>
				<td><b>'.(100+10*$stufe).'</b> Prozent</td>
				</tr><tr><td width="250">Geschwindigkeit bei Stufe '.
				$nachste_stufe.':</td>
				<td><b>'.(100+10*$nachste_stufe).'</b> Prozent</td></tr>
				</tbody></table>';
		}
		//Hauptgebäude
		elseif ($id==15) {
			require('geb/hauptgebäude.php');
		}
		//Versammlungsplatz
		elseif ($id==16) {
			require('geb/versammlungsplatz.php');
		}
		//Marktplatz
		elseif ($id==17) {
			require('geb/marktplatz.php');
		}
		//Botschaft
		elseif ($id==18) {
			require('geb/botschaft.php');
		}
		//Kaserne
		elseif ($id==19) {
			require('geb/kaserne.php');
		}
		//Stall
		elseif ($id==20) {
			require('geb/stall.php');
		}
		//Werkstatt
		elseif ($id==21) {
			require('geb/werkstatt.php');
		}
		//Akademie
		elseif ($id==22) {
			require('geb/akademie.php');
		}
		//Versteck
		elseif ($id==23) {
			$versteck=explode(':',Diverses::get('versteck'));
			echo'<table class="f10" cellpadding="0" cellspacing="4"><tbody>';
			$aktuell=$versteck[$stufe-1];
			$nachste=$versteck[$nachste_stufe-1];
			if ($login_user->get('volk')==3) {
				$aktuell*=2;
				$nachste*=2;
			}
			if ($login_dorf->versteck()>$aktuell)
				echo'<tr><td width="250">Gesamte Versteckkapazität:</td>
					<td class="right"><b>'.$login_dorf->versteck().'</b></td><td>Einheiten</td></tr>';
			echo'<tr><td width="250">
				Aktuelles Versteck:</td><td class="right"><b>'.$aktuell.
					'</b></td><td>Einheiten</td></tr>
				<tr><td width="250">Versteck bei Stufe '.
					$nachste_stufe.':</td>
				<td class="right"><b>'.$nachste.
					'</b></td><td>Einheiten</td></tr></tbody></table>';
		}
		//Ratshaus
		elseif ($id==24) {
			require('geb/ratshaus.php');
		}
		//Residenz, Palast
		elseif ($id==25 OR $id==26) {
			require('geb/palast_residenz.php');
		}
		//Handelkontor
		elseif ($id==28) {
			$tragen=$login_dorf->handlerKapazitat();
			$ntragen=$login_dorf->handlerKapazitat(1);
			echo'<table class="f10" cellpadding="0" cellspacing="4"
				width="100%"><tbody><tr><td width="250">
				Aktuelle Tragfähigkeit:</td>
				<td><b>'.$tragen.'</b> Einheiten</td></tr>
				<tr><td width="250">Tragfähigkeit bei Stufe '.
					$nachste_stufe.':</td>
				<td><b>'.$ntragen.'</b> Einheiten</td></tr>
				</tbody></table>';
		}
		//Grosse Kaserne
		elseif( $id==29){
			$form='recrut_gkaserne';
			$typ=1;
			$showtyp=14;
			require('geb/kaserne.php');
		}
		//Grosser Stall
		elseif ($id==30){
			$form='recrut_gstall';
			$typ=2;
			$showtyp=15;
			require('geb/kaserne.php');
		}
		//Mauern und Wälle
		elseif ($id>=31 and $id<=33) {
			$schutzbonus=$login_dorf->mauerSchutzbonus();
			$nschutzbonus=$login_dorf->mauerSchutzbonus(1);
			echo'<table class="f10" cellpadding="0" cellspacing="4"
				width="100%"><tbody><tr>
				<td width="250">Aktueller Verteidigungsbonus:</td>
				<td><b>'.$schutzbonus.'</b> Prozent</td></tr>';
			if ($stufe<$nachste_stufe) {
				echo'<tr><td width="250">Verteidigungsbonus bei Stufe '.
					$nachste_stufe.':</td><td><b>'.$nschutzbonus.'</b> Prozent</td></tr>';
			}
			echo'</tbody></table>';
		}
		//Steinmetz
		elseif ($id==34) {
			echo'<table cellpadding="0" cellspacing="4" width="100%">
				<tbody><tr><td width="250">Aktuelle Stabilität</td>
				<td><b>'.(100+$stufe*10).'</b> Prozent</td></tr><tr>
				<td width="250">Stabilität bei Stufe '.
				$nachste_stufe.':</td><td><b>'.
				(100+$nachste_stufe*10).'</b> Prozent</td>
				</tr></tbody></table>';
		}
		//Brauerei
		elseif ($id==35) {
			echo'<table cellpadding="0" cellspacing="4" width="100%">
				<tbody><tr><td width="250">Aktueller Boni für
				Feste</td><td><b>+'.($stufe*10).'</b> Prozent</td></tr>
				<tr><td width="250">Boni für Feste bei Stufe '.
				$nachste_stufe.':</td><td><b>+'.
				($nachste_stufe*10).'</b> Prozent</td>
				</tr></tbody></table>';
		}
		//Fallensteller
		elseif ($id==36) {
			require('geb/fallensteller.php');
		}
		//Heldenhof
		elseif ($id==37) {
			require('geb/heldenhof.php');
		}
		elseif ($id==40) {
			echo'Oh mein Gott, du hast ein Weltwunder zeig mal her!!<br>';
		}
		else
			echo'<p class="c">Noch keine Details
				für dieses Gebäude</p><br>';
	}
	else
		echo'<p class="c">Das Gebäude wurde
			noch nicht fertiggestellt</p><br>';


	//Admintools
	//TODO: remove functionality or make sure it is only possible with admintools
	if (ADMINTOOLS) {
	//	echo'<br><a href="?page=admintools&do=delgeb&gid='.$gid.'">
	//		Gebäude löschen</a>';
	}

	//Ausbau des Gebäudes
	$kosten=$gebeude->baukosten($nachste_stufe);
	
	if ($stufe<$max_stufe) {
		echo'<p style="margin-bottom:2px;margin-top:20px;"><b>Kosten</b> für Ausbau auf Stufe '.
			$nachste_stufe.':</p><table class="f10"><tbody><tr><td>
			<img class="res" src="img/un/r/1.gif">'.$kosten[0].' |
			<img class="res" src="img/un/r/2.gif">'.$kosten[1].' |
			<img class="res" src="img/un/r/3.gif">'.$kosten[2].' |
			<img class="res" src="img/un/r/4.gif">'.$kosten[3].' |
			<img class="res" src="img/un/r/5.gif">'.
				$gebeude->get('arbeiter').' |
			<img class="clock" src="img/un/a/clock.gif"
				height="12" width="18"> '.
			zeit_dauer($gebeude->bauzeit($nachste_stufe,$login_dorf)).
			'</td></tr></tbody></table>';

		$grund=$login_dorf->baumeisterFreiMitGrund($gid);
		if($grund!==true)
			echo'<span class="c">'.$grund.'</span>';
		if ($grund===true) {
			if ($login_dorf->genugRess($kosten)) { //Genug Rohstoffe
				//kein Nahrungsmangel
				//oder Getreidefarm ausbauen
				if ($produktion[3]-$login_dorf->get('einwohner')
						-$gebeude->get('arbeiter')>2 or
						$gebeude->get('name')=='Getreidefarm') {
					echo'<a href="?page=build&do=build&id='.$id.
						'&gid='.$gid.'">Ausbau auf Stufe '.
						$nachste_stufe.'</a>';
				}
				else	//Nahrungsmangel
					echo'<span class="c">Nahrungsmangel: Erst eine
						Getreidefarm ausbauen</span>';
			}
			else {	//Zuwenig Rohstoffe
				//Lagergrösse genug gross?
				if (!$login_dorf->genugLager($kosten)) {
					echo'<span class="c">Zuerst Rohstofflager und/oder
						Kornspeicher ausbauen</span>';
				}
				else {
					if ($produktion[3]>0) {
						$zeit=$login_dorf->zeitGenugRess($kosten);
						echo'<span class="c"><span>Genug Rohstoffe '.
							zeitAngabe($zeit).'</span>';
					}
					else
						echo'<span class="c"><span>Getreidemangel</span></span>';
				}
			}
		}
	}
	else
		echo'<p class="c">'.$gebeude->get('name').
			' vollständig ausgebaut</p>';
}

echo'<div>';

?>