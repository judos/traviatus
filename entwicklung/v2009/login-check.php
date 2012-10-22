<?php

include("functions.php");
connect();

$name=$_POST['name'];
$pw=$_POST['pw'];

$sql="SELECT * FROM `tr".$round_id."_user` WHERE `name`='$name';";
$result=mysql_query($sql);

if (mysql_num_rows($result)==0) header('Location: login.php?login=false');
else
{
	$data=mysql_fetch_array($result);
	$id=$data['id'];

	$sql="SELECT `name` FROM `tr".$round_id."_user` WHERE `id`='$id';";
	$result=mysql_query($sql);
	$spieler_data=mysql_fetch_array($result);
	$name=$spieler_data['name'];

	$sql="SELECT `x`,`y` FROM `tr".$round_id."_dorfer` WHERE `user`='$id' AND `grosse`='1';";
	$result=mysql_query($sql);
	$data=mysql_fetch_array($result);

	setcookie('name',$name);
	setcookie('id',$id);
	setcookie('dorfx',$data['x']);
	setcookie('dorfy',$data['y']);

	header('Location: dorf1.php');
}

?>