<?php
if (!$execute) die('');
needed_login();
$stview=1;

Updater::dorf($login_dorf);

$sx=$login_dorf->get('x');
$sy=$login_dorf->get('y');

$s=saveGet('s','');

if ($s=='') {
	$dname=$_POST['dname'];
	$x=$_POST['x'];
	$y=$_POST['y'];
	$aktion=$_POST['c'];

	$result=Dorf::searchByKoordsOrName($x,$y,$dname);
	$msg=str_replace('+OBJECT_IS+','Die Truppen sind',$result['msg']);
	$x=$result['x'];
	$y=$result['y'];
	$dorf=$result['dorf'];

	if ($msg==''){
		if ($dorf->user() == $login_user){
			if (TruppeMove::aktionIstFeindlich($aktion)){
				$msg='Du kannst nicht dein eigenes Dorf angreifen!';
			}
		}
	}
	
	if ($msg!='') {
		//Umleiten wenn Dorf nicht gefunden wurde oder Ziel ungültig ist
		$_GET['va']=implode(':',$_POST);
		$_GET['keys']=implode(':',array_keys($_POST));
		gotoP('build&gid=39&s=2');
	}
	
	$spio=1;
	//Aktionen:
	//1=Dorf gründen 2=Unterstützung 3=Angriff 4=Raubzug 5=Spionieren
	if ($aktion==2) $spio=0;

	//Truppen anschauen
	$eigeneTruppe=Truppe::getByXYU($sx,$sy,$login_user);
	$eigeneSoldaten=$eigeneTruppe->soldatenId();
	$einheiten=$login_user->truppenTypen();
	
	//Truppen zählen, Spionage möglichkeit testen
	foreach($einheiten as $id=>$einheit) {
		$soldaten[$id]=(int)$_POST['t'.$id];
		if ($id=='hero')
			$einheit=$einheit->getTruppenTyp();

		if ($soldaten[$id]>0) {
			if ($soldaten[$id]>$eigeneSoldaten[$id])
				$soldaten[$id]=$eigeneSoldaten[$id];

			if ($spio==1 and $einheit->get('spio')==0)
				$spio=0;
		}
	}
	$anzahl=array_sum($soldaten);
	$speed=TruppeMove::maxSpeed($soldaten,$login_user);
	
	if ($spio==1) $aktion=5;
	if ($anzahl==0) {
		$msg='Es wurden keine Truppen ausgewählt';
		gotoP('build&gid=39&s=2');
	}

	//Informationen für die Truppe sammeln
	$spieler=$dorf->user();

	$aktion_name=array(TruppeMove::TYP_UNTERSTUETZUNG=>'Unterstützung für',
					   TruppeMove::TYP_ANGRIFF =>'Angriff auf',
	                   TruppeMove::TYP_RAUBZUG =>'Raubzug gegen',
					   TruppeMove::TYP_AUSSPAEHEN =>'Ausspähen von');
	//Weg berechnung
	$weg=sqrt(pow($x-$sx,2)+pow($y-$sy,2));

	//Geschwindigkeit der Truppe
	$speed_angabe=$speed;
	$highest=$login_dorf->highest();
	if ($weg>=Diverses::get('turnierplatz_ausdauer'))
		$speed_boni=(10+$highest[14])*($speed/10);
	else 
		$speed_boni=$speed;
	
	$dauer=$weg/$speed_boni*3600;

	if ($highest[14]>0 and $weg>=Diverses::get('turnierplatz_ausdauer'))
		$speed_angabe.=' + '.round($speed/10*$highest[14],1);
	$speed_angabe.=' Felder/Stunde';

	//Ausgabe
	echo'<h1>'.$aktion_name[$aktion].' '.$dorf->get('name').'</h1>
		<form method="post" action="?page=a2b&do=sendtroops">
		<p><table><tr><td width="11%">Ziel:</td>
		<td class="s7">
		<a href="?page=karte-show&x='.$x.'&y='.$y.'">'.
			$dorf->get('name').' ('.$x.'|'.$y.')</a></td></tr>
		<tr><td width="11%">Spieler:</td>
		<td class="s7">
		'.$spieler->getLink().'
		</td></tr><tr><td width="11%">Entfernung:</td>
		<td class="s7">'.round($weg,2).' Felder</td></tr>
		<tr><td width="11%">Geschwindigkeit:</td>
		<td class="s7">'.$speed_angabe.'</td></tr>
		</table></p>';
	
	$r=new InfoMessage();
	$r->addPartTextTitle('Eigene Truppen',
				$aktion_name[$aktion].' '.$dorf->getLink());
	$r->addPartUnitTypes($login_user->get('volk'));
	$r->addPartUnitCount('Einheit',$soldaten);
	$r->addPartTimeDuration('Ankunft',$dauer);
	
	echo $r->toHtml();

	echo'<input type="hidden" name="aktion" value="'.$aktion.'">
		<input type="hidden" name="x" value="'.$x.'">
		<input type="hidden" name="y" value="'.$y.'">
		<input type="hidden" name="dauer" value="'.$dauer.'">';
	foreach($einheiten as $id=>$einheit) {
		echo'<input type="hidden" name="t'.$id.'"
			value="'.$soldaten[$id].'">';
	}
	?>
	<p><input type="image" value="ok" border="0" name="s1"
		src="img/de/b/ok1.gif" width="50" height="20"
		onMousedown="btm1('s1','','img/de/b/ok2.gif',1)"
		onMouseOver="btm1('s1','','img/de/b/ok3.gif',1)"
		onMouseUp="btm0()" onMouseOut="btm0()"></input></form></p></div>
	<?php
}




if ($s=='newvillage') {
	$x=$_GET['x'];
	$y=$_GET['y'];

	$weg=sqrt(pow($x-$sx,2)+pow($y-$sy,2));
	$volk=$login_user->get('volk');
	$siedler=TruppenTyp::getById($volk*10);
	$speed=$siedler->get('speed');
	if ($weg>Diverses::get('turnierplatz_ausdauer')) {
		$speed*=1+TruppeMove::speedBoni($login_dorf,$x,$y,$login_user);
	}
	$dauer=$weg/$speed*3600;

	$expansion=explode(':',$login_dorf->get('expansion'));
	$slots=$login_dorf->dorf_slots();

	$neue_dorfer=explode(':',Diverses::get('neue_dorfer'));

	$anz_dorfer=sizeof($login_user->dorfer());
	$kp=floor($login_user->get('kps'));

	$truppenmove=TruppeMove::getByUser($login_user);
	$kolos=arrayObjectsContaining($truppenmove,'aktion',1);
	$anz_kolo=sizeof($kolos);

	echo'<h1>Neue Siedlung gründen</h1>
		<p class="f10">Die Siedler werden nun aufbrechen um
		neues Land zu erschließen.<br> Für den Aufbau der neuen
		Siedlung benötigen sie jedoch noch jeweils 750 Einheiten
		Holz, Lehm, Eisen und Getreide.</p>
		<form method="post" action="?page=a2b&do=newvillage">
		<table class="tbg" cellpadding="2" cellspacing="1">
		<tbody><tr class="rbg"><td width="21%">&nbsp;</td>
		<td colspan="10">Gründung einer neuen Siedlung ('.$x.'|'.$y.')
		</td></tr>

		<tr><td align="center" width="100">Einheiten</td>
		<td class="s7">3 Siedler</td></tr>
		<tr><td align="center" width="100">Dauer</td>
		<td class="s7">'.zeit_dauer($dauer).'</td></tr>

		<tr class="cbg1"><td>Rohstoffe</td><td class="s7">';
	for ($i=1;$i<=4;$i++)
		echo'<img class="res" src="img/un/r/'.$i.'.gif">750 '.
			($i<4?'|':'');
	echo'</td></tr></tbody></table>';

	//Genug Ress
	if ($login_dorf->genugRess(array_fill(0,4,750))) {
		//Genug Slots in diesem Dorf
		if ($expansion[0]<$slots) {
			//Genug Kulturpunkte
			if ($neue_dorfer[$anz_dorfer+$anz_kolo-1]*1000<=$kp) {
				echo'<input type="hidden" name="x" value="'.$x.'">
					<input type="hidden" name="y" value="'.$y.'">
					<p>';
				Outputer::button('s1','ok');
				echo'</p>';
			}
			else
				echo'<p class="c">Zuwenig Kulturpunkte ('.$kp.'/'.
					($neue_dorfer[$anz_dorfer+$anz_kolo-1]*1000).')</p>';
		}
		else
			echo'<p class="c">Bauen Sie zuerst ihren Palast
				oder ihre Residenz aus</p>';
	}
	else
		echo'<p class="c">Zu wenig Rohstoffe</p>';

	echo'</form>';
}
?>