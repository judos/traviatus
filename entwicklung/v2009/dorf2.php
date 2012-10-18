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
//Gebäude Stufen des Landes
$geb1_stufe=split(':',$dorf_data['geb1']);
$geb2_typ=split(':',$dorf_data['geb2t']);
$geb2_stufe=split(':',$dorf_data['geb2']);

//Daten des Landes
$sql="SELECT `typ` FROM `tr".$round_id."_lander` WHERE `x`='$dorfx' AND `y`='$dorfy';";
$result=mysql_query($sql);
$land_data=mysql_fetch_array($result);

//Verteilung der Rohstoffgebäude
$sql="SELECT `geb` FROM `tr".$round_id."_land_typen` WHERE `typ`='".$land_data['typ']."';";
$result=mysql_query($sql);
$land_typ_data=mysql_fetch_array($result);
$land_geb=split(':',$land_typ_data['geb']);

//Lager laden
$lager=load_lager($dorf_data);

//Lager grösse laden
$lager_grosse=lager_grosse($dorf_data);

//Gebeude Daten laden
$gebeude=load_gebeude($dorf_data,$land_typ_data);

//Produktion laden
$produktion=load_produktion($dorf_data,$land_data);

//Truppen
$troops=load_troops($dorf_data,$spieler_data);
$troops_village=load_troops_in_village($userid,$dorfx,$dorfy,$troops);



unset($na_st);
for ($i=19;$i<=40;$i++)
{
	if ($geb2_typ[$i-19]>0)
		$na_st[$i]=$gebeude[$geb2_typ[$i-19]]['name'].' Stufe '.$geb2_stufe[$i-19];
	else
	{
		if ($i<39) $na_st[$i]='Bauplatz';
		if ($i==39) $na_st[$i]='Versammlungsbauplatz';
		if ($i==40) $na_st[$i]='Aussen Bauplatz';
	}
}



if (isset($_GET['id']) AND isset($_GET['gid']) AND $_GET['do']=='del')
{
	$id=$_GET['id'];
	$gid=$_GET['gid'];
	if ($gid<19) {$stufe=$geb1_stufe[$gid-1];}
	if ($gid>18) {$stufe=$geb2_stufe[$gid-19];}

	$new_geb2_typ='';
	if ($gid>18 AND $stufe==0)
	{
		$new_geb2_typ=$geb2_typ;
		$new_geb2_typ[$gid-19]=0;
		$new_geb2_typ=implode(':',$new_geb2_typ);
	}
	if ($stufe<1) $stufe=1;

	//Auftrag löschen
	$sql="DELETE FROM `tr".$round_id."_others` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `id`='$gid' AND `typ`='10';";
	$result=mysql_query($sql);

    $neu_lager[0]=$lager[0]+$gebeude[$id]['kosten_holz'][$stufe];
    $neu_lager[1]=$lager[1]+$gebeude[$id]['kosten_lehm'][$stufe];
    $neu_lager[2]=$lager[2]+$gebeude[$id]['kosten_eisen'][$stufe];
    $neu_lager[3]=$lager[3]+$gebeude[$id]['kosten_getreide'][$stufe];

    $l_lager[4]=$lager[4]-$gebeude[$id]['arbeiter'];

	//Einwohner und Rohstoffe aktualisieren
	$sql="UPDATE `tr".$round_id."_dorfer` SET `einwohner`='".$l_lager[4]."', `lager`='".implode(':',$neu_lager)."'";
	if ($new_geb2_typ!='') {$sql.=", `geb2t`='".$new_geb2_typ."'"; $geb2_typ=split(':',$new_geb2_typ); }
	$sql.=" WHERE `x`='$dorfx' AND `y`='$dorfy';";
	$result=mysql_query($sql);

	$lager=$neu_lager;
	$lager[4]=$l_lager[4];
}

if (isset($_GET['id']) AND isset($_GET['gid']) AND !isset($_GET['do']))
{
	$id=$_GET['id'];
	$gid=$_GET['gid'];

	if ($geb2_stufe[$gid-19]<$gebeude[$id]['stufen'])	//Nicht vollständig ausgebaut
	{
	    $sql="SELECT * FROM `tr".$round_id."_others` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `typ`='10';";
        $result=mysql_query($sql);
        $build=1;
        if (mysql_num_rows($result)==2)
            $build=0;
        if (mysql_num_rows($result)==1)
        {
            $data1=mysql_fetch_array($result);
            if ($data1['id']==$gid) $build=0;
        }

        if ($build==1)				//Wenn dieses Gebäude nicht schon ausgebaut wird und nicht zwei andere gebaut werden.
        {
        	$nachste_stufe=$geb2_stufe[$gid-19]+1;
			if ($lager[0]>=$gebeude[$id]['kosten_holz'][$nachste_stufe] AND		//Genug Rohstoffe
				$lager[1]>=$gebeude[$id]['kosten_lehm'][$nachste_stufe] AND
				$lager[2]>=$gebeude[$id]['kosten_eisen'][$nachste_stufe] AND
				$lager[3]>=$gebeude[$id]['kosten_getreide'][$nachste_stufe])
			{
            	if ($produktion[3]-$gebeude[$id]['arbeiter']>2)					//kein Nahrungsmangel
            	{
            		//Auftrag speichern
            		$sql="INSERT INTO `tr".$round_id."_others` (`x`,`y`,`id`,`zeit`,`typ`) VALUES ('$dorfx','$dorfy','$gid','".
            			date('y-m-d H:i:s',time()+$gebeude[$id]['bauzeit'][$nachste_stufe])."','10');";
            		$result=mysql_query($sql);

					$new_geb2_typ='';
					if ($geb2_typ[$gid-19]!=$id)
					{
						$new_geb2_typ=$geb2_typ;
						$new_geb2_typ[$gid-19]=$id;
						$new_geb2_typ=implode(':',$new_geb2_typ);
					}


					$neu_lager[0]=$lager[0]-$gebeude[$id]['kosten_holz'][$nachste_stufe];
					$neu_lager[1]=$lager[1]-$gebeude[$id]['kosten_lehm'][$nachste_stufe];
					$neu_lager[2]=$lager[2]-$gebeude[$id]['kosten_eisen'][$nachste_stufe];
					$neu_lager[3]=$lager[3]-$gebeude[$id]['kosten_getreide'][$nachste_stufe];

					$l_lager[4]=$lager[4]+$gebeude[$id]['arbeiter'];

            		//Einwohner und Rohstoffe aktualisieren
            		$sql="UPDATE `tr".$round_id."_dorfer` SET `einwohner`='".$l_lager[4]."', `lager`='".
            			implode(':',$neu_lager)."'";
            		if ($new_geb2_typ!='') {$sql.=", `geb2t`='".$new_geb2_typ."'";$geb2_typ=explode(':',$new_geb2_typ);}
            		$sql.=" WHERE `x`='$dorfx' AND `y`='$dorfy';";
            		$result=mysql_query($sql);

					$lager=$neu_lager;
					$lager[4]=$l_lager[4];
            	}
			}
        }
	}
}



?>


<!-- Top links -->
<?php top_links($username); ?>


<div id="lmidall">
<div id="lmidlc">


<!-- Links am linken Rand -->
<?php links(); ?>


<!-- Dorfname -->
<div id="lmid1"><div id="lmid2"><div class="dname">
<h1><?php echo $dorf_data['name']; ?></h1>
</div>

<?php
//Gebäude anzeigen
for ($i=1;$i<=20;$i++)
{
	if ($geb2_typ[$i-1]>0)
	{
		if ($geb2_stufe[$i-1]>0)
			echo'<img class="d'.$i.'" src="img/un/g/g'.$geb2_typ[$i-1].'.gif">';
		else
			echo'<img class="d'.$i.'" src="img/un/g/g'.$geb2_typ[$i-1].'b.gif">';
	}
	else
		echo'<img class="d'.$i.'" src="img/un/g/iso.gif" height="100" width="75">';
}
?>


<!-- Versammlungsplatz falls nicht gebraucht einfach weglassen -->
<?php
if ($geb2_typ[20]==16 AND $geb2_stufe[20]>0)
	echo'<img class="dx1" src="img/un/g/g16.gif">';
?>

<!-- Stadtmauer: d2_11, Erdwall: d2_12, Palisade: d2_1, keine Mauer: d2_0 -->
<?php
if ($geb2_typ[21]==0 OR $geb2_stufe[21]==0)
	echo'<div class="d2_x d2_0">';
elseif ($geb2_typ[21]==31)
	echo'<div class="d2_x d2_1">';
elseif ($geb2_typ[21]==32)
	echo'<div class="d2_x d2_2">';
elseif ($geb2_typ[21]==33)
	echo'<div class="d2_x d2_3">';
?>

<img usemap="#map2" src="img/un/a/x.gif" border="0" height="450" width="540">
</div>

<map name="map2">
<?php	//Stadtmauer anwählen
echo'<area href="build.php?id=40" title="'.$na_st[40].'" coords="325,225,180" shape="circle">
	<area href="build.php?id=40" title="'.$na_st[40].'" coords="220,230,185" shape="circle">';
?>
</map>


<map name="map1">
<?php


echo'
<area href="build.php?id=19" title="'.$na_st[19].'"
	coords="53,91,53,37,128,37,128,91,91,112" shape="poly">
<area href="build.php?id=20" title="'.$na_st[20].'"
	coords="136,66,136,12,211,12,211,66,174,87" shape="poly">
<area href="build.php?id=21" title="'.$na_st[21].'"
	coords="196,56,196,2,271,2,271,56,234,77" shape="poly">
<area href="build.php?id=22" title="'.$na_st[22].'"
	coords="270,69,270,15,345,15,345,69,308,90" shape="poly">
<area href="build.php?id=23" title="'.$na_st[23].'"
	coords="327,117,327,63,402,63,402,117,365,138" shape="poly">
<area href="build.php?id=24" title="'.$na_st[24].'"
	coords="14,129,14,75,89,75,89,129,52,150" shape="poly">
<area href="build.php?id=25" title="'.$na_st[25].'"
	coords="97,137,97,83,172,83,172,137,135,158" shape="poly">
<area href="build.php?id=26" title="'.$na_st[26].'"
	coords="182,119,182,65,257,65,257,119,220,140" shape="poly">
<area href="build.php?id=27" title="'.$na_st[27].'"
	coords="337,156,337,102,412,102,412,156,375,177" shape="poly">
<area href="build.php?id=28" title="'.$na_st[28].'"
	coords="2,199,2,145,77,145,77,199,40,220" shape="poly">
<area href="build.php?id=29" title="'.$na_st[29].'"
	coords="129,164,129,110,204,110,204,164,167,185" shape="poly">
<area href="build.php?id=30" title="'.$na_st[30].'"
	coords="92,189,92,135,167,135,167,189,130,210" shape="poly">
<area href="build.php?id=31" title="'.$na_st[31].'"
	coords="342,216,342,162,417,162,417,216,380,237" shape="poly">
<area href="build.php?id=32" title="'.$na_st[32].'"
	coords="22,238,22,184,97,184,97,238,60,259" shape="poly">
<area href="build.php?id=33" title="'.$na_st[33].'"
	coords="167,232,167,178,242,178,242,232,205,253" shape="poly">
<area href="build.php?id=34" title="'.$na_st[34].'"
	coords="290,251,328,231,364,251,328,272" shape="poly">
<area href="build.php?id=35" title="'.$na_st[35].'"
	coords="95,273,95,219,170,219,170,273,133,294" shape="poly">
<area href="build.php?id=36" title="'.$na_st[36].'"
	coords="222,284,222,230,297,230,297,284,260,305" shape="poly">
<area href="build.php?id=37" title="'.$na_st[37].'"
	coords="80,306,80,252,155,252,155,306,118,327" shape="poly">
<area href="build.php?id=38" title="'.$na_st[38].'"
	coords="199,316,199,262,274,262,274,316,237,337" shape="poly">

<area href="build.php?id=39" title="'.$na_st[39].'"
	coords="270,158,303,135,316,155,318,178,304,211,288,227,263,238,250,215" shape="poly">

<area href="build.php?id=40" title="'.$na_st[40].'"
	coords="312,338,347,338,377,320,406,288,421,262,421,222,396,275,360,311" shape="poly">
<area href="build.php?id=40" title="'.$na_st[40].'"
	coords="49,338,0,274,0,240,33,286,88,338" shape="poly">
<area href="build.php?id=40" title="'.$na_st[40].'"
	coords="0,144,34,88,93,39,181,15,252,15,305,31,358,63,402,106,421,151,421,93,378,47,280,0,175,0,78,28,0,92" shape="poly">
';
?>


</map>

<img class="dmap" usemap="#map1" src="img/un/a/x.gif" border="0" height="339" width="422">
<div id="lplz2"></div>


<!-- Bauauftrag -->
<?php
$sql="SELECT `id`,`zeit` FROM `tr".$round_id."_others` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `typ`='10' ORDER BY `zeit` ASC;";
$result=mysql_query($sql);
$anz=mysql_num_rows($result);
if ($anz>0)
{
	echo'<div id="lbau2">';				//Titel
	echo'<div class="f10 b">Bauauftrag:</div>';
	echo'<table width="100%" class="f10"><tbody>';
	for ($i=1;$i<=$anz;$i++)
	{
		$data=mysql_fetch_array($result);

		$gid=$data['id'];
        if ($gid<19){ $id=$land_geb[$gid-1]; $stufe=$geb1_stufe[$gid-1]; }
        else        { $id=$geb2_typ[$gid-19]; $stufe=$geb2_stufe[$gid-19]; }

		echo'<tr><td><a href="?do=del&gid='.$gid.'&id='.$id.'">'.	//Löschknopf
			'<img src="img/un/a/del.gif" border="0" width="12" height="12" title="abbrechen"></a></td>'.
			'<td>'.$gebeude[$id]['name'].' (Stufe '.($stufe+1).')</td>'.
			'<td><span id=timer'.$i.'>'.zeit_dauer(strtotime($data['zeit'])-time()).'</span> Std.</td>'.
			'<td>Fertig um '.date('H:i',strtotime($data['zeit'])).'</span><span> Uhr</td></tr>';
	}
	echo'</tbody></table></div>';
}

?>

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