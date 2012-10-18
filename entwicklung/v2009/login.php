<html>
<head>
<title>Traviatus</title>
<link rel=stylesheet type="text/css" href="unx.css">
<script src="unx.js" type=text/javascript></script>
</head>

<body>

<div id="ltop1" align="right">
</div>

</div><div id="lmidall"><div id="lmidlc"><div id="lleft"><a href="http://www.travian.de/"><img class="logo" src="img/de/a/travian0.gif"></a>

<table id="navi_table" cellspacing="0" cellpadding="0">
<tr>
<td class="menu">
<a href="login.php">Login</a>
<a href="anmelden.php">Anmelden</a>
<a href="../">Neue Version</a>
</td>
</tr>
</table>

</div><div id="lmid1"><div id="lmid3"><div align="center"><img src="img/de/t1/login.gif" width="468" height="60"></div>
<h5><img src="img/de/t2/u04.gif" width="160" height="15" border="0"></h5>
<p class="f9">Um sich einloggen zu können, müssen in ihrem Browser Cookies aktiviert sein. Wenn andere Personen ihren PC mitbenutzen, sollten Sie aus Sicherheitsgründen "<i>In Zukunft automatisch einloggen</i>" deaktivieren, damit das Cookie beim schließen des Browsers wieder gelöscht wird.</p>


<form method="post" name="snd" action="login-check.php">


<p><table class="p1" style="width:100%"cellspacing="1" cellpadding="0"><tr><td>

<table width="100%" cellspacing="1" cellpadding="0">
<tr><td><label>Name:</label>
<input class="fm fm110" type="text" name="name" value="" maxlength="15"> <span class="e f7"></span>
</td></tr>
<tr><td><label>Passwort:</label>
<input class="fm fm110" type="password" name="pw" value="" maxlength="20"> <span class="e f7">
<?php
if ($_GET['login']=='false') echo'Passwort ist falsch';
?>
</span>
</td></tr>
</table></td></tr></table></p>


<p align="center"><input type="hidden" name="ed0bc12" value="8a07c87344">

<?php
include("functions.php");
connect();
$sql="SELECT `value` FROM `tr1_diverses` WHERE `id`='register';";
$result=mysql_query($sql);
$data=mysql_fetch_array($result);
if ($data['value']=='yes')
{
	?><input type="image" value="login" border="0" name="s1" src="img/de/b/l1.gif" width="80" height="20" onMousedown="btm1('s1','','img/de/b/l2.gif',1)" onMouseover="btm1('s1','','img/de/b/l3.gif',1)" onMouseUp="btm0()" onMouseOut="btm0()" onClick="xy()"></input><?php
}	else
	echo'<p style="color:red">'.$data['value'].'</p>';


?>	

</p></form></div></div></div><div id="lright1"></div></div>


<div id="lfooter1"><div id="lfooter2">by judos 2007, visit <a href="http://www.travian.de">www.travian.de</a></div></div>

</body>
</html>