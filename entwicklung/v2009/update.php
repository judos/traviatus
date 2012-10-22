<?php

include("functions.php");
connect();

$pw=$_GET['pw'];
if ($pw=='noob')
{
	$sql="SELECT `user`,`einwohner` FROM `tr".$round_id."_dorfer`;";
	$result=mysql_query($sql);
	for ($i=1;$i<=mysql_num_rows($result);$i++)
	{
		$data=mysql_fetch_array($result);
		$user_punkte[$data['user']]+=$data['einwohner'];
	}

	$sql="SELECT `id`,`name` FROM `tr".$round_id."_user`;";
	$result=mysql_query($sql);
	for ($i=1;$i<=mysql_num_rows($result);$i++)
	{
		$data=mysql_fetch_array($result);

		echo'User: '.$data['name'].', Punkte:'.$user_punkte[$data['id']].'<br>';

		$sql2="UPDATE `tr".$round_id."_user` SET `einwohner`='".$user_punkte[$data['id']]."' WHERE `id`='".$data['id']."';";
		$result2=mysql_query($sql2);
	}

}

?>