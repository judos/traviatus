<?php
$dx=$login_dorf->get('x');
$dy=$login_dorf->get('y');

$c=' class="selected"';
$s=$_GET['s'];
echo'<p class="txt_menue">
	<a href="?page=build&gid=39" '.($s==0?$c:'').'>Übersicht</a> |
	<a href="?page=build&gid=39&s=2" '.($s==2?$c:'').'>Truppen schicken</a> |
	<a href="?page=build&gid=39&s=1" '.($s==1?$c:'').'>Truppen im Exil</a> |
	<a href="?page=warsim">Kampfsimulator</a>
	</p>';

function print_troops($dorf_name,$x,$y,$titel,$volk,
	$army,$unterhalt=NULL,$ankunft=NULL,$user_show=1,$user_name='') {
	global $troops,$timerNr;
	if (!isset($timerNr)) $timerNr=1;
	$volk--;
	echo'<table class="tbg" cellpadding="2" cellspacing="1"><tbody>
		<tr class="cbg1"><td width="21%">
		<a href="?page=karte-show&x='.$x.'&y='.$y.'"><span class="c0">'.
		$dorf_name.'</span></a>
		</td><td colspan="11" class="b">'.$titel.'</td></tr>
		<tr class="unit">
		<td>&nbsp;<a href="spieler.php?name='.$user_name.'">'.
		$user_name.'</a></td>';
	for ($j=1;$j<=10;$j++)
		echo'<td><img src="img/un/u/'.($j+$volk*10).'.gif"
			title="'.$troops[$j+$volk*10]['name'].'"></td>';
	echo'<td><img src="img/un/u/hero.gif" title="Held"></td></tr>
		<tr><td>Einheiten</td>';
	for ($j=1;$j<=10;$j++) {
		if ($user_show==1) {
			if ($army[$j+$volk*10]>0) echo'<td>'.$army[$j+$volk*10].'</td>';
			else	echo'<td class="c">0</td>';
		}
		else
			echo'<td class="c">?</td>';
	}
	if ($user_show==1) {
		if (!isset($army['hero'])) echo'<td class="c">0</td>';
		else	echo'<td>1</td>';
	}
	else
		echo'<td class="c">?</td>';
	echo'</tr><tr class="cbg1">';
	if ($ankunft==NULL)
		echo'<td>Unterhalt</td><td class="s7" colspan="11">'.$unterhalt.
			'<img class="res" src="img/un/r/4.gif">pro Stunde</td>';
	else {
		$dauer=zeit_dauer(strtotime($ankunft)-time());
		$akt=date('H:i:s',strtotime($ankunft));
		echo'<td>Ankunft</td><td colspan="11">
			<table class="f10" cellpadding="0" cellspacing="0" width="100%">
			<tbody><tr align="center">
			<td width="50%">&nbsp; in <span id="timer'.$timerNr.'">'.
				$dauer.'</span> Std.</td>
			<td width="50%">um '.$akt.'<span> Uhr</span>
			</td></tr></tbody></table></td>';
		$timerNr++;
	}
	echo'</tr></tbody></table><p></p>';

}

if (!isset($_GET['s'])) {		//Truppen im Dorf
	echo'<p><b>Truppen im Dorf</b></p>';


	$user_ids=Truppe::getUsersByXY($dx,$dy);
	foreach($user_ids as $user_id) {
		$truppe=Truppe::getByXYU($dx,$dy,$user_id);
		$spieler=Spieler::getById($user_id);
		$volk=$spieler->get('volk');
		if ($user_id==$login_user->get('id')) {
			$name='Eigene Truppen';
			$dorf=$login_dorf;
		}
		else {
			$showname=$spieler->get('name');
			$name='Truppen von <a href="?page=spieler&name='.$showname.'">'.$showname.'</a>';
			$dorf=Dorf::getByXY($truppe->get('ursprung_x'),$truppe->get('ursprung_y'));
		}

		$soldaten=$truppe->soldatenId();
		$versorgung=$truppe->versorgung();
		print_troops($dorf->get('name'),$dorf->get('x'),$dorf->get('y'),$name,$volk,$soldaten,$versorgung);
	}

	//Truppen unterwegs in dieses dorf
	$truppenmove=TruppeMove::getByZiel($dx,$dy);
	if (!empty($truppenmove)) {
		echo'<p><b>Truppen unterwegs in dieses Dorf</b></p>';
		$zielName=$login_dorf->get('name');

		foreach($truppenmove as $truppe) {
			$sx=$truppe->get('start_x');
			$sy=$truppe->get('start_y');
			$startDorf=$truppe->startDorf();
			$startName=$startDorf->get('name');
			$spieler=$startDorf->user();
			$volk=$spieler->get('volk');
			$soldaten=$truppe->soldatenId();

			switch($truppe->get('aktion')) {
				case 1:$lnk='Neues Dorf gründen';break;
				case 2:$lnk='Unterstützung für '.$zielName;break;
				case 3:$lnk='Angriff gegen '.$zielName;break;
				case 4:$lnk='Raubzug gegen '.$zielName;break;
				case 5:$lnk='Ausspähen von '.$zielName;break;
			}
			$lnk='<a href="?page=karte-show&x='.$dx.'&y='.$dy.'">
				<span class="c0">'.$lnk.'</span></a>';
			$show=1;
			$name_of_user='';
			if ($spieler->get('id')!=$login_user->get('id')) $show=0;
			if ($show) $name_of_user=$spieler->get('name');

			print_troops($startName,$sx,$sy,$lnk,$volk,$soldaten,NULL,
									 $truppe->get('ziel_zeit'),$show,$name_of_user);
		}
	}

	//Truppen unterwegs von diesem Dorf
	$truppenmove=TruppeMove::getByStart($dx,$dy);
	if (!empty($truppenmove)) {
		echo'<p><b>Truppen unterwegs von diesem Dorf</b></p>';
		$startName=$login_dorf->get('name');
		$volk=$login_user->get('volk');
		foreach($truppenmove as $truppe) {
			$zx=$truppe->get('ziel_x');
			$zy=$truppe->get('ziel_y');
			$zielDorf=$truppe->zielDorf();
			if ($zielDorf!==NULL)
				$zielName=$zielDorf->get('name');
			$soldaten=$truppe->soldatenId();

			switch($truppe->get('aktion')) {
				case 1:$lnk='Neues Dorf gründen ('.$zx.'|'.$zy.')';break;
				case 2:$lnk='Unterstützung für '.$zielName;break;
				case 3:$lnk='Angriff gegen '.$zielName;break;
				case 4:$lnk='Raubzug gegen '.$zielName;break;
				case 5:$lnk='Ausspähen von '.$zielName;break;
			}
			$lnk='<a href="?page=karte-show&x='.$zx.'&y='.$zy.'">
				<span class="c0">'.$lnk.'</span></a>';

			print_troops($startName,$dx,$dy,$lnk,$volk,$soldaten,NULL,
									 $truppe->get('ziel_zeit'));
		}
	}
}
if ($_GET['s']==1) {			//Truppen im Exil
	echo'<p><b>Truppen im Exil</b></p>';

	$sql="SELECT tr".ROUND_ID."_truppen.*,tr".ROUND_ID."_dorfer.name,tr".ROUND_ID."_user.name AS name2
		FROM `tr".ROUND_ID."_truppen`,`tr".ROUND_ID."_dorfer`,`tr".ROUND_ID."_user`
		WHERE tr".ROUND_ID."_truppen.user='".$userid."' AND tr".ROUND_ID."_dorfer.user!='".$userid."'
			AND tr".ROUND_ID."_truppen.x=tr".ROUND_ID."_dorfer.x
			AND tr".ROUND_ID."_truppen.y=tr".ROUND_ID."_dorfer.y
			AND tr_dorfer.user=tr_user.id;";
	$result=mysql_query($sql);
	for ($i=1;$i<=mysql_num_rows($result);$i++)
	{
		$data=mysql_fetch_array($result);
//					var_dump($data);

		$t=split(':',$data['troops']);
		for ($j=1;$j<=10;$j++)
			$truppen[$j]=$t[$j-1];
		$versorgung=versorgung_von_truppen($troops,$t,$spieler_volk);

		print_troops($data['name'],$data['x'],$data['y'],'Dorf von '.$data['name2'],$spieler_volk,
			$truppen,$versorgung);
	}
}
if ($_GET['s']==2) {			//Truppen schicken

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
	$truppe=Truppe::getByXYU($dx,$dy,$login_user->get('id'));
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
					<a href="#" onclick="document.snd.t'.$id.'.value=0;
					return false;">(0)</a></td>';
			}
			else { echo'<td colspan="3">'; }
		}
		echo'</tr>';
	}
	?>

	</tbody></table></td></tr></tbody></table>
	<p></p><table class="f10" width="100%">
	<tbody><tr><td valign="top" width="33%">
	<?php
	if (isset($arr['x'])) $_GET['x']=$arr['x'];
	if (isset($arr['y'])) $_GET['y']=$arr['y'];
	if (isset($arr['dname'])) $_GET['dname']=$arr['dname'];
	$c=2;
	if (isset($arr['c'])) $c=$arr['c'];
	echo'<div class="f10"><input name="c" value="2" '.($c==2?'checked="checked"':'').'
      type="radio">Unterstützung</div>
    <div class="f10"><input name="c" value="3" '.($c==3?'checked="checked"':'').'
      type="radio">Angriff: Normal</div>
    <div class="f10"><input name="c" value="4" '.($c==4?'checked="checked"':'').'
      type="radio">Angriff: Raubzug</div>
    </td><td valign="top"><div class="b f135">Dorf: ';

	echo'<input class="fm" id="dname" name="dname" value="'.$_GET['dname'].'" size="10"
			maxlength="20" type="text"></div>
		<div><i>oder</i></div><div class="b f135">';
	echo'X: <input class="fm" id="zx" name="x" value="'.$_GET['x'].'"
		size="2" maxlength="4" type="text" autocomplete="off">
			Y: <input class="fm" id="zy" name="y" value="'.$_GET['y'].'"
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