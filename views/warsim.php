<?php
if (!$execute) die('');
needed_login();
$stview=1;

Updater::dorf($login_dorf);

echo'<table width="100%"><tr><td><h1>Kampfsimulator</h1></td>
	<td class="right" valign="top"><a href="?page=build&gid=39">(Zurück zum Versammlungsplatz)</a></td></tr></table>
	<form method="get" action="?">
	<input type="hidden" name="page" value="warsim" />
	<br>';

//Mauern
$w1=saveGet('w1','');
$w2=saveGet('w2','');
$w3=saveGet('w3','');
$wall=array(31=>$w1,32=>$w2,33=>$w3);
unset($w1,$w2,$w3);

$hero_v=saveGet('hero_v','');
$hero_a=saveGet('hero_a','');
$pala=saveGet('pala','');
$fallen=saveGet('fa','');

if (isset($_GET['a_v'])) {
	//Truppen
	$deffTruppen=array();
	for ($volk=1;$volk<=4;$volk++) {
		unset($truppe);
		$d_v[$volk]=saveGet('d_v'.$volk, 0);
		if ($d_v[$volk]==1) {
			$truppe=array('volk'=>$volk);
			$ids=TruppenTyp::getIdsByVolk($volk);
			foreach($ids as $tid)
				$truppe[$tid]=saveGet('d_'.$tid, 0);
			$deffTruppen[]=$truppe;
		}
	}

	//Deffheld
	if ($hero_v>0) {
		$deffTruppen[0]['hero']=1;
		$deffTruppen[0]['heroboni']=$hero_v;
	}
	
	//Deffdorf erstellen
	$dorf=new DeffDorf($pala,$wall,$fallen,$deffTruppen);

	//Angreifer zusammenstellen
	$offTruppen=array();
	$volk=saveGet('a_v','1');
	$offTruppen['volk']=$volk;
	$ids=TruppenTyp::getIdsByVolk($volk);
	foreach($ids as $tid) {
		$offTruppen[$tid]=saveGet('a_'.$tid, 0);
	}
  
	//OffHeld
	if ($hero_a>0) {
		$offTruppen['hero']=1;
		$offTruppen['heroboni']=$hero_a;
	}
  
	//Angriff simulieren
	$deffboni=$dorf->getDeffBoni();

	$offTruppen2=$dorf->attack($offTruppen,$_GET['ktyp']);
  
	//Angreiffer ausgeben
	print_volk($offTruppen['volk'],$offTruppen,$offTruppen2,1,'Angreifer');
	
	//Verbleibende DeffTruppen abfragen und ausgeben
	$deffTruppen2=$dorf->getDeffTruppen();
	$t='Verteidiger';
	foreach($deffTruppen as $nr => $truppe) {
		print_volk($truppe['volk'],$truppe,$deffTruppen2[$nr],2,$t);
		$t='Unterst.';
	}
	
}

/* Eingegebene Werte + Standardwerte */
$volk=$login_user->get('volk');
$c='checked="checked"';


//Angreifer eingeben
$a_v = saveGet('a_v',$volk); //Angreiffer volk

echo'<table class="tbg" cellpadding="2" cellspacing="1" width="90%">
	<tr class="rbg"><td colspan="10">Angreifer </td></tr>
	<tr><td colspan="10"><input class="radio" name="a_v" value="1"
	'.($a_v==1?$c:'').' type="radio"> Römer
	<input class="radio" name="a_v" value="2"
	'.($a_v==2?$c:'').' type="radio"> Germanen
	<input class="radio" name="a_v" value="3"
	'.($a_v==3?$c:'').' type="radio"> Gallier</td></tr>
	<tr>';

$truppen=TruppenTyp::getByVolk($a_v);
foreach($truppen as $tid => $einheit) {
	echo'<td><img src="img/un/u/'.$tid.'.gif" title="'.$einheit->get('name').'"></td>';
}
echo'</tr><tr>';

foreach($truppen as $tid => $einheit) {
  echo'<td style="padding:0px;">
  	<input class="fm" name="a_'.$tid.'" value="'.$_GET['a_'.$tid].'" maxlength="6" type="text" style="width:40px;"></td>';
}
echo'</tr></table>';


//Verteidiger eingeben
$set=false;
for ($i=1;$i<=4;$i++) {
	$d_v[$i]=saveGet('d_v'.$i, 0);
	if ($d_v[$i]!=0) $set=true;
}
if (!$set)
	$d_v[$volk]=1;
echo'<table class="tbg" cellpadding="2" cellspacing="1" width="90%">
	<tr class="rbg"><td colspan="10">Verteidiger </td></tr>
	<tr><td colspan="10">
	<input class="check" name="d_v1" value="1"'.($d_v[1]?$c:'').' type="checkbox"> Römer
	<input class="check" name="d_v2" value="1"'.($d_v[2]?$c:'').' type="checkbox"> Germanen
	<input class="check" name="d_v3" value="1"'.($d_v[3]?$c:'').' type="checkbox"> Gallier
	<input class="check" name="d_v4" value="1"'.($d_v[4]?$c:'').' type="checkbox"> Natur</td></tr>';
for ($volk=1;$volk<=4;$volk++) {
	if ($d_v[$volk]==1) {
		echo'<tr>';
		$truppen=TruppenTyp::getByVolk($volk);
		foreach($truppen as $tid => $einheit) {
			echo'<td><img src="img/un/u/'.$tid.'.gif" title="'.$einheit->get('name').'"></td>';
		}
		echo'</tr><tr>';
		foreach($truppen as $tid => $einheit) {
			echo'<td style="padding:0px;"><input class="fm" name="d_'.$tid.'" value="'.$_GET['d_'.$tid].'"
				maxlength="6" type="text" style="width:40px;"></td>';
		}
		echo'</tr>';
	}
}
echo'</table>';


//Sonstiges
$row2='';
echo'<table class="tbg" cellpadding="2" cellspacing="1" width="90%">
	<tr class="rbg"><td colspan="10">Sonstiges</td></tr><tr>';
//Helden
echo'<td><img src="img/un/a/hero_a.gif" title="Angriffsbonus in % - Held Angreifer"></td>
	<td><img src="img/un/a/hero_v.gif" title="Verteidigungsbonus in % - Held Verteidiger"></td>';
$row2.='<td style="padding:0px;">
	<input class="fm" type="text" name="hero_a" value="'.$hero_a.'" style="width:40px;"></td>
	<td style="padding:0px;">
	<input class="fm" type="text" name="hero_v" value="'.$hero_v.'" style="width:40px;"></td>';
//Verteidigungsmauer
for ($i=1;$i<=3;$i++) {
	$geb=GebeudeTyp::getById(30+$i);
	echo'<td><img src="img/un/a/wall'.$i.'.gif" title="Stufe '.$geb->get('name').'"></td>';
	$row2.='<td style="padding:0px;">
		<input class="fm" type="text" name="w'.$i.'" value="'.$wall[30+$i].'" style="width:40px;"></td>';
}
//Palast & Fallen
echo'<td><img src="img/un/a/pala.gif" title="Stufe Palast"></td>
	<td><img src="img/un/u/99.gif" title="Anzahl Fallen"></td>';
$row2.='<td style="padding:0px;">
	<input class="fm" type="text" name="pala" value="'.$pala.'" style="width:40px;"></td>
	<td style="padding:0px;">
	<input class="fm" type="text" name="fa" value="'.$fallen.'" style="width:40px;"></td>';

//Angriff normal und Raubzug
$ktyp=saveGet('ktyp',4);

echo'<td colspan="3" style="width:142px;" rowspan="2" class="left"><input class="radio" name="ktyp" value="3"
			'.($ktyp==3?$c:'').' type="radio"> Angriff Normal<br>
		<input class="radio" name="ktyp" value="4"
			'.($ktyp==4?$c:'').'type="radio"> Raubzug</td>
	</tr><tr>'.$row2.'</tr>
	</table><p></p>';



Outputer::button('s','ok');
echo'</form>';





/* Anhang */
/* Prüfen: */
function print_volk($volk,$army_vor,$army_nach,$typ,$title,$info=NULL) {
	echo'<table class="tbg" cellpadding="1" cellspacing="1">
		<tbody><tr class="cbg1">
		<td width="21%">';
	if ($typ==1) echo'<span class="c2 b">&nbsp;'.$title.'</span>';
	if ($typ==2) echo'<font color="#71d000"><b>&nbsp;'.$title.'</b></font>';
	echo'</td>';
	$einheiten = TruppenTyp::getByVolk($volk);
	foreach($einheiten as $tid => $einheit) {
		echo'<td width="40"><img class="unit" src="img/un/u/'.$i.'.gif" title="'.$einheit->get('name').'"></td>';
	}
	echo'</tr><tr><td class="left">&nbsp;Einheiten</td>';
	foreach($einheiten as $tid => $einheit) {
		if ($army_vor[$tid]==0)
			echo'<td class="c">0</td>';
		else
			echo'<td>'.$army_vor[$tid].'</td>';
	}
	echo'</tr><tr><td class="left">&nbsp;Verluste</td>';
	foreach($einheiten as $tid => $einheit) {
		if ($army_vor[$tid]-$army_nach[$tid]==0)
			echo'<td class="c">0</td>';
		else
			echo'<td>-'.($army_vor[$tid]-$army_nach[$tid]).'</td>';
	}
	echo'</tr><tr><td class="left">&nbsp;Übrig</td>';
	foreach($einheiten as $tid => $einheit) {
		if ($army_nach[$tid]==0)
			echo'<td class="c">0</td>';
		else
			echo'<td>'.$army_nach[$tid].'</td>';
	}
	if (is_array($info) or is_string($info)) {
		if (is_string($info)) $info=array($info);
		foreach($info as $text) {
			echo'</tr><tr><td class="left">&nbsp;Info:</td><td class="left" colspan="10">&nbsp;'.$text.'</td>';
		}
	}
	echo'</tr></tbody></table><p></p>';
}
/* Ok ab hier */