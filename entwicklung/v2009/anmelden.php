<html>
<head>

<title>Traviatus</title>
<link rel=stylesheet type="text/css" href="unx.css">

</head>

<body>

<?php
include("functions.php");
connect();


if (!isset($_GET['do']))
{
?>


<div id="ltop1" align="right"></div>

<div id="lmidall"><div id="lmidlc"><div id="lleft"><a href="http://www.travian.de/"><img class="logo" src="img/de/a/travian0.gif"></a>

<table id="navi_table" cellspacing="0" cellpadding="0">
<tr>
<td class="menu">
<a href="login.php">Login</a>
<a href="anmelden.php">Anmelden</a>
</td>
</tr>
</table>

</div><div id="lmid1"><div id="lmid3">
<div align="center"><img src="img/de/t1/anmelden.gif" width="468" height="60"></div>
<h5><img src="img/de/t2/u05.gif" width="160" height="15" border="0"></h5>

<form name="snd" method="post" action="anmelden.php?do=register">

<p><table class="p1" style="width:100%"cellspacing="1" cellpadding="0"><tr><td>
<table width="100%" cellspacing="1" cellpadding="0">
<tr><td><label>Nickname:</label><input class="fm fm110" type="text" name="name" value="" maxlength="15"> <span class="e f7"></span></td></tr>
<tr><td><label>Email:</label>
<input class="fm fm110" type="text" name="email" value="" maxlength="40">
 <span class="e f7"></span></td></tr>
<tr><td><label>Passwort</label>
<input class="fm fm110" type="password" name="pw" value="" maxlength="20">
 <span class="e f7"></span></td></tr></table>
</td></tr></table></p>

<p></p><table class="p1" style="width: 100%;" cellpadding="0" cellspacing="1"><tbody><tr><td>
<table cellpadding="0" cellspacing="1">

<tbody><tr>
<td colspan="2"><img src="img/de/t2/u06.gif" border="0" height="15" width="160"></td>
<!--<td colspan="4"><img src="img/de/t2/u07.gif" border="0" height="15" width="160"></td>-->
</tr>

<tr></tr>

<tr>
<td width="25"><input name="vid" value="1" type="radio" checked></td>
<td class="f8" width="150">Römer</td>
<!--<td width="25"><input name="kid" value="0" checked="checked" type="radio"></td>
<td class="f8" width="80">Zufällig</td>
<td width="25">&nbsp;</td>
<td>&nbsp;</td>-->
</tr>

<tr>
<td><input name="vid" value="2" type="radio"></td>
<td class="f8">Germanen</td>

<!--<td><input name="kid" value="1" type="radio"></td>
<td class="f8">Nord-West</td>
<td><input name="kid" value="2" type="radio"></td>
<td class="f8">Nord-Ost</td>-->
</tr>

<tr>
<td><input name="vid" value="3" type="radio"></td>
<td class="f8">Gallier</td>

<!--<td><input name="kid" value="3" type="radio"></td>
<td class="f8">Süd-West</td>
<td><input name="kid" value="4" type="radio"></td>
<td class="f8">Süd-Ost</td>-->

</tr></tbody></table></td></tr></tbody></table>

<p align="center">

<?php
$sql="SELECT `value` FROM `tr1_diverses` WHERE `id`='register';";
$result=mysql_query($sql);
$data=mysql_fetch_array($result);
if ($data['value']=='yes')
{
	?><input type="image" value="anmelden" border="0" name="s1" src="img/de/b/anm1.gif" width="80" height="20" onMousedown="btm1('s1','','img/de/b/anm2.gif',1)" onMouseover="btm1('s1','','img/de/b/anm3.gif',1)" onMouseUp="btm0()" onMouseOut="btm0()"></input></p><?php
}	else
	echo'<p style="color:red;">'.$data['value'].'</p>';
?>



</form>

<p align="center" class="f10 e">Jeder Spieler darf nur einen Account besitzen</p>


</div></div></div><div id="lright1"></div></div>

<div id="lfooter1"><div id="lfooter2">by judos 2007, visit <a href="http://www.travian.de">www.travian.de</a></div></div>

<?php
}
else
{
?>

<div id="ltop1" align="right"></div>

<div id="lmidall"><div id="lmidlc"><div id="lleft"><a href="http://www.travian.de/"><img class="logo" src="img/de/a/travian0.gif"></a>

<table id="navi_table" cellspacing="0" cellpadding="0">
<tr>
<td class="menu">
<a href="login.php">Login</a>
<a href="anmelden.php">Anmelden</a>
</td>
</tr>
</table>

</div><div id="lmid1"><div id="lmid3">
<div align="center"><img src="img/de/t1/anmelden.gif" width="468" height="60"></div>
<h5><img src="img/de/t2/u05.gif" width="160" height="15" border="0"></h5>

<?php
$name=$_POST['name'];
$pw=$_POST['pw'];
$email=$_POST['email'];


$sql="SELECT `value` FROM `tr".$round_id."_diverses` WHERE `id`='register';";
$result=mysql_query($sql);
$data=mysql_fetch_array($result);
if ($data['value']=='yes')
{
	$sql="SELECT `name` FROM `tr".$round_id."_user` WHERE `name`='$name';";
	$result=mysql_query($sql);
	if (mysql_num_rows($result)>0 OR $pw=='' OR $email=='')
	{
		if (mysql_num_rows($result)>0)
			echo'<p align="center" class="f10 e">Dieser Name existiert schon.</p>';
		if ($pw=='')
			echo'<p align="center" class="f10 e">Kein Passwort eingegeben.</p>';
		if ($email=='')
			echo'<p align="center" class="f10 e">Keine Email Adresse angegeben.</p>';
		echo'<br><a href="anmelden.php">zurück</a></p>';
	}
	else
	{
		for ($i=1;$i<=127;$i++)
		{
			$sql="SELECT `id` FROM `tr".$round_id."_user` WHERE `id`='$i';";
			$result=mysql_query($sql);
			if (mysql_num_rows($result)==0) break;
		}


		$sql="INSERT INTO `tr".$round_id."_user` (`id`,`name`,`pw`,`email`,`volk`,`research`,`weapons`,`arms`)
			VALUES ('$i','$name','$pw','$email','$vid','1:0:0:0:0:0:0:0:0:0','0:0:0:0:0:0:0:0:0:0',
					'0:0:0:0:0:0:0:0:0:0');";
		$result=mysql_query($sql);

		do
		{
		$x=mt_rand(1,7);
		$y=mt_rand(1,7);
		$sql="SELECT `user` FROM `tr".$round_id."_dorfer` WHERE `x`='$x' AND `y`='$y';";
		$result=mysql_query($sql);
		$sql2="SELECT `oase` FROM `tr".$round_id."_lander` WHERE `x`='$x' AND `y`='$y' AND `oase`=1;";
		$result2=mysql_query($sql2);
		} while (mysql_num_rows($result)>0 OR mysql_num_rows($result2)>0);

		$gebs1='0';
		for ($nr=2;$nr<=18;$nr++)
			$gebs1=$gebs1.':0';

		$gebs2='';
		$gebs2t='';
		for ($nr=19;$nr<=40;$nr++)
		{
		if ($nr!=26) { $gebs2=$gebs2.'0';$gebs2t=$gebs2t.'0'; }
		if ($nr==26) { $gebs2=$gebs2.'1';$gebs2t=$gebs2t.'15'; }
		if ($nr<40) { $gebs2=$gebs2.':';$gebs2t=$gebs2t.':'; }
		}


		$sql="INSERT INTO `tr".$round_id."_dorfer` (`x`,`y`,`user`,`grosse`,`lager`,`geb1`,`geb2`,`geb2t`)
			VALUES ('$x','$y','$i','1','750:750:750:750','$gebs1','$gebs2','$gebs2t');";
		$result=mysql_query($sql);

		$sql="SELECT `value` FROM `tr".$round_id."_diverses` WHERE `id`='startdorftyp';";
		$result=mysql_query($sql);
		$data=mysql_fetch_array($result);

		$sql="UPDATE `tr".$round_id."_lander` SET `typ`='".$data['value']."' WHERE `x`='$x' AND `y`='$y';";
		$result=mysql_query($sql);


		echo'<p align="center" class="f10 e">Du wurdest erfolgreich angemeldet.</p>';
	}
}
else
	echo'<p align="center" class="f10 e">Die Runde ist voll.</p>';
?>

</div></div></div><div id="lright1"></div></div>

<div id="lfooter1"><div id="lfooter2">by judos 2007, visit <a href="http://www.travian.de">www.travian.de</a></div></div>




<?php
}
?>

</body>
</html>