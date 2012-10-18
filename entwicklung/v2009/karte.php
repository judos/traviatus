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

//oooooooooooooooooooooooooooooooooooooooooooooooo
//oooooooooooooo  CONFIG ooooooooooooooooooooooooo
//oooooooooooooooooooooooooooooooooooooooooooooooo
$size_x=7;
$size_y=9;
//oooooooooooooooooooooooooooooooooooooooooooooooo
//oooooooooooooooooooooooooooooooooooooooooooooooo


include("functions.php");
connect();

$username=$_COOKIE['name'];
$userid=$_COOKIE['id'];
if (!isset($username) OR !isset($userid))
	header('Location: login.php');

$dorfx=$_COOKIE['dorfx'];
$dorfy=$_COOKIE['dorfy'];
change_village();




//Daten des Spielers
$sql="SELECT * FROM `tr".$round_id."_user` WHERE `id`='$userid';";
$result=mysql_query($sql);
$spieler_data=mysql_fetch_array($result);
$volk=$spieler_data['volk'];
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
//Gebäude
$gebeude=load_gebeude($dorf_data,$land_typ_data);




//Daten der Land typen
$sql="SELECT * FROM `tr".$round_id."_land_typen`;";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++)
{
	$data=mysql_fetch_array($result);
	$land_ver_geb=split(':',$data['geb']);
	for ($j=0;$j<=17;$j++)
	{
		$land_verteilung[$data['typ']][$land_ver_geb[$j]]++;
	}
}




$sql="SELECT `value` FROM `tr".$round_id."_diverses` WHERE `id`='völker';";
$result=mysql_query($sql);
$data=mysql_fetch_array($result);
$volk_name=split(':',$data['value']);

$sql="SELECT * FROM `tr".$round_id."_user`;";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++)
{
	$data=mysql_fetch_array($result);
	$user_name[$data['id']]=$data['name'];
	$user_volk[$data['id']]=$volk_name[$data['volk']-1];
}

$px=$_REQUEST['x'];
$py=$_REQUEST['y'];

if (!isset($px)) $px=$dorfx;
if (!isset($py)) $py=$dorfy;
if (!isset($_GET['do']))
{
	if ($px-3<1) $px=3+1;
	if ($py-3<1) $py=3+1;
	if ($px+3>$size_x) $px=-3+$size_x;
	if ($py+3>$size_y) $py=-3+$size_y;
}


$sql="SELECT * FROM `tr".$round_id."_lander`
	WHERE `x`>'".($px-4)."' AND `x`<'".($px+4)."' AND `y`>'".($py-4)."' AND `y`<'".($py+4)."';";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++)
{
	$data=mysql_fetch_array($result);
	$x=$data['x'];
	$y=$data['y'];
	$land_oase[$x][$y]=$data['oase'];
	$land_typ[$x][$y]=$data['typ'];
	$land_aussehen[$x][$y]=$data['aussehen'];
}

$sql="SELECT * FROM `tr".$round_id."_dorfer`
	WHERE `x`>'".($px-4)."' AND `x`<'".($px+4)."' AND `y`>'".($py-4)."' AND `y`<'".($py+4)."';";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++)
{
	$data=mysql_fetch_array($result);
	$x=$data['x'];
	$y=$data['y'];
	$dorf_username[$x][$y]=$user_name[$data['user']];
	$dorf_volkname[$x][$y]=$user_volk[$data['user']];
	$dorf_name[$x][$y]=$data['name'];
	$dorf_einwohner[$x][$y]=$data['einwohner'];
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

<?php
if (!isset($_GET['do']))
{
?>




<!--Karte -->
<div id="lplz3"></div>
<script language="JavaScript" type="text/javascript">
<!--
text_details = 'Details:';
text_spieler = 'Spieler:';
text_einwohner = 'Einwohner:';
text_allianz = 'Allianz:';
// -->
</script>

<div class="map_infobox" id="tb">
<table class="f8 map_infobox_grey" cellpadding="2" cellspacing="1">
<tbody>
<tr><td class="c b" colspan="2" align="center">Details:</td></tr>
<tr><td class="c s7" width="45%">Spieler:</td><td class="c s7">-</td></tr>
<tr><td class="c s7">Einwohner:</td><td class="c s7">-</td></tr>
<tr><td class="c s7">Allianz:</td><td class="c s7">-</td></tr>
</tbody>
</table>
</div>

<div class="mbg"></div>

<div id="map_content">

<div class="map_show_xy">
<table cellpadding="0" cellspacing="0" width="100%">
<tbody><tr>
<td width="30%"><h1>Karte</h1></td>
<td class="right nbr" width="33%"><h1>(<span id="x"><?php echo $px; ?></span></h1></td>
<td align="center" width="4%"><h1>|</h1></td>
<td class="left nbr" width="33%"><h1><span id="y"><?php echo $py; ?></span>)</h1></td>
</tr>
</tbody></table>
</div>

<div class="mdiv" style="z-index: 2;">
<?php

$nr=1;
for ($y=-3;$y<=3;$y++)
{
	for ($x=-3;$x<=3;$x++)
	{
		if ($land_oase[$x+$px][$y+$py]==1)
		{
			$bild=$land_typ[$x+$px][$y+$py];
			echo'<img class="mt'.$nr.'" src="img/un/m/o'.$bild.'.gif">';
		}
		else
		{
			if (isset($dorf_name[$x+$px][$y+$py]))
			{
				$bild=0;
				if ($dorf_einwohner[$x+$px][$y+$py]>99) $bild+=10;
				if ($dorf_einwohner[$x+$px][$y+$py]>399) $bild+=10;
				if ($dorf_einwohner[$x+$px][$y+$py]>999) $bild+=10;
				if ($dorf_username[$x+$px][$y+$py]==$username) $bild+=1;
				if ($bild<10) $bild='0'.$bild;
				echo'<img class="mt'.$nr.'" src="img/un/m/d'.$bild.'.gif">';
			}
			else
			{
				if (isset($land_aussehen[$x+$px][$y+$py]))
					echo'<img class="mt'.$nr.'" src="img/un/m/t'.$land_aussehen[$x+$px][$y+$py].'.gif">';
				else
				{
					$typ=(pow($px+$x,2)+($py+$y)*$size_x)%6+1;
					echo'<img class="mt'.$nr.'" src="img/un/m/t_no'.$typ.'.png">';
				}
			}
		}
		$nr++;
	}
}

?>
</div>


<?php
for ($y=1;$y<=7;$y++)
	echo'<div class="my'.$y.'">'.($y-4+$py).'</div>';
for ($x=1;$x<=7;$x++)
	echo'<div class="mx'.$x.'">'.($x-4+$px).'</div>';
?>

<map id="map190888" name="map190888">
<?php
if ($py-4>=1) echo'<area href="karte.php?x='.$px.'&y='.($py-1).'" coords="422,137,25" shape="circle" title="Norden">';
if ($px+4<=$size_x)
	echo'<area href="karte.php?x='.($px+1).'&y='.$py.'" coords="427,324,25" shape="circle" title="Osten">';
if ($py+4<=$size_y)
	echo'<area href="karte.php?x='.$px.'&y='.($py+1).'" coords="119,325,25" shape="circle" title="Süden">';
if ($px-4>=1) echo'<area href="karte.php?x='.($px-1).'&y='.$py.'" coords="114,133,25" shape="circle" title="Westen">';
?>

<?php echo'<area href="karte.php?x='.$px.'&y='.($py-7); ?>" coords="475,369, 497,357, 519,369, 497,381" shape="poly" title="Norden">
<?php echo'<area href="karte.php?x='.($px+7).'&y='.$py; ?>" coords="475,395, 497,383, 519,395, 497,407" shape="poly" title="Osten">
<?php echo'<area href="karte.php?x='.$px.'&y='.($py+7); ?>" coords="428,395, 450,383, 472,395, 450,407" shape="poly" title="Süden">
<?php echo'<area href="karte.php?x='.($px-7).'&y='.$py; ?>" coords="428,369, 450,357, 472,369, 450,381" shape="poly" title="Westen">


<?php
for ($y=1;$y<=7;$y++)
{
	for ($x=1;$x<=7;$x++)
	{
		$showx=$x-4+$px;
		$showy=$y-4+$py;
		$d_name=$dorf_name[$showx][$showy];
		$s_name=$dorf_username[$showx][$showy];
		$d_einwohner=$dorf_einwohner[$showx][$showy];
		$ally='-';

		if (isset($d_name))
			echo'<area href="karte.php?do=show&x='.$showx.'&y='.$showy.'" '.
'onmouseover="'."map('".$d_name."','".$s_name."','".$d_einwohner."','".$ally."','".$showx."','".$showy."')".'" '.
'onmouseout="'."map('','','','','".$px."','".$py."')".'" '.
'coords="'.(229+37*($x-1)-36*($y-1)).','.(110+20*($x-1)+20*($y-1)).','.
(265+37*($x-1)-36*($y-1)).','.(90+20*($x-1)+20*($y-1)).','.
(302+37*($x-1)-36*($y-1)).','.(110+20*($x-1)+20*($y-1)).','.
(265+37*($x-1)-36*($y-1)).','.(130+20*($x-1)+20*($y-1)).'" '.
'shape="poly">';
		else
			echo'<area href="karte.php?do=show&x='.$showx.'&y='.$showy.'" '.
'onmouseover="'."x_y('".$showx."','".$showy."')".'" '.
'onmouseout="'."x_y('".$showx."','".$showy."')".'" '.
'coords="'.(229+37*($x-1)-36*($y-1)).','.(110+20*($x-1)+20*($y-1)).','.
(265+37*($x-1)-36*($y-1)).','.(90+20*($x-1)+20*($y-1)).','.
(302+37*($x-1)-36*($y-1)).','.(110+20*($x-1)+20*($y-1)).','.
(265+37*($x-1)-36*($y-1)).','.(130+20*($x-1)+20*($y-1)).'" '.
'shape="poly">';

	}
}
?>

</map>


<img class="mdiv" style="z-index: 15;" usemap="#map190888" src="img/un/a/x.gif">
<div class="map_insert_xy">
<form method="post" action="karte.php">
<table align="center" cellpadding="3" cellspacing="0">
<tbody><tr>
<td><b>x</b></td>
<?php
echo'<td><input class="fm fm25" name="x" value="'.$px.'" size="2" maxlength="4"></td>
<td><b>y</b></td>
<td><input class="fm fm25" name="y" value="'.$py.'" size="2" maxlength="4"></td>';
?>
<td></td>
<td><input value="ok" name="s1" src="img/de/b/ok1.gif" onmousedown="btm1('s1','','img/de/b/ok2.gif',1)" onmouseover="btm1('s1','','img/de/b/ok3.gif',1)" onmouseup="btm0()" onmouseout="btm0()" height="20" type="image" width="50"></td>
</tr></tbody>
</table>
</form>

<?php
}
if ($_GET['do']=='show')
{

if (isset($dorf_username[$px][$py]))
{
?>
<div class="dname"><h1><?php echo $dorf_name[$px][$py].' ('.$px.'|'.$py.')'; ?></h1></div>

<?php echo'<div id="f'.$land_typ[$px][$py].'"></div>'; ?>

<div class="map_details_right">
<div class="f10 b">&nbsp;<?php echo $dorf_name[$px][$py].' ('.$px.'|'.$py.')'; ?></div>

<table class="f10">
<tbody><tr>
<td><img src="img/un/a/x.gif" border="0" height="12" width="3"></td>
<td>Volk:</td><td> <b><?php echo$dorf_volkname[$px][$py]; ?></b></td>
</tr>

<tr>
<td><img src="img/un/a/x.gif" border="0" height="12" width="3"></td>
<td>Allianz:</td><td><a href="allianz.php?name=">-</a></td>
</tr>
<tr>
<td><img src="img/un/a/x.gif" border="0" height="12" width="3"></td>
<td>Besitzer:</td><td>
<?php echo'<a href="spieler.php?name='.$dorf_username[$px][$py].'"> <b>'.$dorf_username[$px][$py]; ?></b></a></td>
</tr>
<tr>
<td><img src="img/un/a/x.gif" border="0" height="12" width="3"></td>
<td>Einwohner:</td><td><b> <?php echo$dorf_einwohner[$px][$py]; ?></b></td>

</tr>
</tbody></table>
</div>



<div class="map_details_troops">
<div class="f10 b">&nbsp;Einheiten:</div>
<table class="f10">
<tbody><tr>
<td><img src="img/un/a/x.gif" border="0" height="12" width="3"></td>
<td>Es liegen keine <br>Informationen vor</td>
</tr>
</tbody></table></div>



<div class="map_details_actions">
<div class="f10 b">Optionen:</div>
<table class="f10" width="100%">
<tbody><tr><td><?php echo'<a href="karte.php?x='.$px.'&y='.$py.'">» Karte zentrieren</a>'; ?></td></tr>
<tr>
<?php
if ($gebeude[16]['highest']>0)
	echo'<td><a href="build.php?x='.$px.'&y='.$py.'&s=2&id='.$gebeude[16]['highid'].'">» Truppen schicken</a>';
else
	echo'<td class="c">» kein Versammlungsplatz vorhanden';
?>
</td></tr>
<tr>
<?php
if ($gebeude[17]['highest']>0)
   echo'<td><a href="build.php?x='.$px.'&y='.$py.'&id='.$gebeude[17]['highid'].
		'">» Händler schicken</a></td></tr></tbody></table>';
else
	echo'<td class="c">» kein Marktplatz vorhanden</td></tr></tbody></table>';
}
if (!isset($dorf_username[$px][$py]))
{
if (!isset($land_oase[$px][$py]))
{
echo'<h1>Ödland ('.$px.'|'.$py.')</h1>';//<div id="f'.$land_typ[$px][$py].'"></div>';
?>
<div id="fo1"></div>
<div id="pr" class="map_details_right">
<div class="f10 b">&nbsp;Landverteilung:</div>
<table class="f10">
<tbody><tr><td><img class="res" src="img/un/r/1.gif"></td><td class="s7 b">0</td><td> Holzfäller</td></tr>
<tr><td><img class="res" src="img/un/r/2.gif"></td><td class="s7 b">0</td><td> Lehmgruben</td></tr>
<tr><td><img class="res" src="img/un/r/3.gif"></td><td class="s7 b">0</td><td> Eisenminen</td></tr>
<tr><td><img class="res" src="img/un/r/4.gif"></td><td class="s7 b">0</td><td> Getreidefarmen</td></tr>
</tbody></table></div>
<div class="map_details_actions"><div class="f10 b">Optionen:</div>
<table class="f10" width="100%"><tbody>
<tr><td><?php echo'<a href="karte.php?x='.$px.'&y='.$py.'">» Karte zentrieren</a>'; ?></td></tr>
<!-- <tr><td class="c">» Neues Dorf gründen (0/3 Siedlern vorhanden)</td></tr>-->
<!-- <tr><td><a href="a2b.php?id=190889&amp;s=1">» Neues Dorf gründen</a></td></tr> -->
</tbody></table>

<?php
}
if (isset($land_oase[$px][$py]) AND $land_oase[$px][$py]==0)
{
echo'<h1>verlassenes Tal ('.$px.'|'.$py.')</h1>

<div id="f'.$land_typ[$px][$py].'"></div>';
?>
<div id="pr" class="map_details_right">
<div class="f10 b">&nbsp;Landverteilung:</div>

<table class="f10">
<tbody><tr>
<td><img class="res" src="img/un/r/1.gif"></td>
<td class="s7 b"><?php echo $land_verteilung[$land_typ[$px][$py]][1]; ?></td><td> Holzfäller</td>

</tr>
<tr>
<td><img class="res" src="img/un/r/2.gif"></td>
<td class="s7 b"><?php echo $land_verteilung[$land_typ[$px][$py]][2]; ?></td><td> Lehmgruben</td>
</tr>
<tr>
<td><img class="res" src="img/un/r/3.gif"></td>
<td class="s7 b"><?php echo $land_verteilung[$land_typ[$px][$py]][3]; ?></td><td> Eisenminen</td>
</tr>
<tr>
<td><img class="res" src="img/un/r/4.gif"></td>

<td class="s7 b"><?php echo $land_verteilung[$land_typ[$px][$py]][4]; ?></td><td>Getreidefarmen</td>
</tr>

</tbody></table>
</div>

<div class="map_details_actions">
<div class="f10 b">Optionen:</div>
<table class="f10" width="100%">
<tbody><tr>

<td><?php echo'<a href="karte.php?x='.$px.'&y='.$py.'">» Karte zentrieren</a>'; ?></td>
</tr><tr>
<?php
$siedler=$troops_village['own'][$volk*10];
if ($siedler<3) echo'<td class="c">» Neues Dorf gründen ('.$siedler.'/3 Siedlern vorhanden)</td></tr>';
else	echo'<tr><td><a href="a2b.php?do=newvillage&x='.$px.'&y='.$py.'">» Neues Dorf gründen</a></td></tr>';
?>
</tbody></table>

<?php
}
if (isset($land_oase[$px][$py]) AND $land_oase[$px][$py]==1)
{

echo'
<h1>verlassenes Tal ('.$px.'|'.$py.')</h1>
<img src="http://img.travian.com/de/blue/img/un/m/w'.$land_typ[$px][$py].'.jpg" id="resfeld">';

?>
<div id="pr" class="map_details_right"><p></p>
<div class="f10 b">&nbsp;Einheiten:</div>
<table class="f10"><tbody>

<!-- ändern !!! -->
<tr><td>keine</td></tr>
<!--
<tr><td><img class="unit" src="img/un/u/31.gif" border="0"></td>
	<td align="right">&nbsp;<b>7</b></td><td>Ratten</td></tr>
<tr><td><img class="unit" src="img/un/u/32.gif" border="0"></td>
	<td align="right">&nbsp;<b>4</b></td><td>Spinnen</td></tr>
<tr><td><img class="unit" src="img/un/u/35.gif" border="0"></td>
	<td align="right">&nbsp;<b>2</b></td><td>Wildschweine</td></tr>
-->

</tbody></table></div><div class="map_details_actions">
<div class="f10 b">Optionen:</div>
<table class="f10" width="100%">
<tbody><tr>
<td><?php echo'<a href="karte.php?x='.$px.'&y='.$py.'">» Karte zentrieren</a>'; ?></td>
</tr><tr>
<?php
if ($gebeude[16]['highest']>0)
	echo'<td><a href="build.php?x='.$px.'&y='.$py.'&s=2&id='.$gebeude[16]['highid'].'">» Verlassenes Tal erkunden</a>';
else
	echo'<td class="c">» kein Versammlungsplatz vorhanden';
?>
</td></tr></tbody></table>

<?php
}
}
}
?>
</div></div>

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