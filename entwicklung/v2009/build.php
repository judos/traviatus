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

$gid=$_GET['id'];
if (!isset($gid)) header('Location: dorf2.php');


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



?>


<!-- Top links -->
<?php top_links($username); ?>

<div id="lmidall">
<div id="lmidlc">

<!-- Links am linken Rand -->
<?php links(); ?>


<!-- Nachrichten Menü -->
<div id="lmid1"><div id="lmid2">

<?php

//Daten des Spielers
$sql="SELECT * FROM `tr".$round_id."_user` WHERE `id`='$userid';";
$result=mysql_query($sql);
$spieler_data=mysql_fetch_array($result);
$spieler_volk=$spieler_data['volk'];

$research=split(':',$spieler_data['research']);
$weapons=split(':',$spieler_data['weapons']);
$arms=split(':',$spieler_data['arms']);

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
//Gebäude Stufen des Dorfes
$geb2_stufe=split(':',$dorf_data['geb2']);
$geb2_typ=split(':',$dorf_data['geb2t']);

//Verteilung der Rohstoffgebäude
$sql="SELECT `geb` FROM `tr".$round_id."_land_typen` WHERE `typ`='".$land_data['typ']."';";
$result=mysql_query($sql);
$land_typ_data=mysql_fetch_array($result);
$land_geb=split(':',$land_typ_data['geb']);

//Lagerstand
$lager=load_lager($dorf_data);
//Gebäude infos
$gebeude=load_gebeude($dorf_data,$land_typ_data,$spieler_volk);
//Produktion
$produktion=load_produktion($dorf_data,$land_data);
//Lager grösse
$lager_grosse=lager_grosse($dorf_data);
//Truppeninformationen
$troops=load_troops($dorf_data,$spieler_data);
$troops_village=load_troops_in_village($userid,$dorfx,$dorfy,$troops);





//Gebäude ID und Grid ID und Stufe finden
if ($gid<19){ $id=$land_geb[$gid-1]; $stufe=$geb1_stufe[$gid-1]; }
else		{ $id=$geb2_typ[$gid-19]; $stufe=$geb2_stufe[$gid-19]; }




//Prüfen ob schon gebaut wird
$sql="SELECT * FROM `tr".$round_id."_others` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `typ`='10';";
$result=mysql_query($sql);
$build=1;
if (mysql_num_rows($result)==2)
{
    $data1=mysql_fetch_array($result);
    $data2=mysql_fetch_array($result);
    $build=0;
    if ($data1['id']==$gid) $build=-1;
    if ($data2['id']==$gid) $build=-1;
}
if (mysql_num_rows($result)==1)
{
    $data1=mysql_fetch_array($result);
    if ($data1['id']==$gid) $build=-1;
}



if ($id==12)
{				//Prüfen ob schon Waffen verbessert werden
	$forsch_wr[1]=0;
	$forsch_wr=array();
	$sql="SELECT * FROM `tr".$round_id."_others` WHERE `user`='$userid' AND `typ`='6';";
	$result=mysql_query($sql);
	for ($i=1;$i<=mysql_num_rows($result);$i++)
	{
		$data=mysql_fetch_array($result);
		if ($data['x']==$dorfx AND $data['y']==$dorfy) { $forsch_wr[1]=1; $forsch_wr_data[1]=$data; }
		else	$forsch_wr[1][$data['id']]=1;
	}
}
elseif ($id==13)
{				//Prüfen ob schon Rüstungen verbessert werden
	$forsch_wr[2]=0;
	$forsch_wr=array();
	$sql="SELECT * FROM `tr".$round_id."_others` WHERE `user`='$userid' AND `typ`='7';";
	$result=mysql_query($sql);
	for ($i=1;$i<=mysql_num_rows($result);$i++)
	{
		$data=mysql_fetch_array($result);
		if ($data['x']==$dorfx AND $data['y']==$dorfy) { $forsch_wr[2]=1; $forsch_wr_data[2]=$data; }
		else	$forsch_wr[2][$data['id']]=1;
	}
}
elseif ($id==15)
{				//Prüfen ob schon ein Gebäude abgerissen wird
	$crash=0;
	$sql="SELECT * FROM `tr".$round_id."_others` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `typ`='9';";
	$result=mysql_query($sql);
	if (mysql_num_rows($result)>0)
	{
		$crash=1;
		$crash_data=mysql_fetch_array($result);
	}
}
elseif ($id==19)
{    			//Prüfen ob schon Truppen rekrutiert wird
    $recrut=0;
    $sql="SELECT * FROM `tr".$round_id."_others` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `typ`='1' ORDER BY `zeit` ASC;";
    $result=mysql_query($sql);
    if (mysql_num_rows($result)>0)
    {
        $recrut=1;
        $recrut_data['anzahl']=mysql_num_rows($result);
        for ($i=1;$i<=$recrut_data['anzahl'];$i++)
            $recrut_data[$i]=mysql_fetch_array($result);
    }
}
elseif ($id==20)
{    			//Prüfen ob schon Kavallerie rekrutiert wird
    $recrut2=0;
    $sql="SELECT * FROM `tr".$round_id."_others` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `typ`='2' ORDER BY `zeit` ASC;";
    $result=mysql_query($sql);
    if (mysql_num_rows($result)>0)
    {
        $recrut2=1;
        $recrut2_data['anzahl']=mysql_num_rows($result);
        for ($i=1;$i<=$recrut2_data['anzahl'];$i++)
            $recrut2_data[$i]=mysql_fetch_array($result);
    }
}
elseif ($id==21)
{    			//Prüfen ob schon Artillerie rekrutiert wird
    $recrut3=0;
    $sql="SELECT * FROM `tr".$round_id."_others` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `typ`='3' ORDER BY `zeit` ASC;";
    $result=mysql_query($sql);
    if (mysql_num_rows($result)>0)
    {
        $recrut3=1;
        $recrut3_data['anzahl']=mysql_num_rows($result);
        for ($i=1;$i<=$recrut3_data['anzahl'];$i++)
            $recrut3_data[$i]=mysql_fetch_array($result);
    }
}
elseif ($id==22)
{				//Prüfen ob schon geforscht wird
	$forsch=0;
	$sql="SELECT * FROM `tr".$round_id."_others` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `typ`='5';";
	$result=mysql_query($sql);
	if (mysql_num_rows($result)>0) { $forsch=1; $forsch_data=mysql_fetch_array($result); }
}
elseif ($id==24)
{    			//Prüfen ob schon gefestet wird
    $festen=0;
    $sql="SELECT * FROM `tr".$round_id."_others` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `typ`='8';";
    $result=mysql_query($sql);
    if (mysql_num_rows($result)>0)
    {
        $festen=1;
        $festen_data=mysql_fetch_array($result);
    }
}
elseif ($id==25 OR $id==26)
{    			//Prüfen ob schon Diplomaten rekrutiert wird
    $recrut4=0;
    $sql="SELECT * FROM `tr".$round_id."_others` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `typ`='4' ORDER BY `zeit` ASC;";
    $result=mysql_query($sql);
    if (mysql_num_rows($result)>0)
    {
        $recrut4=1;
        $recrut4_data['anzahl']=mysql_num_rows($result);
        for ($i=1;$i<=$recrut4_data['anzahl'];$i++)
            $recrut4_data[$i]=mysql_fetch_array($result);
    }
}





//Neues Dorf besiedeln
if ($_GET['do']=='newvillage')
{
	if ($_POST['s1']=='ok')
	{
		if ($lager[0]>=750 AND $lager[1]>=750 AND $lager[2]>=750 AND $lager[3]>=750)
		{
			if ($troops_village['own'][$spieler_volk*10]>=3)
			{
				$x=$_POST['x'];
				$y=$_POST['y'];
				$weg=sqrt(pow($x-$dorfx,2)+pow($y-$dorfy,2));
				$dauer=$weg/$troops[$spieler_volk*10]['speed']*3600;
				$time=date('Y-m-d H:i:s',time()+$dauer);

				//Truppe losschicken
				$sql="INSERT INTO `tr".$round_id."_truppen_move`
					(`user`,`start_x`,`start_y`,`ziel_x`,`ziel_y`,`start_zeit`,`ziel_zeit`,`aktion`,`truppen`)
					VALUES ('$userid','$dorfx','$dorfy','$x','$y',NOW(),'$time','1','0:0:0:0:0:0:0:0:0:3');";
				$result=mysql_query($sql);

				//Rohstoff abziehen
				for ($i=0;$i<=4;$i++)
					$lager[$i]-=750;
				$lager_string=implode(':',$lager);
				$sql="UPDATE `tr".$round_id."_dorfer` SET `lager`='$lager_string' WHERE `x`='$dorfx' AND `y`='$dorfy';";
				$result=mysql_query($sql);

				//Siedler abziehen
				$troops_village['own'][$spieler_volk*10]-=3;
				$truppen_s=implode(':',$troops_village['own']);
				$sql="UPDATE `tr".$round_id."_truppen` SET `troops`='$truppen_s' WHERE `x`='$dorfx' AND `y`='$dorfy'
					AND `user`='$userid';";
				$result=mysql_query($sql);
			}
		}
	}
}


//Waffen oder Rüstungen verbessern
if ($_GET['do']=='res1' OR $_GET['do']=='res2')
{
	$wr=substr($_GET['do'],-1);
    if ($wr==1) $stufen=$weapons;
    if ($wr==2) $stufen=$arms;
    if ($stufe>$stufen[$i-($spieler_volk-1)*10])
    {
        if ($forsch_wr[$wr]==0)
        {
        	$i=$_GET['tid'];
            if ($forsch_wr[$wr][$i]!=1)
            {
                if ($lager[0]>=$troops[$i]['wr'.$wr][0] AND $lager[1]>=$troops[$i]['wr'.$wr][1] AND
                    $lager[2]>=$troops[$i]['wr'.$wr][2] AND $lager[3]>=$troops[$i]['wr'.$wr][3])
                {
                	$fertig=date('Y-m-d H:i:s',$troops[$i]['wr'.$wr]['zeit']+time());
                	$sql="INSERT INTO `tr".$round_id."_others` (`x`,`y`,`typ`,`id`,`zeit`,`user`)
                		VALUES ('$dorfx','$dorfy','".($wr+5)."','$i','$fertig','$userid');";
                	$result=mysql_query($sql);
                	$forsch_wr[$wr]=1;
                	$forsch_wr_data[$wr]=array('zeit'=>$fertig,'id'=>$i);
                }
            }
        }
    }
}

//Gebäude abriss abbrechen
if ($_GET['do']=='delcrash')
{
	$sql="DELETE FROM `tr".$round_id."_others` WHERE `typ`='9' AND `x`='$dorfx' AND `y`='$dorfy';";
	$result=mysql_query($sql);
}

//Gebäude abreissen
if ($_GET['do']=='crash')
{
	if ($build>-1)
	{
		$crash_id=$_POST['abriss'];
		$crash_stufe=$geb2_stufe[$crash_id-19];
		$zeit=date('Y-m-d H:i:s',time()+$gebeude[$crash_id]['bauzeit'][$crash_stufe-1]/3);
		$sql="INSERT INTO `tr".$round_id."_others` (`x`,`y`,`typ`,`id`,`zeit`)
			VALUES ('$dorfx','$dorfy','9','$crash_id','$zeit');";
		$result=mysql_query($sql);
		$crash=1;
		$crash_data=array('id'=>$crash_id,'zeit'=>$zeit);
	}
}

//Fest starten
if ($_GET['do']=='fest')
{
	if ($festen==0)
	{
        $x=$_GET['x'];
        $cost[1]=array(0=>6400,1=>6650,2=>5940,3=>1340);
        $cost[2]=array(0=>29700,1=>33250,2=>32000,3=>6700);

        $zeit[1]=round((3600*24)*(100-($gebeude[24]['highest']-1)/19*50)/100);
        $zeit[2]=round((3600*48)*(100-($gebeude[24]['highest']-1)/19*50)/100);

        if ($lager[0]>=$cost[$x][0] AND $lager[1]>=$cost[$x][1] AND $lager[2]>=$cost[$x][2] AND $lager[3]>=$cost[$x][3])
        {
            if ($x==1 OR ($x==2 AND $gebeude[24]['highest']>9))
            {
            	$kp=500;
            	if ($x==2) $kp=2000;
            	$kp*=(1+$gebeude[35]['highest']/10);

                //Rohstoffe abziehen
                for ($i=0;$i<=3;$i++)
                    $lager[$i]-=$cost[$x][$i];
                $lager_string=$lager[0].':'.$lager[1].':'.$lager[2].':'.$lager[3];
                $sql="UPDATE `tr".$round_id."_dorfer` SET `lager`='$lager_string' WHERE `x`='$dorfx' AND `y`='$dorfy';";
                $result=mysql_query($sql);

                //Fest speichern
                $sql="INSERT INTO `tr".$round_id."_others` (`x`,`y`,`typ`,`id`,`zeit`,`dauer`)
                    VALUES ('$dorfx','$dorfy','8','$x','".date('Y-m-d H:i:s',time()+$zeit[$x])."','$kp');";
                $result=mysql_query($sql);

                $festen=1;
                $festen_data=array('id'=>$x,'zeit'=>date('Y-m-d H:i:s',time()+$zeit[$x]));
            }
        }
	}
}


//Angebot annehmen
if ($_GET['do']=='buyoffer')
{
	$ok=0;
	$sql="SELECT * FROM `tr".$round_id."_angebote` WHERE `ursprung`='".$_GET['u']."' AND `angebot`='".$_GET['a']."' AND
		`nachfrage`='".$_GET['n']."' AND `ally`='".$_GET['ally']."' AND `maxzeit`='".$_GET['max']."' LIMIT 1;";
	$result=mysql_query($sql);
	if (mysql_num_rows($result)>0)
	{
		$data_ang=mysql_fetch_array($result);
		$ur=split(':',$data_ang['ursprung']);
		$ux=$ur[0];$uy=$ur[1];

		$nachfrage=split(':',$data_ang['nachfrage']);
		$angebot=split(':',$data_ang['angebot']);

		if ($lager[$nachfrage[0]-1]>=$nachfrage[1])
		{
            //Händler berechnen
            $handler=$gebeude[17]['highest']*2;
            $sql="SELECT sum(`handler`) FROM `tr".$round_id."_handler` WHERE `ursprung`='$dorfx:$dorfy';";
            $result=mysql_query($sql);
            $data=mysql_fetch_array($result);
            $handler_gebraucht=$data['sum(`handler`)'];
            $sql="SELECT sum(`handler`) FROM `tr".$round_id."_angebote` WHERE `ursprung`='$dorfx:$dorfy';";
            $result=mysql_query($sql);
            $data=mysql_fetch_array($result);
            $handler_gebraucht2=$data['sum(`handler`)'];

            $anz_ver_handler=$handler-$handler_gebraucht-$handler_gebraucht2;
            $tragen=500;
            if ($spieler_volk==2) $tragen=1000;
            if ($spieler_volk==3) $tragen=750;
            $tragen=$tragen+50*$gebeude[28]['highest'];

            if (ceil($nachfrage[1]/$tragen)<=$anz_ver_handler)
            {
                //Geschwindigkeiten der Händler bestimmen
                $sql="SELECT `name`,`volk` FROM `tr".$round_id."_user` WHERE `name`='$username' OR `name`='".$data_ang['user']."';";
                $result=mysql_query($sql);
                $speed_volk=array(1=>16,2=>12,3=>24);
                for ($i=1;$i<=mysql_num_rows($result);$i++)
                {
                    $data=mysql_fetch_array($result);
                    $spieler_hspeed[$data['name']]=$speed_volk[$data['volk']];
                }
                //Dauer berechnen
                $ort=split(':',$data_ang['ursprung']);
                $weg=sqrt(pow($ort[0]-$dorfx,2)+pow($ort[1]-$dorfy,2));
                $eigene_dauer=$weg/$spieler_hspeed[$username]*3600;
                $andere_dauer=$weg/$spieler_hspeed[$data_ang['user']]*3600;

                //Angebot löschen
                $sql="DELETE FROM `tr".$round_id."_angebote` WHERE `ursprung`='".$_GET['u']."' AND `angebot`='".$_GET['a']."' AND
                    `nachfrage`='".$_GET['n']."' AND `ally`='".$_GET['ally']."' AND `maxzeit`='".$_GET['max']."'
                    LIMIT 1;";
                $result=mysql_query($sql);

                //Rohstoff ladungen berechnen
                $schicken=array(0,0,0,0);
                $erhalten=array(0,0,0,0);
				$schicken[$nachfrage[0]-1]=$nachfrage[1]; $schicken=implode(':',$schicken);
				$erhalten[$angebot[0]-1]=$angebot[1]; $erhalten=implode(':',$erhalten);

				//Eigene Rohstoffe abziehen
				$lager[$nachfrage[0]-1]-=$nachfrage[1];
                $lager_string=$lager[0].':'.$lager[1].':'.$lager[2].':'.$lager[3];
                $sql="UPDATE `tr".$round_id."_dorfer` SET `lager`='$lager_string' WHERE `x`='$dorfx' AND `y`='$dorfy';";
                $result=mysql_query($sql);

                //eigener Händler schicken
                $sql="INSERT INTO `tr".$round_id."_handler` (`user`,`ursprung`,`von`,`nach`,`start`,`ziel`,`handler`,`ress`)
                    VALUES ('$username','$dorfx:$dorfy','$dorfx:$dorfy','$ux:$uy',
                            NOW(),'".date('Y-m-d H:i:s',time()+$eigene_dauer)."','".
                            ceil($nachfrage[1]/$tragen)."','$schicken');";
                $result=mysql_query($sql);

                //anderer Händler schicken
                $sql="INSERT INTO `tr".$round_id."_handler` (`user`,`ursprung`,`von`,`nach`,`start`,`ziel`,`handler`,`ress`)
                    VALUES ('".$data_ang['user']."','$ux:$uy','$ux:$uy','$dorfx:$dorfy',
                            NOW(),'".date('Y-m-d H:i:s',time()+$andere_dauer)."','".$data_ang['handler']."',
                            '$erhalten');";
                $result=mysql_query($sql);
				$ok=1;
                $_GET['s']=4;
                $angenommen['user']=$data_ang['user'];
                $angenommen['r1']=$angebot;
                $angenommen['r2']=$nachfrage;
            }
        }
	}
	if ($ok==0) $_GET['s']=2;
}



//Angebot löschen
if ($_GET['do']=='deloffer')
{
	$angebot=split(':',$_GET['a']);
	$lager[$angebot[0]-1]+=$angebot[1];

    $lager_string=$lager[0].':'.$lager[1].':'.$lager[2].':'.$lager[3];

    $sql="UPDATE `tr".$round_id."_dorfer` SET `lager`='$lager_string' WHERE `x`='$dorfx' AND `y`='$dorfy';";
    $result=mysql_query($sql);

	$sql="DELETE FROM `tr".$round_id."_angebote` WHERE `ursprung`='$dorfx:$dorfy' AND `angebot`='".$_GET['a']."' AND
		`nachfrage`='".$_GET['n']."' AND `ally`='".$_GET['ally']."' AND `maxzeit`='".$_GET['md']."' LIMIT 1;";
	$result=mysql_query($sql);
}


//Neues Angebot
if ($_GET['do']=='newoffer')
{
	if ($r1>0 AND $r2>0)
	{
		if ($r1/$r2>=0.5 AND $r1/$r2<=2)
		{
			if ($typ1!=$typ2)
			{
				if ($r1<=$lager[$typ1-1])
				{
					if ($r1/$tragen<=$anz_ver_handler)
					{
						$h=ceil($r1/$tragen);
						$max=10;
						if ($d1==1) $max=$d2;
						$sql="INSERT INTO `tr".$round_id."_angebote` (`user`,`ursprung`,`angebot`,`nachfrage`,`handler`,`maxzeit`,
							`ally`) VALUES ('$username','$dorfx:$dorfy','$typ1:$r1','$typ2:$r2','$h','$max','$ally');";
						$result=mysql_query($sql);

						$lager[$typ1-1]-=$r1;
						$lager_string=$lager[0].':'.$lager[1].':'.$lager[2].':'.$lager[3];

						$sql="UPDATE `tr".$round_id."_dorfer` SET `lager`='$lager_string' WHERE `x`='$dorfx' AND `y`='$dorfy';";
						$result=mysql_query($sql);
					}
					else $error="Zu wenig Händler";
				}
				else $error="Zu wenig Rohstoffe";
			}
			else $error="Ungültiges Angebot";
		}
		else $error="maximal 2:1";
	}
}

//Rohstoffe versenden
if ($_GET['do']=='sendgoods')
{
	if ($r1=='') $r1=0;
	if ($r2=='') $r2=0;
	if ($r3=='') $r3=0;
	if ($r4=='') $r4=0;
	$sql="INSERT INTO `tr".$round_id."_handler` (`user`,`ursprung`,`von`,`nach`,`start`,`ziel`,`handler`,`ress`)
		VALUES ('$username','$dorfx:$dorfy','$dorfx:$dorfy','$zielx:$ziely','".date('Y-m-d H:i:s',time())."',
		'".date('Y-m-d H:i:s',time()+$dauer)."','".$need_h."','$r1:$r2:$r3:$r4');";
	$result=mysql_query($sql);

	$lager[0]-=$r1;	$lager[1]-=$r2;
	$lager[2]-=$r3;	$lager[3]-=$r4;

	$sql="UPDATE `tr".$round_id."_dorfer` SET `lager`='".$lager[0].":".$lager[1].":".$lager[2].":".
		$lager[3]."' WHERE `x`='$dorfx' AND `y`='$dorfy';";
	$result=mysql_query($sql);

	$done='Rohstoffe wurden verschickt';
}



//Forschen in der Akademie
if ($_GET['do']=='research')
{
	$rid=$_GET['rid'];
	if ($lager[0]>=$troops[$rid]['forsch_holz'] AND $lager[1]>=$troops[$rid]['forsch_lehm'] AND
		$lager[2]>=$troops[$rid]['forsch_eisen'] AND $lager[3]>=$troops[$rid]['forsch_getreide'] AND $forsch==0)
	{
		$lager[0]-=$troops[$rid]['forsch_holz'];
		$lager[1]-=$troops[$rid]['forsch_lehm'];
		$lager[2]-=$troops[$rid]['forsch_eisen'];
		$lager[3]-=$troops[$rid]['forsch_getreide'];
		$lagerstring=implode(':',$lager);
		$sql="UPDATE `tr".$round_id."_dorfer` SET `lager`='$lagerstring' WHERE `x`='$dorfx' AND `y`='$dorfy';";
		$result=mysql_query($sql);
		$zeit=date('Y-m-d H:i:s',time()+$troops[$rid]['forsch_zeit']);
		$sql="INSERT INTO `tr".$round_id."_others` (`x`,`y`,`typ`,`id`,`zeit`) VALUES ('$dorfx','$dorfy','5','$rid','".
			$zeit."');";
		$result=mysql_query($sql);

		$forsch=1;
		$forsch_data['id']=$rid;
		$forsch_data['zeit']=$zeit;
	}
}

//Truppen rekrutieren
if ($_GET['do']=='recrut')
{
	$tid=$_POST['re'];
	if (isset($tid))
	{
        $anzahl=$_POST['t'.$tid];
		if ($anzahl>0)
		{
            if ($lager[0]>=$troops[$tid]['kosten_holz']*$anzahl AND $lager[1]>=$troops[$tid]['kosten_lehm']
                AND $lager[2]>=$troops[$tid]['kosten_eisen']*$anzahl AND $lager[3]>=$troops[$tid]['kosten_getreide'])
            {
                $lager[0]-=$troops[$tid]['kosten_holz']*$anzahl;
                $lager[1]-=$troops[$tid]['kosten_lehm']*$anzahl;
                $lager[2]-=$troops[$tid]['kosten_eisen']*$anzahl;
                $lager[3]-=$troops[$tid]['kosten_getreide']*$anzahl;
                $lagerstring=implode(':',$lager);
                $sql="UPDATE `tr".$round_id."_dorfer` SET `lager`='$lagerstring' WHERE `x`='$dorfx' AND `y`='$dorfy';";
                $result=mysql_query($sql);

                $akt=$recrut_data['anzahl'];

                if ($recrut==0) $zeit=date('Y-m-d H:i:s',time()+$troops[$tid]['bauzeit']);
                else    $zeit=date('Y-m-d H:i:s',strtotime($recrut_data[$akt]['zeit'])+
                                $recrut_data[$akt]['dauer']*$recrut_data[$akt]['anzahl']);
                $sql="INSERT INTO `tr".$round_id."_others` (`x`,`y`,`typ`,`id`,`zeit`,`anzahl`,`dauer`)
                    VALUES ('$dorfx','$dorfy','1','$tid','".$zeit."','$anzahl','".$troops[$tid]['bauzeit']."');";
                $result=mysql_query($sql);

                $recrut_data['anzahl']++;
                $recrut=1;
                $recrut_data[$akt+1]['id']=$tid;
                $recrut_data[$akt+1]['zeit']=$zeit;
                $recrut_data[$akt+1]['anzahl']=$anzahl;
                $recrut_data[$akt+1]['dauer']=$troops[$tid]['bauzeit'];
            }
        }
	}
}

//Kavallerie rekrutieren
if ($_GET['do']=='recrut2')
{
	$tid=$_POST['re'];
	if (isset($tid))
	{
        $anzahl=$_POST['t'.$tid];
		if ($anzahl>0)
		{
            if ($lager[0]>=$troops[$tid]['kosten_holz']*$anzahl AND $lager[1]>=$troops[$tid]['kosten_lehm']
                AND $lager[2]>=$troops[$tid]['kosten_eisen']*$anzahl AND $lager[3]>=$troops[$tid]['kosten_getreide'])
            {
                $lager[0]-=$troops[$tid]['kosten_holz']*$anzahl;
                $lager[1]-=$troops[$tid]['kosten_lehm']*$anzahl;
                $lager[2]-=$troops[$tid]['kosten_eisen']*$anzahl;
                $lager[3]-=$troops[$tid]['kosten_getreide']*$anzahl;
                $lagerstring=implode(':',$lager);
                $sql="UPDATE `tr".$round_id."_dorfer` SET `lager`='$lagerstring' WHERE `x`='$dorfx' AND `y`='$dorfy';";
                $result=mysql_query($sql);

                $akt=$recrut2_data['anzahl'];

                if ($recrut2==0) $zeit=date('Y-m-d H:i:s',time()+$troops[$tid]['bauzeit']);
                else    $zeit=date('Y-m-d H:i:s',strtotime($recrut2_data[$akt]['zeit'])+
                                $recrut2_data[$akt]['dauer']*$recrut2_data[$akt]['anzahl']);
                $sql="INSERT INTO `tr".$round_id."_others` (`x`,`y`,`typ`,`id`,`zeit`,`anzahl`,`dauer`)
                    VALUES ('$dorfx','$dorfy','2','$tid','".$zeit."','$anzahl','".$troops[$tid]['bauzeit']."');";
                $result=mysql_query($sql);

                $recrut2_data['anzahl']++;
                $recrut2=1;
                $recrut2_data[$akt+1]['id']=$tid;
                $recrut2_data[$akt+1]['zeit']=$zeit;
                $recrut2_data[$akt+1]['anzahl']=$anzahl;
                $recrut2_data[$akt+1]['dauer']=$troops[$tid]['bauzeit'];
            }
        }
	}
}


//Artillerie rekrutieren
if ($_GET['do']=='recrut3')
{
	$tid=$_POST['re'];
	if (isset($tid))
	{
        $anzahl=$_POST['t'.$tid];
		if ($anzahl>0)
		{
            if ($lager[0]>=$troops[$tid]['kosten_holz']*$anzahl AND $lager[1]>=$troops[$tid]['kosten_lehm']
                AND $lager[2]>=$troops[$tid]['kosten_eisen']*$anzahl AND $lager[3]>=$troops[$tid]['kosten_getreide'])
            {
                $lager[0]-=$troops[$tid]['kosten_holz']*$anzahl;
                $lager[1]-=$troops[$tid]['kosten_lehm']*$anzahl;
                $lager[2]-=$troops[$tid]['kosten_eisen']*$anzahl;
                $lager[3]-=$troops[$tid]['kosten_getreide']*$anzahl;
                $lagerstring=implode(':',$lager);
                $sql="UPDATE `tr".$round_id."_dorfer` SET `lager`='$lagerstring' WHERE `x`='$dorfx' AND `y`='$dorfy';";
                $result=mysql_query($sql);

                $akt=$recrut3_data['anzahl'];

                if ($recrut3==0) $zeit=date('Y-m-d H:i:s',time()+$troops[$tid]['bauzeit']);
                else    $zeit=date('Y-m-d H:i:s',strtotime($recrut3_data[$akt]['zeit'])+
                                $recrut3_data[$akt]['dauer']*$recrut3_data[$akt]['anzahl']);
                $sql="INSERT INTO `tr".$round_id."_others` (`x`,`y`,`typ`,`id`,`zeit`,`anzahl`,`dauer`)
                    VALUES ('$dorfx','$dorfy','3','$tid','".$zeit."','$anzahl','".$troops[$tid]['bauzeit']."');";
                $result=mysql_query($sql);

                $recrut3_data['anzahl']++;
                $recrut3=1;
                $recrut3_data[$akt+1]['id']=$tid;
                $recrut3_data[$akt+1]['zeit']=$zeit;
                $recrut3_data[$akt+1]['anzahl']=$anzahl;
                $recrut3_data[$akt+1]['dauer']=$troops[$tid]['bauzeit'];
            }
        }
	}
}

//Diplomaten rekrutieren
if ($_GET['do']=='recrut4')
{
	$tid=$_POST['re'];
	if (isset($tid))
	{
        $anzahl=$_POST['t'.$tid];
		if ($anzahl>0)
		{
            if ($lager[0]>=$troops[$tid]['kosten_holz']*$anzahl AND $lager[1]>=$troops[$tid]['kosten_lehm']
                AND $lager[2]>=$troops[$tid]['kosten_eisen']*$anzahl AND $lager[3]>=$troops[$tid]['kosten_getreide'])
            {
                $lager[0]-=$troops[$tid]['kosten_holz']*$anzahl;
                $lager[1]-=$troops[$tid]['kosten_lehm']*$anzahl;
                $lager[2]-=$troops[$tid]['kosten_eisen']*$anzahl;
                $lager[3]-=$troops[$tid]['kosten_getreide']*$anzahl;
                $lagerstring=implode(':',$lager);
                $sql="UPDATE `tr".$round_id."_dorfer` SET `lager`='$lagerstring' WHERE `x`='$dorfx' AND `y`='$dorfy';";
                $result=mysql_query($sql);

                $akt=$recrut4_data['anzahl'];

                if ($recrut4==0) $zeit=date('Y-m-d H:i:s',time()+$troops[$tid]['bauzeit']);
                else    $zeit=date('Y-m-d H:i:s',strtotime($recrut4_data[$akt]['zeit'])+
                                $recrut4_data[$akt]['dauer']*$recrut4_data[$akt]['anzahl']);
                $sql="INSERT INTO `tr".$round_id."_others` (`x`,`y`,`typ`,`id`,`zeit`,`anzahl`,`dauer`)
                    VALUES ('$dorfx','$dorfy','4','$tid','".$zeit."','$anzahl','".$troops[$tid]['bauzeit']."');";
                $result=mysql_query($sql);

                $recrut4_data['anzahl']++;
                $recrut4=1;
                $recrut4_data[$akt+1]['id']=$tid;
                $recrut4_data[$akt+1]['zeit']=$zeit;
                $recrut4_data[$akt+1]['anzahl']=$anzahl;
                $recrut4_data[$akt+1]['dauer']=$troops[$tid]['bauzeit'];
            }
        }
	}
}



//Neues Gebäude im Dorfzentrum bauen
if ($gid>18 AND $id==0 AND $stufe==0)
{
	echo'<h1>Neues Gebäude errichten</h1><br>';
	$anz=0;
	$max=40;
	if($gid>38 AND $gid<41) $max=1;

	for ($i=1;$i<=$max;$i++)
	{
		$allowed=0;
		if ($gid==39) { $i=16; $allowed=1; }
		if ($gid==40) { $i=$spieler_data['volk']+30; $allowed=1; }

		if ($gebeude[$i]['ok']==1 OR $allowed==1)
		{
			$anz=1;
    		$kosten[0]=$gebeude[$i]['kosten_holz'][1];
    		$kosten[1]=$gebeude[$i]['kosten_lehm'][1];
    		$kosten[2]=$gebeude[$i]['kosten_eisen'][1];
    		$kosten[3]=$gebeude[$i]['kosten_getreide'][1];
			$nr='';
			if ($gebeude[$i]['anzahl']>0) $nr=($gebeude[$i]['anzahl']+1).'. ';

    		echo'<h2>'.$nr.$gebeude[$i]['name'].'</h2>'.
				'<p class="f10">'.$gebeude[$i]['besch'].'</p>'.
				'<p></p><table class="f10"><tbody><tr><td>'.
				'<img class="res" src="img/un/r/1.gif">'.$kosten[0].' | '.
				'<img class="res" src="img/un/r/2.gif">'.$kosten[1].' | '.
				'<img class="res" src="img/un/r/3.gif">'.$kosten[2].' | '.
				'<img class="res" src="img/un/r/4.gif">'.$kosten[3].' | '.
				'<img class="res" src="img/un/r/5.gif">'.$gebeude[$i]['arbeiter'].' | '.
				'<img class="clock" src="img/un/a/clock.gif"> '.zeit_dauer($gebeude[$i]['bauzeit'][1]).
				'</td></tr></tbody></table>';
			if ($lager[0]>=$kosten[0] AND $lager[1]>=$kosten[1] AND $lager[2]>=$kosten[2] AND $lager[3]>=$kosten[3])
			{	//Genug Rohstoffe
				if ($produktion[3]-$gebeude[$i]['arbeiter']>2)
				{	//Kein Nahrungsmangel
					if ($build==1)
						echo'<a href="dorf2.php?id='.$i.'&gid='.$gid.'">Gebäude bauen</a>';
					else
						echo'<span class="c">Es wird bereits gebaut</span>';
				}
				else
					echo'<span class="c">Nahrungsmangel: Erst eine Getreidefarm ausbauen</span>';
			}
			else	echo'<span class="c">Zu wenig Rohstoffe</span>';
		}
	}
	if ($anz==0)
	{
		echo'<p class="c">Zur Zeit können keine neuen Gebäude errichtet werden.
<br><br>Viele Gebäude benötigen bestimmte Voraussetzungen,<br> um gebaut werden zu können. Die Gebäudevoraussetzungen<br> kannst du in der Anleitung nachlesen.</p>';
	}
}


//Gebäude ausbauen
if (($gid<19) OR ($gid>18 AND $id>0))
{
    $nachste_stufe=$stufe+1;
    if ($build==-1) $nachste_stufe++;
    if ($gebeude[$id]['stufen']<$nachste_stufe) $nachste_stufe=$gebeude[$id]['stufen'];

    $kosten[0]=$gebeude[$id]['kosten_holz'][$nachste_stufe];
    $kosten[1]=$gebeude[$id]['kosten_lehm'][$nachste_stufe];
    $kosten[2]=$gebeude[$id]['kosten_eisen'][$nachste_stufe];
    $kosten[3]=$gebeude[$id]['kosten_getreide'][$nachste_stufe];

    //Name Stufe Beschreibung
    echo'<h1><b>'.$gebeude[$id]['name'].' Stufe '.$stufe.'</b></h1>
    <p class="f10">'.$gebeude[$id]['besch'].'</p>';

	if ($stufe>0 OR ($stufe==0 AND $id<=4))
	{
        //Informationen zu Nutzen des Gebäudes
        if ($id<=4) //Rohstoffgebäude
        {
            echo'<table class="f10" cellpadding="0" cellspacing="4" width="100%">
                <tbody><tr><td width="200">Aktuelle Produktion:</td>
                <td><b>'.$produktion['allgemein'][$stufe].'</b> pro Stunde</td></tr>

                <tr><td width="200">Produktion bei Stufe '.$nachste_stufe.':</td>
                <td><b>'.$produktion['allgemein'][$nachste_stufe].'</b> pro Stunde</td></tr>
                </tbody></table>';
        }
        elseif ($id>=5 AND $id<=9)	//Erweiterungen Sägewerk etc.
        {
        	echo'<table class="f10" cellpadding="0" cellspacing="4" width="100%">
				<tbody><tr><td width="250">Aktuelle Produktionssteigerung:</td>
				<td><b>'.(5*$stufe).'</b> Prozent</td></tr><tr>
				<td width="250">Steigerung bei Stufe '.$nachste_stufe.':</td>
				<td><b>'.(5*$nachste_stufe).'</b> Prozent</td></tr></tbody></table>';
        }
        elseif ($id==10 OR $id==11 OR $id==38 OR $id==39)	//Lagergebäude
        {
        	$f=1;
        	if ($id>37) $f=3;
        	if ($id==10) $ress='Rohstoffeinheiten';
        	if ($id==11) $ress='Einheiten Getreide';
        	echo'<table class="f10" cellpadding="0" cellspacing="4" width="100%">
				<tbody><tr><td width="250">Aktuelle Speicherkapazität:</td>
				<td><b>'.($lager_grosse['allgemein'][$stufe-1]*100*$f).'</b> '.$ress.'</td>
				</tr><tr><td width="250">Speicherkapazität bei Stufe '.$nachste_stufe.':</td>
				<td><b>'.($lager_grosse['allgemein'][$nachste_stufe-1]*100*$f).'</b> '.$ress.'</td>
				</tr></tbody></table>';
        }
        elseif ($id==12 OR $id==13)	//Waffenschmid, Rüstungsschmid
        {
        	if ($id==12) $stufen=$weapons;
        	if ($id==13) $stufen=$arms;
        	$wr=$id-11;
			echo'<p></p><table class="tbg" cellpadding="2" cellspacing="1">
				<tbody><tr class="cbg1"><td>'.$gebeude[$id]['name'].'</td><td>Aktion</td></tr>';
			for ($i=1;$i<=30;$i++)
			{
				if ($troops[$i]['ok2']==1 AND $troops[$i]['typ']<4)
				{
					echo'<tr><td><table class="f10" cellpadding="0" cellspacing="2" width="100%"><tbody><tr>
					<td rowspan="2" class="s7" valign="top" width="6%"><img class="unit" src="img/un/u/'.$i.'.gif"></td>
					<td class="s7"><div><a href="#" onclick="Popup(1,'.$i.'); return false;">'.$troops[$i]['name'].'</a>
					<span class="f8">(Stufe '.$stufen[$i-($spieler_volk-1)*10-1].')</span></div></td></tr>
					<tr><td class="s7" nowrap="nowrap"><img src="img/un/a/x.gif" height="15" width="1">
					<img class="res" src="img/un/r/1.gif">'.$troops[$i]['wr'.$wr][0];
					for ($j=1;$j<=3;$j++)
						echo'|<img class="res" src="img/un/r/'.($j+1).'.gif">'.$troops[$i]['wr'.$wr][$j];
					echo' <img class="clock" src="img/un/a/clock.gif"> '.
						zeit_dauer($troops[$i]['wr'.$wr]['zeit']).'</td></tr></tbody></table></td><td width="28%">';
					if ($stufe>$stufen[$i-($spieler_volk-1)*10])
					{
						if ($forsch_wr[$wr]==0)
						{
							if ($forsch_wr[$wr][$i]!=1)
							{
								if ($lager[0]>=$troops[$i]['wr'.$wr][0] AND $lager[1]>=$troops[$i]['wr'.$wr][1] AND
									$lager[2]>=$troops[$i]['wr'.$wr][2] AND $lager[3]>=$troops[$i]['wr'.$wr][3])
									echo'<a href="build.php?id='.$gid.'&do=res'.$wr.'&tid='.$i.'">verbessern</a>';
								else
									echo'<div class="c">Zu wenig<br>Rohstoffe</div>';
							}
							else echo'<div class="c">Es wird<br>geforscht</div>';
						}
						else echo'<div class="c">Es wird<br>geforscht</div>';
					}
					else
					{
						if ($stufe<20) echo'<div class="c">'.$gebeude[$id]['name'].'<br>ausbauen</div>';
						else echo'<div class="c">Vollständig<br>erforscht</div>';
					}
					echo'</td></tr>';
				}
			}
			echo'</tbody></table>';
			if  ($forsch_wr[$wr]==1)
			{
				$dauer=strtotime($forsch_wr_data[$wr]['zeit'])-time();
				echo'<p></p><table class="tbg" cellpadding="2" cellspacing="1"><tbody><tr class="cbg1">
					<td colspan="2">In Forschung</td><td>Dauer</td><td>Fertig</td></tr>
					<tr><td width="6%"><img class="unit" src="img/un/u/'.$forsch_wr_data[$wr]['id'].'.gif" border="0">
					</td><td class="s7" width="44%">'.$troops[$forsch_wr_data[$wr]['id']]['name'].'</td>
					<td width="25%"><span id="timer1">'.zeit_dauer($dauer).'</span></td>
					<td width="25%">'.date('H:i',$dauer+time()).'<span> Uhr</span></td></tr></tbody></table>';
			}
        }
        elseif ($id==14)	//Turnierplatz
        {
        	echo'<table class="f10" cellpadding="0" cellspacing="4" width="100%"><tbody><tr>
        		<td width="250">Aktuelle Geschwindigkeit</td><td><b>'.(100+10*$stufe).'</b> Prozent</td>
        		</tr><tr><td width="250">Geschwindigkeit bei Stufe '.$nachste_stufe.':</td>
        		<td><b>'.(100+10*$nachste_stufe).'</b> Prozent</td></tr></tbody></table>';
        }
        elseif ($id==15)	//Hauptgebäude
        {
            echo'<table class="f10" cellpadding="0" cellspacing="4" width="100%">
                <tbody><tr>
                <td width="250">Aktuelle Bauzeit</td><td><b>'.round(100-60*(($stufe-1)/19)).'</b> Prozent</td>
                </tr><tr>
                <td width="250">Bauzeit bei Stufe '.$nachste_stufe.':</td>
                <td><b>'.round(100-60*(($nachste_stufe-1)/19)).'</b> Prozent</td>
                </tr></tbody></table>';
            if ($stufe>9)
            {
            	echo'<br><h2>Gebäude abreißen:</h2><p class="f10">Falls du ein Gebäude nicht mehr benötigen solltest,
            		kannst du deinen Baumeistern hier den Befehl geben, das Gebäude Stück für Stück wieder
            		abzureißen:</p>';
            	if ($crash==0)
            	{
            		echo'<form action="build.php?id='.$gid.'&do=crash" method="post">
            			<select name="abriss" class="f8">';
            		for ($i=19;$i<=40;$i++)
            		{
            			echo'<option value="'.$i.'">'.$i.' - '.$gebeude[$geb2_typ[$i-19]]['name'].'</option>';
            		}
					echo'</select><input class="f8" name="ok" value="Abreißen" type="submit"></form>';
            	}
            	else
            	{
            		$dauer=strtotime($crash_data['zeit'])-time();
            		echo'<p></p><table class="f10" width="100%"><tbody><tr><td>
            			<a href="build.php?id='.$gid.'&do=delcrash"><img src="img/un/a/del.gif" title="abbrechen"
            			border="0" height="12" width="12"></a></td><td>'.
            			$gebeude[$geb2_typ[$crash_data['id']-19]]['name'].
            			' (Stufe '.$geb2_stufe[$crash_data['id']-19].')</td><td>
            			<span id="timer1">'.zeit_dauer($dauer).'</span> Std.</td>
            			<td>Fertig um '.date('H:i',time()+$dauer).'<span> Uhr</span></td></tr></tbody></table>';
            	}
            }
        }
        elseif ($id==16)	//Versammlungsplatz
        {
        	echo'<p class="txt_menue">
        		<a href="build.php?id=39">Übersicht</a> |
        		<a href="build.php?id=39&s=1">Truppen im Exil</a> |
        		<a href="build.php?id=39&s=2">Truppen schicken</a> |
				<a href="warsim.php">Kampfsim</a>
				</p>';

			function print_troops($dorf_name,$x,$y,$titel,$volk,$army,$unterhalt=NULL,$ankunft=NULL,$user_show=1,
				$user_name='')
			{
				global $troops,$i;
				$volk--;
				echo'<table class="tbg" cellpadding="2" cellspacing="1"><tbody>
					<tr class="cbg1"><td width="21%">
					<a href="karte.php?do=show&x='.$x.'&y='.$y.'"><span class="c0">'.$dorf_name.'</span></a>
					</td><td colspan="11" class="b">'.$titel.'</td></tr><tr class="unit">
					<td>&nbsp;<a href="spieler.php?name='.$user_name.'">'.$user_name.'</a></td>';
				for ($j=1;$j<=10;$j++)
					echo'<td><img src="img/un/u/'.($j+$volk*10).'.gif" title="'.$troops[$j+$volk*10]['name'].'"></td>';
				echo'<td><img src="img/un/u/hero.gif" title="Held"></td>
					</tr>
					<tr><td>Einheiten</td>';
//				var_dump($army);
				for ($j=1;$j<=10;$j++)
				{
					if ($user_show==1)
					{
						if ($army[$j+$volk*10]>0) echo'<td>'.$army[$j+$volk*10].'</td>';
						else	echo'<td class="c">0</td>';
					}
					else
						echo'<td class="c">?</td>';
				}
				if ($user_show==1)
				{
					if (!isset($army['hero'])) echo'<td class="c">0</td>';
					else	echo'<td>1</td>';
				}
				else
					echo'<td class="c">?</td>';
				echo'</tr><tr class="cbg1">';
				if ($ankunft==NULL)
					echo'<td>Unterhalt</td><td class="s7" colspan="11">'.$unterhalt.
						'<img class="res" src="img/un/r/4.gif">pro Stunde</td>';
				else
				{
					$dauer=zeit_dauer(strtotime($ankunft)-time());
					$akt=date('H:i:s',strtotime($ankunft));
					echo'<td>Ankunft</td><td colspan="11"><table class="f10" cellpadding="0" cellspacing="0" width="100%"><tbody><tr align="center">'.
						'<td width="50%">&nbsp; in <span id="timer'.$i.'">'.$dauer.'</span> Std.</td><td width="50%">um '.$akt.'<span> Uhr</span>'.
						' </td></tr></tbody></table></td>';
				}
				echo'</tr></tbody></table><p></p>';
			}

			if (!isset($_GET['s']))		//Truppen im Dorf
			{
                echo'<p><b>Truppen im Dorf</b></p>';
                print_troops($dorf_data['name'],$dorfx,$dorfy,'Eigene Truppen',$spieler_volk,
                    $troops_village['own'],$troops_village['own']['versorgung']);

                $sql="SELECT `name`,`id`,`volk` FROM `tr".$round_id."_user`;";
                $result=mysql_query($sql);
                for ($i=1;$i<=mysql_num_rows($result);$i++)
                {
                    $data=mysql_fetch_array($result);
                    if ($data['id']!=$userid AND isset($troops_village[$data['id']]))
                    {
                        print_troops($dorf_data['name'],$dorfx,$dorfy,'Truppen von '.$data['name'],$data['volk'],
                            $troops_village[$data['id']],$troops_village[$data['id']]['versorgung']);
                    }
                }

                $sql="SELECT * FROM `tr".$round_id."_truppen_move`
                	WHERE `ziel_x`='$dorfx' AND `ziel_y`='$dorfy';";
                $result=mysql_query($sql);
                if (mysql_num_rows($result)>0) echo'<p><b>Truppen unterwegs in dieses Dorf</b></p>';
                for ($i=1;$i<=mysql_num_rows($result);$i++)
                {
                	$data=mysql_fetch_array($result);
                	$sx=$data['start_x'];$sy=$data['start_y'];

                	$sql2="SELECT `name`,`user` FROM `tr".$round_id."_dorfer` WHERE `x`='$sx' AND `y`='$sy';";
                	$result2=mysql_query($sql2);
                	$data2=mysql_fetch_array($result2);

                	$sql2="SELECT `volk` FROM `tr".$round_id."_user` WHERE `id`='".$data2['user']."';";
                	$result2=mysql_query($sql2);
                	$data3=mysql_fetch_array($result2);

                	if ($data['aktion']==1) $lnk='Neues Dorf gründen';
                	elseif ($data['aktion']==2) $lnk='Unterstützung für '.$dorf_data['name'];
                	elseif ($data['aktion']==3) $lnk='Angriff gegen '.$dorf_data['name'];
                	elseif ($data['aktion']==4) $lnk='Raubzug gegen '.$dorf_data['name'];
                	elseif ($data['aktion']==5) $lnk='Ausspähen von '.$dorf_data['name'];
                	$lnk='<a href="karte.php?do=show&x='.$dorfx.'&y='.$dorfy.'"><span class="c0">'.$lnk.'</span></a>';

                	$army=split(':',$data['truppen']);
                	for ($j=10;$j>=1;$j--)
                		$army[$j]=$army[$j-1];
					for ($j=1;$j<=10;$j++)
						$army2[$j+$data3['volk']*10-10]=$army[$j];

					$show=1;
					if ($data['user']!=$userid) $show=0;
					$name_of_user='';
					if ($show==0)
					{
						$sql2="SELECT `name` FROM `tr".$round_id."_user` WHERE `id`='".$data['user']."';";
	                	$result2=mysql_query($sql2);
	                	$data4=mysql_fetch_array($result2);
	                	$name_of_user=$data4['name'];
					}
                	print_troops($data2['name'],$sx,$sy,$lnk,$data3['volk'],$army2,NULL,$data['ziel_zeit'],
                		$show,$name_of_user);
                }

                $sql="SELECT * FROM `tr".$round_id."_truppen_move`
                	WHERE `user`='$userid' AND `start_x`='$dorfx' AND `start_y`='$dorfy';";
                $result=mysql_query($sql);
                if (mysql_num_rows($result)>0) echo'<p><b>Truppen unterwegs von diesem Dorf</b></p>';
                for ($i=1;$i<=mysql_num_rows($result);$i++)
                {
                	$data=mysql_fetch_array($result);
                	$zx=$data['ziel_x'];$zy=$data['ziel_y'];

                	$sql2="SELECT `name` FROM `tr".$round_id."_dorfer` WHERE `x`='$zx' AND `y`='$zy';";
                	$result2=mysql_query($sql2);
                	$data2=mysql_fetch_array($result2);

                	if ($data['aktion']==1) $lnk='Neues Dorf gründen';
                	elseif ($data['aktion']==2) $lnk='Unterstützung für '.$data2['name'];
                	elseif ($data['aktion']==3) $lnk='Angriff gegen '.$data2['name'];
                	elseif ($data['aktion']==4) $lnk='Raubzug gegen '.$data2['name'];
                	elseif ($data['aktion']==5) $lnk='Ausspähen von '.$data2['name'];
                	$lnk='<a href="karte.php?do=show&x='.$zx.'&y='.$zy.'"><span class="c0">'.$lnk.'</span></a>';

                	$army=split(':',$data['truppen']);
                	for ($j=10;$j>=1;$j--)
                		$army[$j]=$army[$j-1];
					for ($j=1;$j<=10;$j++)
						$army2[$j+$spieler_volk*10-10]=$army[$j];
//					var_dump($army);
//					var_dump($army2);

                	print_troops($dorf_data['name'],$dorfx,$dorfy,$lnk,$spieler_volk,$army2,NULL,$data['ziel_zeit']);
                }
			}
			if ($_GET['s']==1)			//Truppen im Exil
			{
                echo'<p><b>Truppen im Exil</b></p>';

                $sql="SELECT tr".$round_id."_truppen.*,tr".$round_id."_dorfer.name,tr".$round_id."_user.name AS name2
                	FROM `tr".$round_id."_truppen`,`tr".$round_id."_dorfer`,`tr".$round_id."_user`
                	WHERE tr".$round_id."_truppen.user='".$userid."' AND tr".$round_id."_dorfer.user!='".$userid."'
                		AND tr".$round_id."_truppen.x=tr".$round_id."_dorfer.x
                		AND tr".$round_id."_truppen.y=tr".$round_id."_dorfer.y
                		AND tr_dorfer.user=tr_user.id;";
                $result=mysql_query($sql);
                for ($i=1;$i<=mysql_num_rows($result);$i++)
                {
                    $data=mysql_fetch_array($result);
//                    var_dump($data);

                    $t=split(':',$data['troops']);
                    for ($j=1;$j<=10;$j++)
                    	$truppen[$j]=$t[$j-1];
                    $versorgung=versorgung_von_truppen($troops,$t,$spieler_volk);

                    print_troops($data['name'],$data['x'],$data['y'],'Dorf von '.$data['name2'],$spieler_volk,
                		$truppen,$versorgung);
				}
			}
			if ($_GET['s']==2)			//Truppen schicken
			{
				?>
				<table class="p1" style="width: 100%;" cellpadding="0" cellspacing="1"><tbody>
				<tr><td><table class="f10" width="100%">
				<form method="post" name="snd" action="a2b.php?do=sendtroops">
				<tbody>
				<?php
				for ($i=1;$i<=3;$i++)	//Einheiten auflisten
				{
					$n=$i+$spieler_volk*10-10;
					echo'<tr>';
					echo'<td width="20"><img class="unit" src="img/un/u/'.$n.'.gif" title="'.$troops[$n]['name'].
					'" onclick="document.snd.t'.$n.'.value='."''".'; return false;" border="0"></td>
					<td width="35"><input class="fm" name="t'.$n.'" value="" size="2" maxlength="6" type="text"></td>
					<td class="f8" width="70">
					<a href="#" onclick="document.snd.t'.$n.'.value='.$troops_village['own'][$n].
					'; return false;">('.$troops_village['own'][$n].')</a></td>';

					$n=$i+3+$spieler_volk*10-10;
					echo'<td width="20"><img class="unit" src="img/un/u/'.$n.'.gif" title="'.$troops[$n]['name'].
					'" onclick="document.snd.t'.$n.'.value='."''".'; return false;" border="0"></td>
					<td width="35"><input class="fm" name="t'.$n.'" value="" size="2" maxlength="6" type="text"></td>
					<td class="f8" width="70">
					<a href="#" onclick="document.snd.t'.$n.'.value='.$troops_village['own'][$n].
					'; return false;">('.$troops_village['own'][$n].')</a></td>';

					$n=$i+6+$spieler_volk*10-10;
					echo'<td width="20"><img class="unit" src="img/un/u/'.$n.'.gif" title="'.$troops[$n]['name'].
					'" onclick="document.snd.t'.$n.'.value='."''".'; return false;" border="0"></td>
					<td width="35"><input class="fm" name="t'.$n.'" value="" size="2" maxlength="6" type="text"></td>
					<td class="f8" width="70">
					<a href="#" onclick="document.snd.t'.$n.'.value='.$troops_village['own'][$n].
					'; return false;">('.$troops_village['own'][$n].')</a></td>';

					$n=$i+9+$spieler_volk*10-10;
					if ($n<=$spieler_volk*10)
					{
					echo'<td width="20"><img class="unit" src="img/un/u/'.$n.'.gif" title="'.$troops[$n]['name'].
					'" onclick="document.snd.t'.$n.'.value='."''".'; return false;" border="0"></td>
					<td width="35"><input class="fm" name="t'.$n.'" value="" size="2" maxlength="6" type="text"></td>
					<td class="f8" width="70">
					<a href="#" onclick="document.snd.t'.$n.'.value='.$troops_village['own'][$n].
					'; return false;">('.$troops_village['own'][$n].')</a></td>';
					}
					elseif ($n==$spieler_volk*10+2)
						echo'<td width="20"><img class="unit" src="img/un/u/hero.gif"
						title="" onclick="document.snd.thr.value='."''".'; return false;" border="0"></td>
						<td width="35"><input class="fm" name="thr" value="" size="2" maxlength="6" type="text"></td>
						<td class="f8" width="70">
						<a href="#" onclick="document.snd.thr.value='."'".$troops_village['own']['hero'].
						"'".'; return false;">('.$troops_village['own']['hero'].')</a></td>';
					else echo'<td colspan="3"></td>';


					echo'</tr>';
				}
				?>

                </tbody></table></td></tr></tbody></table>
                <p></p><table class="f10" width="100%">
                <tbody><tr><td valign="top" width="33%">
                <div class="f10"><input name="c" value="2" checked="checked" type="radio">Unterstützung</div>
                <div class="f10"><input name="c" value="3" type="radio">Angriff: Normal</div>
                <div class="f10"><input name="c" value="4" type="radio">Angriff: Raubzug</div>
                </td><td valign="top"><div class="b f135">Dorf:
                <input class="fm" name="dname" value="" size="10" maxlength="20" type="text"></div>
                <div><i>oder</i></div><div class="b f135">
                <?php echo'X: <input class="fm" name="x" value="'.$x.'" size="2" maxlength="4" type="text">
                Y: <input class="fm" name="y" value="'.$y.'" size="2" maxlength="4" type="text"> '; ?>
                </div></td></tr></tbody></table>

                <p><input value="ok" name="s1" src="img/de/b/ok1.gif" onmousedown="btm1('s1','','img/de/b/ok2.gif',1)" onmouseover="btm1('s1','','img/de/b/ok3.gif',1)" onmouseup="btm0()" onmouseout="btm0()" border="0" height="20" type="image" width="50"></p>
                </form>
                <?php
				if (isset($_GET['error']))
					echo'<div class="f10 e b">'.$_GET['error'].'</div>';

			}
        }
        elseif ($id==17)	//Marktplatz
        {
        	$handler=$gebeude[17]['highest']*2;
        	$sql="SELECT sum(`handler`) FROM `tr".$round_id."_handler` WHERE `ursprung`='$dorfx:$dorfy';";
        	$result=mysql_query($sql);
        	$data=mysql_fetch_array($result);
        	$handler_gebraucht=$data['sum(`handler`)'];
        	$sql="SELECT sum(`handler`) FROM `tr".$round_id."_angebote` WHERE `ursprung`='$dorfx:$dorfy';";
        	$result=mysql_query($sql);
        	$data=mysql_fetch_array($result);
        	$handler_gebraucht2=$data['sum(`handler`)'];

        	$anz_ver_handler=$handler-$handler_gebraucht-$handler_gebraucht2;
        	$tragen=500;$speed=16;
        	if ($spieler_volk==2) { $tragen=1000; $speed=12; }
        	if ($spieler_volk==3) { $tragen=750; $speed=24; }
        	$tragen=$tragen+50*$gebeude[28]['highest'];

        	echo'<p class="txt_menue">
        		<a href="build.php?id='.$gid.'">Rohstoffe verschicken</a> |
                <a href="build.php?id='.$gid.'&s=2">Kaufen</a> |
                <a href="build.php?id='.$gid.'&s=3">Verkaufen</a></p><p>
                </p>
                <script language="JavaScript">
                <!--
                var haendler = '.$anz_ver_handler.';
                var carry = '.$tragen.';
                //-->
                </script>';
            if ($_GET['s']==1)
            {
                $error='';
                $need_handler=ceil(($r1+$r2+$r3+$r4)/$tragen);

                if ($need_handler>0)
                {
                    if ($need_handler<=$anz_ver_handler)
                    {
                        if ($dname=='' AND ($x=='' OR $y=='')) { $error='Keine Zielangabe'; unset($_GET['s']); }
                        if ($dname!='' AND $x!='' AND $y!='') { $error='Zuviele Angaben'; unset($_GET['s']); }
                        if ($dname!='' AND $x=='' AND $y=='')
                        {
                            $sql="SELECT * FROM `tr".$round_id."_dorfer` WHERE `name`='$dname';";
                            $result=mysql_query($sql);
                            if (mysql_num_rows($result)==1)
                            {
                                $data=mysql_fetch_array($result);
                                $x=$data['x']; $y=$data['y'];
                                $dauer=sqrt(pow($x-$dorfx,2)+pow($y-$dorfy,2))/$speed*3600;
                                $user=$data['user'];
                            }
                            else
                            {   $error='Dorfname nicht oder mehrfach vorhanden.'; unset($_GET['s']); }
                        }
                        if ($dname=='' AND $x!='' AND $y!='')
                        {
                            $sql="SELECT * FROM `tr".$round_id."_dorfer` WHERE `x`='$x' AND `y`='$y';";
                            $result=mysql_query($sql);
                            if (mysql_num_rows($result)==1)
                            {
                                $data=mysql_fetch_array($result);
                                $dname=$data['name'];
                                $dauer=sqrt(pow($x-$dorfx,2)+pow($y-$dorfy,2))/$speed*3600;
                                $user=$data['user'];
                            }
                            else
                            {   $error='Dorf nicht gefunden.'; unset($_GET['s']); }
                        }
                        if ($dauer==0) { $error='Die Händler sind schon in diesem Dorf'; unset($_GET['s']); }
                        if ($error=='')
                        {
                            $sql="SELECT `name` FROM `tr".$round_id."_user` WHERE `id`='$user';";
                            $result=mysql_query($sql);
                            $data=mysql_fetch_array($result);
                            $empfanger_name=$data['name'];
                            echo'<form method="post" name="snd" action="build.php?id='.$gid.'&do=sendgoods"><input
                           type=hidden name="zielx" value="'.$x.'"><input type=hidden name="ziely" value="'.$y.'">
                            <input type=hidden name="dauer" value="'.$dauer.'"><input type=hidden name="need_h" value="'.
                            $need_handler.'">
                            <table valign="top" cellpadding="0" cellspacing="0" width="100%"><tbody><tr valign="top">
                            <td width="45%"><table class="f10"><tbody><tr><td><img class="res" src="img/un/r/1.gif"></td>
                            <td>Holz:</td><td align="right"><input class="fm" name="r1" value="'.$r1.'" size="4"
                            readonly="readonly" type="text"></td><td class="s7 f8 c b">('.$tragen.')</td></tr><tr>
                            <td><img class="res" src="img/un/r/2.gif"></td><td>Lehm:</td><td align="right">
                            <input class="fm" name="r2" value="'.$r2.'" size="4" readonly="readonly" type="text"></td>
                            <td class="s7 f8 c b">('.$tragen.')</td></tr><tr><td><img class="res" src="img/un/r/3.gif">
                            </td><td>Eisen:</td><td align="right"><input class="fm" name="r3" value="'.$r3.'" size="4"
                            readonly="readonly" type="text"></td><td class="s7 f8 c b">('.$tragen.')</td></tr><tr>
                            <td><img class="res" src="img/un/r/4.gif"></td><td>Getreide:</td><td align="right">
                            <input class="fm" name="r4" value="'.$r4.'" size="4" readonly="readonly" type="text"></td>
                            <td class="s7 f8 c b">('.$tragen.')</td></tr>
                            </tbody></table></td><td valign="top" width="55%">
                            <p class="f135">'.$dname.' ('.$x.'|'.$y.')</p><table><tbody><tr class="left">
                            <td>Spieler:</td><td><a href="spieler.php?name='.$empfanger_name.'">'.$empfanger_name.'</a></td>
                            </tr><tr class="left"><td>Dauer:</td><td>'.zeit_dauer($dauer).'</td></tr>
                            <tr class="left"><td>Händler:</td><td>'.$need_handler.'</td></tr></tbody></table>
                            </td></tr></tbody></table><p><input value="ok" name="s1" src="img/de/b/ok1.gif" onmousedown="btm1'."('s1','','img/de/b/ok2.gif',1".')" onmouseover="btm'."1('s1','','img/de/b/ok3.gif',1".')" onmouseup="btm0()" onmouseout="btm0()" border="0" height="20" type="image" width="50"></p></form>';

                        }
                    }
                    else
                    {   $error='Zu wenig Händler. Es werden '.ceil(($r1+$r2+$r3+$r4)/$tragen).' benötigt, es sind im Moment aber nur '.$anz_ver_handler.' Händler verfügbar.'; unset($_GET['s']); }
                }
                else {$error='Keine Rohstoffe ausgewählt.'; unset($_GET['s']); }
            }
            if (!isset($_GET['s']))
            {
                echo'<form method="post" name="snd" action="build.php?id='.$gid.'&s=1">
                    <table valign="top" cellpadding="0" cellspacing="0" width="100%">
                    <tbody><tr valign="top">
                    <td width="45%">
                    <table class="f10"><tbody><tr>
                    <td><a href="#" onclick="upd_res(1,1)"><img class="res" src="img/un/r/1.gif"></a></td>
                    <td>Holz:</td><td align="right"><input class="fm" name="r1" id="r1" value="" size="4" maxlength="5" onkeyup="upd_res(1)" tabindex="1" type="text"></td>
                    <td class="s7 f8"><a href="#" onclick="add_res(1)" ondblclick="add_res(1)">('.$tragen.')</a></td></tr>
                    <tr><td><a href="#" onclick="upd_res(2,1)"><img class="res" src="img/un/r/2.gif"></a></td>
                    <td>Lehm:</td><td align="right"><input class="fm" name="r2" id="r2" value="" size="4" maxlength="5" onkeyup="upd_res(2)" tabindex="2" type="text"></td>
                    <td class="s7 f8"><a href="#" onclick="add_res(2)" ondblclick="add_res(2)">('.$tragen.')</a></td></tr>
                    <tr><td><a href="#" onclick="upd_res(3,1)"><img class="res" src="img/un/r/3.gif"></a></td>
                    <td>Eisen:</td><td align="right"><input class="fm" name="r3" id="r3" value="" size="4" maxlength="5" onkeyup="upd_res(3)" tabindex="3" type="text"></td>
                    <td class="s7 f8"><a href="#" onclick="add_res(3)" ondblclick="add_res(3)">('.$tragen.')</a></td></tr>
                    <tr><td><a href="#" onclick="upd_res(4,1)"><img class="res" src="img/un/r/4.gif"></a></td>
                    <td>Getreide:</td><td align="right"><input class="fm" name="r4" id="r4" value="" size="4" maxlength="5" onkeyup="upd_res(4)" tabindex="4" type="text"></td>
                    <td class="s7 f8"><a href="#" onclick="add_res(4)" ondblclick="add_res(4)">('.$tragen.')</a></td></tr>
                    </tbody></table>
                    </td><td valign="top" width="55%"><table class="f10">
                    <tbody><tr><td colspan="2">Händler '.$anz_ver_handler.'/'.$handler.'<br><br></td></tr>
                    <tr><td colspan="2"><span class="f135 b">Dorf:</span>
                    <input class="fm" name="dname" value="" size="10" maxlength="20" tabindex="5" type="text"></td></tr>
                    <tr><td colspan="2"><i>oder</i></td></tr>
                    <tr><td colspan="2"><span class="f135 b">X:
                    <input class="fm" name="x" value="'.$x.'" size="2" maxlength="4" tabindex="6" type="text">
                    Y:<input class="fm" name="y" value="'.$y.'" size="2" maxlength="4" tabindex="7" type="text">
                    </span></td></tr></tbody></table></td></tr></tbody></table>
                    <p><input value="ok" name="s1" src="img/de/b/ok1.gif" onmousedown="bt'."m1('s1','','img/de/b/ok2.gif',1".')" onmouseover="bt'."m1('s1','','img/de/b/ok3.gif',1".')" onmouseup="btm0()" onmouseout="btm0()" tabindex="9" border="0" height="20" type="image" width="50"></p>
                    ';
                if ($error=='' AND $done=='') echo'<p>Jeder deiner Händler kann <b>'.$tragen.'</b> Rohstoffe tragen.</p>';
                elseif ($error!='') echo'<p class="b c5">'.$error.'</p>';
                elseif ($done!='') echo'<p class="b c3">'.$done.'</p>';
            }

			if (!isset($_GET['s']) OR $_GET['s']==1)
			{
                function draw_transport($data,$i)
                {
                    global $dorfx,$dorfy,$username,$dorfnamen;
            $nach=split(':',$data['nach']);
                    $von=split(':',$data['von']);
                    $res=split(':',$data['ress']);


                    if ($data['von']==$dorfx.':'.$dorfy) $ziel=$nach;
                    else    $ziel=$von;
                    if (!isset($dorfnamen[$ziel[0]][$ziel[1]]))
                    {
                        $sql2="SELECT `name`,`user` FROM `tr".$round_id."_dorfer`
                        	WHERE `x`='".$ziel[0]."' AND `y`='".$ziel[1]."';";
                        $result2=mysql_query($sql2);
                        $data2=mysql_fetch_array($result2);
                        $dorfnamen[$ziel[0]][$ziel[1]]=$data2['name'];
                    }

                    echo'<table class="tbg" cellpadding="2" cellspacing="1"><tbody><tr class="cbg1">
                        <td width="21%"><span class="c0">'.$data['user'].'</span></td><td colspan="2">';
                    if ($data['von']==$dorfx.':'.$dorfy) echo'Transport nach <a href="karte.php?do=show&x='.
                        $nach[0].'&y='.$nach[1].'"><span class="c0">'.$dorfnamen[$ziel[0]][$ziel[1]];
                    elseif ($data['ursprung']=="$dorfx:$dorfy")
                        echo'Rückkehr aus <a href="karte.php?do=show&x='.$von[0].'&y='.$von[1].
                            '"><span class="c0">'.$dorfnamen[$ziel[0]][$ziel[1]];
                    else
                        echo'Transport von <a href="karte.php?do=show&x='.$ziel[0].'&y='.$ziel[1].
                            '"><span class="c0">'.$dorfnamen[$ziel[0]][$ziel[1]];
                    echo'</span></a></td></tr>
                        <tr><td>Ankunft</td><td><span id="timer'.$i.'">'.zeit_dauer(strtotime($data['ziel'])-time()).
                        '</span> Std.</td><td>um '.date('H:i',strtotime($data['ziel'])).' Uhr</td></tr>
                        <tr class="cbg1"><td>Rohstoffe</td><td class="s7" colspan="2">';
                    $x=''; if (array_sum($res)==0) $x='c ';
                    echo'<span class="'.$x.'f10">
                        <img class="res" src="img/un/r/1.gif">'.$res[0];
                    for ($fj=2;$fj<=4;$fj++)
                        echo' | <img class="res" src="img/un/r/'.$fj.'.gif">'.$res[$fj-1];
                    echo'</span></td></tr></tbody></table><p></p>';

                }
                unset($dorfnamen);

                $sql="SELECT * FROM `tr".$round_id."_handler` WHERE `nach`='$dorfx:$dorfy' AND `ursprung`!='$dorfx:$dorfy'
                    ORDER BY `ziel` ASC;";
                $result=mysql_query($sql);
                $j=0;
                if (mysql_num_rows($result)>0)
                {
                	echo'<p class="b">Ankommende Händler:</p><p></p>';
                	for ($j=1;$j<=mysql_num_rows($result);$j++)
                	{
                	    $data=mysql_fetch_array($result);
                	    draw_transport($data,$j);
                	}
                }
                $sql="SELECT * FROM `tr".$round_id."_handler` WHERE `ursprung`='$dorfx:$dorfy' ORDER BY `ziel` ASC;";
                $result=mysql_query($sql);
                if (mysql_num_rows($result)>0) echo'<p class="b">Eigene Händler unterwegs:</p><p></p>';
                for ($i=1;$i<=mysql_num_rows($result);$i++)
                {
                    $data=mysql_fetch_array($result);
                    draw_transport($data,$i+$j-1);
                }
			}
			if ($_GET['s']==2)
			{
				echo'<table class="tbg" cellpadding="2" cellspacing="1"><tbody>
					<tr class="rbg"><td colspan="8"><a name="h2">Angebote am Marktplatz</a></td></tr>
					<tr class="cbg1"><td colspan="2">Biete</td><td colspan="2">Suche</td><td>Spieler</td>
					<td>Händler</td><td>Dauer</td><td>Aktion</td></tr>';
				$sql="SELECT `name`,`volk` FROM `tr".$round_id."_user`;";
				$result=mysql_query($sql);
				$speed_volk=array(1=>16,2=>12,3=>24);
				for ($i=1;$i<=mysql_num_rows($result);$i++)
				{
					$data=mysql_fetch_array($result);
					$spieler_hspeed[$data['name']]=$speed_volk[$data['volk']];
				}

				$sql="SELECT * FROM `tr".$round_id."_angebote` WHERE `user`!='$username';";
				$result=mysql_query($sql);
				$anz=0;
				$rohstoff_name=array(1=>'Holz',2=>'Lehm',3=>'Eisen',4=>'Getreide');

				for ($i=1;$i<=mysql_num_rows($result);$i++)
				{
					$data=mysql_fetch_array($result);

					$ort=split(':',$data['ursprung']);
					$weg=sqrt(pow($ort[0]-$dorfx,2)+pow($ort[1]-$dorfy,2));
					$dauer=$weg/$spieler_hspeed[$data['user']];
					if ($dauer<=$data['maxzeit'])
					{
						$angebot=split(':',$data['angebot']);
						$nachfrage=split(':',$data['nachfrage']);
						$anz++;
						echo'<tr><td><img class="res" src="img/un/r/'.$angebot[0].'.gif" title="'.
							$rohstoff_name[$angebot[0]].'"></td><td>'.$angebot[1].'</td><td>
							<img class="res" src="img/un/r/'.$nachfrage[0].'.gif" title="'.
							$rohstoff_name[$nachfrage[0]].'"></td><td>'.$nachfrage[1].'</td>
							<td title=""><a href="spieler.php?name='.$data['user'].'">'.$data['user'].'</a></td>
							<td>'.ceil($nachfrage[1]/$tragen).'</td>
							<td>'.zeit_dauer($weg/$spieler_hspeed[$username]*3600).'</td>';

						if ($lager[$nachfrage[0]-1]>=$nachfrage[1])
						{
							if (ceil($nachfrage[1]/$tragen)<=$anz_ver_handler)
							{
								echo'<td><a href="build.php?id='.$gid.'&do=buyoffer&u='.$data['ursprung'].'&a='.
									$data['angebot'].'&n='.$data['nachfrage'].'&ally='.$data['ally'].'&max='.
									$data['maxzeit'].'&s=4">Angebot annehmen</a>';
							}
							else echo'<td class="c">Zu wenig Händler';
						}
						else echo'<td class="c">Zu wenig Rohstoffe';
						echo'</td></tr>';
					}
				}
				if ($anz==0)
					echo'<tr bgcolor="#f5f5f5"><td class="rowpic" colspan="8"><span class="c">
						<b>Keine Angebote vorhanden</b></span></td></tr>';

				echo'</tbody></table>';

			}
			if ($_GET['s']==3)
			{
				echo'<form method="post" action="build.php?id='.$gid.'&s=3&do=newoffer">
					<input type=hidden name="anz_ver_handler" value="'.$anz_ver_handler.'">
					<input type=hidden name="tragen" value="'.$tragen.'">
					<table class="f10"><tbody><tr>
					<td>Biete</td><td><input class="fm" name="r1" value="" size="4" maxlength="5"></td>
					<td><select name="typ1" size="" class="fm">
					<option value="1" selected="selected">Holz</option><option value="2">Lehm</option>
					<option value="3">Eisen</option><option value="4">Getreide</option></select></td>
					<td>&nbsp;</td><td><input name="d1" value="1" type="checkbox" checked disabled> Max. Transportdauer:
					<input class="fm fm25" name="d2" value="10" maxlength="2"> Stunden</td></tr>

					<tr><td>Suche</td><td><input class="fm" name="r2" value="" size="4" maxlength="5"></td>
					<td><select name="typ2" size="" class="f8"><option value="1">Holz</option>
					<option value="2" selected="selected">Lehm</option><option value="3">Eisen</option>
					<option value="4">Getreide</option></select></td>

					<td>&nbsp;</td><td><input name="ally" value="1" type="checkbox"> Nur eigene Allianz</td>
					</tr><tr><td colspan=6>&nbsp;</td></tr>
					<tr><td colspan=4>Händler: '.$anz_ver_handler.'/'.$handler.'</td>
					<td colspan=2>&nbsp;Tragfähigkeit: '.$tragen.'</td></tr></tbody></table></p>';
				if (isset($error)) echo'<p class="e">'.$error.'</p>';
				echo'<p><input value="ok" name="s1" src="img/de/b/ok1.gif"
					onmousedown="btm1'."('s1','','img/de/b/ok2.gif',1".')"
					onmouseover="btm1'."('s1','','img/de/b/ok3.gif',1".')"
					onmouseup="btm0()" onmouseout="btm0()" border="0" height="20" type="image" width="50"></p></form>';
				$sql="SELECT * FROM `tr".$round_id."_angebote` WHERE `ursprung`='$dorfx:$dorfy';";
				$result=mysql_query($sql);
				if (mysql_num_rows($result)>0)
				{
					echo'<p></p><table class="f10" bgcolor="#c0c0c0" cellpadding="1" cellspacing="1" width="100%">
						<tbody><tr align="center" bgcolor="#f5f5f5">
						<td colspan="6"><b>Eigene Angebote</b></td></tr><tr align="center" bgcolor="#f5f5f5">
						<td>&nbsp;</td><td>Biete</td><td>Suche</td><td width="20%">Händler</td><td>Öffentlich</td>
						<td>Max. Transportdauer</td></tr>';
					for ($i=1;$i<=mysql_num_rows($result);$i++)
					{
						$data=mysql_fetch_array($result);
						$nach=split(':',$data['nachfrage']);
						$ang=split(':',$data['angebot']);
						echo'<tr align="center" bgcolor="#ffffff"><td>
							<a href="build.php?id='.$gid.'&s=3&do=deloffer&a='.$data['angebot'].'&n='.
							$data['nachfrage'].'&ally='.$data['ally'].'&md='.$data['maxzeit'].'">
							<img src="img/un/a/del.gif" alt="löschen" title="löschen" border="0" height="12" width="12">
							</a></td><td><img src="img/un/r/'.$ang[0].'.gif" height="12" width="18">'.$ang[1].'</td>
							<td><img src="img/un/r/'.$nach[0].'.gif" height="12" width="18">'.$nach[1].'</td>
							<td>'.$data['handler'].'</td><td>';
						if ($data['ally']==1) echo'<img src="img/un/a/b3.gif" height="12" width="12"
							title="Nur für Allianz">';
						else	echo'<img src="img/un/a/b2.gif" height="12" width="12"
							title="Für alle sichtbar">';
						echo'</td><td>'.$data['maxzeit'].'h</td></tr>';
					}
					echo'</tbody></table>';
				}
			}
			if ($_GET['s']==4)
			{
				$rohstoff_name=array(1=>'Holz',2=>'Lehm',3=>'Eisen',4=>'Getreide');
				echo'<table class="tbg" cellpadding="2" cellspacing="1"><tbody>
				<tr class="rbg"><td colspan="3">Marktplatz</td></tr>
				<tr><td colspan="3">Das Angebot von '.$angenommen['user'].' wurde angenommen.</td></tr>
				<tr><td><img src="img/un/r/'.$angenommen['r1'][0].'.gif" alt="'.$rohstoff_name[$angenommen['r1'][0]].'" height="12"
				 width="18"></td><td>'.$angenommen['r1'][1].'</td><td>sind zu dir unterwegs</td></tr>
				<tr><td><img src="img/un/r/'.$angenommen['r2'][0].'.gif" alt="'.$rohstoff_name[$angenommen['r2'][0]].'" height="12"
				 width="18"></td><td>'.$angenommen['r2'][1].'</td><td>haben deine Händler soeben verschickt</td>
				</tr></tbody></table>';
			}
        }
        elseif ($id==18)	//Botschaft
        {
        	if ($spieler_data['ally']<1)
        	{
        		echo'<table class="tbg" style="width: 60%;" cellpadding="2" cellspacing="1">
        			<form method="post" action="build.php"></form><tbody><tr class="rbg">
        			<td colspan="3">Allianz beitreten</td></tr><tr bgcolor="#ffffff">
        			<td colspan="3" class="c" align="center">Es liegen keine Einladungen vor</td>
        			</tr></tbody></table>';
        	}
        }
        elseif ($id==19)	//Kaserne
        {
        	?>
        	<script type="text/javascript">
        	function klick(id){ document.getElementById(id).click(); }
        	</script>
        	<?php
        	echo'<form method="post" name="snd" action="build.php?id='.$gid.'&do=recrut">
        		<p></p><table class="tbg" cellpadding="2" cellspacing="1"><tbody>
				<tr class="cbg1"><td>Name</td><td>Anzahl</td><td>max</td></tr>';
			$nr=0;
			for ($i=1;$i<=10;$i++)
			{
				$tid=$i+($spieler_volk-1)*10;
				if ($troops[$tid]['ok2']==1 AND $troops[$tid]['typ']==1)
				{
					$max[0]=floor($lager[0]/$troops[$tid]['kosten_holz']);
					$max[1]=floor($lager[1]/$troops[$tid]['kosten_lehm']);
					$max[2]=floor($lager[2]/$troops[$tid]['kosten_eisen']);
					$max[3]=floor($lager[3]/$troops[$tid]['kosten_getreide']);
					$buildmax=min($max);

					echo'<tr><td>
						<table class="f10" cellpadding="0" cellspacing="2" width="100%">
						<tbody><tr><td rowspan="2" class="s7" valign="top" width="6%">
						<img class="unit" src="img/un/u/'.$tid.'.gif"></td>
						<td class="s7"><div><a href="#" onclick="Popup(1,'.$tid.');"> '.
						$troops[$tid]['name'].'</a>
						<span class="c f75">(Vorhanden: '.$troops_village['own'][$tid].')</span></div></td></tr>
						<tr><td class="s7">
						<img class="res" src="img/un/r/1.gif">'.$troops[$tid]['kosten_holz'].
						'|<img class="res" src="img/un/r/2.gif">'.$troops[$tid]['kosten_lehm'].
						'|<img class="res" src="img/un/r/3.gif">'.$troops[$tid]['kosten_eisen'].
						'|<img class="res" src="img/un/r/4.gif">'.$troops[$tid]['kosten_getreide'].
						'|<img class="res" src="img/un/r/5.gif">'.$troops[$tid]['versorgung'].
						' |<img class="clock" src="img/un/a/clock.gif"> '.zeit_dauer($troops[$tid]['bauzeit']).
						'</td></tr></tbody></table></td>
						<td><input type=radio size=1 name="re" value="'.$tid.'" id="r'.$nr.'">
						<input name="t'.$tid.'" value="0" size="2" maxlength="4" type="text"
						onclick="klick('."'r".$nr."'".');"></td>
						<td><div class="f75"><a href="#" onclick="document.snd.t'.$tid.'.value='.$buildmax.';
						klick('."'r".$nr."'".');">('.$buildmax.')</a></div></td></tr>';
					$nr++;
				}
			}
			echo'</tbody></table><p>
			<input value="ok" src="img/de/b/b1.gif" border="0" height="20" type="image" width="80">
			</p><p></p>
			';
			if ($recrut==1)
			{
				echo'<table class="tbg" cellpadding="2" cellspacing="1"><tbody><tr class="cbg1">
					<td colspan="3">In Ausbildung</td><td>Dauer</td><td>Fertig</td></tr>';


				$dauer=0;
				for ($i=1;$i<=$recrut_data['anzahl'];$i++)
				{
					$dauer+=$recrut_data[$i]['anzahl']*$recrut_data[$i]['dauer'];
					if ($i==1) $dauer-=$recrut_data[$i]['dauer']-(strtotime($recrut_data[$i]['zeit'])-time());

					echo'<tr><td width="5%"><img class="unit" src="img/un/u/'.$recrut_data[$i]['id'].
					'.gif" border="0"></td><td align="right" width="6%">'.$recrut_data[$i]['anzahl'].'&nbsp;</td>
					<td class="s7" width="39%">'.$troops[$recrut_data[$i]['id']]['name'].'</td>
					<td width="25%"><span id="timer'.($i+1).'">'.
					zeit_dauer($dauer).'</span></td>
					<td width="25%">'.date('H:i',$dauer+time()).'<span> Uhr </span>'.
					date('d.m.Y',$dauer+time()).'</td></tr>';
				}
				echo'<tr class="cbg1" align="center"><td colspan="5">
					Fertigstellung der nächsten Einheit in <span id="timer1">'.
					zeit_dauer(strtotime($recrut_data[1]['zeit'])-time()).'</span></td>
					</tr></tbody></table>';
			}
        }
        elseif ($id==20)	//Stall
        {
        	echo'<form method="post" name="snd" action="build.php?id='.$gid.'&do=recrut2">
        		<p></p><table class="tbg" cellpadding="2" cellspacing="1"><tbody>
				<tr class="cbg1"><td>Name</td><td>Anzahl</td><td>max</td></tr>';
			$nr=0;
			for ($i=1;$i<=10;$i++)
			{
				$tid=$i+($spieler_volk-1)*10;
				if ($troops[$tid]['ok2']==1 AND $troops[$tid]['typ']==2)
				{
					$max[0]=floor($lager[0]/$troops[$tid]['kosten_holz']);
					$max[1]=floor($lager[1]/$troops[$tid]['kosten_lehm']);
					$max[2]=floor($lager[2]/$troops[$tid]['kosten_eisen']);
					$max[3]=floor($lager[3]/$troops[$tid]['kosten_getreide']);
					$buildmax=min($max);

					echo'<tr><td>
						<table class="f10" cellpadding="0" cellspacing="2" width="100%">
						<tbody><tr><td rowspan="2" class="s7" valign="top" width="6%">
						<img class="unit" src="img/un/u/'.$tid.'.gif"></td>
						<td class="s7"><div><a href="#" onclick="Popup(1,'.$tid.');"> '.
						$troops[$tid]['name'].'</a>
						<span class="c f75">(Vorhanden: '.$troops_village['own'][$tid].')</span></div></td></tr>
						<tr><td class="s7">
						<img class="res" src="img/un/r/1.gif">'.$troops[$tid]['kosten_holz'].
						'|<img class="res" src="img/un/r/2.gif">'.$troops[$tid]['kosten_lehm'].
						'|<img class="res" src="img/un/r/3.gif">'.$troops[$tid]['kosten_eisen'].
						'|<img class="res" src="img/un/r/4.gif">'.$troops[$tid]['kosten_getreide'].
						'|<img class="res" src="img/un/r/5.gif">'.$troops[$tid]['versorgung'].
						' |<img class="clock" src="img/un/a/clock.gif"> '.zeit_dauer($troops[$tid]['bauzeit']).
						'</td></tr></tbody></table></td>
						<td><input type=radio size=1 name="re" value="'.$tid.'">
						<input name="t'.$tid.'" value="0" size="2" maxlength="4" type="text"
						onclick="document.snd.re['.($nr).'].click();"></td>
						<td><div class="f75"><a href="#" onclick="document.snd.t'.$tid.'.value='.$buildmax.
						'; document.snd.re['.($nr).'].click();">('.$buildmax.')</a></div></td></tr>';
					$nr++;
				}
			}
			echo'</tbody></table><p>
			<input value="ok" src="img/de/b/b1.gif" border="0" height="20" type="image" width="80">
			</p><p></p>
			';
			if ($recrut2==1)
			{
				echo'<table class="tbg" cellpadding="2" cellspacing="1"><tbody><tr class="cbg1">
					<td colspan="3">In Ausbildung</td><td>Dauer</td><td>Fertig</td></tr>';


				$dauer=0;
				for ($i=1;$i<=$recrut2_data['anzahl'];$i++)
				{
					$dauer+=$recrut2_data[$i]['anzahl']*$recrut2_data[$i]['dauer'];
					if ($i==1) $dauer-=$recrut2_data[$i]['dauer']-(strtotime($recrut2_data[$i]['zeit'])-time());

					echo'<tr><td width="5%"><img class="unit" src="img/un/u/'.$recrut2_data[$i]['id'].
					'.gif" border="0"></td><td align="right" width="6%">'.$recrut2_data[$i]['anzahl'].'&nbsp;</td>
					<td class="s7" width="39%">'.$troops[$recrut2_data[$i]['id']]['name'].'</td>
					<td width="25%"><span id="timer'.($i+1).'">'.
					zeit_dauer($dauer).'</span></td>
					<td width="25%">'.date('H:i',$dauer+time()).'<span> Uhr </span>'.
					date('d.m.Y',$dauer+time()).'</td></tr>';
				}
				echo'<tr class="cbg1" align="center"><td colspan="5">
					Fertigstellung der nächsten Einheit in <span id="timer1">'.
					zeit_dauer(strtotime($recrut2_data[1]['zeit'])-time()).'</span></td>
					</tr></tbody></table>';
			}
        }
        elseif ($id==21)	//Werkstatt
        {
        	echo'<form method="post" name="snd" action="build.php?id='.$gid.'&do=recrut3">
        		<p></p><table class="tbg" cellpadding="2" cellspacing="1"><tbody>
				<tr class="cbg1"><td>Name</td><td>Anzahl</td><td>max</td></tr>';
			$nr=0;
			for ($i=1;$i<=10;$i++)
			{
				$tid=$i+($spieler_volk-1)*10;
				if ($troops[$tid]['ok2']==1 AND $troops[$tid]['typ']==3)
				{
					$max[0]=floor($lager[0]/$troops[$tid]['kosten_holz']);
					$max[1]=floor($lager[1]/$troops[$tid]['kosten_lehm']);
					$max[2]=floor($lager[2]/$troops[$tid]['kosten_eisen']);
					$max[3]=floor($lager[3]/$troops[$tid]['kosten_getreide']);
					$buildmax=min($max);

					echo'<tr><td>
						<table class="f10" cellpadding="0" cellspacing="2" width="100%">
						<tbody><tr><td rowspan="2" class="s7" valign="top" width="6%">
						<img class="unit" src="img/un/u/'.$tid.'.gif"></td>
						<td class="s7"><div><a href="#" onclick="Popup(1,'.$tid.');"> '.
						$troops[$tid]['name'].'</a>
						<span class="c f75">(Vorhanden: '.$troops_village['own'][$tid].')</span></div></td></tr>
						<tr><td class="s7">
						<img class="res" src="img/un/r/1.gif">'.$troops[$tid]['kosten_holz'].
						'|<img class="res" src="img/un/r/2.gif">'.$troops[$tid]['kosten_lehm'].
						'|<img class="res" src="img/un/r/3.gif">'.$troops[$tid]['kosten_eisen'].
						'|<img class="res" src="img/un/r/4.gif">'.$troops[$tid]['kosten_getreide'].
						'|<img class="res" src="img/un/r/5.gif">'.$troops[$tid]['versorgung'].
						' |<img class="clock" src="img/un/a/clock.gif"> '.zeit_dauer($troops[$tid]['bauzeit']).
						'</td></tr></tbody></table></td>
						<td><input type=radio size=1 name="re" value="'.$tid.'">
						<input name="t'.$tid.'" value="0" size="2" maxlength="4" type="text"
						onclick="document.snd.re['.$nr.'].click();"></td>
						<td><div class="f75"><a href="#" onclick="document.snd.t'.$tid.'.value='.$buildmax.
						'; document.snd.re['.$nr.'].click();">('.$buildmax.')</a></div></td></tr>';
					$nr++;
				}
			}
			echo'</tbody></table><p>
			<input value="ok" src="img/de/b/b1.gif" border="0" height="20" type="image" width="80">
			</p><p></p>
			';
			if ($recrut3==1)
			{
				echo'<table class="tbg" cellpadding="2" cellspacing="1"><tbody><tr class="cbg1">
					<td colspan="3">In Ausbildung</td><td>Dauer</td><td>Fertig</td></tr>';


				$dauer=0;
				for ($i=1;$i<=$recrut3_data['anzahl'];$i++)
				{
					$dauer+=$recrut3_data[$i]['anzahl']*$recrut3_data[$i]['dauer'];
					if ($i==1) $dauer-=$recrut3_data[$i]['dauer']-(strtotime($recrut3_data[$i]['zeit'])-time());

					echo'<tr><td width="5%"><img class="unit" src="img/un/u/'.$recrut3_data[$i]['id'].
					'.gif" border="0"></td><td align="right" width="6%">'.$recrut3_data[$i]['anzahl'].'&nbsp;</td>
					<td class="s7" width="39%">'.$troops[$recrut3_data[$i]['id']]['name'].'</td>
					<td width="25%"><span id="timer'.($i+1).'">'.
					zeit_dauer($dauer).'</span></td>
					<td width="25%">'.date('H:i',$dauer+time()).'<span> Uhr </span>'.
					date('d.m.Y',$dauer+time()).'</td></tr>';
				}
				echo'<tr class="cbg1" align="center"><td colspan="5">
					Fertigstellung der nächsten Einheit in <span id="timer1">'.
					zeit_dauer(strtotime($recrut3_data[1]['zeit'])-time()).'</span></td>
					</tr></tbody></table>';
			}
        }
        elseif ($id==22)	//Akademie
        {
			echo'<table class="tbg" cellpadding="2" cellspacing="1">
				<tbody><tr class="cbg1"><td>Akademie</td><td>Aktion</td></tr>';
			$anz=0;
			for ($i=1;$i<=30;$i++)
			{
				if ($troops[$i]['ok1']==1 AND $research[(($i-1) % 10)]==0)
				{
					$anz=1;
					echo'<tr><td><table class="f10" cellpadding="0" cellspacing="2" width="100%"><tbody><tr>
					<td rowspan="2" class="s7" valign="top" width="6%">
					<img class="unit" src="img/un/u/'.$i.'.gif" border="0">
					</td><td class="s7"><div><span>'.$troops[$i]['name'].'</span></div></td></tr><tr><td class="s7">
					<img src="img/un/a/x.gif" height="15" width="1">
					<img class="res" src="img/un/r/1.gif">'.$troops[$i]['forsch_holz'].
					'|<img class="res" src="img/un/r/2.gif">'.$troops[$i]['forsch_lehm'].
					'|<img class="res" src="img/un/r/3.gif">'.$troops[$i]['forsch_eisen'].
					'|<img class="res" src="img/un/r/4.gif">'.$troops[$i]['forsch_getreide'].
					'|<img src="img/un/a/clock.gif" height="12" width="18"> '.zeit_dauer($troops[$i]['forsch_zeit']).
					'</td></tr></tbody></table></td><td width="30%">';
					if ($lager[0]>=$troops[$i]['forsch_holz'] AND $lager[1]>=$troops[$i]['forsch_lehm'] AND
						$lager[2]>=$troops[$i]['forsch_eisen'] AND $lager[3]>=$troops[$i]['forsch_getreide'])
					{
						if ($forsch==0)
							echo'<a href="build.php?id='.$gid.'&do=research&rid='.$i.'">erforschen</a>';
						else
							echo'<div class="c">Es wird geforscht</div>';
					}
					else
						echo'<div class="c">Zu wenig<br>Rohstoffe</div>';
					echo'</td></tr>';
				}
			}
			if ($anz==0)
				echo'<tr><td colspan="2" class="f10 c">Im Moment können keine Einheiten erforscht werden. Um die Vorraussetzungen für neue Truppentypen nachzulesen, klicke auf das entsprechende Einheitenbild in der Anleitung.</td></tr>';
			echo'</tbody></table>';
			if ($forsch==1)
			{
				$fid=$forsch_data['id'];
				echo'<p></p><table class="tbg" cellpadding="2" cellspacing="1"><tbody>
				<tr class="cbg1"><td colspan="2">In Forschung</td><td>Dauer</td><td>Fertig</td></tr>
				<tr><td width="6%"><img class="unit" src="img/un/u/'.$fid.'.gif" border="0"></td>
				<td class="s7" width="44%">'.$troops[$fid]['name'].'</td><td width="25%">
				<span id="timer1">'.zeit_dauer(strtotime($forsch_data['zeit'])-time()).'</span></td>
				<td width="25%">'.date('H:i',strtotime($forsch_data['zeit'])).'<span> Uhr</span></td></tr>
				</tbody></table>';
			}
        }
        elseif ($id==23)				//Versteck
        {
        	echo'<table class="f10" cellpadding="0" cellspacing="4" width="100%"><tbody><tr><td width="250">
				Aktuelles Versteck:</td><td><b>'.$lager_grosse['versteck'][$stufe-1].'</b> Einheiten</td>
				</tr><tr><td width="250">Versteck bei Stufe '.$nachste_stufe.':</td>
				<td><b>'.$lager_grosse['versteck'][$nachste_stufe-1].'</b> Einheiten</td></tr></tbody></table>';
        }
        elseif ($id==24)				//Ratshaus
        {
        	$zeit_kf=round((3600*24)*(100-($stufe-1)/19*50)/100);
        	$zeit_gf=round((3600*48)*(100-($stufe-1)/19*50)/100);
        	$kp_kf=500*(1+$gebeude[35]['highest']/10);
        	$kp_gf=2000*(1+$gebeude[35]['highest']/10);

        	echo'<table class="tbg" cellpadding="2" cellspacing="1"><tbody><tr class="cbg1"><td>Rathaus</td>
        		<td>Aktion</td></tr><tr><td><table class="f10" cellpadding="0" cellspacing="2" width="100%">
        		<tbody><tr><td class="s7"><div><a href="#">kleines Fest</a> <span class="f8">('.$kp_kf.
        		' Kulturpunkte)</span></div></td></tr><tr><td class="s7" nowrap="nowrap">
        		<img src="img/un/a/x.gif" height="15" width="1">
        		<img class="res" src="img/un/r/1.gif">6400|<img class="res" src="img/un/r/2.gif">6650|'.
        		'<img class="res" src="img/un/r/3.gif">5940|<img class="res" src="img/un/r/4.gif">1340| '.
        		'<img src="img/un/a/clock.gif" height="12" width="18"> '.zeit_dauer($zeit_kf).
        		'</td></tr></tbody></table></td><td width="28%">';
        	if ($festen==0)
        	{
        		if ($lager[0]>=6400 AND $lager[1]>=6650 AND $lager[2]>=5940 AND $lager[3]>=1340)
        			echo'<a href="build.php?id='.$gid.'&do=fest&x=1">veranstalten</a>';
        		else
        			echo'<span class="c">Zu wenig Rohstoffe</span>';
        	}
        	else
        		echo'<span class="c">Es wird bereits ein Fest gefeiert</span>';
        	echo'</td></tr>';

        	if ($stufe>9)
        	{
        		echo'<tr><td><table class="f10" cellpadding="0" cellspacing="2" width="100%"><tbody><tr>
        			<td class="s7"><div><a href="#">großes Fest</a> <span class="f8">('.$kp_gf.' Kulturpunkte)</span>
        			</div></td></tr><tr><td class="s7" nowrap="nowrap"><img src="img/un/a/x.gif" height="15" width="1">
        			<img class="res" src="img/un/r/1.gif">29700|'.
        			'<img class="res" src="img/un/r/2.gif">33250|<img class="res" src="img/un/r/3.gif">32000|'.
        			'<img class="res" src="img/un/r/4.gif">6700| <img src="img/un/a/clock.gif" height="12" width="18">'.
        			zeit_dauer($zeit_gf).'</td></tr></tbody></table></td><td width="28%">';
                if ($festen==0)
                {
                    if ($lager[0]>=29700 AND $lager[1]>=33250 AND $lager[2]>=32000 AND $lager[3]>=6700)
                        echo'<a href="build.php?id='.$gid.'&do=fest&x=2">veranstalten</a>';
                    else
                        echo'<span class="c">Zu wenig Rohstoffe</span>';
                }
                else
                    echo'<span class="c">Es wird bereits ein Fest gefeiert</span>';
        		echo'</td></tr>';
        	}
        	echo'</tbody></table>';

        	if ($festen==1)
        	{
        		$dauer=strtotime($festen_data['zeit'])-time();
        		$fest_gr=array(1=>'kleines',2=>'grosses');
        		echo'<p></p><table class="tbg" cellpadding="2" cellspacing="1"><tbody>
        			<tr class="cbg1"><td>Fest</td><td>Dauer</td><td>Fertig</td></tr><tr>
        			<td class="s7" width="44%">'.$fest_gr[$festen_data['id']].' Fest <span class="c">(erbringt '.
        			$festen_data['dauer'].'KP)</span></td><td width="25%"><span id="timer1">'.
        			zeit_dauer($dauer).'</span></td><td width="25%">'.date('H:i',time()+$dauer).'<span> Uhr </span>'.
        			date('d.m.Y',$dauer+time()).'</td></tr></tbody></table>';
        	}
        }
        elseif ($id==25 OR $id==26)				//Residenz, Palast
        {
        	$sql="SELECT `value` FROM `tr".$round_id."_diverses` WHERE `id`='neue_dorfer';";
            $result=mysql_query($sql);
            $data=mysql_fetch_array($result);
            $neue_dorfer=split(':',$data['value']);

            $sql="SELECT `einwohner`,`x`,`y`,`name` FROM `tr".$round_id."_dorfer` WHERE `user`='$userid';";
            $result=mysql_query($sql);
            $anz_dorfer=mysql_num_rows($result);

			echo'<p class="txt_menue"><a href="build.php?id='.$gid.'">Ausbilden</a> | <a href="build.php?id='.$gid.'&s=2">Kulturpunkte</a> | '.
        		'<a href="build.php?id='.$gid.'&s=3">Zustimmung</a> | <a href="build.php?id='.$gid.'&s=4">Expansion</a></p>';

        	if (!isset($_GET['s']))
        	{
        		if ($stufe<10)
        		{
        			$x=array(25=>'eine Residenz',26=>'einen Palast');
        			echo'<div class="c">Um eine weitere Siedlung zu gründen oder zu erobern benötigst du '.
	        			$x[$id].' Stufe 10.</div>';
	        	}
	        	else
	        	{
                    echo'<form method="post" name="snd" action="build.php?id='.$gid.'&do=recrut4">
                        <p></p><table class="tbg" cellpadding="2" cellspacing="1"><tbody>
                        <tr class="cbg1"><td>Name</td><td>Anzahl</td><td>max</td></tr>';
                    $nr=0;
                    for ($i=1;$i<=10;$i++)
                    {
                        $tid=$i+($spieler_volk-1)*10;
                        if ($troops[$tid]['ok2']==1 AND $troops[$tid]['typ']==4)
                        {
                            $max[0]=floor($lager[0]/$troops[$tid]['kosten_holz']);
                            $max[1]=floor($lager[1]/$troops[$tid]['kosten_lehm']);
                            $max[2]=floor($lager[2]/$troops[$tid]['kosten_eisen']);
                            $max[3]=floor($lager[3]/$troops[$tid]['kosten_getreide']);
                            $buildmax=min($max);

                            echo'<tr><td>
                                <table class="f10" cellpadding="0" cellspacing="2" width="100%">
                                <tbody><tr><td rowspan="2" class="s7" valign="top" width="6%">
                                <img class="unit" src="img/un/u/'.$tid.'.gif"></td>
                                <td class="s7"><div><a href="#" onclick="Popup(1,'.$tid.');"> '.
                                $troops[$tid]['name'].'</a>
                                <span class="c f75">(Vorhanden: '.$troops_village['own'][$tid].')</span></div></td></tr>
                                <tr><td class="s7">
                                <img class="res" src="img/un/r/1.gif">'.$troops[$tid]['kosten_holz'].
                                '|<img class="res" src="img/un/r/2.gif">'.$troops[$tid]['kosten_lehm'].
                                '|<img class="res" src="img/un/r/3.gif">'.$troops[$tid]['kosten_eisen'].
                                '|<img class="res" src="img/un/r/4.gif">'.$troops[$tid]['kosten_getreide'].
                                '|<img class="res" src="img/un/r/5.gif">'.$troops[$tid]['versorgung'].
                                ' <br><img class="clock" src="img/un/a/clock.gif"> '.zeit_dauer($troops[$tid]['bauzeit']).
                                '</td></tr></tbody></table></td>
                                <td><input type=radio size=1 name="re" value="'.$tid.'">
                                <input name="t'.$tid.'" value="0" size="2" maxlength="4" type="text"
                                onclick="document.snd.re['.$nr.'].click();"></td>
                                <td><div class="f75"><a href="#" onclick="document.snd.t'.$tid.'.value='.$buildmax.
                                '; document.snd.re['.$nr.'].click();">('.$buildmax.')</a></div></td></tr>';
                            $nr++;
                        }
                    }
                    echo'</tbody></table><p>
                    <input value="ok" src="img/de/b/b1.gif" border="0" height="20" type="image" width="80">
                    </p><p></p>
                    ';
                    if ($recrut4==1)
                    {
                        echo'<table class="tbg" cellpadding="2" cellspacing="1"><tbody><tr class="cbg1">
                            <td colspan="3">In Ausbildung</td><td>Dauer</td><td>Fertig</td></tr>';


                        $dauer=0;
                        for ($i=1;$i<=$recrut4_data['anzahl'];$i++)
                        {
                            $dauer+=$recrut4_data[$i]['anzahl']*$recrut4_data[$i]['dauer'];
                            if ($i==1) $dauer-=$recrut4_data[$i]['dauer']-(strtotime($recrut4_data[$i]['zeit'])-time());

                            echo'<tr><td width="5%"><img class="unit" src="img/un/u/'.$recrut4_data[$i]['id'].
                            '.gif" border="0"></td><td align="right" width="6%">'.$recrut4_data[$i]['anzahl'].'&nbsp;</td>
                            <td class="s7" width="39%">'.$troops[$recrut4_data[$i]['id']]['name'].'</td>
                            <td width="25%"><span id="timer'.($i+1).'">'.
                            zeit_dauer($dauer).'</span></td>
                            <td width="25%">'.date('H:i',$dauer+time()).'<span> Uhr </span>'.
                            date('d.m.Y',$dauer+time()).'</td></tr>';
                        }
                        echo'<tr class="cbg1" align="center"><td colspan="5">
                            Fertigstellung der nächsten Einheit in <span id="timer1">'.
                            zeit_dauer(strtotime($recrut4_data[1]['zeit'])-time()).'</span></td>
                            </tr></tbody></table>';
                    }
	        	}
        	}
        	if ($_GET['s']==2)
        	{
        		echo'<p>Um dein Reich zu vergrößern benötigst du Kulturpunkte. Diese nehmen mit der Zeit zu. '.
        			'Je weiter deine Gebäude ausgebaut sind, desto schneller.</p><table class="f10" cellpadding="0" cellspacing="4" width="100%">
        			<tbody><tr><td width="250">Produktion dieses Dorfes:</td><td><b>'.round($dorf_data['einwohner']/2).'</b> Kulturpunkte pro
        			Tag</td></tr>
        			<tr><td width="250">Produktion aller Dörfer:</td><td><b>'.round($spieler_data['einwohner']/2).'</b> Kulturpunkte pro Tag</td></tr>
        			</tbody></table><p>Insgesamt haben deine Dörfer bis jetzt <b>'.round($spieler_data['kps']).'</b> Punkte erwirtschaftet.
        			Um ein weiteres Dorf zu gründen oder zu erobern, würdest du <b>'.($neue_dorfer[$anz_dorfer-1]*1000).'</b> Punkte benötigen.</p>';
        	}
        	if ($_GET['s']==3)
        	{
        		echo'Durch Angriffe mit Senatoren, Stammesführern oder Häuptlingen kann die Zustimmung gesenkt werden. Sinkt die Zustimmung auf Null, '.
        			'schließt sich die Bevölkerung des Dorfes dem Reich des Angreifers an. '.
        			'Die Zustimmung in diesem Dorf liegt bei <b>'.$dorf_data['zustimmung'].'</b> Prozent.';
        	}
        	if ($_GET['s']==4)
        	{
        		$expansion=split(':',$dorf_data['expansion']);
        		for ($i=1;$i<=$anz_dorfer;$i++)
        		{
        			$data=mysql_fetch_array($result);
        			$dd[$data['x']][$data['y']]=$data;
        		}

        		echo'<table class="tbg" cellpadding="2" cellspacing="1"><tbody><tr><td class="rbg" colspan="4"><a name="h2"></a>
        			Von diesem Dorf gegründete oder eroberte Dörfer</td></tr><tr><td width="6%">&nbsp;</td><td width="25%">Dorf</td>
        			<td width="17%">Einwohner</td><td width="17%">Koordinaten</td></tr>';
        		if ($expansion[0]==0)
        			echo'<tr><td colspan="4" class="c">Von diesem Dorf aus wurde noch kein anderes Dorf gegründet/erobert.</td></tr>';
        		else
        		{
        			for ($i=1;$i<=$expansion[0];$i++)
        			{
        				$data=$dd[$expansion[$i*2-1]][$expansion[$i*2]];
        				echo'<tr><td align="right">'.$i.'.&nbsp;</td><td class="s7">'.$data['name'].'</td>
        					<td>'.$data['einwohner'].'</td><td><table class="f10" cellpadding="0" cellspacing="0">
        					<tbody><tr><td align="right" width="35">('.$expansion[$i*2-1].'</td><td width="2">|</td><td align="left" width="35">
        					'.$expansion[$i*2].')</td></tr></tbody></table></td></tr>';
        			}
        		}
        		echo'</tbody></table>';
        	}
        }
        elseif ($id==28)				//Handelkontor
        {
        	$stragen=500;
        	if ($spieler_volk==2) $stragen=1000;
        	if ($spieler_volk==3) $stragen=750;
        	$tragen=$stragen+50*$stufe;
        	$ntragen=$stragen+50*$nachste_stufe;
        	echo'<table class="f10" cellpadding="0" cellspacing="4" width="100%"><tbody><tr><td width="250">
				Aktuelle Tragfähigkeit:</td><td><b>'.$tragen.'</b> Einheiten</td>
				</tr><tr><td width="250">Tragfähigkeit bei Stufe '.$nachste_stufe.':</td>
				<td><b>'.$ntragen.'</b> Einheiten</td></tr></tbody></table>';
        }
        elseif ($id>=31 AND $id<=33)	//Mauern und Wälle
        {
        	if ($spieler_data['volk']==1) {$ver_bon_akt=round(85*$stufe/20); $ver_bon_nex=round(85*$nachste_stufe/20); }
        	if ($spieler_data['volk']==2) {$ver_bon_akt=round(50*$stufe/20); $ver_bon_nex=round(50*$nachste_stufe/20); }
        	if ($spieler_data['volk']==3) {$ver_bon_akt=round(65*$stufe/20); $ver_bon_nex=round(65*$nachste_stufe/20); }
            echo'<table class="f10" cellpadding="0" cellspacing="4" width="100%">
                <tbody><tr>
                <td width="250">Aktueller Verteidigungsbonus:</td><td><b>'.$ver_bon_akt.'</b> Prozent</td>
                </tr><tr>
                <td width="250">Verteidigungsbonus bei Stufe '.$nachste_stufe.':</td>
                <td><b>'.$ver_bon_nex.'</b> Prozent</td>
                </tr></tbody></table>';
        }
        elseif ($id==34)
        {
        	echo'<table cellpadding="0" cellspacing="4" width="100%"><tbody><tr><td width="250">Aktuelle Stabilität</td>
        		<td><b>'.(100+$stufe*10).'</b> Prozent</td></tr><tr>
        		<td width="250">Stabilität bei Stufe '.$nachste_stufe.':</td><td><b>'.
        		(100+$nachste_stufe*10).'</b> Prozent</td></tr></tbody></table>';
        }
        elseif ($id==35)
        {
        	echo'<table cellpadding="0" cellspacing="4" width="100%"><tbody><tr><td width="250">Aktueller Boni für
        		Feste</td><td><b>+'.($stufe*10).'</b> Prozent</td></tr><tr>
        		<td width="250">Boni für Feste bei Stufe '.$nachste_stufe.':</td><td><b>+'.
        		($nachste_stufe*10).'</b> Prozent</td></tr></tbody></table>';
        }
        else
            echo'<p class="c">Noch keine Details für dieses Gebäude</p><br>';
	}
	else
		echo'<p class="c">Das Gebäude wurde noch nicht fertiggestellt</p><br>';



    //Ausbau des Gebäudes
    if ($stufe<$gebeude[$id]['stufen'])
    {
        echo'<p><b>Kosten</b> für Ausbau auf Stufe '.$nachste_stufe.':
            </p><table class="f10"><tbody><tr><td>
            <img class="res" src="img/un/r/1.gif">'.$kosten[0].' |
            <img class="res" src="img/un/r/2.gif">'.$kosten[1].' |
            <img class="res" src="img/un/r/3.gif">'.$kosten[2].' |
            <img class="res" src="img/un/r/4.gif">'.$kosten[3].' |
            <img class="res" src="img/un/r/5.gif">'.$gebeude[$id]['arbeiter'].' |
            <img class="clock" src="img/un/a/clock.gif" height="12" width="18"> '.
            zeit_dauer($gebeude[$id]['bauzeit'][$nachste_stufe]).'</td></tr></tbody></table>';



        if ($build==0)  echo'<span class="c">Es wird bereits gebaut</span>';
        if ($build==-1) echo'<span class="c">Dieses Gebäude wird bereits ausgebaut</span>';
        if ($build==1)
        {
            if ($lager[0]>=$kosten[0] AND $lager[1]>=$kosten[1] AND $lager[2]>=$kosten[2] AND
                $lager[3]>=$kosten[3]) //Genug Rohstoffe
            {
                if ($produktion[3]-$dorf_data['einwohner']-$gebeude[$id]['arbeiter']>2 OR       //kein Nahrungsmangel
                    $gebeude[$id]['name']=='Getreidefarm')                                 //oder Getreidefarm ausbauen
                {
                	if ($gid<19)
                		echo'<a href="dorf1.php?id='.$id.'&gid='.$gid.'">Ausbau auf Stufe '.$nachste_stufe.'</a>';
                	if ($gid>18)
                		echo'<a href="dorf2.php?id='.$id.'&gid='.$gid.'">Ausbau auf Stufe '.$nachste_stufe.'</a>';
                }
                else    //Nahrungsmangel
                    echo'<span class="c">Nahrungsmangel: Erst eine Getreidefarm ausbauen</span>';
            }
            else    //Zuwenig Rohstoffe
            {
                $lg[0]=0;$lg[1]=0;  //Lagergrösse genug gross?
                if ($lager_grosse[0]<$kosten[0] OR $lager_grosse[0]<$kosten[1] OR
                    $lager_grosse[0]<$kosten[2]) $lg[0]=1;
                if ($lager_grosse[1]<$kosten[3]) $lg[1]=1;
                if ($lg[0]==1 OR $lg[1]==1)
                {
                    if ($lg[0]==1 AND $lg[1]==1)
                        echo'<span class="c">Zuerst Rohstofflager und Kornspeicher ausbauen</span>';
                    if ($lg[0]==1 AND $lg[1]==0)
                        echo'<span class="c">Zuerst Rohstofflager ausbauen</span>';
                    if ($lg[0]==0 AND $lg[1]==1)
                        echo'<span class="c">Zuerst Kornspeicher ausbauen</span>';
                }
                else
                {
                	if ($produktion[3]>0)
                	{
                    	$time_x[0]=($kosten[0]-$lager[0])/$produktion[0]*3600;
                    	$time_x[1]=($kosten[1]-$lager[1])/$produktion[1]*3600;
                    	$time_x[2]=($kosten[2]-$lager[2])/$produktion[2]*3600;
                    	$time_x[3]=($kosten[3]-$lager[3])/$produktion[3]*3600;

                    	$timed=max($time_x);
	                    $word='';
	                    if (date('d.m.Y',time())==date('d.m.Y',time()+$timed)) $word='heute';
	                    if (date('d.m.Y',time()+86400)==date('d.m.Y',time()+$timed)) $word='morgen';
	                    if ($word=='') $word='am '.date('d.m.Y',time()+$timed);

	                    echo'<span class="c"><span>Genug Rohstoffe '.$word.' um '.date('H:i',time()+$timed).'</span>'.
                        '<span> Uhr</span></span>';
					}
					else
						echo'<span class="c"><span>Ihre Getreideproduktion ist negativ.</span></span>';
                }
            }
        }
    }
    else
        echo'<p class="c">'.$gebeude[$id]['name'].' vollständig ausgebaut</p>';
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