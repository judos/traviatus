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


//Daten des Dorfes
$sql="SELECT * FROM `tr".$round_id."_dorfer` WHERE `x`='$dorfx' AND `y`='$dorfy';";
$result=mysql_query($sql);
$dorf_data=mysql_fetch_array($result);
//Daten des Landes
$sql="SELECT `typ` FROM `tr".$round_id."_lander` WHERE `x`='$dorfx' AND `y`='$dorfy';";
$result=mysql_query($sql);
$land_data=mysql_fetch_array($result);
//Lager laden
$lager=load_lager($dorf_data);
//Lager grösse laden
$lager_grosse=lager_grosse($dorf_data);
//Produktion laden
$produktion=load_produktion($dorf_data,$land_data);
//Truppen
$troops=load_troops($dorf_data,$spieler_data);
$troops_village=load_troops_in_village($userid,$dorfx,$dorfy,$troops);







$sql="SELECT * FROM `tr".$round_id."_user`;";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++)
{
	$data=mysql_fetch_array($result);
	$user_name[$data['id']]=$data['name'];
}


$t=$_GET['t'];
$start=$_GET['start'];
if (!isset($start)) $start=0;

?>


<!-- Top links -->
<?php top_links($username); ?>

<div id="lmidall">
<div id="lmidlc">

<!-- Links am linken Rand -->
<?php links(); ?>

<div id="lmid1">
<div id="lmid2">


<!--Statistiken Menü -->


<h1>Statistiken</h1>
<p class="txt_menue">
<a href="statistiken.php">Spieler</a> |
<a href="statistiken.php?t=1">Dörfer</a> |
<a href="statistiken.php?t=2">Allgemein</a>
<!--| <a href="statistiken.php?t=3">WW</a>-->
</p>


<?php
if (!isset($t)) //Spieler
{
?>
<table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr>
<td class="rbg" colspan="5"><a name="h2"></a>Die größten Spieler</td>
</tr>


<tr>
<td width="6%">&nbsp;</td>
<td width="35%">Spieler</td>
<td width="25%">Bevölkerung</td>
<td width="20%">Bestes Dorf</td>
<td width="14%">Dörfer</td>
</tr>

<?php
$sql="SELECT * FROM `tr".$round_id."_user` ORDER BY `einwohner` DESC, `name` ASC;";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++)
{
	$data=mysql_fetch_array($result);

	$sql2="SELECT * FROM `tr".$round_id."_dorfer` WHERE `user`='".$data['id']."' ORDER BY `einwohner` DESC;";
	$result2=mysql_query($sql2);
	$data2=mysql_fetch_array($result2);

	if ($data['name']==$username)
	{
		echo'<tr>'.
			'<td class="li ou nbr" align="right">'.$i.'.&nbsp;</td>'.
			'<td class="s7 ou"><a href="spieler.php?name='.$data['name'].'">'.$data['name'].'</a></td>'.
			'<td class="ou">'.$data['einwohner'].'</td>'.
			'<td class="ou">'.$data2['einwohner'].'</td>'.
			'<td class="re ou">'.mysql_num_rows($result2).'</td>'.
			'</tr>';
	}
	else
	{
		echo'<tr>'.
			'<td class="nbr" align="right">'.$i.'.&nbsp;</td>'.
			'<td class="s7"><a href="spieler.php?name='.$data['name'].'">'.$data['name'].'</a></td>'.
			'<td>'.$data['einwohner'].'</td>'.
			'<td>'.$data2['einwohner'].'</td>'.
			'<td>'.mysql_num_rows($result2).'</td>'.
			'</tr>';
	}
}

echo'</tbody></table>';
}
if ($t==1)	//Dörfer
{
$limit=20;

?>
<table class="tbg" cellpadding="2" cellspacing="1">

<tbody><tr>
<td class="rbg" colspan="5"><a name="h2"></a>Die größten Dörfer</td>
</tr>

<tr>
<td width="6%">&nbsp;</td>
<td width="35%">Dorf</td>
<td width="25%">Spieler</td>
<td width="17%">Einwohner</td>
<td width="17%">Koordinaten</td>
</tr>

<?php
$sql="SELECT * FROM `tr".$round_id."_dorfer` ORDER BY `einwohner` DESC, `name` ASC LIMIT $start,$limit;";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++)
{
	$data=mysql_fetch_array($result);
	if ($data['x']==$dorfx AND $data['y']==$dorfy)
	{
		echo'<tr>'.
			'<td class="li ou nbr right">'.($i+$start).'.&nbsp;</td>'.
			'<td class="s7 ou"><a href="karte.php?x='.$data['x'].'&y='.$data['y'].'">'.$data['name'].'</a></td>'.
			'<td class="s7 ou"><a href="spieler.php?name='.$user_name[$data['user']].'">'.$user_name[$data['user']].
			'</a></td><td class="ou">'.$data['einwohner'].'</td>'.
			'<td class="re ou">('.$data['x'].'|'.$data['y'].')</td>'.
			'</tr>';
	}
	else
	{
		echo'<tr>'.
			'<td class="right nbr">'.($i+$start).'.&nbsp;</td>'.
			'<td class="s7"><a href="karte.php?x='.$data['x'].'&y='.$data['y'].'">'.$data['name'].'</a></td>'.
			'<td class="s7"><a href="spieler.php?name='.$user_name[$data['user']].'">'.$user_name[$data['user']].
			'</a></td><td>'.$data['einwohner'].'</td>'.
			'<td>('.$data['x'].'|'.$data['y'].')</td>'.
			'</tr>';
	}
}

?>

</tbody></table>
<p></p><table class="tbg" cellpadding="0" cellspacing="1"><tbody><tr><td>
<table class="tbg" cellpadding="2" cellspacing="0">
<tbody><tr><td width="240"></td><td class="c r7">
<?php
$sql="SELECT * FROM `tr".$round_id."_dorfer`;";
$result=mysql_query($sql);
$anz=mysql_num_rows($result);

if ($start>0) echo'<a href="statistiken.php?t=1&start='.($start-$limit).'">« zurück</a> | ';
else echo'<span class="c b">« zurück</span> | ';
if ($start+$limit<$anz) echo'<a href="statistiken.php?t=1&start='.($start+$limit).'">weiter »</a>';
else echo'<span class="c b">weiter »</span>';

echo'</td></tr></tbody></table></td></tr></tbody></table>';
}

if ($t==2)
{
$sql="SELECT `id` FROM `tr".$round_id."_user`;";
$result=mysql_query($sql);
$sql="SELECT `name` FROM `tr".$round_id."_dorfer`;";
$result2=mysql_query($sql);
echo'
<table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr class="rbg">
<td colspan="2">Allgemeine Statistiken</td>
</tr>
<tr><td>Anzahl Spieler angemeldet:</td><td width="100">'.mysql_num_rows($result).'</td></tr>
<tr><td>Dörfer insgesamt:</td><td width="100">'.mysql_num_rows($result2).'</td></tr>
</tbody></table>';

}
?>



</div>
</div>
</div>



<!-- Dörfer -->
<?php dorfer($userid,$dorfx,$dorfy); ?>


</div>

<!-- Lager -->
<?php lager($dorf_data,$produktion,$lager,$lager_grosse,$troops_village['versorgung']); ?>

<?php
// Ausgabe der Berechnungs- und Serverzeit
serverzeit($load_time);
?>

<div id="ce">
</div>

</body>
</html>