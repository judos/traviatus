
<html>
<head>
<style type="text/css">
p { font-family: Comic Sans MS; margin:2px; }
input { margin:5px; }
</style>
</head>
<body background="../GFX/back1.gif" text=white link="#DDDDDD" alink="#DDDDDD" vlink="#DDDDDD">
<center>

<?


//Verbinde mit der Datenbank
$link = mysql_pconnect('localhost','meintb','sirius1989');
if (!$link)
    {die('Verbindung nicht möglich : ' . mysql_error());}
if(!mysql_select_db('meintb'))
    {die('Fehler Datenbank konnte nicht ausgewählt werden.');}

$sql="SELECT * FROM `travian_ally`;";
$result=mysql_query($sql);
$anz_ally=mysql_num_rows($result);
for ($i=1;$i<=$anz_ally;$i++)
{
	$data=mysql_fetch_array($result);
	$id=$data['id'];
	$ally_id[$i]=$id;
	$ally_name[$id]=$data['name'];
	$ally_tag[$id]=$data['tag'];
}

$user=$_COOKIE['user'];

$sql="SELECT `travian_name` FROM `user` WHERE `name`='$user';";
$result=mysql_query($sql);
$data=mysql_fetch_array($result);
$tname=$data['travian_name'];

$act=$_GET['do'];
if ($act=='delete')
{
	$sql="UPDATE `user` SET `travian_name`='' WHERE `name`='$user';";
	$result=mysql_query($sql);
}
if ($act=='goally')
{
	$sql="UPDATE `travian_user` SET `ally`='$ally' WHERE `name`='$tname';";
	$result=mysql_query($sql);
	$sql="UPDATE `travian_ally` SET `date`=NOW() WHERE `id`='$ally';";
	$result=mysql_query($sql);
}
if ($act=='newally')
{
	$sql="SELECT `id` FROM `travian_ally` ORDER BY `id` DESC LIMIT 1;";
	$result=mysql_query($sql);
	$data=mysql_fetch_array($result);
	$newid=$data['id']+1;
	$sql="INSERT INTO `travian_ally` (`id`,`tag`,`name`,`date`) VALUES ('$newid','$tag','$name',NOW());";
	$result=mysql_query($sql);
	$sql="UPDATE `travian_user` SET `ally`='$newid' WHERE `name`='$tname';";
	$result=mysql_query($sql);
	$ally_id[$anz_ally+1]=$newid;
	$ally_tag[$newid]=$tag;
	$ally_name[$newid]=$name;
}
if ($act=='leaveally')
{
	$sql="UPDATE `travian_user` SET `ally`='0' WHERE `name`='$tname';";
	$result=mysql_query($sql);
}

include("../intern/intern.php");

echo'<br>';


if (isset($user) AND $tname!='')
{
    $sql="SELECT * FROM `travian_user` WHERE `name`='$tname';";
    $result=mysql_query($sql);
    if (mysql_num_rows($result)==0)
    {
        $sql="SELECT `id` FROM `travian_user` ORDER BY `id` DESC LIMIT 1;";
        $result=mysql_query($sql);
        $data=mysql_fetch_array($result);
        $sql="INSERT INTO `travian_user` (`id`,`name`,`date`) VALUES ('".($data['id']+1)."','$tname',NOW());";
        $result=mysql_query($sql);
        $sql="SELECT * FROM `travian_user` WHERE `name`='$tname';";
        $result=mysql_query($sql);
    }
    if (mysql_num_rows($result)>0)
        $data=mysql_fetch_array($result);
    
    
    echo'<table bgcolor=black border=1 style="border:solid white 1px; border-collapse:collapse;"><tr><td>'.
        '<form method=post action="profil.php?do=delete">'.
        '<p>travian account löschen</p><input type=image src="GFX/ok.gif"></form></td></tr></table><br>';
    echo'<table bgcolor=black style="border:solid white 1px;"><tr><td>';
    if ($data['ally']==0)
    {
        echo'<form method=post action="profil.php?do=goally"><p>du bist in keiner allianz.'.
            ' einer beitreten:</p><select name="ally">';
        for ($i=1;$i<=$anz_ally;$i++)
            echo'<option value="'.$ally_id[$i].'">'.$ally_tag[$ally_id[$i]].' - '.
                $ally_name[$ally_id[$i]].'</option>';
        echo'</select><input type=image src="GFX/ok.gif"></form><br>'.
            '<p>oder allianz gründen:</p><form method=post action="profil.php?do=newally">'.
            'ally-tag:<input name="tag" size=10> ally-name:<input name="name" size=10>'.
            '<input type=image src="GFX/ok.gif"></form>';
    }
    else
    {
    	echo'<p>du bist in der allianz '.$ally_name[$data['ally']].'<br>verlassen:</p>'.
    		'<form method=post action="profil.php?do=leaveally"><input type=image src="GFX/ok.gif">'.
    		'</form><p>gespeicherte Mitglieder:</p>';
        $sql="SELECT `name` FROM `travian_user` WHERE `ally`='".$data['ally']."';";
        $result=mysql_query($sql);
        for ($i=1;$i<=mysql_num_rows($result);$i++)
        {
        	$data=mysql_fetch_array($result);
        	echo'<li>'.$data['name'].'</li>';
        }
    }
    echo'</td></tr></table>';
    
}


?>

</center>
</font>
</body>
</html>