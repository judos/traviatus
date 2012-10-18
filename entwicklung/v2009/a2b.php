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
//Gebäude laden
$gebeude=load_gebeude($dorf_data,$land_data,$volk);
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

<?php
if ($_GET['do']=='sendtroops2')
{
//	echo'<br><br><br><br>';
	$truppen_string='';
	$truppen_v_string='';

	for ($i=1;$i<=10;$i++)
	{
		$nr=$i+$volk*10-10;

		$t=$_POST['t'.$i];
		if (!isset($t) OR $t=='') $t=0;

		if ($t>$troops_village['own'][$nr])
			$t=$troops_village['own'][$nr];
		$truppen_string.=$t;
		$truppen_v_string.=($troops_village['own'][$nr]-$t);

		if ($i<10) { $truppen_string.=':'; $truppen_v_string.=':'; }
	}

//	var_dump($troop);
	$aktion=$_POST['aktion'];
	$x=$_POST['x'];$y=$_POST['y'];
	$dauer=$_POST['dauer'];

	$sql="INSERT INTO `tr".$round_id."_truppen_move`
	(`user`,`start_x`,`start_y`,`ziel_x`,`ziel_y`,`start_zeit`,`ziel_zeit`,`aktion`,`truppen`) VALUES
	('$userid','$dorfx','$dorfy','$x','$y',NOW(),'".date('Y-m-d H:i:s',time()+$dauer)."','$aktion','$truppen_string');";
	$result=mysql_query($sql);

	$sql="UPDATE `tr".$round_id."_truppen` SET `troops`='$truppen_v_string'
		WHERE `x`='$dorfx' AND `y`='$dorfy' AND `user`='$userid';";
	$result=mysql_query($sql);

//	var_dump($sql);
	header('Location: build.php?id=39');
}

if ($_GET['do']=='sendtroops')
{
	$anzahl=0;
	$aktion=$_POST['c'];
	$spio=0;
	//1=Dorf gründen 2=Unterstützung 3=Angriff 4=Raubzug 5=Spionieren
	if ($aktion!=2) $spio=1;

	for ($i=1;$i<=10;$i++)
	{
		$nr=$i+$volk*10-10;
		$troop[$i]=$_POST['t'.$nr];
		if (!isset($troop[$i]) OR $troop[$i]=='') $troop[$i]=0;

		if ($troop[$i]>0)
		{
			if ($spio==1)
			{	if ($troops[$nr]['spio']==0) $spio=0; }

			if (!isset($speed)) $speed=$troops[$nr]['speed'];
			elseif ($troops[$nr]['speed']<$speed) $speed=$troops[$nr]['speed'];
		}

		$anzahl+=$troop[$i];
		if ($troop[$i]>$troops_village['own'][$nr])
			$troop[$i]=$troops_village['own'][$nr];
	}
	if ($spio==1) $aktion=5;

	if ($anzahl==0) $error='Es wurden keine Truppen ausgewählt';
    if ($dname=='' AND ($x=='' OR $y=='')) $error='Keine Zielangabe';
    if ($dname!='' AND $x!='' AND $y!='') $error='Zuviele Angaben';
    if ($dname!='' AND $x=='' AND $y=='')
    {
        $sql="SELECT * FROM `tr".$round_id."_dorfer` WHERE `name`='$dname';";
        $result=mysql_query($sql);
        if (mysql_num_rows($result)==1)
        {
            $data=mysql_fetch_array($result);
            $x=$data['x']; $y=$data['y'];
            $weg=sqrt(pow($x-$dorfx,2)+pow($y-$dorfy,2));
            $user=$data['user'];
        }
        else
        	$error='Dorfname nicht oder mehrfach vorhanden.';
    }
    if ($dname=='' AND $x!='' AND $y!='')
    {
        $sql="SELECT * FROM `tr".$round_id."_dorfer` WHERE `x`='$x' AND `y`='$y';";
        $result=mysql_query($sql);
        if (mysql_num_rows($result)==1)
        {
            $data=mysql_fetch_array($result);
            $dname=$data['name'];
            $weg=sqrt(pow($x-$dorfx,2)+pow($y-$dorfy,2));
            $user=$data['user'];
        }
        else
        	$error='Dorf nicht gefunden.';
    }
    if ($weg==0) $error='Die Truppen sind schon in diesem Dorf';
	if (isset($error)) header('Location: build.php?id=39&s=2&error='.$error);

	$sql="SELECT `name` FROM `tr".$round_id."_user` WHERE `id`='$user';";
	$result=mysql_query($sql);
	$data=mysql_fetch_array($result);
	$user_n=$data['name'];

	$aktion_name=array(2=>'Unterstützung für',3=>'Angriff auf',4=>'Raubzug gegen',5=>'Ausspähen von');
	$speed_angabe=$speed;
	$speed_boni=(10+$gebeude[14]['highest'])*($speed/10);
	$dauer=$weg/$speed_boni*3600;

	if ($gebeude[14]['highest']>0) $speed_angabe.=' + '.round($speed/10*$gebeude[14]['highest'],1);
	$speed_angabe.=' Felder/Stunde';

	echo'<div id="lmid2"><h1>'.$aktion_name[$aktion].' '.$dname.'</h1>
		<form method="POST" action="a2b.php?do=sendtroops2">
		<p><table><tr><td width="11%">Ziel:</td>
		<td class="s7"><a href="karte.php?do=show&x='.$x.'&y='.$y.'">'.$dname.' ('.$x.'|'.$y.')</a></td></tr>
    	<tr><td width="11%">Spieler:</td><td class="s7"><a href="spieler.php?name='.$user_n.'">'.$user_n.'</a>
    	</td></tr><tr><td width="11%">Entfernung:</td><td class="s7">'.round($weg,2).' Felder</td></tr>
    	<tr><td width="11%">Geschwindigkeit:</td><td class="s7">'.$speed_angabe.'</td></tr>
    	</table></p><p>

    	<table  cellspacing="1" cellpadding="1" class="tbg"><tr class="cbg1">
    	<td width="21%"><b>&nbsp;'.$dorf_data['name'].'</b></td><td colspan="10"><b>'.$aktion_name[$aktion].
    	' '.$dname.'</b></td></tr>
    	<tr class="unit"><td>&nbsp;</td>';

    for ($i=1+$volk*10-10;$i<=$volk*10;$i++)
    	echo'<td><img src="img/un/u/'.$i.'.gif" title="'.$troops[$i]['name'].'"></td>';

    echo'</tr><tr><td>Einheiten</td>';
    for ($i=1;$i<=10;$i++)
    {
    	if ($troop[$i]==0) echo'<td class="c">0</td>';
    	else	echo'<td>'.$troop[$i].'</td>';
    }
    echo'</tr><tr class="cbg1"><td>Ankunft</td><td colspan="10"><table cellspacing="0" cellpadding="0" class="tbg">
    	<tr><td width="50%">in '.zeit_dauer($dauer).' Std.</td><td width="50%">um <span id=tp2>'.
    	date('H:i:s',time()+$dauer).'</span><span> Uhr</span></td></tr></table></td></tr></table></p>
    	<input type="hidden" name="aktion" value="'.$aktion.'">
    	<input type="hidden" name="x" value="'.$x.'"><input type="hidden" name="y" value="'.$y.'">
    	<input type="hidden" name="dauer" value="'.$dauer.'">';
    for ($i=1;$i<=10;$i++)
    	echo'<input type="hidden" name="t'.$i.'" value="'.$troop[$i].'">';

	echo'<p><input type="image" value="ok" border="0" name="s1" src="img/de/b/ok1.gif" width="50" height="20"
		onMousedown="btm1'."('s1','','img/de/b/ok2.gif',1)".'" onMouseOver="btm1'."('s1','','img/de/b/ok3.gif',1)".'"
		onMouseUp="btm0()" onMouseOut="btm0()"></input></form></p></div>';
}

if ($_GET['do']=='newvillage')
{
	$x=$_GET['x'];
	$y=$_GET['y'];

	$weg=sqrt(pow($x-$dorfx,2)+pow($y-$dorfy,2));
	$dauer=$weg/$troops[$volk*10]['speed']*3600;

	$expansion=split(':',$dorf_data['expansion']);
	$slots=dorf_slots($dorf_data);

    $sql="SELECT `value` FROM `tr".$round_id."_diverses` WHERE `id`='neue_dorfer';";
    $result=mysql_query($sql);
    $data=mysql_fetch_array($result);
    $neue_dorfer=split(':',$data['value']);

    $sql="SELECT `einwohner` FROM `tr".$round_id."_dorfer` WHERE `user`='$userid';";
    $result=mysql_query($sql);
    $anz_dorfer=mysql_num_rows($result);

    $kp=floor($spieler_data['kps']);

    $sql="SELECT `user` FROM `tr".$round_id."_truppen_move` WHERE `user`='$userid' AND `aktion`='1';";
    $result=mysql_query($sql);
    $anz_kolo=mysql_num_rows($result);

	echo'<div id="lmid2"><h1>Neue Siedlung gründen</h1>
		<p class="f10">Die Siedler werden nun aufbrechen um neues Land zu erschließen.<br> Für den Aufbau der neuen
		Siedlung benötigen sie jedoch noch jeweils 750 Einheiten Holz, Lehm, Eisen und Getreide.</p>
		<form method="post" action="build.php?id=39&do=newvillage">
		<table class="tbg" cellpadding="2" cellspacing="1">
		<tbody><tr class="cbg1"><td width="21%">&nbsp;</td>
		<td colspan="10">Gründung einer neuen Siedlung ('.$x.'|'.$y.')</td></tr>';

	echo'<tr><td align="center" width="100">Einheiten</td><td class="s7">3 Siedler</td></tr>
		<tr><td align="center" width="100">Dauer</td><td class="s7">'.zeit_dauer($dauer).'</td></tr>

		<tr class="cbg1"><td>Rohstoffe</td><td class="s7"><img class="res" src="img/un/r/1.gif">750 | '.
		'<img class="res" src="img/un/r/2.gif">750 | <img class="res" src="img/un/r/3.gif">750 | '.
		'<img class="res" src="img/un/r/4.gif">750</td></tr></tbody></table>';

	if ($lager[0]>=750 and $lager[1]>=750 and $lager[2]>=750 and $lager[3]>=750)
	{
		if ($expansion[0]<$slots)
		{
			if ($neue_dorfer[$anz_dorfer+$anz_kolo-1]*1000<=$kp)
			{
				echo'<input type=hidden name="x" value="'.$x.'"><input type=hidden name="y" value="'.$y.'">
					<p>';
				?>
				<input value="ok" name="s1" src="img/de/b/ok1.gif" onmousedown="btm1('s1','','img/de/b/ok2.gif',1)"
					onmouseover="btm1('s1','','img/de/b/ok3.gif',1)" onmouseup="btm0()" onmouseout="btm0()" border="0"
					height="20" type="image" width="50"></p>
				<?php
			}
			else
				echo'<p class="c">Zuwenig Kulturpunkte</p>';
		}
		else
			echo'<p class="c">Bauen Sie zuerst ihren Palast oder ihre Residenz aus</p>';
	}
	else
		echo'<p class="c">Zu wenig Rohstoffe</p>';

	echo'</form></div>';
}
?>

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