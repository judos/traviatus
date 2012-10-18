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

//Gebäude Stufen des Landes
$geb1_stufe=split(':',$dorf_data['geb1']);
$geb2_typ=split(':',$dorf_data['geb2t']);
$geb2_stufe=split(':',$dorf_data['geb2']);

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


for ($i=1;$i<=18;$i++)
	$geb_name[$i]=$gebeude[$land_geb[$i-1]]['name'];


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

	//Einwohner und Rohstoffe aktualisieren
	$sql="UPDATE `tr".$round_id."_dorfer` SET `einwohner`='".($dorf_data['einwohner']-$gebeude[$id]['arbeiter'])."', `lager`='".
		($lager[0]+$gebeude[$id]['kosten_holz'][$stufe]).":".($lager[1]+$gebeude[$id]['kosten_lehm'][$stufe]).":".
		($lager[2]+$gebeude[$id]['kosten_eisen'][$stufe]).":".($lager[3]+$gebeude[$id]['kosten_getreide'][$stufe])."'";
	if ($new_geb2_typ!='') {$sql.=", `geb2t`='".$new_geb2_typ."'"; $geb2_typ=$new_geb2_typ; }
	$sql.=" WHERE `x`='$dorfx' AND `y`='$dorfy';";
	$result=mysql_query($sql);

	//Daten des Dorfes
	$sql="SELECT * FROM `tr".$round_id."_dorfer` WHERE `x`='$dorfx' AND `y`='$dorfy';";
	$result=mysql_query($sql);
	$dorf_data=mysql_fetch_array($result);
	//Daten des Landes
	$sql="SELECT `typ` FROM `tr".$round_id."_lander` WHERE `x`='$dorfx' AND `y`='$dorfy';";
	$result=mysql_query($sql);
	$land_data=mysql_fetch_array($result);

    //Produktion und Lager neu laden
    $produktion=load_produktion($dorf_data,$land_data);
    $lager=load_lager($dorf_data);
    $lager_grosse=lager_grosse($dorf_data);
}

if (isset($_GET['id']) AND isset($_GET['gid']) AND !isset($_GET['do']))
{
	$id=$_GET['id'];
	$gid=$_GET['gid'];

	if ($geb1_stufe[$gid-1]<$gebeude[$id]['stufen'])	//Nicht vollständig ausgebaut
	{
	    $sql="SELECT * FROM `tr".$round_id."_others` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `typ`='10';";
        $result=mysql_query($sql);
        $build=1;
        if (mysql_num_rows($result)==2)
            $build=0;
        if (mysql_num_rows($result)==1)
        {
            $build=1;
            $data1=mysql_fetch_array($result);
            if ($data1['id']==$gid) $build=0;
        }

        if ($build==1)				//Wenn dieses Gebäude nicht schon ausgebaut wird und nicht zwei andere gebaut werden.
        {
        	$nachste_stufe=$geb1_stufe[$gid-1]+1;
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

            		//Einwohner und Rohstoffe aktualisieren
            		$sql="UPDATE `tr".$round_id."_dorfer` SET `einwohner`='".($lager[4]+$gebeude[$id]['arbeiter'])."', `lager`='".
            			($lager[0]-$gebeude[$id]['kosten_holz'][$nachste_stufe]).":".
            			($lager[1]-$gebeude[$id]['kosten_lehm'][$nachste_stufe]).":".
            			($lager[2]-$gebeude[$id]['kosten_eisen'][$nachste_stufe]).":".
            			($lager[3]-$gebeude[$id]['kosten_getreide'][$nachste_stufe])."'
            			WHERE `x`='$dorfx' AND `y`='$dorfy';";
            		$result=mysql_query($sql);

                    //Daten des Dorfes
                    $sql="SELECT * FROM `tr".$round_id."_dorfer` WHERE `x`='$dorfx' AND `y`='$dorfy';";
                    $result=mysql_query($sql);
                    $dorf_data=mysql_fetch_array($result);
                    //Daten des Landes
                    $sql="SELECT `typ` FROM `tr".$round_id."_lander` WHERE `x`='$dorfx' AND `y`='$dorfy';";
                    $result=mysql_query($sql);
                    $land_data=mysql_fetch_array($result);

                    //Produktion und Lager neu laden
                    $produktion=load_produktion($dorf_data,$land_data);
                    $lager=load_lager($dorf_data);
                    $lager_grosse=lager_grosse($dorf_data);
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

<!-- anklickbare Felder -->
<map name="rx">
<?php
echo'<area href="build.php?id=1" coords="101,33,28" shape="circle" title="'.$geb_name[1].' Stufe '.$geb1_stufe[0].'">'.
'<area href="build.php?id=2" coords="165,32,28" shape="circle" title="'.$geb_name[2].' Stufe '.$geb1_stufe[1].'">'.
'<area href="build.php?id=3" coords="224,46,28" shape="circle" title="'.$geb_name[3].' Stufe '.$geb1_stufe[2].'">'.
'<area href="build.php?id=4" coords="46,63,28" shape="circle" title="'.$geb_name[4].' Stufe '.$geb1_stufe[3].'">'.
'<area href="build.php?id=5" coords="138,74,28" shape="circle" title="'.$geb_name[5].' Stufe '.$geb1_stufe[4].'">'.
'<area href="build.php?id=6" coords="203,94,28" shape="circle" title="'.$geb_name[6].' Stufe '.$geb1_stufe[5].'">'.
'<area href="build.php?id=7" coords="262,86,28" shape="circle" title="'.$geb_name[7].' Stufe '.$geb1_stufe[6].'">'.
'<area href="build.php?id=8" coords="31,117,28" shape="circle" title="'.$geb_name[8].' Stufe '.$geb1_stufe[7].'">'.
'<area href="build.php?id=9" coords="83,110,28" shape="circle" title="'.$geb_name[9].' Stufe '.$geb1_stufe[8].'">'.
'<area href="build.php?id=10" coords="214,142,28" shape="circle" title="'.$geb_name[10].' Stufe '.$geb1_stufe[9].'">'.
'<area href="build.php?id=11" coords="269,146,28" shape="circle" title="'.$geb_name[11].' Stufe '.$geb1_stufe[10].'">'.
'<area href="build.php?id=12" coords="42,171,28" shape="circle" title="'.$geb_name[12].' Stufe '.$geb1_stufe[11].'">'.
'<area href="build.php?id=13" coords="93,164,28" shape="circle" title="'.$geb_name[13].' Stufe '.$geb1_stufe[12].'">'.
'<area href="build.php?id=14" coords="160,184,28" shape="circle" title="'.$geb_name[14].' Stufe '.$geb1_stufe[13].'">'.
'<area href="build.php?id=15" coords="239,199,28" shape="circle" title="'.$geb_name[15].' Stufe '.$geb1_stufe[14].'">'.
'<area href="build.php?id=16" coords="87,217,28" shape="circle" title="'.$geb_name[16].' Stufe '.$geb1_stufe[15].'">'.
'<area href="build.php?id=17" coords="140,231,28" shape="circle" title="'.$geb_name[17].' Stufe '.$geb1_stufe[16].'">'.
'<area href="build.php?id=18" coords="190,232,28" shape="circle" title="'.$geb_name[18].' Stufe '.$geb1_stufe[17].'">'.
'<area href="dorf2.php" coords="144,131,36" shape="circle" title="Dorfzentrum">';
?>
</map>

<?php
echo'<div id="f'.$land_data['typ'].'">';
for ($i=1;$i<=18;$i++)
{
if($geb1_stufe[$i-1]>0)
{
echo'<img src="img/un/g/s/s'.$geb1_stufe[$i-1].'.gif" class="rf'.$i.'">';
}
}
?>
</div>

<img id="resfeld" usemap="#rx" src="img/un/a/x.gif">
<div><div id="lplz1"></div>

<div id="ltbw0">


<?php
function print_angriffe($aktion,$bild,$soz)
{
global $dorfx,$dorfy,$userid,$titel,$nr;
global $round_id;
if ($aktion==3 AND $soz==1) $z=5;	//farben bestimmen
if ($aktion!=3 AND $soz==1) $z=3;	// ^^
if ($soz==2) $z=4;					// ^^

if ($soz==1) { $x="`ziel_x`='$dorfx' AND `ziel_y`='$dorfy'"; $y='»'; }
if ($soz==2) { $x="`start_x`='$dorfx' AND `start_y`='$dorfy' AND `user`='$userid'"; $y='«'; }
if ($aktion==2) { $akt="`aktion`='$aktion'"; $text='Unterst.'; }
if ($aktion==3) { $akt="(`aktion`>='3' AND `aktion`<='5')"; $text='Angriff'; }
$sql="SELECT `ziel_zeit` FROM `tr".$round_id."_truppen_move` WHERE ".$x." AND ".$akt."
	ORDER BY `ziel_zeit` ASC;";
$result=mysql_query($sql);
$anz=mysql_num_rows($result);
if ($anz>0)
{
	if ($titel==0)
	{
	   $titel=1;
	   echo'<div class="f10 b">&nbsp;Truppenbewegungen:</div><table class="f10"><tbody>';
	}
	$data=mysql_fetch_array($result);
	echo'<tr><td><a href="build.php?id=39"><img src="img/un/a/'.$bild.'.gif" border="0"></a></td>
		<td><b class="c'.$z.' f10" align="right">» '.$anz.'</b></td><td><b class="c'.$z.' f10">'.$text.'</b></td>
		<td class="f10" align="center">in</td><td class="f10"><span id="timer'.$nr.'">'.
		zeit_dauer(strtotime($data['ziel_zeit'])-time()).'</span> Std.</td></tr>';
	$nr++;
}
}

$titel=0;$nr=1;
print_angriffe(3,'att1',1);
print_angriffe(2,'def1',1);
print_angriffe(3,'att2',2);
print_angriffe(2,'def2',2);
if ($titel==1) echo'</tbody></table>';

?>

</div>
<div id="lrpr">


<!-- Produktion pro Stunde -->
<div class="f10 b">&nbsp;Produktion:</div>
<table>
<tr>
<td><img class="res" src="img/un/r/1.gif"></td>
<td>Holz:</td><td align="right"><b><?php echo $produktion[0]; ?>&nbsp;</b></td><td>pro Stunde</td>
</tr>
<tr>
<td><img class="res" src="img/un/r/2.gif"></td>
<td>Lehm:</td><td align="right"><b><?php echo $produktion[1]; ?>&nbsp;</b></td><td>pro Stunde</td>
</tr>
<tr>
<td><img class="res" src="img/un/r/3.gif"></td>
<td>Eisen:</td><td align="right"><b><?php echo $produktion[2]; ?>&nbsp;</b></td><td>pro Stunde</td>
</tr>
<tr>
<td><img class="res" src="img/un/r/4.gif"></td>
<td>Getreide:</td><td align="right"><b><?php
echo $produktion[3]-$lager[4]-$troops_village['versorgung']; ?>
&nbsp;</b></td><td>pro Stunde</td>
</tr>
</table></div>


<!-- Einheiten im Dorf -->
<div id="ltrm"><div class="f10 b">&nbsp;Einheiten:</div>
<table class="f10"><tbody>
<?php
if ($troops_village['any']==0)	echo'<tr><td class="s7">keine</td></tr>';
else
{
	for ($i=1;$i<=30;$i++)
	{
		if ($troops_village['all'][$i]>0)
		{
			echo'<tr><td><a href="build.php?id=39"><img class="unit" src="img/un/u/'.$i.'.gif" border="0"></a></td>
			<td align="right">&nbsp;<b>'.$troops_village['all'][$i].'</b></td><td>';
			if ($troops_village['all'][$i]>1) echo $troops[$i]['namepl'];
			else echo $troops[$i]['name'];
			echo'</td></tr>';
		}
	}
}
?>
</tbody></table>
</div>


<!-- Bauauftrag -->
<?php
$sql="SELECT `id`,`zeit` FROM `tr".$round_id."_others` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `typ`='10' ORDER BY `zeit` ASC;";
$result=mysql_query($sql);
$anz=mysql_num_rows($result);
if ($anz>0)
{
	echo'<div id="lbau1">';				//Titel
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
			'<td><span id=timer'.($i+$nr-1).'>'.zeit_dauer(strtotime($data['zeit'])-time()).'</span> Std.</td>'.
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