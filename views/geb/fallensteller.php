<?php

$akt_fallen=Diverses::fallen($stufe);
$nachste_fallen=Diverses::fallen($nachste_stufe);

$belegt=0;

$truppenUser=Truppe::getUsersByD($login_dorf,1);
foreach($truppenUser as $userid) {
	$truppe=Truppe::getByDU($login_dorf,$userid,1);
	$belegt+=$truppe->anzSoldaten();
}


?>
<table cellpadding="2" cellspacing="1">
	<tbody><tr>
		<td>Aktuell maximale Anzahl:</td>
		<td><b><?php echo $akt_fallen; ?></b> Fallen</td>
	</tr>
	<tr>
		<td style="padding-right:10px;">Maximale Anzahl bei Stufe <?php echo $nachste_stufe; ?>:</td>
		<td><b><?php echo $nachste_fallen; ?></b> Fallen</td>

	</tr>
</tbody></table>
<p>Du besitzt im Moment <b><?php echo $login_dorf->get('fallen'); ?></b> 
Fallen, davon sind <b><?php echo $belegt;?></b> belegt.</p>


<script type="text/javascript">
function klick(id) {
	dgei(id).click();
}
</script>

<?php
echo'<form method="post" name="snd"
		action="?page=build&gid='.$gid.'&do=recrut_fallen">
	<p></p>
	<table class="tbg" cellpadding="2" cellspacing="1"><tbody>
	<tr class="cbg1"><td>Name</td><td>Anzahl</td><td>max</td></tr>';

$lager=$login_dorf->lager();

$kosten=explode(':',Diverses::get('fallen_kosten'));
$maxBaubar=TruppenTyp::maxAnzahlBaubarLager($kosten,$lager);

//Bestehende Produktion anzeigen
$werdenGebaut=0;
$auftrage=$login_dorf->auftrage(11);
if (!empty($auftrage)) {
	foreach($auftrage as $index => $auftrag) {
		$werdenGebaut+=$auftrag->get('anzahl');
	}
}

$moglich=$akt_fallen-$login_dorf->get('fallen')-$werdenGebaut;
if ($maxBaubar>$moglich) $maxBaubar=$moglich;

$fallen_bauzeit=explode(':',Diverses::get('fallen_bauzeit'));
$bauzeit=$fallen_bauzeit[0]*pow($fallen_bauzeit[1],$stufe-1);

echo'<tr><td>
  <table class="f10" cellpadding="0" cellspacing="2"
    width="100%">
  <tbody><tr><td rowspan="2" class="s7"
    valign="top" width="6%">
  <img class="unit" src="img/un/u/99.gif"></td>
  <td class="s7"><div>
  <a href="#" onclick="Popup(2,36);"> Fallen </a>
  <span class="c f75">(Vorhanden: '.$login_dorf->get('fallen').')</span>
  </div></td></tr>
  <tr><td class="s7">';
for ($j=0;$j<4;$j++)
  echo'<img class="res" src="img/un/r/'.($j+1).'.gif">'.
    $kosten[$j].'|';

echo'<img class="res" src="img/un/r/5.gif">0 |
  <img class="clock" src="img/un/a/clock.gif"> '.
    zeit_dauer($bauzeit).'
  </td></tr></tbody></table></td>
  <td><input type="hidden" name="re"
    value="99">
  <input name="t99" id="t99" value="0" size="2" maxlength="4"
    type="text">
  </td>
  <td><div class="f75">
  <a href="#" onclick="dgei(\'t99\').value='.$maxBaubar.';">('.
  	$maxBaubar.')</a></div></td></tr>';

echo'</tbody></table><p>';
Outputer::button('ok','b');


//Bestehende Produktion anzeigen
$auftrage=$login_dorf->auftrage(11);
if (!empty($auftrage)) {
	echo'<table class="tbg" cellpadding="2" cellspacing="1">
		<tbody><tr class="cbg1">
		<td colspan="3">Wird gebaut</td>
		<td>Dauer</td><td>Fertig</td></tr>';


	$dauer=0;
	foreach($auftrage as $index => $auftrag) {
		$dauer+=$auftrag->get('anzahl')*$auftrag->get('dauer');
		if ($index==0) {
			$ersterAuftrag=$auftrag;
			$dauer-=$auftrag->get('dauer')-
				(strtotime($auftrag->get('zeit'))-time());
		}

		echo'<tr><td width="5%">
			<img class="unit" src="img/un/u/99.gif" border="0"></td>
			<td align="right" width="6%">'.
			$auftrag->get('anzahl').'&nbsp;</td>
			<td class="s7" width="39%">Fallen</td>
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