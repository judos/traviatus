<?php
//Bereits gesetzt:
//$gebeude=GebeudeTyp::getById($id);

if ($id==12) $stufen=$login_user->waffen();
if ($id==13) $stufen=$login_user->rustungen();
$wr=$id-12;
echo'<p></p><table class="tbg" cellpadding="2"
	cellspacing="1">
	<tbody><tr class="cbg1">
	<td>'.$gebeude->get('name').'</td><td>Aktion</td></tr>';

$typen=$login_user->truppenTypen();
$auftrage=$login_dorf->auftrage(6+$wr);
$auftrage_user=$login_user->auftrage(6+$wr);
$auftrage_ids=array();
foreach($auftrage_user as $a) {
	array_push($auftrage_ids,$a->get('id'));
}

foreach($typen as $tid => $einheit) {
	$index=($tid-1)%10;
	if ($login_user->einheitErforscht($tid) && $einheit->get('typ')<4) {
		
		$kosten=$einheit->verbesserungskosten($stufen[$index]+1);
		$zeit=$einheit->verbesserungszeit($stufen[$index]+1);
		
		echo'<tr><td><table class="f10" cellpadding="0" 
			cellspacing="2" width="100%"><tbody><tr>
			<td rowspan="2" class="s7" valign="top" width="6%">
			<img class="unit" src="img/un/u/'.$tid.'.gif"></td>
			<td class="s7"><div><a href="#" onclick="Popup(1,'.$tid.');
				return false;">'.$einheit->get('name').'</a>
			<span class="f8">(Stufe '.
				$stufen[$index].')</span>
			</div></td></tr>
			<tr><td class="s7" nowrap="nowrap">
			<img src="img/un/a/x.gif" height="15" width="1">';
		for($i=0;$i<4;$i++)
			echo'<img class="res" src="img/un/r/'.($i+1).'.gif">'.
				$kosten[$i].'|';

		echo' <img class="clock" src="img/un/a/clock.gif"> '.
			zeit_dauer($zeit).'</td></tr>
			</tbody></table></td><td width="28%">';
		
		if ($stufe>$stufen[$index]) {
			if (empty($auftrage)) {
				if (!in_array($tid,$auftrage_ids)) {
					if ($login_dorf->genugRess($kosten))
						echo'<a href="?page=build&gid='.$gid.
							'&do=res'.($wr+1).'&tid='.$tid.'">verbessern</a>';
					else
						echo'<div class="c">Zu wenig<br>Rohstoffe</div>';
				}
				else echo'<div class="c">Es wird<br>geforscht</div>';
			}
			else echo'<div class="c">Es wird<br>geforscht</div>';
		}
		else {
			if ($stufe<20) echo'<div class="c">'.
				$gebeude->get('name').'<br>ausbauen</div>';
			else echo'<div class="c">Vollständig<br>
				erforscht</div>';
		}
		echo'</td></tr>';
	}
}
echo'</tbody></table>';
if (!empty($auftrage)) {
	$auftrag=$auftrage[0];
	$einheit=TruppenTyp::getById($auftrag->get('id'));
	$dauer=strtotime($auftrag->get('zeit'))-time();
	echo'<p></p><table class="tbg" cellpadding="2" 
		cellspacing="1"><tbody><tr class="cbg1">
		<td colspan="2">In Forschung</td><td>Dauer</td>
		<td>Fertig</td></tr>
		<tr><td width="6%"><img class="unit" src="img/un/u/'.
			$auftrag->get('id').'.gif" border="0">
		</td><td class="s7" width="44%">'.
			$einheit->get('name').'</td>
		<td width="25%"><span id="timer1">'.
			zeit_dauer($dauer).'</span></td>
		<td width="25%">'.date('H:i',$dauer+time()).
			'<span> Uhr</span></td></tr></tbody></table>';
}