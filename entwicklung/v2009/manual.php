<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title></title><link rel=stylesheet type="text/css" href="unx.css">
<meta http-equiv="imagetoolbar" content="no">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

<body>
<?php

include('functions.php');
connect();

$gebeude=load_gebeude($dorf_data,$land_typ_data);
$troops=load_troops($dorf_data,$spieler_data);

$sql="SELECT `value` FROM `tr".$round_id."_diverses` WHERE `id`='völker';";
$data=mysql_fetch_array(mysql_query($sql));
$volk_name=split(':',$data['value']);



$typ=$_GET['typ'];

//var_dump($typ);

if (!isset($typ) OR $typ==0)
{
?>
<h1><img src="img/un/u/geb.gif" class="unit"> Übersicht</h1>

<p>Diese Ingame Hilfe ermöglicht es dir wichtige Informationen jederzeit schnell nachzuschlagen.
<img src="img/un/a/troops.gif" width="140" height="89" border="0" align="right" style="position:absolute; z-index:61; right:0px; top:80px; margin-right:10px;"><img src="img/un/a/buildings.gif" width="140" height="89" border="0" align="right" style="position:absolute; z-index:61; right:0px; top:170px; margin-right:10px;"><ul>
<li>Die Einheiten</li>

<ul>

<li><a href="manual.php?typ=1&volk=1">Römer</a></li>
<li><a href="manual.php?typ=1&volk=2">Germanen</a></li>
<li><a href="manual.php?typ=1&volk=3">Gallier</a></li>
</ul>

<br>
<li>Die Gebäude</li>

<ul>
<li><a href="manual.php?typ=2&gebs=1">Rohstoffe</a></li>
<li><a href="manual.php?typ=2&gebs=2">Militär</a></li>

<li><a href="manual.php?typ=2&gebs=3">Infrastruktur</a></li>
</ul>


<br>
<li><a href="http://www.travian.de/anleitung.php" target="_blank">Spielanleitung <img src="img/un/a/external.gif" width="10" height="10" border="0"></a><br>Die Ingame Hilfe stellt nur Kurzinformationen zur Verfügung. Mehr Informationen zum Spiel gibt es<br>in der Spielanleitung.</li><br><br><li><a href="http://help.travian.de/" target="_blank">Travian FAQ <img src="img/un/a/external.gif" width="10" height="10" border="0"></a><br>Sehr ausf&uuml;hrliche Travian Dokumentation</li></ul></p>
<?php
}
if ($typ==1)
{
	$volk=$_GET['volk'];
	if (isset($volk))
	{

		echo'
		<h1><img src="img/un/u/geb.gif" class="unit"> Einheiten ('.$volk_name[$volk-1].')</h1>
		<ul>';

		for ($i=1+($volk-1)*10;$i<=$volk*10;$i++)
			echo'<li><a href="manual.php?typ=1&id='.$i.'">'.$troops[$i]['name'].'</a></li>';
		echo'</ul>';
		$vor_volk=($volk+1)%3 +1;
		$nac_volk=($volk)%3 +1;
		$para1='typ=1&volk='.$vor_volk;
		$para2='typ=1&volk='.$nac_volk;
	}
	else
	{
		$id=$_GET['id'];
		echo'
<div style="background-image:url(img/un/u2/u'.$id.'.gif); background-repeat:no-repeat;background-position:260px 95px;">
<h1><img class="unit" src="img/un/u/'.$id.'.gif" border="0" alt="'.$troops[$id]['name'].'"> '.$troops[$id]['name'].'
<span class="f10 c t">('.$volk_name[$troops[$id]['volk']-1].')</span></h1>
<p><table cellspacing="1" cellpadding="2" class="tbg">
<tr class="rbg">
<td><img src="img/un/a/att_all.gif" alt="Angriffswert" title="Angriffswert"></td>
<td><img src="img/un/a/def_i.gif" alt="Verteidigungswert gegen Infantrie" title="Verteidigungswert gegen Infantrie"></td>
<td><img src="img/un/a/def_c.gif" alt="Verteidigungswert gegen Kavallerie" title="Verteidigungswert gegen Kavallerie"></td>
<td><img class="res" src="img/un/r/1.gif" alt="Holz"></td>
<td><img class="res" src="img/un/r/2.gif" alt="Lehm"></td>
<td><img class="res" src="img/un/r/3.gif" alt="Eisen"></td>
<td><img class="res" src="img/un/r/4.gif" alt="Getreide"></td></tr>
<tr><td>'.$troops[$id]['off'].'</td><td>'.$troops[$id]['deff1'].'</td><td>'.$troops[$id]['deff2'].'</td>
<td>'.$troops[$id]['kosten_holz'].'</td><td>'.$troops[$id]['kosten_lehm'].'</td><td>'.$troops[$id]['kosten_eisen'].'</td>
<td>'.$troops[$id]['kosten_getreide'].'</td></tr></table></p>
<p><table cellspacing="1" cellpadding="2" class="f10">
<tr><td>Geschwindigkeit:</td><td><b>'.$troops[$id]['speed'].'</b> Felder/Stunde</td>
</tr><tr>
<td>Tragfähigkeit:</td><td><b>'.$troops[$id]['tragen'].'</b> Rohstoffe</td>
</tr><tr>
<td>Versorgung:</td><td><img class="res" src="img/un/r/5.gif" alt="supply"> '.$troops[$id]['versorgung'].'</td>
</tr><tr><td>Ausbildungszeit:</td><td>
<img class="clock" src="img/un/a/clock.gif" width="18" height="12" border="0">
 '.zeit_dauer($troops[$id]['start_bauzeit']).'</td></tr>
</table></p><p>'.$troops[$id]['besch'].'</p><div class="b">Voraussetzungen:</div>';
		if ($troops[$id]['needs'][0]==-1)
			echo'<a href="manual.php?typ=2&gid=26">Palast</a> Stufe 10 oder
				<a href="manual.php?typ=2&gid=25">Residenz</a> Stufe 10';
        for ($i=1;$i<=$troops[$id]['needs'][0];$i++)
        {
            $gid=$troops[$id]['needs'][$i*2-1];
            $stufe=$troops[$id]['needs'][$i*2];
            echo'<a href="manual.php?typ=2&gid='.$gid.'">'.$gebeude[$gid]['name'].'</a> Stufe '.$stufe;
            if ($i<$troops[$id]['needs'][0]) echo', ';
        }
        $nid=$id+1;if ($nid>30) $nid=1;
        $vid=$id-1;if ($vid<1) $vid=30;
        $para1='typ=1&id='.$vid;
        $para2='typ=1&id='.$nid;
	}
}
if ($typ==2)
{
	$gebs=$_GET['gebs'];
	$gid=$_GET['gid'];
	if (isset($gebs) AND !isset($gid))
	{
		$gebeude_typ=array(1=>'Rohstoffe',2=>'Militär',3=>'Infrastruktur');
		echo'
		<h1><img src="img/un/u/geb.gif" class="unit"> Gebäude ('.$gebeude_typ[$gebs].')</h1>
		<ul>';

		for ($i=1;$i<=$gebeude['anzahl'];$i++)
		{
			if ($gebeude[$i]['typ']==$gebs)
				echo'<li><a href="manual.php?typ=2&gid='.$i.'">'.$gebeude[$i]['name'].'</a></li>';
		}
		echo'</ul>';
		$vor_gebs=($gebs+1)%3 +1;
		$nac_gebs=($gebs)%3 +1;
		$para1='typ=2&gebs='.$vor_gebs;
		$para2='typ=2&gebs='.$nac_gebs;
	}
	if (!isset($gebs) AND isset($gid))
	{
		echo'<h1><img class="unit" src="img/un/u/geb.gif"> '.$gebeude[$gid]['name'].'</h1>'.
			'<p>'.$gebeude[$gid]['besch'].'</p><p><b>Kosten</b> und <b>Bauzeit</b> bei Stufe 1:<br>
			<img class="res" src="img/un/r/1.gif" alt="Holz" title="Holz" style="padding-top:4px">'.
			$gebeude[$gid]['kosten_holz'][1].' | <img class="res" src="img/un/r/2.gif" alt="Lehm" title="Lehm">'.
			$gebeude[$gid]['kosten_lehm'][1].' | <img class="res" src="img/un/r/3.gif" alt="Eisen" title="Eisen">'.
			$gebeude[$gid]['kosten_eisen'][1].' | <img class="res" src="img/un/r/4.gif" alt="Getreide"
			title="Getreide">'.$gebeude[$gid]['kosten_getreide'][1].' | <img src="img/un/r/5.gif" width="18"
			height="12" alt="Getreideverbrauch" title="Getreideverbrauch">'.$gebeude[$gid]['arbeiter'].' |
			<img src="img/un/a/clock.gif" class="clock"> '.zeit_dauer($gebeude[$gid]['start_bauzeit']).'<br><br>
			<b>Voraussetzungen:</b> <br>';
		if ($gebeude[$gid]['needs'][0]==0)
			echo'keine';
		if ($gebeude[$gid]['needs'][0]==-1)
			echo'nicht baubar';
        for ($i=1;$i<=$gebeude[$gid]['needs'][0];$i++)
        {
            $id=$gebeude[$gid]['needs'][$i*2-1];
            $stufe=$gebeude[$gid]['needs'][$i*2];
//            var_dump($gid);
            if ($id==-1) echo'<a href="#">Nur '.$volk_name[$stufe-1].'</a>';
            if ($id>0) echo'<a href="manual.php?typ=2&gid='.$id.'">'.$gebeude[$id]['name'].'</a> Stufe '.$stufe;
            if ($i<$gebeude[$gid]['needs'][0]) echo', ';
        }
        $nid=$gid+1;if ($nid>$gebeude['anzahl']) $nid=1;
        $vid=$gid-1;if ($vid<1) $vid=$gebeude['anzahl'];
        $para1='typ=2&gid='.$vid;
        $para2='typ=2&gid='.$nid;

        echo'<br><br><b>Wiederbaubar:</b><br>';

        if ($gebeude[$gid]['rebuild']==0) echo'Nein';
        else	echo'Ja, ab Stufe '.$gebeude[$gid]['rebuild'];
        echo'</p>';
	}
}
?>

<map name="nav">
<?php
echo'<area href="manual.php?'.$para1.'" title="zurück" coords="0,0,45,18" shape="rect"><area href="manual.php" title="Übersicht" coords="46,0,70,18" shape="rect"><area href="manual.php?'.$para2.'" title="weiter" coords="71,0,116,18" shape="rect">';
?>
</map>

<img usemap="#nav" src="img/un/a/navi.gif" border="0" class="m_navi">
</body>
</html>