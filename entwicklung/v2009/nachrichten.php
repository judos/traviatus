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
			$sql="DELETE FROM `tr".$round_id."_msg`
				WHERE `zeit`='".$_POST['n'.$i]."' AND `an`='$username' AND `von`!='' AND `typ`='0';";
			$result=mysql_query($sql);
		}
	}
}
if ($_GET['do']=='delout')
{
	for ($i=1;$i<=10;$i++)
	{
		if ($_POST['n'.$i]!='')
		{
			$sql="DELETE FROM `tr".$round_id."_msg`
				WHERE `zeit`='".$_POST['n'.$i]."' AND `an`='$username' AND `von`!='' AND `typ`='1';";
			$result=mysql_query($sql);
		}
	}
}
if ($_GET['do']=='send')
{
	$an1=$_POST['an'];
	$betreff1=$_POST['betreff'];
	unset($text1);
	$text1=$_POST['text'];

	$sql="SELECT `id` FROM `tr".$round_id."_user` WHERE `name`='$an1';";
	$result=mysql_query($sql);
	if (mysql_num_rows($result)>0)
	{

/*		$text1=str_replace('ä','&auml;',$text1);
		$text1=str_replace('ö','&ouml;',$text1);
		$text1=str_replace('ü','&uuml;',$text1);
		$text1=str_replace('Ä','&Auml;',$text1);
		$text1=str_replace('Ö','&Ouml;',$text1);
		$text1=str_replace('Ü','&Uuml;',$text1);	*/

		$text1=str_replace('Ã¤','ä',$text1);
		$text1=str_replace('Ã¶','ö',$text1);
		$text1=str_replace('Ã¼','ü',$text1);

		//Nachricht senden
		$sql="INSERT INTO `tr".$round_id."_msg` (`von`,`an`,`betreff`,`zeit`,`text`,`typ`)
			VALUES ('$username','$an1','$betreff1',NOW(),'$text1','0');";
		$result=mysql_query($sql);

		//Kopie im Postausgang speichern
		$sql="INSERT INTO `tr".$round_id."_msg` (`von`,`an`,`betreff`,`zeit`,`text`,`typ`)
			VALUES ('$an1','$username','$betreff1',NOW(),'$text1','1');";
		$result=mysql_query($sql);
		unset($view);
		$antwort='Gesendet!';
	}
	else
	{
		$_REQUEST['view']='answer';
		$antwort='Dieser User existiert nicht!';
	}
}
?>


<!-- Top links -->
<?php top_links($username); ?>

<div id="lmidall">
<div id="lmidlc">

<!-- Links am linken Rand -->
<?php links(); ?>


<!-- Nachrichten Menü -->
<div id="lmid1"><div id="lmid2">
<h1>Nachrichten</h1>
<p class="txt_menue">
<?php
if (!isset($_REQUEST['view'])) echo'<a href="nachrichten.php"><span class="c">Eingang</span></a>';
else	echo'<a href="nachrichten.php">Eingang</a>';
echo' | ';
if ($_REQUEST['view']=='answer') echo'<a href="nachrichten.php?view=answer"><span class="c">Schreiben</span></a>';
else	echo'<a href="nachrichten.php?view=answer">Schreiben</a>';
echo' | ';
if ($_REQUEST['view']=='out') echo'<a href="nachrichten.php?view=out"><span class="c">Gesendet</span></a>';
else	echo'<a href="nachrichten.php?view=out">Gesendet</a>';
?>
</p>


<?php
if (isset($antwort)) echo'<p class="f10">'.$antwort.'</p>';



if (!isset($_REQUEST['view']))
{
?>
<!-- Form zum löschen von Nachrichten -->
<form method="post" action="nachrichten.php?do=del" name="msg">
<table cellspacing="1" cellpadding="2" class="tbg">
<tr class="rbg">
<td width="22"></td>
<td>Betreff</td>
<td>Absender</td>
<td width="125">Gesendet</td>
</tr>
<?php
$sql="SELECT * FROM `tr".$round_id."_msg` WHERE `an`='$username' AND `von`!='' AND `typ`='0' ORDER BY `zeit` DESC LIMIT $start,10;";
$result=mysql_query($sql);
if (mysql_num_rows($result)==0)
	echo'<tr><td colspan="4" class="c" align="center">Es sind keine Nachrichten im Posteingang vorhanden</td></tr>';
else
{
	for ($i=1;$i<=mysql_num_rows($result);$i++)
	{
		$data=mysql_fetch_array($result);
		if ($data['betreff']=='') $data['betreff']='kein Betreff';
		echo'<tr>'.
			'<td><input type="Checkbox" name="n'.$i.'" value="'.$data['zeit'].'"></td>'.
			'<td class="s7"><a href="nachrichten.php?view=msg&id='.$data['zeit'].'&typ='.$data['typ'].'">'.
			$data['betreff'].'</a> ';
		if ($data['neu']==1) echo'(neu)';
		echo'</td><td style="text-align:left;">&nbsp;<a href="spieler.php?name='.$data['von'].'">'.$data['von'].'</a></td>'.
			'<td>'.date('d.m.y H:i',strtotime($data['zeit'])).'</td>'.
			'</tr>';
	}
}


?>

<!-- unteres Menü -->
<tr class="rbg">
<td align="center"><input id="s10" name="s10" onClick="Allmsg(this.form);" align="bottom" type="checkbox"></td>
<td class="s7"colspan="2"><input class="std" name="delmsg" type="Submit" value="Löschen"></td>
<td align="right">
<!-- Vor und zurück -->
<?php
$sql="SELECT * FROM `tr".$round_id."_msg` WHERE `an`='$username' AND `von`!='' AND `typ`='0' ORDER BY `zeit` DESC;";
$result=mysql_query($sql);
$anz=mysql_num_rows($result);
if ($start>0)
	echo'<a href="nachrichten.php?start='.($start-10).'"><b>&laquo;</b></a>';
else
	echo'<span class="c"><b>&laquo;</b></span>';
if ($start+10<$anz)
	echo'<a href="nachrichten.php?start='.($start+10).'"><b>&raquo;</b></a>';
else
	echo'<span class="c"><b>&raquo;</b></span>&nbsp;';
?>
</td>
</tr>
</table>
</form>
<?php
}

if ($_REQUEST['view']=='out')
{
?>
<!-- Form zum löschen von Nachrichten -->
<form method="post" action="nachrichten.php?do=delout&view=out" name="msg">
<table cellspacing="1" cellpadding="2" class="tbg">
<tr class="rbg">
<td width="22"></td>
<td>Betreff</td>
<td>Empfänger</td>
<td width="125">Gesendet</td>
</tr>
<?php
$sql="SELECT * FROM `tr".$round_id."_msg` WHERE `von`='$username' AND `typ`='0';";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++)
{
	$data=mysql_fetch_array($result);
	$msg_sent_new[$data['an']][$data['zeit']]=$data['neu'];
}

$sql="SELECT * FROM `tr".$round_id."_msg` WHERE `an`='$username' AND `von`!='' AND `typ`='1' ORDER BY `zeit` DESC LIMIT $start,10;";
$result=mysql_query($sql);
if (mysql_num_rows($result)==0)
	echo'<tr><td colspan="4" class="c" align="center">Es sind keine Nachrichten im Postausgang vorhanden</td></tr>';
else
{
	for ($i=1;$i<=mysql_num_rows($result);$i++)
	{
		$data=mysql_fetch_array($result);
		if ($data['betreff']=='') $data['betreff']='kein Betreff';
		echo'<tr>'.
			'<td><input type="Checkbox" name="n'.$i.'" value="'.$data['zeit'].'"></td>'.
			'<td class="s7"><a href="nachrichten.php?view=msg&id='.$data['zeit'].'&typ='.$data['typ'].'">'.
			$data['betreff'].'</a> ';

		if ($msg_sent_new[$data['von']][$data['zeit']]==1) echo'(neu)';
		elseif (!isset($msg_sent_new[$data['von']][$data['zeit']])) echo'<span class="c">(gelöscht)</span>';

		echo'</td><td style="text-align:left;">&nbsp;<a href="spieler.php?name='.
			$data['von'].'">'.$data['von'].'</a></td>'.
			'<td>'.date('d.m.y H:i',strtotime($data['zeit'])).'</td>'.
			'</tr>';
	}
}


?>

<!-- unteres Menü -->
<tr class="rbg">
<td align="center"><input id="s10" name="s10" onClick="Allmsg(this.form);" align="bottom" type="checkbox"></td>
<td class="s7"colspan="2"><input class="std" name="delmsg" type="Submit" value="Löschen"></td>
<td align="right">
<!-- Vor und zurück -->
<?php
$sql="SELECT * FROM `tr".$round_id."_msg` WHERE `an`='$username' AND `von`!='' AND `typ`='1' ORDER BY `zeit` DESC;";
$result=mysql_query($sql);
$anz=mysql_num_rows($result);
if ($start>0)
	echo'<a href="nachrichten.php?view=out&start='.($start-10).'"><b>&laquo;</b></a>';
else
	echo'<span class="c"><b>&laquo;</b></span>';
if ($start+10<$anz)
	echo'<a href="nachrichten.php?view=out&start='.($start+10).'"><b>&raquo;</b></a>';
else
	echo'<span class="c"><b>&raquo;</b></span>&nbsp;';
?>
</td>
</tr>
</table>
</form>
<?php
}




if ($_REQUEST['view']=='msg')
{
$id=$_GET['id'];
$typ=$_GET['typ'];
$sql="SELECT * FROM `tr".$round_id."_msg` WHERE `an`='$username' AND `zeit`='$id' AND `typ`='$typ';";
$result=mysql_query($sql);
$data=mysql_fetch_array($result);

$sql="UPDATE `tr".$round_id."_msg` SET `neu`='0' WHERE `an`='$username' AND `zeit`='$id';";
$result=mysql_query($sql);
?>
<form method="post" action="nachrichten.php">
<table class="f10" background="img/de/msg/block_bg22.gif" cellpadding="0" cellspacing="0" width="440">
<tbody><tr><td colspan="5"><img src="img/de/msg/block_bg21.gif" border="0" height="41" width="440"></td></tr>

<tr>

<td width="12"></td>
<td rowspan="2" width="86">
<?php
$x='a';
if ($data['typ']=='1') $x='b';
echo'<img src="img/de/msg/block_bg24'.$x.'.gif" border="0" height="34" width="77">';
?>
</td>
<td background="img/de/msg/underline.gif" width="230"><?php echo $data['von']; ?></td>
<td class="right" width="100"><?php echo date('d.m.y',strtotime($data['zeit'])); ?></td>
<td width="12"></td>
</tr>

<tr>
<td width="12"></td>
<td background="img/de/msg/underline.gif" width="230"><?php echo $data['betreff']; ?></td>
<td class="right" width="100"><?php echo date('H:i:s',strtotime($data['zeit'])); ?></td>
<td width="12"></td>
</tr>

<tr><td colspan="5"><div><img src="img/un/a/x.gif" border="0" height="6" width="440">
</div>
<img src="img/de/msg/block_bg25.gif" border="0" height="18" width="440">
</td></tr>

<tr>
<td width="12"><img src="img/un/a/x.gif" border="0" height="250" width="1"></td>
<td colspan="3" background="img/de/msg/underline.gif" valign="top">
<?php echo str_replace(chr(13),'<br>',$data['text']); ?></td>
<td width="12"></td>
</tr>
<tr>
<td colspan="5" align="center">
<?php echo'<input name="id" value="'.$data['zeit'].'" type="hidden">'; ?>
<input name="view" value="answer" type="hidden">
<input value="" name="s1" src="img/de/b/ant1.gif" onMouseDown="btm1('s1','','img/de/b/ant2.gif',1)" onMouseOver="btm1('s1','','img/de/b/ant3.gif',1)" onMouseUp="btm0()" onMouseOut="btm0()" border="0" height="20" type="image" width="80">
</td>
</tr>
<tr>
<td colspan="5"><img src="img/de/msg/block_bg23.gif" border="0" height="18" width="440"></td>
</tr>

</tbody></table>
</form>

<?php
}
if ($_REQUEST['view']=='answer')
{
$text='';
$betreff='';
$an=$_GET['name'];

if ($antwort=='Dieser User existiert nicht!')
{
	$text=$text1;
	$betreff=$betreff1;
	$an=$an1;
}

if (isset($_POST['id']))
{
	$sql="SELECT * FROM `tr".$round_id."_msg` WHERE `an`='$username' AND `zeit`='".$_POST['id']."';";
	$result=mysql_query($sql);
	$data=mysql_fetch_array($result);
	$an=$data['von'];
	$betreff=$data['betreff'];
	$text=chr(13).'____________'.chr(13).$an.' schrieb:'.chr(13).chr(13).$data['text'];
}

?>

<form method="post" action="nachrichten.php?do=send" accept-charset="UTF-8" name="msg">
<table class="f10" background="img/de/msg/block_bg22.gif" cellpadding="0" cellspacing="0" width="440">
<tbody><tr><td colspan="4"><img src="img/de/msg/block_bg21.gif" border="0" height="41" width="440"></td></tr>

<tr>

<td width="12"></td>
<td rowspan="2" width="86"><img src="img/de/msg/block_bg24b.gif" border="0" height="34"></td>
<?php echo'<td width="330"><input name="an" value="'.$an.'" size="40" maxlength="20" style="border: 0px none ; background-image: url(img/de/msg/underline.gif);" type="text"></td>'; ?>
<td width="12"></td>
</tr>

<tr>
<td width="12"></td>
<?php echo'<td width="330"><input name="betreff" value="'.$betreff.'" size="40" maxlength="35" style="border: 0px none ; background-image: url(img/de/msg/underline.gif);" type="text"></td>'; ?>
<td width="12"></td>
</tr>

<tr><td colspan="4"><img src="img/de/msg/block_bg25.gif" border="0" height="18" width="440"></td></tr>

<tr>
<td width="12"><img src="img/un/a/x.gif" border="0" height="250" width="1"></td>
<td colspan="2" align="center">
<textarea name="text" id="igm" cols="55" rows="15" class="f10" style="background-image: url(img/de/msg/underline.gif);">
<?php echo $text; ?>
</textarea></td>

<td width="12"></td>
</tr>
<tr><td colspan="4" align="center"><!--<input name="t" value="2" type="hidden">--><input value="" name="s1" src="img/de/b/snd1.gif" onMouseDown="btm1('s1','','img/de/b/snd2.gif',1)" onMouseOver="btm1('s1','',img/de/b/snd3.gif',1)" onMouseUp="btm0()" onMouseOut="btm0()" onClick="return urlaub()" border="0" height="20" type="image" width="80"></td></tr><tr><td colspan="4"><img src="img/de/msg/block_bg23.gif" border="0" height="18" width="440"></td></tr>
</tbody></table>
</form>

<?php
}
?>







</div></div></div>



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