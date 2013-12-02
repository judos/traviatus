<?php

if ($_GET['do']=='del') {
	
	$perPage = Diverses::get('berichte_pro_seite');
	
	for ($i=1;$i<=$perPage;$i++) {
		if (isset($_POST['n'.$i]) and $_POST['n'.$i]!='') {
			$sql="DELETE FROM `tr".ROUND_ID."_msg`
				WHERE `keyid`='".$_POST['n'.$i]."' AND
					`an`='".$login_user->get('name')."' AND `von`='';";
			$result=mysql_query($sql);
		}
	}
}

