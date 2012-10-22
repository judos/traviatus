<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>
<title>Traviatus</title>
<link rel=stylesheet type="text/css" href="unx.css">
<script src="unx.js" type="text/javascript"></script>

</head>


<body onload="start()">
<script src="tooltips/wz_tooltip.js"></script>
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

//Spieler Daten
$sql="SELECT * FROM `tr".$round_id."_user` WHERE `id`='$userid';";
$result=mysql_query($sql);
$spieler_data=mysql_fetch_array($result);
//Dorf Daten
$sql="SELECT * FROM `tr".$round_id."_dorfer` WHERE `x`='$dorfx' AND `y`='$dorfy';";
$result=mysql_query($sql);
$dorf_data=mysql_fetch_array($result);
$geb1s=split(':',$dorf_data['geb1']);
$geb2s=split(':',$dorf_data['geb2']);
$geb2t=split(':',$dorf_data['geb2t']);
//Land daten
$sql="SELECT * FROM `tr".$round_id."_lander` WHERE `x`='$dorfx' AND `y`='$dorfy';";
$result=mysql_query($sql);
$land_data=mysql_fetch_array($result);
//Verteilung der Rohstoffgebäude
$sql="SELECT `geb` FROM `tr".$round_id."_land_typen` WHERE `typ`='".$land_data['typ']."';";
$result=mysql_query($sql);
$land_typ_data=mysql_fetch_array($result);
$geb1t=split(':',$land_typ_data['geb']);

//gebeude daten
$gebeude=load_gebeude($dorf_data,$land_typ_data);
//produktion lager und lagergrössen ausrechnen
$land_produktion=load_produktion($dorf_data,$land_data);
//Lager
$lager=load_lager($dorf_data);
$lager_grosse=lager_grosse($dorf_data);
//Truppen
$troops=load_troops($dorf_data,$spieler_data);
$troops_village=load_troops_in_village($userid,$dorfx,$dorfy,$troops);

?>


<!-- Top links -->
<?php top_links($username); ?>

<div id="lmidall">
<div id="lmidlc">

<!-- Links am linken Rand -->
<?php links(); ?>

<div id="lmid1">
<div id="lmid2">

<p class="txt_menue">
<?php
if (!isset($_GET['s'])) echo'<a href="dorf3.php"><span class="c">Übersicht</span></a> | ';
else echo'<a href="dorf3.php">Übersicht</a> | ';
if ($_GET['s']==2) echo'<a href="dorf3.php?s=2"><span class="c">Rohstoffe</span></a> | ';
else echo'<a href="dorf3.php?s=2">Rohstoffe</a> | ';
if ($_GET['s']==3) echo'<a href="dorf3.php?s=3"><span class="c">Lager</span></a> | ';
else echo'<a href="dorf3.php?s=3">Lager</a> | ';
if ($_GET['s']==4) echo'<a href="dorf3.php?s=4"><span class="c">KP</span></a> | ';
else echo'<a href="dorf3.php?s=4">KP</a> | ';
if ($_GET['s']==5) echo'<a href="dorf3.php?s=5"><span class="c">Truppen</span></a>';
else echo'<a href="dorf3.php?s=5">Truppen</a> | ';
?>
</p><p></p>

<?php
if ($_GET['s']==5)	//Truppen
{
?>

<table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr class="rbg">
<td colspan="12">Eigene Truppen</td></tr>
<tr class="unit"><td width="150">Dorfname</td>
<?php
for ($i=1;$i<=10;$i++)
	echo'<td><img src="img/un/u/'.($i+($spieler_data['volk']-1)*10).'.gif" title="'.
		$troops[$i+($spieler_data['volk']-1)*10]['name'].'"></td>';
echo'<td><img src="img/un/u/hero.gif" title="Held"></td></tr>';


unset($all);
$sql="SELECT * FROM `tr".$round_id."_dorfer` WHERE `user`='$userid' ORDER BY `name` ASC;";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++)
{
	$data=mysql_fetch_array($result); $x=$data['x']; $y=$data['y'];
	$sql2="SELECT `troops` FROM `tr".$round_id."_truppen` WHERE `x`='$x' AND `y`='$y' AND `user`='$userid';";
	$result2=mysql_query($sql2);
	$data2=mysql_fetch_array($result2);
	$truppen=split(':',$data2['troops']);
	if (!isset($truppen)) $truppen=array(0,0,0,0,0,0,0,0,0,0);
	$cl='';
	if ($x==$dorfx AND $y==$dorfy) $cl='ou';
	echo'<tr><td class="s7 '.$cl.'"><a href="dorf1.php?ndx='.$x.'&ndy='.$y.'">'.$data['name'].'</a></td>';
	for ($j=1;$j<=10;$j++)
	{
		$all[$j]+=$truppen[$j-1];
		if ($truppen[$j-1]==0) echo'<td class="c '.$cl.'">0</td>';
		else echo'<td class="'.$cl.'">'.$truppen[$j-1].'</td>';
	}
	echo'<td class="c '.$cl.'">0</td>';
	echo'</tr>';
}
echo'<tr><td colspan="12"></td></tr><tr><td class="s7 f10"><b>Summe</b></td>';
for ($i=1;$i<=10;$i++)
{
    if ($all[$i]==0) echo'<td class="c">0</td>';
    else echo'<td>'.$all[$i].'</td>';
}
echo'<td class="c">0</td>';
echo'</tr></tbody></table>';


}
if ($_GET['s']==4)	//Kulturpunkte KP
{
?>

<table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr class="rbg"><td colspan="6">Kulturpunkte</td></tr>
<tr><td width="150">Dorfname</td><td>KPs/Tag</td><td>Feste</td><td>Einheiten</td><td>Slots</td></tr>
<?php
$sql="SELECT * FROM `tr".$round_id."_truppen` WHERE `user`='$userid';";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++)
{
	$data=mysql_fetch_array($result);
	$truppen[$data['x']][$data['y']]=split(':',$data['troops']);
}

$sql="SELECT tr".$round_id."_dorfer.* FROM `tr".$round_id."_dorfer`
	WHERE user='$userid' ORDER BY `name` ASC;";
$result=mysql_query($sql);
$anz=mysql_num_rows($result);
$kps=0;

for ($i=1;$i<=$anz;$i++)
{
	$data=mysql_fetch_array($result);
	$x=$data['x'];$y=$data['y'];
	$expansion=split(':',$data['expansion']);
	
	$sql2="SELECT * FROM `tr".$ri."_others` WHERE `x`='$x' AND `y`='$y' AND `typ`='8';";
	$result2=mysql_query($sql2);
	if (mysql_num_rows($result2)>0)
		$data2=mysql_fetch_array($result2);
	else
		unset($data2);
	
	$c='';
	if ($x==$dorfx AND $y==$dorfy) $c='ou';
	echo'<tr><td class="s7 '.$c.'"><a href="dorf1.php?ndx='.$x.'&ndy='.$y.'">'.$data['name'].'</a></td>'.
		'<td class="'.$c.'">'.round($data['einwohner']/2).'</td>';

	if (isset($data2))
		echo'<td class="'.$c.'">
			<a href="build.php?ndx='.$data['x'].'&ndy='.$data['y'].'&id=">
			<span onmouseover="Tip('."'dauert bis ".nzf($data2['zeit'])."','Fest')".';" class="c"><b> • </b></span></a>
			</td><td class="'.$c.'">';
	else echo'<td class="'.$c.'">-</td><td class="'.$c.'">';
	$kps+=round($data['einwohner']/2);
	if ($truppen[$x][$y][9]==0) echo'<span class="c">-</span>';
	else
	{
		if ($truppen[$x][$y][9]<7)
		{
			for ($j=1;$j<=$truppen[$x][$y][9];$j++)
				echo'<img src="img/un/u/'.($spieler_data['volk']*10).'.gif" title="Siedler">';
		}
		else
			echo $truppen[$x][$y][9].' <img src="img/un/u/'.($spieler_data['volk']*10).'.gif" title="Siedler">';
	}
	$slots=dorf_slots($data);
	echo'</td><td class="'.$c.'">'.$expansion[0].'/'.$slots.'</td></tr>';
	$x1+=$expansion[0];
	$x2+=$slots;
}
echo'<tr><td colspan="5"></td></tr><tr><td class="s7"><b>Summe</b></td><td>'.$kps.'</td><td colspan="2"></td><td>'.$x1.'/'.$x2.'</td></tr>';
echo'</tbody></table>';

/*
<tr><td class="s7"><a href="dorf1.php?newdid=187930">Priaturo</a></td>
<td>61</td>
<td><span class="c">-</span></td>
<td><span class="c">-</span></td>
<td>0/0</td></tr>

<tr><td class="s7"><a href="dorf1.php?newdid=147987">Y Raspuna</a></td>
<td>931</td>
<td><a href="build.php?newdid=147987&amp;gid=24"><span title="lvl14" class="c"><b> • </b></span></a></td>
<td><span class="c">-</span></td>
<td>1/1</td></tr>

<tr><td class="s7 li ou"><a href="dorf1.php?newdid=127030">Z Walderin</a></td>
<td class="ou">1000</td>
<td class="ou"><span class="c">-</span></td>
<td class="ou"><span class="c">-</span></td>
<td class="re ou">2/2</td></tr>
*/

}
if ($_GET['s']==2)	//Rohstoffe
{
?>

<table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr class="rbg"><td colspan="6">Rohstoffe</td></tr>
<tr><td width="150">Dorfname</td><td><img class="res" src="img/un/r/1.gif" title="Holz"></td>
<td><img class="res" src="img/un/r/2.gif" title="Lehm"></td><td><img class="res" src="img/un/r/3.gif" title="Eisen"></td>
<td><img class="res" src="img/un/r/4.gif" title="Getreide"></td><td>Händler</td></tr>
<?php
$sql="SELECT * FROM `tr".$round_id."_dorfer` WHERE `user`='$userid' ORDER BY `name` ASC;";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++)
{
	$data=mysql_fetch_array($result);
	$lager=split(':',$data['lager']);
	$x=$data['x'];
	$y=$data['y'];
	$cl='';
	if ($x==$dorfx AND $y==$dorfy) $cl='ou';
	echo'<tr><td class="s7 '.$cl.'"><a href="dorf1.php?ndx='.$x.'&ndy='.$y.'">'.$data['name'].'</a></td>';
	for ($j=0;$j<=3;$j++)
	{
		$totlager[$j]+=$lager[$j];
		$la=number_format($lager[$j],0,'','´');
		echo'<td class="r7 '.$cl.'">'.$la.'</td>';
	}

	$gebeude=load_gebeude($data,NULL);
    $handler=$gebeude[17]['highest']*2;
    $sql2="SELECT sum(`handler`) FROM `tr".$round_id."_handler` WHERE `ursprung`='$x:$y';";
    $result2=mysql_query($sql2);
    $data2=mysql_fetch_array($result2);
    $handler_gebraucht=$data2['sum(`handler`)'];
    $sql2="SELECT sum(`handler`) FROM `tr".$round_id."_angebote` WHERE `ursprung`='$x:$y';";
    $result2=mysql_query($sql2);
    $data2=mysql_fetch_array($result2);
    $handler_gebraucht2=$data2['sum(`handler`)'];

    $anz_ver_handler=$handler-$handler_gebraucht-$handler_gebraucht2;

	echo'<td class="'.$cl.'"><a href="dorf2.php?ndx='.$x.'&ndy='.$y.'&id='.$gebeude[17]['highid'].'">
		<span class="c0 t">'.$anz_ver_handler.'/'.$handler.'</span></a></td></tr>';
	$gesamt_ver_handler+=$anz_ver_handler;
	$gesamt_handler+=$handler;
}
echo'<tr><td colspan="12"></td></tr><tr><td class="s7 f10"><b>Summe</b></td>';
for ($i=0;$i<=3;$i++)
{
    if ($totlager[$i]==0) echo'<td class="c">0</td>';
    else echo'<td>'.number_format($totlager[$i],0,'','´').'</td>';
}
echo'<td>'.$gesamt_ver_handler.'/'.$gesamt_handler.'</td>';
echo'</tbody></table>';
}


if (!isset($_GET['s']))	//Übersicht
{

echo'<table class="tbg" cellpadding="2" cellspacing="1">
	<tbody><tr class="rbg"><td colspan="6">Übersicht</td></tr>
	<tr><td width="150">Dorfname</td><td>Angriffe</td><td>Bau</td><td>Truppenbau</td><td>Händler</td></tr>';

$sql="SELECT * FROM `tr".$round_id."_dorfer` WHERE `user`='$userid' ORDER BY `name` ASC;";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++)
{
	$data=mysql_fetch_array($result);
	$x=$data['x'];
	$y=$data['y'];
	$cl='';
	if ($x==$dorfx AND $y==$dorfy) $cl='ou';
	//Name
	echo'<tr><td class="s7 '.$cl.'"><a href="dorf1.php?ndx='.$x.'&ndy='.$y.'">'.$data['name'].'</a></td>';

	//Angriffe
	echo'<td class="'.$cl.'"><span class="c">?</span></td>';
	//<td><a href="build.php?newdid=127030&amp;id=39"><img src="img/un/a/att2.gif" title="1" border="0"></a> </td>
	//<td><a href="build.php?newdid=147987&amp;id=39"><img src="img/un/a/att1.gif" title="1" border="0"></a> </td>

	//Gebäude Bau
	$sql2="SELECT `id`,`zeit` FROM `tr".$round_id."_others` WHERE `x`='$x' AND `y`='$y' AND `typ`='10' ORDER BY `zeit` ASC;";
	$result2=mysql_query($sql2);
	echo'<td class="'.$cl.'">';
	for ($j=1;$j<=mysql_num_rows($result2);$j++)
	{
		$data2=mysql_fetch_array($result2);
		$gid=$data2['id'];
		if ($gid<19)
		{	$id=$geb1t[$gid-1];
			$stufe=$geb1s[$gid-1]+1;	}
		else
		{	$id=$geb2t[$gid-19];
			$stufe=$geb2s[$gid-19]+1;	}
		echo'<a href="dorf1.php?ndx='.$x.'&ndy='.$y.'"><img src="img/un/a/bau.gif"
			onmouseover="Tip('."'".$gebeude[$id]['name']." Stufe ".$stufe."<br>fertig: ".date('H:i | d.m.y',strtotime($data2['zeit']))."','Bau'".')"
			border="0" height="16" width="10"></a>&nbsp;';
	}
	if (mysql_num_rows($result2)==0) echo'<span class="c">-</span>';
	echo'</td>';

	$sql2="SELECT * FROM `tr".$round_id."_others` WHERE `typ`<5 AND `x`='$x' AND `y`='$y' ORDER BY `id` ASC;";
	$result2=mysql_query($sql2);
	if (mysql_num_rows($result2)>0)
	{
		echo'<td class="'.$cl.'">';
        for ($j=1;$j<=mysql_num_rows($result2);$j++)
        {
            $data2=mysql_fetch_array($result2);
            if ($troops[$data2['id']]['typ']==1) $geb=19;
            if ($troops[$data2['id']]['typ']==2) $geb=20;
            if ($troops[$data2['id']]['typ']==3) $geb=21;
            $timestamp=strtotime($data2['zeit'])+($data2['anzahl']-1)*$data2['dauer'];
            if (date('d.m.Y',$timestamp)==date('d.m.Y',time())) $day='Heute';
            elseif (date('d.m.Y',$timestamp-86400)==date('d.m.Y',time())) $day='Morgen';
            elseif (date('d.m.Y',$timestamp-172800)==date('d.m.Y',time())) $day='Übermorgen';
            else $day='am '.date('d.m.Y',$timestamp);
            $fertig='Fertig '.$day.' um '.date('H:i',$timestamp);
            echo'<a href="build.php?ndx='.$x.'&ndy='.$y.'&id='.$gebeude[$geb]['highid'].'">
                <img class="unit" src="img/un/u/'.$data2['id'].'.gif" onmouseover="Ti'."p('Es werden noch ".$data2['anzahl']." gebaut<br> ".
                $fertig."','Truppenbau')".'" border="0"></a>';
        }
        echo'</td>';
    }
	else echo'<td class="'.$cl.'"><span class="c">-</span></td>';
	//Truppenbau
	//<a href="build.php?newdid=127030&amp;gid=19"><img class="unit" src="img/un/u/1.gif" title="244" border="0"></a>

	$gebeude=load_gebeude($data,NULL);
    $handler=$gebeude[17]['highest']*2;
    $sql2="SELECT sum(`handler`) FROM `tr".$round_id."_handler` WHERE `ursprung`='$x:$y';";
    $result2=mysql_query($sql2);
    $data2=mysql_fetch_array($result2);
    $handler_gebraucht=$data['sum(`handler`)'];
    $sql2="SELECT sum(`handler`) FROM `tr".$round_id."_angebote` WHERE `ursprung`='$x:$y';";
    $result2=mysql_query($sql2);
    $data2=mysql_fetch_array($result2);
    $handler_gebraucht2=$data2['sum(`handler`)'];

    $anz_ver_handler=$handler-$handler_gebraucht-$handler_gebraucht2;

	echo'<td class="'.$cl.'"><a href="build.php?ndx='.$x.'&ndy='.$y.'&id='.$gebeude[17]['highid'].'">
		<span class="c0 t">'.$anz_ver_handler.'/'.$handler.'</span></a></td></tr>';
}

echo'</tbody></table>';

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