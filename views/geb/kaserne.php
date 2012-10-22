<?php
if (!isset($form)) {
	$form='recrut_kaserne';
	$typ=1;
}
if (!isset($showtyp))
	$showtyp=$typ;

?>

<script type="text/javascript">
function klick(id) {
	document.getElementById(id).click();
}
</script>

<?php
echo'<form method="post" name="snd"
		action="?page=build&gid='.$gid.'&do='.$form.'">
	<p></p>
	<table class="tbg" cellpadding="2" cellspacing="1"><tbody>
	<tr class="cbg1"><td>Name</td><td>Anzahl</td><td>max</td></tr>';

$lager=$login_dorf->lager();
$eigeneTruppe=$login_dorf->eigeneTruppe();
$soldaten=$eigeneTruppe->soldatenId();


$volk=$login_user->get('volk');
$anz=0;
for ($i=1;$i<=10;$i++) {
	$tid=$i+($volk-1)*10;

	$einheit=TruppenTyp::getById($tid);
	//Einheit erforscht
	if ($einheit->baubar($login_user) and $einheit->get('typ')==$typ) {
		$maxBaubar=$einheit->maxAnzahlBaubar($lager);
		$kosten=$einheit->baukosten();
		$anz++;
		echo'<tr><td>
			<table class="f10" cellpadding="0" cellspacing="2"
				width="100%">
			<tbody><tr><td rowspan="2" class="s7"
				valign="top" width="6%">
			<img class="unit" src="img/un/u/'.$tid.'.gif"></td>
			<td class="s7"><div>
			<a href="#" onclick="Popup(1,'.$tid.');"> '.
			$einheit->get('name').'</a>
			<span class="c f75">(Vorhanden: '.$soldaten[$tid].')</span>
			</div></td></tr>
			<tr><td class="s7">';
		for ($j=0;$j<4;$j++)
			echo'<img class="res" src="img/un/r/'.($j+1).'.gif">'.
				$kosten[$j].'|';

		echo'<img class="res" src="img/un/r/5.gif">'.
				$einheit->get('versorgung').'|
			<img class="clock" src="img/un/a/clock.gif"> '.
				zeit_dauer($einheit->bauzeit($login_dorf)).'
			</td></tr></tbody></table></td>
			<td><input class="fm" type="radio" size="1" name="re"
				value="'.$tid.'" id="r'.$i.'">
			<input class="fm" autocomplete="off" name="t'.$tid.'" value="0" size="2" maxlength="4"
				type="text"	onclick="klick('."'r".$i."'".');">
			</td>
			<td><div class="f75">
			<a href="#" onclick="document.snd.t'.$tid.
				'.value='.$maxBaubar.'; klick(\'r'.$i.'\');">('.
				$maxBaubar.')</a></div></td></tr>';
	}
}
if ($anz==0) {
	echo'<tr><td colspan="3" class="c">Es können noch keine
		Einheiten gebaut werden.</td></tr>';
}
echo'</tbody></table><p>';
Outputer::button('ok','b');

//Bestehende Produktion anzeigen
$auftrage=$login_dorf->auftrage($showtyp);
if (!empty($auftrage)) {
	echo'<table class="tbg" cellpadding="2" cellspacing="1">
		<tbody><tr class="cbg1">
		<td colspan="3">In Ausbildung</td>
		<td>Dauer</td><td>Fertig</td></tr>';


	$dauer=0;
	foreach($auftrage as $index => $auftrag) {
		$dauer+=$auftrag->get('anzahl')*$auftrag->get('dauer');
		if ($index==0) {
			$ersterAuftrag=$auftrag;
			$dauer-=$auftrag->get('dauer')-
				(strtotime($auftrag->get('zeit'))-time());
		}
		$einheit=TruppenTyp::getById($auftrag->get('id'));

		echo'<tr><td width="5%">
			<img class="unit" src="img/un/u/'.$auftrag->get('id').
			'.gif" border="0"></td>
			<td align="right" width="6%">'.
			$auftrag->get('anzahl').'&nbsp;</td>
			<td class="s7" width="39%">'.
				$einheit->get('name').'</td>
			<td width="25%"><span id="timer'.($index+1).'">'.
				zeit_dauer($dauer).'</span></td>
			<td width="25%">'.date('H:i',$dauer+time()).
				'<span> Uhr </span>'.
			date('d.m.Y',$dauer+time()).'</td></tr>';
	}
	echo'<tr class="cbg1" align="center"><td colspan="5">
		Fertigstellung der nächsten Einheit in
			<span id="timer'.($index+2).'">'.
		zeit_dauer(strtotime($ersterAuftrag->get('zeit'))-time()).'
		</span></td>
		</tr></tbody></table>';
}