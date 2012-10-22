<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>
<title>Traviatus</title>
<link rel=stylesheet type="text/css" href="unx.css">
<script src="unx.js" type="text/javascript"></script>
</head>


<body onload="start()">

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



//Name des Spielers
$name=$_GET['name'];
if (!isset($name)) $name=$username;

if ($_GET['do']=='cd')
{
	//beschreibung speichern
	$sql="UPDATE `tr".$round_id."_user` SET `besch`='".$_POST['besch']."', `konfig`='".$_POST['handel']."' WHERE `name`='$username';";
	$result=mysql_query($sql);
	//dorfname speichern
	$sql="UPDATE `tr".$round_id."_dorfer` SET `name`='".$_POST['dname']."' WHERE `x`='$dorfx' AND `y`='$dorfy';";
	$result=mysql_query($sql);
}
if ($_GET['do']=='cp')
{
	$sql="SELECT `pw` FROM `tr".$round_id."_user` WHERE `name`='$username';";
	$result=mysql_query($sql);
	$data=mysql_fetch_array($result);

	if ($data['pw']==$_POST['pw1'] AND $_POST['pw2']==$_POST['pw3'])
	{
		$sql="UPDATE `tr".$round_id."_user` SET `pw`='".$_POST['pw2']."' WHERE `name`='$username';";
		$result=mysql_query($sql);
	}
}


//Daten über spieler laden
$sql="SELECT * FROM `tr".$round_id."_user` WHERE `name`='$name';";
$result=mysql_query($sql);
$sp_data=mysql_fetch_array($result);
$sp_data['konfig']=split(':',$sp_data['konfig']);


//Rang herausfinden
$sql="SELECT COUNT(`name`) FROM `tr".$round_id."_user` WHERE `einwohner`>".$sp_data['einwohner'].";";
$result=mysql_query($sql);
$data=mysql_fetch_array($result);
$sp_data['rang']=$data['COUNT(`name`)']+1;

//Völker laden
$sql="SELECT `value` FROM `tr".$round_id."_diverses` WHERE `id`='völker';";
$result=mysql_query($sql);
$data=mysql_fetch_array($result);
$volker=split(':',$data['value']);

//Anzahl dörfer
$sql="SELECT `x`,`y`,`name`,`einwohner` FROM `tr".$round_id."_dorfer`
	WHERE `user`='".$sp_data['id']."'
	ORDER BY `einwohner` DESC, `name` ASC;";
$result=mysql_query($sql);
$sp_data['dorfer']=mysql_num_rows($result);
for ($i=1;$i<=$sp_data['dorfer'];$i++)
{
	$data=mysql_fetch_array($result);
	$dorf[$i]['name']=$data['name'];
	$dorf[$i]['x']=$data['x'];
	$dorf[$i]['y']=$data['y'];
	$dorf[$i]['einwohner']=$data['einwohner'];

	if ($dorfx==$data['x'] AND $dorfy==$data['y']) $akt_dorf=$i;
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

<h1>Spieler Profil</h1>
<?php if ($name==$username) { ?>
<p class="txt_menue">
<a href="spieler.php">Übersicht</a> |
<a href="spieler.php?s=1">Profil</a> |
<a href="spieler.php?s=2">Account</a>
</p>
<?php
}

if (!isset($_GET['s']))
{
?>

<p></p><table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr>
<td class="rbg" colspan="3">Spieler <?php echo $name; ?></td>
</tr>

<tr>
<td colspan="2" width="50%">Details:</td>
<td width="50%">Beschreibung:</td>
</tr>

<tr><td colspan="2"></td><td></td></tr>
<tr><td class="s7">Rang:</td><td class="s7"><?php echo $sp_data['rang']; ?></td>

<td rowspan="7" class="slr3"><?php echo str_replace(chr(13),'<br>',$sp_data['besch']); ?></td></tr>
<tr class="s7"><td>Volk:</td><td><?php echo $volker[$sp_data['volk']-1]; ?></td></tr>
<!-- <tr class="s7"><td>Allianz:</td><td><a href="allianz.php?aid=3940">DWH</a></td></tr> -->
<tr class="s7"><td>Dörfer:</td><td><?php echo $sp_data['dorfer']; ?></td></tr>

<tr class="s7"><td>Bevölkerung:</td><td><?php echo $sp_data['einwohner']; ?></td></tr>
<!--<tr class="s7"><td>Alter:</td><td>2</td></tr>
<tr class="s7"><td>Geschlecht:</td><td>männlich</td></tr>-->
<tr><td></td><td></td></tr>
<tr class="s7"><td colspan="2">
<?php
if ($name==$username) echo'<a href="spieler.php?s=1">» Profil bearbeiten</a>';
else	echo'<a href="nachrichten.php?view=answer&name='.$name.'">» Nachricht schreiben</a>';
?>
</td></tr>

</tbody></table><p>

</p><table class="tbg" cellpadding="2" cellspacing="1">

<tbody><tr>
<td class="rbg" colspan="3">Dörfer:</td>
</tr>

<tr>
<td width="50%">Name</td>
<td width="25%">Einwohner</td>
<td width="25%">Koordinaten</td>
</tr>

<?php
for ($i=1;$i<=$sp_data['dorfer'];$i++)
{
	echo'<tr><td class="s7"><a href="karte.php?x='.$dorf[$i]['x'].'&y='.$dorf[$i]['y'].'">'.$dorf[$i]['name'].'</a>';
	if ($dorf[$i]['grosse']==1) echo'<span class="c">(Hauptdorf)</span>';
	echo'</td><td>'.$dorf[$i]['einwohner'].'</td><td>('.$dorf[$i]['x'].'|'.$dorf[$i]['y'].')</td></tr>';
}
echo'</tbody></table>';
}

if ($_GET['s']=='1')
{
?>
<form action="spieler.php?do=cd" method="post">

<p></p><table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr>
<td class="rbg" colspan="3">Spieler <?php echo $username; ?></td>
</tr>

<tr>
<td colspan="2" width="50%">Details:</td>
<td width="50%">Beschreibung:</td>
</tr>

<tr><td colspan="2"></td><td></td></tr>

<tr class="s7">
<td height="20">Dorfname:</td><td>
<?php echo'<input name="dname" value="'.$dorf[$akt_dorf]['name'].'" size="20" maxlength="20" class="fm" type="text">'; ?>
</td>
<td rowspan="3">
<textarea name="besch" cols="27" rows="12" class="f10 fm_text">
<?php echo $sp_data['besch']; ?>
</textarea>
</td>
</tr>

<tr class="s7"><td height="20">Nachrichten:</td>
<td>
<?php echo'<input type=checkbox name="handel" value="1"';
if ($sp_data['konfig'][0]==1) echo' checked';
echo'>&nbsp;Handelsberichte speichern</td></tr>';
?>

<tr class="s7"><td colspan="2"</td></tr>
</tbody></table>

<p align="center"><input value="" name="s1" src="img/de/b/s1.gif" onmousedown="btm1('s1','','img/de/b/s2.gif',1)" onmouseover="btm1('s1','','img/de/b/s3.gif',1)" onmouseup="btm0()" onmouseout="btm0()" border="0" height="20" type="image" width="80"></p>
</form>


<?php
}
if ($_GET['s']==2)
{
?>

<form action="spieler.php?do=cp" method="post">

<p></p><table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr>
<td class="rbg" colspan="2">Passwort ändern</td>
</tr>


<tr class="s7"><td width="50%">Altes Passwort:</td>
<td width="50%"><input class="fm f110" name="pw1" maxlength="20" type="password"></td>
</tr>

<tr class="s7"><td>Neues Passwort:</td>
<td><input class="fm f110" name="pw2" maxlength="20" type="password"></td>

</tr>


<tr class="s7"><td>Neues Passwort:</td>
<td><input class="fm f110" name="pw3" maxlength="20" type="password"></td>
</tr></tbody></table>
<p align="center"><input value="" name="s1" src="img/de/b/s1.gif" onmousedown="btm1('s1','','img/de/b/s2.gif',1)" onmouseover="btm1('s1','','img/de/b/s3.gif',1)" onmouseup="btm0()" onmouseout="btm0()" border="0" height="20" type="image" width="80"></p>
</form>

<?php
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