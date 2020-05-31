<?php


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
	$sql="DELETE FROM `tr".ROUND_ID."_others` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `id`='$gid' AND `typ`='10';";
	$result=mysql_query($sql);

    $neu_lager[0]=$lager[0]+$gebeude[$id]['kosten_holz'][$stufe];
    $neu_lager[1]=$lager[1]+$gebeude[$id]['kosten_lehm'][$stufe];
    $neu_lager[2]=$lager[2]+$gebeude[$id]['kosten_eisen'][$stufe];
    $neu_lager[3]=$lager[3]+$gebeude[$id]['kosten_getreide'][$stufe];

    $l_lager[4]=$lager[4]-$gebeude[$id]['arbeiter'];

	//Einwohner und Rohstoffe aktualisieren
	$sql="UPDATE `tr".ROUND_ID."_dorfer` SET `einwohner`='".$l_lager[4]."', `lager`='".implode(':',$neu_lager)."'";
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
	    $sql="SELECT * FROM `tr".ROUND_ID."_others` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `typ`='10';";
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
            		$sql="INSERT INTO `tr".ROUND_ID."_others` (`x`,`y`,`id`,`zeit`,`typ`) VALUES ('$dorfx','$dorfy','$gid','".
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
            		$sql="UPDATE `tr".ROUND_ID."_dorfer` SET `einwohner`='".$l_lager[4]."', `lager`='".
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
