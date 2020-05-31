<?php

$auftrage=$login_dorf->auftrage(9);

echo'<table class="f10" cellpadding="0" cellspacing="4"
	width="100%"><tbody><tr>
	<td width="250">Aktuelle Bauzeit</td><td><b>'.
		round(100-60*(($stufe-1)/19)).'</b> Prozent</td>
	</tr><tr>
	<td width="250">Bauzeit bei Stufe '.$nachste_stufe.':</td>
	<td><b>'.round(100-60*(($nachste_stufe-1)/19)).'</b>
		Prozent</td>
	</tr></tbody></table>';
if ($stufe>9) {	//Gebäude abreissen
	echo'<br><h2>Gebäude abreißen:</h2>
		<p class="f10">Falls du ein Gebäude
		nicht mehr benötigen solltest,
		kannst du deinen Baumeistern hier den
		Befehl geben, das Gebäude Stück für Stück wieder
		abzureißen:</p>';
	if (empty($auftrage)) {
		echo'<form action="?page=build&gid='.$gid.
			'&do=crash" method="post">
			<table><tr><td><select name="abriss_gid" class="f8">';
		for ($i=19;$i<=40;$i++) {
			$i_id=$login_dorf->gebeudeTyp($i);
			$i_stufe=$login_dorf->gebeudeStufe($i);
			if ($i_id>0) {
				echo'<option value="'.$i.'">'.$i.' - '.
					GebeudeTyp::getById($i_id)->get('name').' '.$i_stufe.'</option>';
			}
		}
		echo'</select></td><td>';
		Outputer::button('ok','ok');
		echo'</td></tr></table></form>';
	}
}
if (!empty($auftrage)) {
	$aauftrag=$auftrage[0];
	$adauer=strtotime($aauftrag->get('zeit'))-time();
	$agid=$aauftrag->get('id');
	$agebeude=GebeudeTyp::getById($login_dorf->gebeudeTyp($agid));
	$astufe=$login_dorf->gebeudeStufe($agid);

	echo'<p></p><table class="f10" width="100%">
		<tbody><tr><td>
		<a href="?page=build&gid='.$gid.'&do=delcrash">
		<img src="img/un/a/del.gif" title="abbrechen"
		border="0" height="12" width="12"></a></td><td>'.
		$agebeude->get('name').
		' (Stufe '.$astufe.')</td>
		<td><span id="timer1">'.zeit_dauer($adauer).
		'</span> Std.</td>
		<td>Fertig um '.date('H:i',time()+$adauer).
		'<span> Uhr</span></td></tr></tbody></table>';
}