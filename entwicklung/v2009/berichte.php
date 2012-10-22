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
//Lager laden
$lager=load_lager($dorf_data);
//Lager grösse laden
$lager_grosse=lager_grosse($dorf_data);
//Produktion laden
$produktion=load_produktion($dorf_data,$land_data);
//Truppen
$troops=load_troops($dorf_data,$spieler_data);
$troops_village=load_troops_in_village($userid,$dorfx,$dorfy,$troops);



if (!isset($_GET['start']))
	$start=0;
else
	$start=$_GET['start'];

if ($_GET['do']=='del')
{
	for ($i=1;$i<=10;$i++)
	{
		if ($_POST['n'.$i]!='')
		{
			$sql="DELETE FROM `tr".$round_id."_msg` WHERE `zeit`='".$_POST['n'.$i]."' AND `an`='$username' AND `von`='';";
			$result=mysql_query($sql);
		}
	}
}

$t=$_GET['t'];

?>


<!-- Top links -->
<?php top_links($username); ?>

<div id="lmidall">
<div id="lmidlc">

<!-- Links am linken Rand -->
<?php links(); ?>

<div id="lmid1">
<div id="lmid2">


<!-- Berichte Menü -->
<h1>Berichte</h1>

<p class="txt_menue">
<?php
if (!isset($_GET['t']) OR $_GET['t']=='') echo'<a href="berichte.php"><span class="c">Alle</span></a>';
else echo'<a href="berichte.php">Alle</a>';
echo' | ';
if ($_GET['t']=='1') echo'<a href="berichte.php?t=1"><span class="c">Handel</span></a>';
else echo'<a href="berichte.php?t=1">Handel</a>';
echo' | ';
if ($_GET['t']=='2') echo'<a href="berichte.php?t=2"><span class="c">Unterstützung</span></a>';
else echo'<a href="berichte.php?t=2">Unterstützung</a>';
echo' | ';
if ($_GET['t']=='3') echo'<a href="berichte.php?t=3"><span class="c">Angriffe</span></a>';
else echo'<a href="berichte.php?t=3">Angriffe</a>';
echo' | ';
if ($_GET['t']=='4') echo'<a href="berichte.php?t=4"><span class="c">Sonstige</span></a>';
else echo'<a href="berichte.php?t=4">Sonstige</a>';
echo'</p>';


if (!isset($_GET['view']))
{
echo'<form method="post" action="berichte.php?do=del&t='.$t.'" name="msg">';
?>
<p></p><table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr class="rbg"><td colspan="2">Betreff:</td><td>Gesendet</td></tr>

<?php

$sql="SELECT * FROM `tr".$round_id."_msg` WHERE `an`='$username' AND `von`='' ";
if (isset($t) AND $t!='') $sql=$sql."AND `typ`='$t' ";
$sql=$sql."ORDER BY `zeit` DESC LIMIT $start,10;";
//echo $sql;
$result=mysql_query($sql);
$anz=mysql_num_rows($result);
if ($anz>0)
{
	for ($i=1;$i<=$anz;$i++)
	{
		$data=mysql_fetch_array($result);

		echo'<tr>'.
			'<td width="22"><input name="n'.$i.'" value="'.$data['zeit'].'" type="checkbox"></td>'.
			'<td class="s7"><a href="berichte.php?view=msg&id='.$data['zeit'].'&b='.$data['betreff'].
			'">'.$data['betreff'].'</a> ';
		if ($data['neu']==1) echo'(neu)';
		echo'</td>'.
			'<td nowrap="nowrap" width="125">'.date('d.m.y H:i',strtotime($data['zeit'])).'</td>'.
			'</tr>';
	}
}
else
	echo'<tr><td colspan="4" class="c">Es sind keine Berichte vorhanden</td></tr>';


echo'<tr class="rbg">';
if ($anz>0) echo'<td align="center"><input id="s10" name="s10" onclick="Allmsg(this.form);" align="bottom" type="checkbox"></td>';
echo'<td class="s7" ';
if ($anz==0) echo'colspan=2';
echo'><input class="std" name="del" value="Löschen" type="submit"></td><td class="r7">';


$sql="SELECT `zeit` FROM `tr".$round_id."_msg` WHERE `an`='$username' AND `von`='' ";
if (isset($t)) $sql=$sql."AND `typ`='$t' ";
$sql=$sql.";";
$result=mysql_query($sql);
$anz=mysql_num_rows($result);
if ($start>0) echo'<a href="berichte.php?t='.$t.'&s='.($start-10).'">«</a>';
else echo'<span class="c"><b>«</b></span>';

if ($start+10<$anz) echo'<a href="berichte.php?t='.$t.'&s='.($start+10).'">»</a>';
else echo'<span class="c"><b>»</b></span>';
?>

</td></tr>
</tbody></table>
</form>
<?php
}
if ($_GET['view']=='msg')
{

$sql="UPDATE `tr".$round_id."_msg` SET `neu`='0' WHERE `an`='$username' AND `von`='' AND `zeit`='".$_GET['id']."';";
$result=mysql_query($sql);

$sql="SELECT * FROM `tr".$round_id."_msg` WHERE `an`='$username' AND `von`='' AND `zeit`='".$_GET['id']."'
	AND `betreff`='".$_GET['b']."';";
$result=mysql_query($sql);
$data=mysql_fetch_array($result);

$alles=split(chr(13),$data['text']);	// [zeile]	->array
$anz_zeilen=split(':',$alles[0]);		// [tabelle]->array
$anz_tabellen=count($anz_zeilen);			// zahl

?>

<table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr class="rbg">
<td class="s7">Betreff:</td>
<td class="s7"><?php echo $data['betreff']; ?></td>
</tr>

<tr>
<td class="s7 b">Gesendet:</td>
<td class="s7">am <?php echo date('d.m.y',strtotime($data['zeit'])).' um '.date('H:i:s',strtotime($data['zeit']));?><span> Uhr</span></td>
</tr>

<tr><td colspan="2"></td></tr>
<tr height="200">
<td colspan="2" valign="top"><p>
</p>

<?php

$zeile=1;
for ($tabelle=0;$tabelle<=$anz_tabellen-1;$tabelle++)
{
	if ($tabelle>0) echo'<br>';
	echo'<table class="tbg" cellpadding="2" cellspacing="1"><tbody>';
	for ($i=0;$i<=$anz_zeilen[$tabelle]-1;$i++)
	{
		$teile=split(':',$alles[$zeile+$i]);
		if ($teile[0]==1)	//Text
		{
			echo'<tr class="cbg1">';
            if ($teile[1]=='Angreifer') echo'<td width="21%" class="c2 b">';
            elseif($teile[1]=='Verteidiger') echo'<td width="21%" class="c1 b">';
            elseif($teile[1]=='Unterstützung') echo'<td width="21%" class="c1 b">';
            else    echo'<td width="21%" class="b">';
			echo $teile[1].'</td>
				<td colspan=10 class="b">'.$teile[2].'</td></tr>';
		}
		if ($teile[0]==2)	//Rohstoffe
		{
			echo'<tr><td>Rohstoffe</td><td class="s7">'.
				'<img class="res" src="img/un/r/1.gif">'.$teile[1].' | '.
				'<img class="res" src="img/un/r/2.gif">'.$teile[2].' | '.
				'<img class="res" src="img/un/r/3.gif">'.$teile[3].' | '.
				'<img class="res" src="img/un/r/4.gif">'.$teile[4].
				'</td></tr>';
		}
		if ($teile[0]==3)	//Bilder von Einheiten
		{
			echo'<tr class="unit"><td>&nbsp;</td>';
			$v=$teile[1]*10-10;
			for ($j=1;$j<=10;$j++)
				echo'<td><img src="img/un/u/'.($j+$v).'.gif" title="'.$troops[$j+$v]['name'].'"></td>';
			echo'</tr>';
		}
		if ($teile[0]==4)	//Text und Einheitenanzahl
		{
			echo'<tr><td>'.$teile[1].'</td>';
			for ($j=1;$j<=10;$j++)
			{
				if ($teile[$j+1]>0) echo'<td>'.$teile[$j+1].'</td>';
				else	echo'<td class="c">0</td>';
			}
			echo'</tr>';
		}
		if ($teile[0]==5)	//Unterhalt
		{
			echo'<tr class="cbg1"><td>Unterhalt</td>
				<td class="s7" colspan="10">'.$teile[1].'<img class="res" src="img/un/r/4.gif">pro Stunde</td></tr>';

		}
	}
	$zeile+=$anz_zeilen[$tabelle];
	echo'</tbody></table>';
}
?>

</td></tr>
</tbody></table>



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
<?php lager($dorf_data,$produktion,$lager,$lager_grosse,$troops_village['versorgung']); ?>

<?php
// Ausgabe der Berechnungs- und Serverzeit
serverzeit($load_time);
?>

<div id="ce">
</div>

</body>
</html>