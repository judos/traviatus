<?php

echo'<table class="tbg" cellpadding="2" cellspacing="1">
	<tbody><tr class="cbg1"><td>Akademie</td><td>Aktion</td></tr>';
$anz=0;
$typen=$login_user->truppenTypen();
$auftrage=$login_dorf->auftrage(5);


foreach($typen as $tid => $typ) {
	if ($tid=='hero')
		continue;
	if ($typ->erforschbar($login_user,$login_dorf) and
			!$login_user->einheitErforscht($tid)) {

		$anz++;
		echo'<tr><td><table class="f10" cellpadding="0"
			cellspacing="2" width="100%"><tbody><tr>
			<td rowspan="2" class="s7" valign="top" width="6%">
			<img class="unit" src="img/un/u/'.$tid.'.gif" border="0">
			</td>
			<td class="s7"><div><span>'.$typ->get('name').'</span>
			</div></td></tr><tr><td class="s7">
			<img src="img/un/a/x.gif" height="15" width="1">';
		$kosten=$typ->forschungskosten();
		for ($i=0;$i<4;$i++)
			echo'<img class="res" src="img/un/r/'.($i+1).'.gif">'.
				$kosten[$i].'|';

		$zeit=$typ->forschungszeit();
		echo'<img src="img/un/a/clock.gif" height="12" width="18"> '.
			zeit_dauer($zeit).'</td></tr></tbody></table></td>
			<td width="30%">';
		if ($login_dorf->genugRess($kosten)) {
			if (empty($auftrage))
				echo'<a href="?page=build&gid='.
					$gid.'&do=research&tid='.$tid.'">erforschen</a>';
			else
				echo'<div class="c">Es wird geforscht</div>';
		}
		else
			echo'<div class="c">Zu wenig<br>Rohstoffe</div>';
		echo'</td></tr>';
	}
}
if ($anz==0)
	echo'<tr><td colspan="2" class="f10 c">Im Moment können keine
		Einheiten erforscht werden. Um die Vorraussetzungen für neue
		Truppentypen nachzulesen, klicke auf das entsprechende
		Einheitenbild in der Anleitung.</td></tr>';

echo'</tbody></table>';


if (!empty($auftrage)) {
	$auftrag=$auftrage[0];
	$tid=$auftrag->get('id');
	$einheit=TruppenTyp::getById($tid);
	$dauer=strtotime($auftrag->get('zeit'))-time();

	echo'<p></p><table class="tbg" cellpadding="2" cellspacing="1">
		<tbody>
		<tr class="cbg1"><td colspan="2">In Forschung</td>
		<td>Dauer</td><td>Fertig</td></tr>
		<tr><td width="6%"><img class="unit"
			src="img/un/u/'.$tid.'.gif" border="0"></td>
		<td class="s7" width="44%">'.$einheit->get('name').'</td>
		<td width="25%">
		<span id="timer1">'.zeit_dauer($dauer).'</span></td>
		<td width="25%">'.date('H:i',strtotime($auftrag->get('zeit'))).
		'<span> Uhr</span></td></tr>
		</tbody></table>';
}

