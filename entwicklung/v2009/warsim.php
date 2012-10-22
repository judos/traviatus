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

update_village($dorfx,$dorfy);

//Daten des Dorfes
$sql="SELECT * FROM `tr".$round_id."_dorfer` WHERE `x`='$dorfx' AND `y`='$dorfy';";
$result=mysql_query($sql);
$dorf_data=mysql_fetch_array($result);
//Daten des Landes
$sql="SELECT `typ` FROM `tr".$round_id."_lander` WHERE `x`='$dorfx' AND `y`='$dorfy';";
$result=mysql_query($sql);
$land_data=mysql_fetch_array($result);
//Spieler daten
$sql="SELECT * FROM `tr".$round_id."_user` WHERE `id`='$userid';";
$result=mysql_query($sql);
$spieler_data=mysql_fetch_array($result);

//Lager laden
$lager=load_lager($dorf_data);

//Lager grösse laden
$lager_grosse=lager_grosse($dorf_data);

//Gebeude Daten laden
$gebeude=load_gebeude($dorf_data,$land_typ_data);

//Produktion laden
$produktion=load_produktion($dorf_data,$land_data);

//Truppen laden
$troops=load_troops($dorf_data,$spieler_data);
$troops_village=load_troops_in_village($userid,$dorfx,$dorfy,$troops);


//Völker laden
$sql="SELECT `value` FROM `tr".$round_id."_diverses` WHERE `id`='völker';";
$result=mysql_query($sql);
$volker=mysql_fetch_array($result);
$volker_namen=split(':',$volker);



function print_volk($volk,$army_vor,$army_nach,$typ,$title)
{
	echo'<table class="tbg" cellpadding="1" cellspacing="1">
		<tbody><tr class="cbg1">
		<td width="21%">';
	if ($typ==1) echo'<span class="c2 b">&nbsp;'.$title.'</span>';
	if ($typ==2) echo'<font color="#71d000"><b>&nbsp;'.$title.'</b></font>';
	echo'</td>';
	for ($i=$volk*10+1;$i<=$volk*10+8;$i++)
		echo'<td><img class="unit" src="img/un/u/'.$i.'.gif" title="'.$troops[$i]['name'].'"></td>';
	echo'</tr><tr><td>Einheiten</td>';
	for ($i=$volk*10+1;$i<=$volk*10+8;$i++)
	{
		if ($army_vor[$i]==0) echo'<td class="c">0</td>';
		else			echo'<td>'.$army_vor[$i].'</td>';
	}
	echo'</tr><tr><td>Verluste</td>';
	for ($i=$volk*10+1;$i<=$volk*10+8;$i++)
	{
		if ($army_vor[$i]-$army_nach[$i]==0) echo'<td class="c">0</td>';
		else			echo'<td>-'.($army_vor[$i]-$army_nach[$i]).'</td>';
	}
	echo'</tr><tr><td>Übrig</td>';
	for ($i=$volk*10+1;$i<=$volk*10+8;$i++)
	{
		if ($army_nach[$i]==0) echo'<td class="c">0</td>';
		else			echo'<td>'.$army_nach[$i].'</td>';
	}
	echo'</tr></tbody></table><p></p>';
}



?>


<!-- Top links -->
<?php top_links($username); ?>


<div id="lmidall">
<div id="lmidlc">


<!-- Links am linken Rand -->
<?php links(); ?>


<!-- Dorfname -->
<div id="lmid1"><div id="lmid2"><h1>Kampfsimulator</h1>
<a href="build.php?id=39">(Zurück zum Versammlungsplatz)</a>
<form action="warsim.php?do=calc" method="post">

<?php
if ($_GET['do']=='calc')
{

for ($id=1;$id<=30;$id++)
{
	$army1[$id]=$_POST['a1_'.$id];
	if ($army1[$id]>0) $v1[floor(($id-1)/10)]=1;

	$army2[$id]=$_POST['a2_'.$id];
	if ($army2[$id]>0) $v2[floor(($id-1)/10)]=1;
}


$ktyp=$_POST['ktyp'];
$army=kampfsim($troops,$army1,$army2,$ktyp);

echo'<p></p>';

$x="Angreifer";
if ($v1[0]==1)
{
	print_volk(0,$army1,$army[1],1,$x);
	$x="Unterstützung";
}
if ($v1[1]==1)
{
	print_volk(1,$army1,$army[1],1,$x);
	$x="Unterstützung";
}
if ($v1[2]==1)
	print_volk(2,$army1,$army[1],1,$x);

$x="Verteidiger";
if ($v2[0]==1)
{
	print_volk(0,$army2,$army[2],2,$x);
	$x="Unterstützung";
}
if ($v2[1]==1)
{
	print_volk(1,$army2,$army[2],2,$x);
	$x="Unterstützung";
}
if ($v2[2]==1)
	print_volk(2,$army2,$army[2],2,$x);

unset($x);
$x[1]='Normal';
$x[2]='Raubzug';

echo'<p class="f10">Kampftyp:<b>'.$x[$ktyp].'</b></p>';


}
?>

<p>
</p>


<table class="f10" width="100%"><tbody><tr>
<td valign="top" width="50%">

<div><font size="+1"><b>Angreifer</b></font></div>

<table class="f10" cellpadding="0" cellspacing="1" width="100%"><tbody>
<?php
for ($id=1;$id<=30;$id++)
{
	if ($troops[$id]['typ']<4)
	{
		if ($volk!=$troops[$id]['volk']) echo'<tr><td colspan="3">
			<img src="img/un/a/x.gif" height="7" width="1"></td></tr>';
		echo'<tr><td><img class="unit" src="img/un/u/'.$id.'.gif" border="0"></td>'.
			'<td class="s7">'.$troops[$id]['name'].'</td>'.
			'<td><input class="fm fm60" name="a1_'.$id.'" value="'.$army1[$id].'" maxlength="6" type="text"></td></tr>';
		$volk=$troops[$id]['volk'];
	}
}
?>
</tbody></table>

</td><td valign="top" width="50%">

<div><font size="+1"><b>Verteidiger</b></font></div>

<table class="f10" cellpadding="0" cellspacing="1" width="100%"><tbody>
<?php
for ($id=1;$id<=30;$id++)
{
	if ($troops[$id]['typ']<4)
	{
		if ($volk!=$troops[$id]['volk']) echo'<tr><td colspan="3">
			<img src="img/un/a/x.gif" height="7" width="1"></td></tr>';
		echo'<tr><td><img class="unit" src="img/un/u/'.$id.'.gif" border="0"></td>'.
			'<td class="s7">'.$troops[$id]['name'].'</td>'.
			'<td><input class="fm fm60" name="a2_'.$id.'" value="'.$army2[$id].'" maxlength="6" type="text"></td></tr>';
		$volk=$troops[$id]['volk'];
	}
}
?>
</tbody></table>

</td></tr></tbody></table><p>
</p><table class="tbg" cellpadding="2" cellspacing="1">

<tbody><tr class="cbg1">
<td>Kampftyp</td>

</tr>
<?php
if (!isset($ktyp)) $ktyp=1;
echo'<td><div><input name="ktyp" value="1" ';
if ($ktyp==1) echo'checked="checked" ';
echo'type="radio"> Normal</div><div><input name="ktyp" value="2" type="radio" ';
if ($ktyp==2) echo'checked="checked" ';
echo'> Raubzug</div>';
?>
<div id="anzeige"><input name="uid" value="13542" type="hidden">&nbsp;</div></td></tr>

</tbody></table>

<p><input value="ok" name="s1" src="img/de/b/ok1.gif" onmousedown="btm1('s1','','img/de/b/ok2.gif',1)" onmouseover="btm1('s1','','img/de/b/ok3.gif',1)" onmouseup="btm0()" onmouseout="btm0()" border="0" height="20" type="image" width="50"></p>

</form></div>


</div></div></div></div>

<!-- Dörfer -->
<?php dorfer($userid,$dorfx,$dorfy); ?>

</div>



<!-- Lager -->
<?php lager($dorf_data,$produktion,$lager,$lager_grosse,$troops_village['versorgung']); ?>

<?php
// Ausgabe der Berechnungs- und Serverzeit
serverzeit($load_time);
?>

<div id="ce"></div>

</body>
</html>