<?php

set_include_path('../');
$path='ajax/';
$script=true;

require("../index.php");

$newmsg=$_POST['newmsg'];
$lastmsg=$_POST['lastmsg'];


if ($login_user!==NULL) {
	$allyId=$login_user->get('ally');
	$ally=Allianz::getById($allyId);
	if ($ally!==NULL) {
		//Nachricht schreiben
		if ($newmsg!='') {
			$sql="INSERT INTO tr".ROUND_ID."_ally_chat
				(ally_id,user_id,zeit,text)
				VALUES
				(".$allyId.",".$login_user->get('id').",'".now()."','".$newmsg."');";
			mysql_query($sql);
		}

		//Nachrichten abrufen
		$sql="SELECT a.zeit as zeit,a.text as text,b.name as name
			FROM tr".ROUND_ID."_ally_chat as a,tr".ROUND_ID."_user as b
			WHERE a.user_id=b.id AND a.zeit>'".$lastmsg."'
			ORDER BY a.zeit ASC;";
		$result=mysql_query($sql);
		$output='';
		while($data=mysql_fetch_assoc($result)) {
			$name=$data['name'];
			$lastmsg=$data['zeit'];
			$output.='<p>['.date('H:i',strtotime($data['zeit'])).'] '.
				'<a href="?page=spieler&name='.$name.'">'.$name.'</a>: '.$data['text'].'</p>';
		}
		echo $lastmsg.'<br>'.$output;
	}
}
?>