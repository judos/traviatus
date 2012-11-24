<?php
if (ADMINTOOLS) {

	if ($_GET['do']=='terra') {
		$sizex=$_POST['sx'];
		$sizey=$_POST['sy'];
		$oasen=$_POST['oasen'];
		$g15=$_POST['15er'];
		$g9=$_POST['9er'];
		$g1=$_POST['1er'];
		$typen=array(6=>$g15,1=>$g9,14=>$g1);
		Land::createNewMap($sizex,$sizey,$oasen,$typen);
		$msg='Karte neu erstellt.';
	}

	if ($_GET['do']=='delgeb') {
		$login_dorf->gebeudeBau($_GET['gid'],-30);
		gotoP('dorf2');
	}

	if ($_GET['do']=='deltodo') {
		$keyid=$_GET['keyid'];
		$sql="DELETE FROM tr".ROUND_ID."_todo
			WHERE keyid=$keyid;";
		mysql_query($sql);
	}
	if ($_GET['do']=='finishtodo') {
		$keyid=$_GET['keyid'];
		$sql="UPDATE tr".ROUND_ID."_todo
			SET status='finished',fertig='".now()."' WHERE keyid=$keyid;";
		mysql_query($sql);
	}
	if ($_GET['do']=='newtodo') {
		$text=$_POST['text'];
		$sql="INSERT INTO tr".ROUND_ID."_todo
			(text,status,erfasst) VALUES ('$text','','".now()."');";
		mysql_query($sql);
	}
	if ($_GET['do']=='later') {
		$keyid=$_GET['keyid'];
		$sql="UPDATE tr".ROUND_ID."_todo SET erfasst='".now()."'
			WHERE keyid=$keyid;";
		mysql_query($sql);
	}

	if ($_GET['do']=='enoughress') {
		$inf=999999999;
		$login_dorf->set('lager',"$inf:$inf:$inf:$inf");
		gotoP('dorf2');
	}

	if ($_GET['do']=='sendreport') {
		$betreff=$_POST['betreff'];
		$text=$_POST['text'];
		$typ=$_POST['typ'];
		$sql="SELECT `name` FROM `tr".ROUND_ID."_user`;";
		$result=mysql_query($sql);
		while($data=mysql_fetch_array($result)) {
			$sql2="INSERT INTO `tr".ROUND_ID."_msg`
				( `an`,`typ`,`zeit`,`betreff`,`text` )
				VALUES ('".$data['name']."','$typ','".now()."','$betreff','$text');";
			mysql_query($sql2);
		}
	}
	if ($_GET['do']=='newbug') {
		$sql="SELECT `id` FROM `tr".ROUND_ID."_bugs`
			ORDER BY `id` DESC LIMIT 1;";
		$result=mysql_query($sql);
		$data=mysql_fetch_array($result);
		$id=$data['id']+1;

		$sql="INSERT INTO `tr".ROUND_ID."_bugs`
			(`id`,`titel`,`text`,`zeit`)
				VALUES ('$id','$titel','$text',NOW());";
		$result=mysql_query($sql);
		$msg='Bug gespeichert';
	}
	if ($_GET['do']=='deluser') {
		$sql="SELECT name FROM tr".ROUND_ID."_user
			WHERE id=".$_POST['uid'].";";
		$result=mysql_query($sql);

		$ud=mysql_fetch_assoc($result);
		$name=$ud['name'];
		$id=$_POST['uid'];
		
		mysql_query("DELETE FROM tr".ROUND_ID."_angebote
								WHERE user='$name';");
		mysql_query("DELETE FROM tr".ROUND_ID."_dorfer WHERE user='$id';");
		mysql_query("DELETE FROM tr".ROUND_ID."_handler
								WHERE user='$name';");
		mysql_query("DELETE FROM tr".ROUND_ID."_msg WHERE an='$name';");
		mysql_query("DELETE FROM tr".ROUND_ID."_others WHERE user='$id';");
		mysql_query("DELETE FROM tr".ROUND_ID."_truppen WHERE user='$id';");
		mysql_query("DELETE FROM tr".ROUND_ID."_truppen_move
								WHERE user='$id';");
		mysql_query("DELETE FROM tr".ROUND_ID."_user WHERE id='$id';");
		
		$msg='User '.$name.' mit ID '.$id.' gelöscht!<br>';
	}
}