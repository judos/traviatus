<?php

if ($_GET['do']=='del') {
	for ($i=1;$i<=10;$i++) {
		if (isset($_POST['n'.$i]) and $_POST['n'.$i]!='') {
			$sql="DELETE FROM `tr".ROUND_ID."_msg`
				WHERE `keyid`='".$_POST['n'.$i]."' AND
					`an`='".$login_user->get('name')."' AND `von`='';";
			$result=mysql_query($sql);
		}
	}
}

