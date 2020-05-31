<?php

$feste=Fest::getAll();
$anz=0;
$auftrage=$login_dorf->auftrage(8);

echo'<table class="tbg" cellpadding="2" cellspacing="1">
	<tbody><tr class="cbg1"><td>Rathaus</td>
	<td>Aktion</td></tr>';
	
foreach($feste as $fest) {
	
	if ($fest->feierbar($login_dorf)) {
		$kosten=$fest->get('kosten');
		
		echo'<tr><td>
			<table class="f10" cellpadding="0" cellspacing="2" width="100%">
			<tbody><tr><td class="s7"><div>
			<a href="#">'.$fest->get('name').'</a>
			<span class="f8">('.$fest->kp($login_dorf).' Kulturpunkte)</span>
			</div></td></tr><tr><td class="s7" nowrap="nowrap">
			<img src="img/un/a/x.gif" height="15" width="1">';
		for ($i=0;$i<4;$i++)
			echo'<img class="res" src="img/un/r/'.($i+1).'.gif">'.
				$kosten[$i].'|';
		
		echo'<img src="img/un/a/clock.gif" height="12" width="18"> '.
			zeit_dauer($fest->dauer($login_dorf)).'
			</td></tr></tbody></table></td><td width="28%">';
		
		if (empty($auftrage)) {
			if ($login_dorf->genugRess($kosten))
				echo'<a href="?page=build&gid='.$gid.
					'&do=fest&x='.$fest->get('id').'">veranstalten</a>';
			else
				echo'<span class="c">Zu wenig Rohstoffe</span>';
		}
		else
			echo'<span class="c">Es wird bereits ein Fest gefeiert</span>';
		echo'</td></tr>';
		$anz++;
	}
}
if ($anz==0) {
	echo'<tr><td colspan="3" class="c">Es kann noch nichts
		veranstaltet werden.</td></tr>';
}
echo'</tbody></table>';

if (!empty($auftrage)) {
	$auftrag=$auftrage[0];
	
	$dauer=strtotime($auftrag->get('zeit'))-time();
	$fest=Fest::getById($auftrag->get('id'));
	
	echo'<p></p><table class="tbg" cellpadding="2" cellspacing="1"><tbody>
		<tr class="cbg1"><td>Fest</td><td>Dauer</td><td>Fertig</td></tr><tr>
		<td class="s7" width="44%">'.$fest->get('name').'
		<span class="c">(erbringt '.$fest->kp($login_dorf).'KP)</span>
		</td><td width="25%">
		<span id="timer1">'.zeit_dauer($dauer).'</span></td>
		<td width="25%">'.date('H:i',time()+$dauer).'<span> Uhr </span>'.
		date('d.m.Y',$dauer+time()).'</td></tr></tbody></table>';
}