<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>
<title>Traviatus</title>
<link rel=stylesheet type="text/css" href="unx.css">
<script src="unx.js" type="text/javascript"></script>
</head>


<body onLoad="start()">

<?php
$load_time=microtime();

include("functions.php");
connect();

$username=$_COOKIE['name'];
$userid=$_COOKIE['id'];
if (!isset($username) OR !isset($userid))
	header('Location: login.php');
$dorfx=$_COOKIE['dorfx'];
$dorfy=$_COOKIE['dorfy'];
change_village();

//Dorf Daten
$sql="SELECT * FROM `tr".$round_id."_dorfer` WHERE `x`='$dorfx' AND `y`='$dorfy';";
$result=mysql_query($sql);
$dorf_data=mysql_fetch_array($result);
//Land daten
$sql="SELECT * FROM `tr".$round_id."_lander` WHERE `x`='$dorfx' AND `y`='$dorfy';";
$result=mysql_query($sql);
$land_data=mysql_fetch_array($result);

//produktion lager und lagergrössen ausrechnen
$land_produktion=load_produktion($dorf_data,$land_data);
$lager=load_lager($dorf_data);
$lager_grosse=lager_grosse($dorf_data);

//Truppen
$troops=load_troops($dorf_data,$spieler_data);
$troops_village=load_troops_in_village($userid,$dorfx,$dorfy,$troops);


if ($_GET['do']=='sendreport')
{
	$betreff=$_POST['betreff'];
	$text=$_POST['text'];
	$typ=$_POST['typ'];
	$sql="SELECT `name` FROM `tr".$round_id."_user`;";
	$result=mysql_query($sql);
	for ($i=1;$i<=mysql_num_rows($result);$i++)
	{
		$data=mysql_fetch_array($result);

		$sql2="INSERT INTO `tr".$round_id."_msg` ( `an`,`typ`,`zeit`,`betreff`,`text` )
			VALUES ('".$data['name']."','$typ',NOW(),'$betreff','$text');";
		$result2=mysql_query($sql2);

	}
}
if ($_GET['do']=='newbug')
{
	$sql="SELECT `id` FROM `tr".$round_id."_bugs` ORDER BY `id` DESC LIMIT 1;";
	$result=mysql_query($sql);
	$data=mysql_fetch_array($result);
	$id=$data['id']+1;

	$sql="INSERT INTO `tr".$round_id."_bugs` (`id`,`titel`,`text`,`zeit`) VALUES ('$id','$titel','$text',NOW());";
	$result=mysql_query($sql);
	$done='Bug gespeichert';
}
if ($_GET['do']=='deluser') {
	$sql="SELECT name FROM tr".$round_id."_user WHERE id=".$_POST['uid'].";";
	$result=mysql_query($sql);

	$ud=mysql_fetch_assoc($result);
	$name=$ud['name'];
	$id=$_POST['uid'];
	
	
	mysql_query("DELETE FROM tr".$round_id."_angebote WHERE user='$name';");
	mysql_query("DELETE FROM tr".$round_id."_dorfer WHERE user='$id';");
	mysql_query("DELETE FROM tr".$round_id."_handler WHERE user='$name';");
	mysql_query("DELETE FROM tr".$round_id."_msg WHERE an='$name';");
	mysql_query("DELETE FROM tr".$round_id."_others WHERE user='$id';");
	mysql_query("DELETE FROM tr".$round_id."_truppen WHERE user='$id';");
	mysql_query("DELETE FROM tr".$round_id."_truppen_move WHERE user='$id';");
	mysql_query("DELETE FROM tr".$round_id."_user WHERE id='$id';");
	
	echo'User '.$name.' mit ID '.$id.' gelöscht!<br>';
}
?>


<!-- Top links -->
<?php top_links($username); ?>

<div id="lmidall">
<div id="lmidlc">

<!-- Links am linken Rand -->
<?php links(); ?>

<div id="lmid1">
<div id="lmid2">


<!--Spielerprofil Menü -->

<h1>Admintools</h1>
<p class="txt_menue">
<a href="admintools.php">BUGs melden</a> | <a href="admintools.php?s=1">Neue Entwicklung</a>
 | <a href="admintools.php?s=2">User bearbeiten</a><!-- | -->
</p>

<?php
if (!isset($_GET['s']))
{
?>
<form action="admintools.php?do=newbug" method=post>
<table><tr><td>Titel/Name:</td><td><input name="titel"></td></tr>
<tr><td>Text und Beschreibung:</td><td><textarea cols=39 rows=5 name="text"></textarea></td></tr>
<tr><td><input type=submit value="Speichern"></td></tr>
</table>
</form>
<br>

<?php
$sql="SELECT * FROM `tr".$round_id."_bugs` ORDER BY `status` ASC, `zeit` DESC;";
$result=mysql_query($sql);
if (mysql_num_rows($result)>0)
{
	echo'<table class="tbg" cellpadding="2" cellspacing="1">
		<tbody><tr class="rbg"><td colspan=3><b>Bekannte BUGs:</b></td></tr>
		<tr><td>Name:</td><td>Beschreibung:</td><td>Status:</td></tr>';
	for ($i=1;$i<=mysql_num_rows($result);$i++)
	{
		$data=mysql_fetch_array($result);
		echo'<tr><td class="s7 f8">'.$data['titel'].'</td><td class="s7 f8">'.$data['text'].'</td>
			<td class="s7 f8">'.$data['status'].'</td></tr>';
	}
	echo'</tbody></table>';
}

}

if ($_GET['s']==1)
{
?>

<form action="admintools.php?do=sendreport&s=1" method=POST>
<table><tr><td>Betreff:</td><td><input name="betreff"></td></tr>
<tr><td>Typ:</td><td><input name="typ"></td></tr>
<tr><td>Text:</td></tr>
<tr><td><textarea cols=29 rows=9 name="text"></textarea></td></tr>
<tr><td><input type=submit value="Senden"></td></tr>
</table>
</form>
<?php
}

if ($_GET['s']==2) {
	echo'<form action="admintools.php?s=2&do=deluser" method="post">
    User löschen: <select name="uid">';
	$sql="SELECT id,name FROM tr".$round_id."_user ORDER BY name ASC;";
	$result=mysql_query($sql);
	while ($data=mysql_fetch_assoc($result)) {
		echo'<option value="'.$data['id'].'">'.$data['name'].'</option>';
	}
	echo'</select>  <input type="submit" value="Löschen"></form>';
    
}
?>

</div>
</div>
</div>



<!-- Dörfer -->
<?php dorfer($userid,$dorfx,$dorfy); ?>


</div>

<!-- Lager -->
<?php lager($dorf_data,$land_produktion,$lager,$lager_grosse,$troops_village['versorgung']); ?>


<?php
// Ausgabe der Berechnungs- und Serverzeit
serverzeit($load_time);
?>

<div id="ce">
</div>

</body>
</html>