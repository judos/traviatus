<?php
$dx=$login_dorf->get('x');
$dy=$login_dorf->get('y');

$c=' class="selected"';

$s=saveGet('s',0);


echo'<p class="txt_menue">
	<a href="?page=build&gid=39" '.($s==0?$c:'').'>Übersicht</a> |
	<a href="?page=build&gid=39&s=2" '.($s==2?$c:'').'>Truppen schicken</a> |
	<a href="?page=build&gid=39&s=1" '.($s==1?$c:'').'>Truppen im Exil</a> |
	<a href="?page=warsim">Kampfsimulator</a>
	</p>';


if ($s==0) {		//Truppen im Dorf
	echo'<p><b>Truppen im Dorf</b></p>';

	$user_ids=Truppe::getUsersByXY($dx,$dy);
	if (empty($user_ids)) {
		echo'Keine Truppen im Dorf.';
	}
	foreach($user_ids as $user_id) {
		$truppe=Truppe::getByXYU($dx,$dy,$user_id);
		echo $truppe->toHtmlBox($login_user,$login_dorf);
	}

	//Truppen unterwegs in dieses dorf
	$truppenmove=TruppeMove::getByZiel($dx,$dy);
	if (!empty($truppenmove)) {
		echo'<p><b>Truppen unterwegs in dieses Dorf</b></p>';
		$zielName=$login_dorf->get('name');

		foreach($truppenmove as $truppe) {
			echo $truppe->toHtmlBox($login_user,$login_dorf);
		}
	}

	//Truppen unterwegs von diesem Dorf
	$truppenmove=TruppeMove::getByStart($dx,$dy);
	if (!empty($truppenmove)) {
		echo'<p><b>Truppen unterwegs von diesem Dorf</b></p>';
		$startName=$login_dorf->get('name');
		$volk=$login_user->get('volk');
		foreach($truppenmove as $truppe) {
			echo $truppe->toHtmlBox($login_user,$login_dorf);
		}
	}
}
if ($s==1) {			//Truppen im Exil
	$userid=$login_user->get('id');
	echo'<p><b>Truppen im Exil</b></p>';

	$sql="SELECT tr".ROUND_ID."_truppen.x,tr".ROUND_ID."_truppen.y
		FROM `tr".ROUND_ID."_truppen`,`tr".ROUND_ID."_dorfer`,`tr".ROUND_ID."_user`
		WHERE tr".ROUND_ID."_truppen.user='".$userid."' AND tr".ROUND_ID."_dorfer.user!='".$userid."'
			AND tr".ROUND_ID."_truppen.x=tr".ROUND_ID."_dorfer.x
			AND tr".ROUND_ID."_truppen.y=tr".ROUND_ID."_dorfer.y
			AND tr".ROUND_ID."_dorfer.user=tr".ROUND_ID."_user.id;";
	$result=mysql_query($sql);
	if (mysql_num_rows($result)==0)
		echo'Keine Truppen im Exil.';
	for ($i=1;$i<=mysql_num_rows($result);$i++) {
		$data=mysql_fetch_array($result);
		$x=$data['x'];
		$y=$data['y'];
		$truppe=Truppe::getByXYU($x,$y,$userid);
		$ruck='<a href="?page=build&gid=39&s=3&x='.$x.'&y='.$y.'">'.
			'Zurückziehen</a>';
		echo $truppe->toHtmlBox($login_user,$login_dorf,$ruck);
	}
}
if ($s==2) {			//Truppen schicken

	$dorfer_zusatz='<a href="#" onclick="dgei(\'zx\').value=$x;dgei(\'zy\').value=$y;dgei(\'dname\').value=\'$dname\';">Ziel</a>';

	if (isset($_GET['msg'])) {
		$values=explode(':',$_GET['va']);
		$keys=explode(':',$_GET['keys']);
		$arr=array_combine($keys,$values);
	}
	?>
	<table class="p1" style="width: 100%;" cellpadding="0" cellspacing="1"><tbody>
	<tr><td><table class="f10" width="100%">
	<form method="post" name="snd" action="?page=a2b">
	<tbody>
	<?php
	$truppe=Truppe::getByXYU($dx,$dy,$login_user);
	$soldaten=$truppe->soldatenId();
	for ($row=0;$row<3;$row++) {	//Einheiten auflisten
		echo'<tr>';
		for ($colomn=0;$colomn<4;$colomn++) {
			$nr=$row+1+$colomn*3;
			$id=$nr+($login_user->get('volk')-1)*10;
			//Nur für die Reihenfolge eingefügt:
			if ($id>$login_user->get('volk')*10) $id-=2;
			//letzte Felder bleiben leer
			if ($row<2 or $colomn<2) {
				$einheit=TruppenTyp::getById($id);
				$value='';
				if (isset($arr['t'.$id])) $value=$arr['t'.$id];
				echo'<td width="20">
					<img class="unit" src="img/un/u/'.$id.'.gif"
						title="'.$einheit->get('name').'"
						onclick="document.snd.t'.$id.'.value=\'\'; return false;"
						border="0"></td>
					<td width="35"><input class="fm" name="t'.$id.'" value="'.$value.'"
						size="2" maxlength="6" type="text" autocomplete="off"></td>
					<td class="f8" width="70">
					<a href="#" onclick="document.snd.t'.$id.'.value='.
					$soldaten[$id].'; return false;">('.
					$soldaten[$id].')</a></td>';
			}
			elseif ($colomn==3) {	//Held
				$value='';
				if (isset($arr['thero'])) $value=$arr['thero'];
				echo'<td width="20">
					<img class="unit" src="img/un/u/hero.gif"
						title="Held"
						onclick="document.snd.thero.value=\'\'; return false;"
						border="0"></td>
					<td width="35"><input class="fm" name="thero" value="'.$value.'"
						size="2" maxlength="6" type="text" autocomplete="off"></td>
					<td class="f8" width="70">
					<a href="#" onclick="document.snd.thero.value='.$soldaten['hero'].';
					return false;">('.$soldaten['hero'].')</a></td>';
			}
			else { echo'<td colspan="3"></td>'; }
		}
		echo'</tr>';
	}
	?>

	</tbody></table></td></tr></tbody></table>
	<p></p><table class="f10" width="100%">
	<tbody><tr><td valign="top" width="33%">
	<?php
	if (isset($arr['x'])) $_GET['x']=$arr['x'];
	$x=saveGet('x','');
	if (isset($arr['y'])) $_GET['y']=$arr['y'];
	$y=saveGet('y','');
	if (isset($arr['dname'])) $_GET['dname']=$arr['dname'];
	$dname=saveGet('dname','');
	$c=2;
	if (isset($arr['c'])) $c=$arr['c'];
	echo'<div class="f10"><input name="c" value="2" '.($c==2?'checked="checked"':'').'
      type="radio">Unterstützung</div>
    <div class="f10"><input name="c" value="3" '.($c==3?'checked="checked"':'').'
      type="radio">Angriff: Normal</div>
    <div class="f10"><input name="c" value="4" '.($c==4?'checked="checked"':'').'
      type="radio">Angriff: Raubzug</div>
    </td><td valign="top"><div class="b f135">Dorf: ';

	echo'<input class="fm" id="dname" name="dname" value="'.$dname.'" size="10"
			maxlength="20" type="text"></div>
		<div><i>oder</i></div><div class="b f135">';
	echo'X: <input class="fm" id="zx" name="x" value="'.$x.'"
		size="2" maxlength="4" type="text" autocomplete="off">
			Y: <input class="fm" id="zy" name="y" value="'.$y.'"
		size="2" maxlength="4" type="text" autocomplete="off">';
	echo'</div></td></tr></tbody></table>
		<p>';
	Outputer::button('s1','ok');
	echo'</p>
		</form>';

	if (isset($_GET['msg'])) {
		echo'<div class="f10 e b">'.$_GET['msg'].'</div>';
	}

}