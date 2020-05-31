<?php
if ($_GET['do']=='del') {		
	//Nachrichten im (t=0)Eingang (t=1)Ausgang
	for ($i=1;$i<=10;$i++) {
		if (isset($_POST['n'.$i]) and $_POST['n'.$i]!='') {
			$sql="DELETE FROM `tr".ROUND_ID."_msg`
				WHERE `keyid`='".$_POST['n'.$i]."' AND
					`an`='".$login_user->get('name')."'
					AND `von`!='' AND `typ`='".$_GET['t']."';";
			$result=mysql_query($sql);
		}
	}
}