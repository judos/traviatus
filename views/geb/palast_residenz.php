<?php

$neue_dorfer=explode(':',Diverses::get('neue_dorfer'));

$anz_dorfer=sizeof($login_user->dorfer());

$c=' class="selected"';

if (isset($_GET['s']))
	$s=$_GET['s'];
else
	$s=0;

echo'<p class="txt_menue">
	<a href="?page=build&gid='.$gid.'" '.($s==0?$c:'').'>Ausbilden</a> |
	<a href="?page=build&gid='.$gid.'&s=2" '.($s==2?$c:'').'>Kulturpunkte</a> |
	<a href="?page=build&gid='.$gid.'&s=3" '.($s==3?$c:'').'>Zustimmung</a> |
	<a href="?page=build&gid='.$gid.'&s=4" '.($s==4?$c:'').'>Expansion</a></p>';

if ($s==0) {
	if ($stufe<10) {
		$x=array(25=>'eine Residenz',26=>'einen Palast');
		echo'<div class="c">Um eine weitere Siedlung zu gründen
			oder zu erobern benötigst du '.$x[$id].' Stufe 10.</div>';
	}
	else {
		$form='recrut_pr';
		$typ=4;
		require('kaserne.php');
	}
}
elseif ($s==2) {
	$kp=round($login_dorf->get('einwohner')/2);
	$kptot=round($login_user->get('einwohner')/2);
	$kp_bis_jetzt=round($login_user->get('kps'));
	$kp_nachstes_dorf=($neue_dorfer[$anz_dorfer-1]*1000);
	$genug_kp_in=round(($kp_nachstes_dorf-$kp_bis_jetzt)/$kptot*86400);
	echo'<p>Um dein Reich zu vergrößern benötigst du Kulturpunkte.
		Diese nehmen mit der Zeit zu.
		Je weiter deine Gebäude ausgebaut sind, desto schneller.</p>
		<table class="f10" cellpadding="0" cellspacing="4" width="100%">
		<tbody><tr><td width="250">Produktion dieses Dorfes:</td>
			<td><b>'.$kp.'</b> Kulturpunkte pro Tag</td></tr>
		<tr><td width="250">Produktion aller Dörfer:</td>
			<td><b>'.$kptot.'</b> Kulturpunkte pro Tag</td></tr>
		</tbody></table>
		<p>Insgesamt haben deine Dörfer bis jetzt <b>'.
			$kp_bis_jetzt.'</b> Punkte erwirtschaftet.
		Um ein weiteres Dorf zu gründen oder zu erobern, würdest du <b>'.
			$kp_nachstes_dorf.'</b> Punkte benötigen. <br>Genug Kulturpunkte (ohne Feste): ';
	if ($kp_nachstes_dorf>=$kp_bis_jetzt) echo zeitAngabe($genug_kp_in + time());
	else echo'Du hast bereits genug KP für ein nächstes Dorf';
	echo'.</p>';
}
elseif ($s==3) {
	echo'Durch Angriffe mit Senatoren, Stammesführern oder
		Häuptlingen kann die Zustimmung gesenkt werden.
		Sinkt die Zustimmung auf Null, schließt sich die
		Bevölkerung des Dorfes dem Reich des Angreifers an.
		Die Zustimmung in diesem Dorf liegt bei <b>'.
			$login_dorf->get('zustimmung').' Prozent</b>.';
}
elseif ($s==4) {
	$expansion=explode(':',$login_dorf->get('expansion'));

	echo'<table class="tbg" cellpadding="2" cellspacing="1">
		<tbody><tr><td class="rbg" colspan="4"><a name="h2"></a>
		Von diesem Dorf gegründete oder eroberte Dörfer</td></tr>
		<tr><td width="6%">&nbsp;</td><td width="25%">Dorf</td>
			<td width="17%">Einwohner</td>
			<td width="17%">Koordinaten</td></tr>';
	if ($expansion[0]==0)
		echo'<tr><td colspan="4" class="c">Von diesem Dorf aus
			wurde noch kein anderes Dorf gegründet/erobert.</td></tr>';
	else {
		for ($i=1;$i<=$expansion[0];$i++) {
			$x=$expansion[$i*2-1];
			$y=$expansion[$i*2];
			$dorf=Dorf::getByXY($x,$y);
			if ($dorf!=null){
				$name='<a href="?page=karte-show&x='.$x.'&y='.$y.'">'.$dorf->get('name').'</a>';
				$einwohner=$dorf->get('einwohner');
			}
			else {
				$name='Zerstört';
				$einwohner=0;
			}
			echo'<tr><td align="right">'.$i.'.&nbsp;</td>
				<td class="s7">'.$name.'</td>
				<td>'.$einwohner.'</td>
				<td><table class="f10" cellpadding="0" cellspacing="0">
				<tbody><tr>
				<td align="right" width="35">('.$expansion[$i*2-1].'</td>
				<td width="2">|</td><td align="left" width="35">
				'.$expansion[$i*2].')</td></tr></tbody></table></td></tr>';
		}
	}
	echo'</tbody></table>';
}