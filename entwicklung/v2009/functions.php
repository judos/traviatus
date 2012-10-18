<?php
//---------------------------------------------------------------------------------------------------------------------
//IIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII
//---------------------------------------------------------------------------------------------------------------------
global $round_id,$ri;

$round_id=1;
$ri=1;
//---------------------------------------------------------------------------------------------------------------------
//IIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII
//---------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function change_village()
{
global $dorfx,$dorfy;
if (isset($_GET['ndx']) AND isset($_GET['ndy']))
{
	setcookie('dorfx',$_GET['ndx']);
	setcookie('dorfy',$_GET['ndy']);
	$dorfx=$_GET['ndx'];
	$dorfy=$_GET['ndy'];
}
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function connect()
{
//zur Datenbank verbinden
$link = mysql_pconnect('localhost','root','localsqlpw');
if (!$link)
    {die('Verbindung nicht möglich : ' . mysql_error());}
if(!mysql_select_db('traviatus2010'))
    {die('Fehler Datenbank konnte nicht ausgewählt werden.');}
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function dorfer($userid,$dorfx,$dorfy)
{
global $round_id;
echo'<div id="lright1">
	<a href="dorf3.php"><span class="f10 c0 s7 b">Dörfer:</span></a>
	<table class="f10">';
$sql="SELECT * FROM `tr".$round_id."_dorfer` WHERE `user`='$userid' ORDER BY `name` ASC;";
$result=mysql_query($sql);
$anz=mysql_num_rows($result);
for ($i=1;$i<=$anz;$i++)
{
	$data=mysql_fetch_array($result);
	$x=$data['x'];$y=$data['y'];$name=$data['name'];
	echo'<tr><td class="nbr"><span';
	if ($x==$dorfx AND $y==$dorfy) echo' class="c2"';
	echo'>&#8226;</span>&nbsp; <a href="?ndx='.$x.'&ndy='.$y.'"';
	if ($x==$dorfx AND $y==$dorfy) echo' class="active_vl"';
	echo'>'.$name.'</a></td><td class="right"><table class="dtbl" cellspacing="0" cellpadding="0"><tr>'.
	'<td class="right dlist1">('.$x.'</td><td class="center dlist2">|</td><td class="left dlist3">'.$y.')</td></tr>'.
	'</table></td></tr>';
}
echo'</table></div>';
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function kampfsim($troops,$army1,$army2,$typ,$f=0.25)
// $troops=allgemeine infos über die truppen, $army1;$army2= array(id 1 bis 30), $typ (1=normal, 2=raubzug)
{

for ($id=1;$id<=30;$id++)	//Blöcke bilden
{
//	if ($army1[$id]>0)
//	{
		$block1a[$troops[$id]['typ']][$id]=$army1[$id]*$troops[$id]['off'];
		$block1d[1][$id]=$army1[$id]*$troops[$id]['deff1'];
		$block1d[2][$id]=$army1[$id]*$troops[$id]['deff2'];

//		echo 'deff2:'.$troops[$id]['deff2'].',';
//		echo 'id:'.$id.', anzahl:'.$army1[$id].', $block1d[2][$id]='.$block1d[2][$id].'<br>';
//	}
//	if ($army2[$id]>0)
//	{
		$block2a[$troops[$id]['typ']][$id]=$army2[$id]*$troops[$id]['off'];
		$block2d[1][$id]=$army2[$id]*$troops[$id]['deff1'];
		$block2d[2][$id]=$army2[$id]*$troops[$id]['deff2'];
//	}
}
for ($j=1;$j<=3;$j++)		//Summen ausrechnen
{

	$block1a[$j]['tot']=array_sum($block1a[$j]);
	if (isset($block1d[$j])) $block1d[$j]['tot']=array_sum($block1d[$j]);
	$block2a[$j]['tot']=array_sum($block2a[$j]);
	if (isset($block2d[$j])) $block2d[$j]['tot']=array_sum($block2d[$j]);
}


while ($block1d[1]['tot']>0 AND $block2d[1]['tot']>0 AND $block1d[2]['tot'] AND $block2d[2]['tot'])
{
	//Kavallerie Angriff
	$angriff=$block1a[2]['tot'];
//	echo 'Kavallerie att:'.$angriff.'<br>';
	for ($id=1;$id<=30;$id++)
	{
		if ($army2[$id]>0)
		{
			$komponente=$angriff*$f/$block2d[2]['tot'];
			if ($komponente>1) $komponente=1;
			$block2d_neu[2][$id]=$block2d[2][$id]*(1-$komponente);
			$block2d_neu[1][$id]=$block2d[1][$id]*$block2d_neu[2][$id]/$block2d[2][$id];
			for ($j=1;$j<=3;$j++)
				$block2a[$j][$id]=$block2a[$j][$id]*$block2d_neu[2][$id]/$block2d[2][$id];
		}
	}
	$block2d=$block2d_neu;
	unset($block2d_neu);
	for ($i=1;$i<=3;$i++)
	{
		$block2d[$i]['tot']=0;
		$block2d[$i]['tot']=array_sum($block2d[$i]);
		$block2a[$i]['tot']=0;
		$block2a[$i]['tot']=array_sum($block2a[$i]);
	}
	if (round($block2d[2]['tot'])==0) break;

	//Infanterie Angriff
	$angriff=$block1a[1]['tot'];
//	echo'Infanterie2 Leben vorher:'.$block2d[1]['tot'].'<br>';
//	echo'Infanterie att:'.$angriff.'<br>';
	for ($id=1;$id<=30;$id++)
	{
		if ($army2[$id]>0)
		{
			$komponente=$angriff*$f/$block2d[1]['tot'];
			if ($komponente>1) $komponente=1;
			$block2d_neu[1][$id]=$block2d[1][$id]*(1-$komponente);
			$block2d_neu[2][$id]=$block2d[2][$id]*$block2d_neu[1][$id]/$block2d[1][$id];
			for ($j=1;$j<=3;$j++)
				$block2a[$j][$id]=$block2a[$j][$id]*$block2d_neu[1][$id]/$block2d[1][$id];
		}
	}
	$block2d=$block2d_neu;
	unset($block2d_neu);
	for ($i=1;$i<=3;$i++)
	{
		$block2d[$i]['tot']=0;
		$block2d[$i]['tot']=array_sum($block2d[$i]);
		$block2a[$i]['tot']=0;
		$block2a[$i]['tot']=array_sum($block2a[$i]);
	}
//	echo'Infanterie2 Leben:'.$block2d[1]['tot'].'<br><br>';
	if (round($block2d[1]['tot'])==0) break;

	//Kavallerie Abwehr
	$angriff=$block2a[2]['tot'];
//	echo'Kavallerie def:'.$angriff.'<br>';
	for ($id=1;$id<=30;$id++)
	{
		if ($army1[$id]>0)
		{
			$komponente=$angriff*$f/$block1d[2]['tot'];
			if ($komponente>1) $komponente=1;
			$block1d_neu[2][$id]=$block1d[2][$id]*(1-$komponente);
			$block1d_neu[1][$id]=$block1d[1][$id]*$block1d_neu[2][$id]/$block1d[2][$id];
			for ($j=1;$j<=3;$j++)
				$block1a[$j][$id]=$block1a[$j][$id]*$block1d_neu[2][$id]/$block1d[2][$id];
		}
	}
	$block1d=$block1d_neu;
	unset($block1d_neu);
	for ($i=1;$i<=3;$i++)
	{
		$block1d[$i]['tot']=0;
		$block1d[$i]['tot']=array_sum($block1d[$i]);
		$block1a[$i]['tot']=0;
		$block1a[$i]['tot']=array_sum($block1a[$i]);
	}
	if (round($block1d[2]['tot'])==0) break;

	//Infanterie Abwehr
	$angriff=$block2a[1]['tot'];
//	echo'Infanterie1 leben vorher:'.$block1d[1]['tot'].'<br>';
//	echo'Infanterie def:'.$angriff.'<br>';
	for ($id=1;$id<=30;$id++)
	{
		if ($army1[$id]>0)
		{
			$komponente=$angriff*$f/$block1d[1]['tot'];
			if ($komponente>1) $komponente=1;
//			echo'Komponente: '.$komponente.'<br>';
			$block1d_neu[1][$id]=$block1d[1][$id]*(1-$komponente);
			$block1d_neu[2][$id]=$block1d[2][$id]*$block1d_neu[1][$id]/$block1d[1][$id];
			for ($j=1;$j<=3;$j++)
				$block1a[$j][$id]=$block1a[$j][$id]*$block1d_neu[1][$id]/$block1d[1][$id];
		}
	}
	$block1d=$block1d_neu;
	unset($block1d_neu);
	for ($i=1;$i<=3;$i++)
	{
		$block1d[$i]['tot']=0;
		$block1d[$i]['tot']=array_sum($block1d[$i]);
		$block1a[$i]['tot']=0;
		$block1a[$i]['tot']=array_sum($block1a[$i]);
	}
//	echo'Infanterie1 leben:'.$block1d[1]['tot'].'<br><br>';
	if (round($block1d[1]['tot'])==0) break;

	if ($typ==2) break;
}

for ($id=1;$id<=30;$id++)
{
	if ($army1[$id]>0)
	{
		if ($block1d[1][$id]<0) $block1d[1][$id]=0;
		if ($block1d[2][$id]<0) $block1d[2][$id]=0;
		$army1[$id]=round( ($block1d[1][$id]/$troops[$id]['deff1'] + $block1d[2][$id]/$troops[$id]['deff2'])/2 ,0);
	}
	if ($army2[$id]>0)
	{
		if ($block2d[1][$id]<0) $block2d[1][$id]=0;
		if ($block2d[2][$id]<0) $block2d[2][$id]=0;
		$army2[$id]=round( ($block2d[1][$id]/$troops[$id]['deff1'] + $block2d[2][$id]/$troops[$id]['deff2'])/2 ,0);
	}
}

$army[1]=$army1;
$army[2]=$army2;
return $army;
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function lager($dorf_data,$land_produktion,$lager,$lager_grosse,$versorgung)
{
echo'<div id="lres0">'.
	'<table align="center" cellspacing="0" cellpadding="0"><tr valign="top">'.
	'<td><img class="res" src="img/un/r/1.gif" title="Holz"></td>'.
	'<td id=l4 title='.$land_produktion[0].'>'.floor($lager[0]).'/'.$lager_grosse[0].'</td>'.
	'<td class="s7"><img class="res" src="img/un/r/2.gif" title="Lehm"></td>'.
	'<td id=l3 title='.$land_produktion[1].'>'.floor($lager[1]).'/'.$lager_grosse[0].'</td>'.
	'<td class="s7"> <img class="res" src="img/un/r/3.gif" title="Eisen"></td>'.
	'<td id=l2 title='.$land_produktion[2].'>'.floor($lager[2]).'/'.$lager_grosse[0].'</td>'.
	'<td class="s7"> <img class="res" src="img/un/r/4.gif" title="Getreide"></td>'.
	'<td id=l1 title='.($land_produktion[3]-$lager[4]-$versorgung).'>'.floor($lager[3]).'/'.$lager_grosse[1].'</td>'.
	'</tr><tr><td colspan="8" class="r7">'.
	'<img class="res" src="img/un/r/5.gif" title="Getreideverbrauch">'.
	'&nbsp;'.($lager[4]+$versorgung).'/'.$land_produktion[3].
	'</td></tr></table>'.
//	'<td class="s7"> &nbsp;<img class="res" src="img/un/r/5.gif" title="Getreideverbrauch">'.
//	'&nbsp;'.($lager[4]+$versorgung).'/'.$land_produktion[3].'</td>'.
//	'</tr></table></div>';
	'</div>';
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function lager_grosse($dorf_data)
{
global $round_id;
//Lagergrösse der Stufen
$sql="SELECT `value` FROM `tr".$round_id."_diverses` WHERE `id`='lager';";
$result=mysql_query($sql);
$data=mysql_fetch_array($result);
$lager_grosse['allgemein']=split(':',$data['value']);
//Lager grösse berechnen
$lager_grosse[0]=0;
$lager_grosse[1]=0;
$geb2_typ=split(':',$dorf_data['geb2t']);
$geb2_stufe=split(':',$dorf_data['geb2']);
for ($i=19;$i<=40;$i++)
{	//Rohstofflager
	if ($geb2_typ[$i-19]==10) $lager_grosse[0]=$lager_grosse[0]+$lager_grosse['allgemein'][$geb2_stufe[$i-19]-1]*100;
	//Kornspeicher
	if ($geb2_typ[$i-19]==11) $lager_grosse[1]=$lager_grosse[1]+$lager_grosse['allgemein'][$geb2_stufe[$i-19]-1]*100;
	//Grosses Rohstofflager
	if ($geb2_typ[$i-19]==38) $lager_grosse[0]=$lager_grosse[0]+$lager_grosse['allgemein'][$geb2_stufe[$i-19]-1]*300;
	if ($geb2_typ[$i-19]==39) $lager_grosse[1]=$lager_grosse[1]+$lager_grosse['allgemein'][$geb2_stufe[$i-19]-1]*300;
}	//^^Grosser Kornspeicher
if ($lager_grosse[0]==0) $lager_grosse[0]=800;
if ($lager_grosse[1]==0) $lager_grosse[1]=800;
//Versteck
$sql="SELECT `value` FROM `tr".$round_id."_diverses` WHERE `id`='versteck';";
$result=mysql_query($sql);
$data=mysql_fetch_array($result);
$lager_grosse['versteck']=split(':',$data['value']);
return $lager_grosse;
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function links()
{
echo'<div id="lleft">'.
	'<a href="http://www.travian.de/"><img class="logo" src="img/de/a/travian0.gif"></a>'.
	'<table id="navi_table" cellspacing="0" cellpadding="0">'.
	'<tr><td class="menu">'.
	//Neue Dateien
	'<a href="spieler.php">Profil</a>'.
	'<a href="#" onclick="Popup(0,0); return false;">Anleitung</a>'.
	'<a href="logout.php">Logout</a>'.
	'<a href="admintools.php">Admintools</a>'.
	'</td></tr>'.
	'</table></div>';
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function load_gebeude($dorf_data,$land_typ_data,$spieler_volk=NULL)
{
global $round_id;
$land_geb=split(':',$land_typ_data['geb']);
//Gebäude Stufen des Landes
$geb1_stufe=split(':',$dorf_data['geb1']);
//Gebäude Stufen des Dorfes
$geb2_stufe=split(':',$dorf_data['geb2']);
$geb2_typ=split(':',$dorf_data['geb2t']);

for ($i=1;$i<=40;$i++)
{
    if ($i<19) {$id=$land_geb[$i-1]; $stufe=$geb1_stufe[$i-1]; }
    if ($i>18) {$id=$geb2_typ[$i-19]; $stufe=$geb2_stufe[$i-19]; }
    $gebeude[$id]['anzahl']++;
    if ($gebeude[$id]['highest']<$stufe)
    {
    	$gebeude[$id]['highest']=$stufe;
    	$gebeude[$id]['highid']=$i;
    }
}

$sql="SELECT * FROM `tr".$round_id."_gebeude`;";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++)
{
	$data=mysql_fetch_array($result);
	$id=$data['id'];

	if ($id>$gebeude['anzahl']) $gebeude['anzahl']=$id;

	$gebeude[$id]['name']=$data['name'];
	$gebeude[$id]['besch']=$data['besch'];
	$gebeude[$id]['typ']=$data['typ'];
	$gebeude[$id]['stufen']=$data['stufen'];
	$gebeude[$id]['arbeiter']=$data['arbeiter'];
	$gebeude[$id]['rebuild']=$data['rebuild'];

    $kosten=split(':',$data['baukosten']);
    for ($j=1;$j<=$data['stufen'];$j++)
    {
        $gebeude[$id]['kosten_holz'][$j]=5*round($kosten[0]*pow($kosten[4],$j-1)/5);
        $gebeude[$id]['kosten_lehm'][$j]=5*round($kosten[1]*pow($kosten[4],$j-1)/5);
        $gebeude[$id]['kosten_eisen'][$j]=5*round($kosten[2]*pow($kosten[4],$j-1)/5);
        $gebeude[$id]['kosten_getreide'][$j]=5*round($kosten[3]*pow($kosten[4],$j-1)/5);
    }


    $zeit=split(':',$data['bauzeit']);
    $gebeude[$id]['start_bauzeit']=$zeit[0];
    $HG_stufe=$gebeude[15]['highest'];
    for ($j=1;$j<=$data['stufen'];$j++)
    {
        if ($j==1)  {$gebeude[$id]['bauzeit'][$j]=$zeit[0];}
        else        {$gebeude[$id]['bauzeit'][$j]=$zeit[0]+round($zeit[1]*pow($zeit[2],$j-2)/10,0)*10;}
        $gebeude[$id]['bauzeit'][$j]=round($gebeude[$id]['bauzeit'][$j]*round(100-60*(($HG_stufe-1)/19))/100);
    }


    $ok=1;
    $needs=split(':',$data['needs']);
    $gebeude[$id]['needs']=$needs;
    if ($needs[0]>-1 AND $data['needs']!='')
    {
        for ($j=1;$j<=$needs[0];$j++)
    	{
    		if ($needs[2*$j-1]>0) { if ($gebeude[$needs[2*$j-1]]['highest']<$needs[2*$j]) $ok=0; }
    		if ($needs[2*$j-1]==-1) { if ($spieler_volk!=$needs[2*$j]) $ok=0; }
        }
    }
    elseif ($needs[0]!='' AND $needs[0]==0) $ok=1;
    else   	$ok=0;

//    echo'id:'.$id.',high:'.$highest[$id].',anzahl:'.$gebeude[$id]['anzahl'].',ok:'.$ok;
    if ($ok==1 AND $gebeude[$id]['anzahl']==1)
    {
        if ($data['rebuild']<1 AND $gebeude[$id]['highest']>=0) $ok=0;
        if ($data['rebuild']>0 AND $gebeude[$id]['highest']<$data['rebuild']) $ok=0;

//        echo',rebuild:'.$data['rebuild'];
    }
    if ($data['rebuild']>0 AND $gebeude[$id]['highest']>=$data['rebuild']) $ok=1;

    if ($id==25 AND $dorf_data['grosse']==1) $ok=0;	//Residenz im Hauptdorf nicht baubar
    if ($id==26 AND $dorf_data['grosse']==0) $ok=0; //Palast im Nebendorf nicht baubar

//    echo'<br>'.$ok;
    $gebeude[$id]['ok']=$ok;
}

return $gebeude;
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function load_lager($dorf_data)
{
$lager=split(':',$dorf_data['lager']);
$lager[4]=$dorf_data['einwohner'];
return $lager;
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function load_produktion($dorf_data,$land_data)
{
global $round_id;
//Gebäude Stufen des Landes
$geb1_stufe=split(':',$dorf_data['geb1']);
$geb2_typ=split(':',$dorf_data['geb2t']);
$geb2_stufe=split(':',$dorf_data['geb2']);
for ($i=1;$i<=40;$i++)
    $highest[$i]=0;
for ($i=11;$i<=40;$i++)
{
    $id=$geb2_typ[$i-19];
    $stufe=$geb2_stufe[$i-19];
    if ($highest[$id]<$stufe) $highest[$id]=$stufe;
}
//Produktion der Stufen
$sql="SELECT `value` FROM `tr".$round_id."_diverses` WHERE `id`='produktion';";
$result=mysql_query($sql);
$data=mysql_fetch_array($result);
$produktion=split(':',$data['value']);

$typ=$land_data['typ'];

//Verteilung der Rohstoffgebäude
$sql="SELECT `geb` FROM `tr".$round_id."_land_typen` WHERE `typ`='".$typ."';";
$result=mysql_query($sql);
$data=mysql_fetch_array($result);
$land_geb=split(':',$data['geb']);
$land_produktion=array(0,0,0,0);
for ($i=1;$i<=18;$i++)
	$land_produktion[$land_geb[$i-1]-1]+=$produktion[$geb1_stufe[$i-1]];
$land_produktion[0]=round($land_produktion[0]*(1+0.05*$highest[5]),0);
$land_produktion[1]=round($land_produktion[1]*(1+0.05*$highest[6]),0);
$land_produktion[2]=round($land_produktion[2]*(1+0.05*$highest[7]),0);
$land_produktion[3]=round($land_produktion[3]*(1+0.05*($highest[8]+$highest[9])),0);

$land_produktion['allgemein']=$produktion;
return $land_produktion;
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function load_troops($dorf_data,$spieler_data)
{
global $round_id;
$research=split(':',$spieler_data['research']);
$geb2_typ=split(':',$dorf_data['geb2t']);
$geb2_stufe=split(':',$dorf_data['geb2']);
for ($i=11;$i<=40;$i++)
{
    $id=$geb2_typ[$i-19];
    $stufe=$geb2_stufe[$i-19];
    if ($highest[$id]<$stufe) $highest[$id]=$stufe;
}

$stufen_w=split(':',$spieler_data['weapons']);
$stufen_r=split(':',$spieler_data['arms']);

$sql="SELECT * FROM `tr".$round_id."_truppen_typen`;";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++)
{
	$data=mysql_fetch_array($result);
	$id=$data['id'];
	$troops[$id]['name']=$data['name'];
	$troops[$id]['namepl']=$data['mehrzahl'];
	if ($troops[$id]['namepl']=='') $troops[$id]['namepl']=$data['name'];
	$troops[$id]['besch']=$data['besch'];
	$troops[$id]['typ']=$data['typ'];
	$troops[$id]['spio']=$data['spio'];
	$troops[$id]['versorgung']=$data['versorgung'];
	$troops[$id]['speed']=$data['speed'];
	$troops[$id]['tragen']=$data['tragen'];
	$troops[$id]['volk']=$data['volk'];

	$werte=split(':',$data['werte']);
	$troops[$id]['off']=$werte[0];
	$troops[$id]['deff1']=$werte[1];
	$troops[$id]['deff2']=$werte[2];

	$kosten=split(':',$data['baukz']);
	$troops[$id]['kosten_holz']=$kosten[0];
	$troops[$id]['kosten_lehm']=$kosten[1];
	$troops[$id]['kosten_eisen']=$kosten[2];
	$troops[$id]['kosten_getreide']=$kosten[3];
	$troops[$id]['start_bauzeit']=$kosten[4];
	if ($data['typ']==1) $troops[$id]['bauzeit']=round($kosten[4]*(100-4.8*($highest[19]-1))/100);
	if ($data['typ']==2) $troops[$id]['bauzeit']=round($kosten[4]*(100-4*($highest[20]-1))/100);
	if ($data['typ']==3) $troops[$id]['bauzeit']=round($kosten[4]*(100-4*($highest[21]-1))/100);
	if ($data['typ']==4)
	{
		if ($highest[25]>$highest[26]) $troops[$id]['bauzeit']=round($kosten[4]*(100-4*($highest[25]-1))/100);
		else $troops[$id]['bauzeit']=round($kosten[4]*(100-4*($highest[26]-1))/100);
	}
	$needs=split(':',$data['needs']);
	$troops[$id]['needs']=$needs;
	$ok=1;
	for ($j=1;$j<=$needs[0];$j++)
		if ($highest[$needs[$j*2-1]]<$needs[$j*2]) $ok=0;
	if ($data['needs']==-1) $ok=0;
	$troops[$id]['ok1']=$ok;
	$ok=0;
	if ($research[$id-($spieler_data['volk']-1)*10-1]==1) $ok=1;
	if ($data['needs']==-1) $ok=1;
	$troops[$id]['ok2']=$ok;

	if ($spieler_data['volk']!=$troops[$id]['volk'] OR $data['needs']=='')
		{$troops[$id]['ok1']=0;$troops[$id]['ok2']=0;}

	$kosten=split(':',$data['reskost']);
	$troops[$id]['forsch_holz']=$kosten[0];
	$troops[$id]['forsch_lehm']=$kosten[1];
	$troops[$id]['forsch_eisen']=$kosten[2];
	$troops[$id]['forsch_getreide']=$kosten[3];
	$troops[$id]['forsch_zeit']=$kosten[4];

	if ($data['typ']<4)
	{
		$forsch=split(':',$data['forsch']);
		$faktor_w=pow($stufen_w[$id-($spieler_data['volk']-1)*10-1]+1,$forsch[4]);
		$faktor_r=pow($stufen_r[$id-($spieler_data['volk']-1)*10-1]+1,$forsch[4]);

		for ($j=0;$j<=3;$j++)
		{
			$troops[$id]['wr1'][$j]=5*round($faktor_w*$forsch[$j]/5);
			$troops[$id]['wr2'][$j]=5*round($faktor_r*$forsch[$j]/5);
		}
		$troops[$id]['wr1']['zeit']=round($faktor_w*$forsch[5]/100*(100-50*($highest[12]-1)/19));
		$troops[$id]['wr2']['zeit']=round($faktor_r*$forsch[5]/100*(100-50*($highest[13]-1)/19));
	}

}
return $troops;
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function top_links($username)
{
global $round_id;
//Neue Nachrichten oder Berichte?
$show=4;
$sql="SELECT * FROM `tr".$round_id."_msg` WHERE `an`='$username' AND `neu`='1' AND `von`!='' AND `typ`='0';";
$result=mysql_query($sql);
if (mysql_num_rows($result)>0) $show=$show-2;
$neue_berichte=0;
$sql="SELECT * FROM `tr".$round_id."_msg` WHERE `an`='$username' AND `neu`='1' AND `von`='';";
$result=mysql_query($sql);
if (mysql_num_rows($result)>0) $show=$show-1;
echo'<div id="ltop1"><div id="ltop3">'.
	'<a href="dorf1.php" id="navileft">'.
	'	<img id="n1" src="img/un/a/x.gif" title="Dorf&uuml;bersicht"></a>'.
	'<a href="dorf2.php"><img id="n2" src="img/un/a/x.gif" title="Dorfzentrum"></a>'.
	'<a href="karte.php"><img id="n3" src="img/un/a/x.gif" title="Karte"></a>'.
	'<a href="statistiken.php"><img id="n4" src="img/un/a/x.gif" title="Statistik"></a>'.
	//Neue Nachrichten oder Berichte
	'<img id="n5" src="img/un/l/m'.$show.'.gif" usemap="#nb">'.
	'</div></div>'.
	//Neue Dateien
	'<map name="nb">'.
	'<area shape=rect coords="0,0,35,100" href="berichte.php" title="Berichte">'.
	'<area shape=rect coords="35,0,70,100" href="nachrichten.php" title="Nachrichten">'.
	'</map>';
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function update_village($dorfx,$dorfy)
{
global $username,$round_id;
//Dorf daten laden
$sql="SELECT * FROM `tr".$round_id."_dorfer` WHERE `x`='$dorfx' AND `y`='$dorfy';";
$result=mysql_query($sql);
$dorf_data=mysql_fetch_array($result);
$userid=$dorf_data['user'];

//Land daten laden
$sql="SELECT * FROM `tr".$round_id."_lander` WHERE `x`='$dorfx' AND `y`='$dorfy';";
$result=mysql_query($sql);
$land_data=mysql_fetch_array($result);

//Spieler daten laden
$sql="SELECT * FROM `tr".$round_id."_user` WHERE `id`='".$dorf_data['user']."';";
$result=mysql_query($sql);
$spieler_data=mysql_fetch_array($result);

//Lager und Produktion und Truppen
$lager=load_lager($dorf_data);
$lager_grosse=lager_grosse($dorf_data);
$produktion=load_produktion($dorf_data,$land_data);
$troops=load_troops($dorf_data,$spieler_data);
$troops_village=load_troops_in_village($userid,$dorfx,$dorfy,$troops);

//Faktor in Produktions-stunden
$faktor=(time()-strtotime($dorf_data['update']))/3600;

//Gebäude1
$geb1=split(':',$dorf_data['geb1']);
$geb2=split(':',$dorf_data['geb2']);
$geb2t=split(':',$dorf_data['geb2t']);
$research=split(':',$spieler_data['research']);
$weapons=split(':',$spieler_data['weapons']);
$arms=split(':',$spieler_data['arms']);


//Auftrage bearbeiten
$sql="SELECT * FROM `tr".$round_id."_others` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `zeit`<=NOW();";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++)
{
	$data=mysql_fetch_array($result);
	if ($data['typ']==10)	//Hauptgebäude, Bau
	{
        if ($data['id']<19) $geb1[$data['id']-1]++;
        if ($data['id']>18) $geb2[$data['id']-19]++;
	}
	if ($data['typ']==9)	//Hauptgebäude, Abriss
	{
		$geb2[$data['id']-19]--;
		if ($geb2[$data['id']-19]==0) $geb2t[$data['id']-19]=0;
		if ($geb2[$data['id']-19]<0) $geb2[$data['id']-19]=0;
	}
	if ($data['typ']==8)	//Ratshaus
	{
		$spieler_data['kps']+=$data['dauer'];
	}
	if ($data['typ']==7)	//Rüstungsschmid
	{
		$arms[$data['id']-1-($spieler_data['volk']-1)*10]++;
	}
	if ($data['typ']==6)	//Waffenschmid
	{
		$weapons[$data['id']-1-($spieler_data['volk']-1)*10]++;
	}
	if ($data['typ']==5)	//Akademie
	{
		$research[($data['id']-1)%10]=1;
	}
	if ($data['typ']==1 OR $data['typ']==2 OR $data['typ']==3 OR $data['typ']==4)	//Kaserne, Stall, Werkstatt, Residenz/Palast
	{
		$x=ceil((time()-strtotime($data['zeit']))/$data['dauer']);
		if ($x>$data['anzahl']) $x=$data['anzahl'];
		$sql2="SELECT * FROM `tr".$round_id."_truppen` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `user`='$userid';";
		$result2=mysql_query($sql2);
		if (mysql_num_rows($result2)>0)		//Falls schon Truppen vorhanden sind
		{
			$data2=mysql_fetch_array($result2);
			$troops_here=split(':',$data2['troops']);
			$id=($data['id']-1)%10;
			$troops_here[$id]=$troops_here[$id]+$x;
			$troops_here=implode(':',$troops_here);
			$sql3="UPDATE `tr".$round_id."_truppen` SET `troops`='$troops_here'
				WHERE `x`='$dorfx' AND `y`='$dorfy' AND `user`='$userid';";
			$result3=mysql_query($sql3);
		}
		else								//Falls noch keine vorhanden sind -> neuer Datensatz
		{
			for ($j=0;$j<=9;$j++)
				$troops_here[$j]=0;
			$id=($data['id']-1)%10;
			$troops_here[$id]=$troops_here[$id]+$x;
			$troops_here=implode(':',$troops_here);
			$sql3="INSERT INTO `tr".$round_id."_truppen` (`x`,`y`,`user`,`troops`)
				VALUES ('$dorfx','$dorfy','$userid','$troops_here');";
			$result3=mysql_query($sql3);
		}
		if ($x<$data['anzahl'])				//Neue Anzahl speichern, falls nicht alle fertig wurden
		{
			$sql2="UPDATE `tr".$round_id."_others`
				SET `zeit`='".date('Y-m-d H:i:s',strtotime($data['zeit'])+$x*$data['dauer'])."',
				`anzahl`='".($data['anzahl']-$x)."'
				WHERE `x`='$dorfx' AND `y`='$dorfy' AND `typ`='".$data['typ']."' AND `id`='".$data['id']."';";
			$result2=mysql_query($sql2);
		}
	}
}
$sql="DELETE FROM `tr".$round_id."_others` WHERE `x`='$dorfx' AND `y`='$dorfy' AND `zeit`<=NOW();";
$result=mysql_query($sql);

//Gebäude 1 String formen
$geb1string=implode(':',$geb1);
$geb2string=implode(':',$geb2);
$geb2tstring=implode(':',$geb2t);
$researchstring=implode(':',$research);
$weaponsstring=implode(':',$weapons);
$armsstring=implode(':',$arms);

//Händler
$sql="SELECT * FROM `tr".$round_id."_handler` WHERE (`ursprung`='$dorfx:$dorfy' OR `nach`='$dorfx:$dorfy') AND `ziel`<='"
	.date('Y-m-d H:i:s',time())."';";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++)
{
	$data=mysql_fetch_array($result);

	$ress=split(':',$data['ress']);
	$ruckfahrt=date('Y-m-d H:i:s',2*strtotime($data['ziel'])-strtotime($data['start']));
	$von=split(':',$data['von']);
	$nach=split(':',$data['nach']);

	if ($data['nach']==$dorfx.':'.$dorfy)	//Rohstoffe im eigenen Dorf speichern
	{
		for ($j=0;$j<=3;$j++)
			$lager[$j]+=$ress[$j];
	}
	else									//Rohstoffe in einem anderen Dorf speichern
	{
		$sql2="SELECT `lager` FROM `tr".$round_id."_dorfer` WHERE `x`='".$nach[0]."' AND `y`='".$nach[1]."';";
		$result2=mysql_query($sql2);
		$data2=mysql_fetch_array($result2);
		$lager2=split(':',$data2['lager']);
		for ($j=0;$j<=3;$j++)
			$lager2[$j]+=$ress[$j];
		$lager2_string=implode(':',$lager2);
		$sql2="UPDATE `tr".$round_id."_dorfer` SET `lager`='$lager2_string' WHERE `x`='".$nach[0]."' AND `y`='".$nach[1]."';";
		$result2=mysql_query($sql2);
	}

	if ($data['ursprung']==$data['nach'])	//Falls Händler zuhause ist -> Datensatz löschen
	{
		$sql2="DELETE FROM `tr".$round_id."_handler` WHERE `ursprung`='".$data['ursprung']."' AND `von`='".$data['von'].
			"' AND `nach`='".$data['nach']."' AND `start`='".$data['start']."' AND `ziel`='".$data['ziel']."';";
		$result2=mysql_query($sql2);
	}
	else									//Andernfalls Händler umkehren lassen
	{
		$sql2="UPDATE `tr".$round_id."_handler` SET `nach`='".$data['von']."', `von`='".$data['nach']."', `ress`='0:0:0:0',
			`start`='".$data['ziel']."', `ziel`='".$ruckfahrt."'
			WHERE `ursprung`='".$data['ursprung']."' AND `von`='".$data['von'].
			"' AND `nach`='".$data['nach']."' AND `start`='".$data['start']."' AND `ziel`='".$data['ziel']."';";
		$result2=mysql_query($sql2);

		//Dorfnamen und Userids laden
		$sql2="SELECT `name`,`x`,`y`,`user` FROM `tr".$round_id."_dorfer`
			WHERE (`x`='$von[0]' AND `y`='$von[1]') OR (`x`='$nach[0]' AND `y`='$nach[1]');";
		$result2=mysql_query($sql2);
		$data2=mysql_fetch_array($result2);
		$dorf_name[$data2['x']][$data2['y']]=$data2['name'];
		$dorf_user[$data2['x']][$data2['y']]=$data2['user'];
		$data2=mysql_fetch_array($result2);
		$dorf_name[$data2['x']][$data2['y']]=$data2['name'];
		$dorf_user[$data2['x']][$data2['y']]=$data2['user'];

		$betreff=$dorf_name[$von[0]][$von[1]].' beliefert '.$dorf_name[$nach[0]][$nach[1]];
		if ($data['ursprung']==$dorfx.':'.$dorfy)
		{
			$spdo='<a href="spieler.php?name='.$username.'">'.$username.'</a> aus Dorf <a href="karte.php?do=show&x='.
				$dorfx.'&y='.$dorfy.'">'.$dorf_name[$dorfx][$dorfy].'</a>';
			$sql2="SELECT `name` FROM `tr".$round_id."_user` WHERE `id`='".$dorf_user[$nach[0]][$nach[1]]."';";
			$result2=mysql_query($sql2);
			$data2=mysql_fetch_array($result2);
			$dr_anger=$data2['name'];
		}
		else
		{
			$spdo='<a href="spieler.php?name='.$data['user'].'">'.$data['user'].'</a> aus Dorf <a href="karte.php?'.
				'do=show&x='.$von[0].'&y='.$von[1].'">'.$dorf_name[$von[0]][$von[1]].'</a>';
			$dr_anger=$data['user'];
		}

		$text='2'.chr(13).'1::'.$spdo.chr(13).'2:'.$data['ress'];


		//Konfig der Benutzer laden
		$sql2="SELECT `name`,`konfig` FROM `tr".$round_id."_user` WHERE `name`='$username' OR `name`='".$dr_anger."';";
		$result2=mysql_query($sql2);
		$data2=mysql_fetch_array($result2);
		$konfig[$data2['name']]=split(':',$data2['konfig']);
		$data2=mysql_fetch_array($result2);
		$konfig[$data2['name']]=split(':',$data2['konfig']);

		//Bericht erstatten fall nötig
		if ($konfig[$username][0]==1)
		{
			$sql2="INSERT INTO `tr".$round_id."_msg` (`von`,`an`,`typ`,`neu`,`zeit`,`betreff`,`text`)
				VALUES ('','$username','1','1',NOW(),'$betreff','$text');";
			$result2=mysql_query($sql2);
		}
		if ($konfig[$data['user']][0]==1)
		{
			$sql2="INSERT INTO `tr".$round_id."_msg` (`von`,`an`,`typ`,`neu`,`zeit`,`betreff`,`text`)
				VALUES ('','".$dr_anger."','1','1',NOW(),'$betreff','$text');";
			$result2=mysql_query($sql2);
		}
	}
}


//Truppen ankommen lassen
$sql="SELECT * FROM `tr".$round_id."_truppen_move` WHERE `ziel_zeit`<='".date('Y-m-d H:i:s',time())."';";
$result=mysql_query($sql);
for ($ii=1;$ii<=mysql_num_rows($result);$ii++)
{
	$data=mysql_fetch_array($result);
	if ($data['aktion']==1)	//Neues Dorf gründen
	{
		$sql2="SELECT * FROM `tr".$round_id."_dorfer` WHERE `x`='".$data['ziel_x']."' AND `y`='".$data['ziel_y']."';";
		$result2=mysql_query($sql2);

		if (mysql_num_rows($result2)==0)	//Falls Dorf noch frei ist
		{
			$sql3="DELETE FROM `tr".$round_id."_truppen_move` WHERE `user`='".$data['user']."' AND `ziel_x`='".$data['ziel_x']."' AND `ziel_y`='".$data['ziel_y']."'
				AND `ziel_zeit`='".$data['ziel_zeit']."' AND `truppen`='".$data['truppen']."';";
			$result3=mysql_query($sql3);

			$sql3="INSERT INTO `tr".$round_id."_dorfer` (`x`,`y`,`user`,`einwohner`,`grosse`,`zustimmung`,`expansion`,`lager`,`geb1`,`geb2`,`geb2t`,`update`)
				VALUES ('".$data['ziel_x']."','".$data['ziel_y']."','".$data['user']."','2','0','100','0','750:750:750:750','0:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0',
				'0:0:0:0:0:0:0:1:0:0:0:0:0:0:0:0:0:0:0:0:0:0','0:0:0:0:0:0:0:15:0:0:0:0:0:0:0:0:0:0:0:0:0:0',NOW());";
//			var_dump($sql3);
			$result3=mysql_query($sql3);

			$sql3="SELECT `expansion` FROM `tr".$round_id."_dorfer` WHERE `x`='".$data['start_x']."' AND `y`='".$data['start_y']."';";
			$result3=mysql_query($sql3);
			$data3=mysql_fetch_array($result3);
			$exp=split(':',$data3['expansion']);
			$exp[$exp[0]*2+1]=$data['ziel_x'];
			$exp[$exp[0]*2+2]=$data['ziel_y'];
			$exp[0]++;
			$exp=implode(':',$exp);
			$sql3="UPDATE `tr".$round_id."_dorfer` SET `expansion`='$exp' WHERE `x`='".$data['start_x']."' AND `y`='".$data['start_y']."';";
//			var_dump($sql3);
			$result3=mysql_query($sql3);
		}
		else	//Falls Dorf schon besiedelt wurde, Siedler umkehren lassen
		{
			$sql3="UPDATE `tr".$round_id."_truppen_move` SET `start_x`='".$data['ziel_x']."', `start_y`='".$data['ziel_y']."',
				`ziel_x`='".$data['start_x']."', `ziel_y`='".$data['start_y']."', `aktion`='2',
				`start_zeit`='".$data['ziel_zeit']."', `ziel_zeit`='".
				date('Y-m-d H:i:s',2*strtotime($data['ziel_zeit'])-strtotime($data['start_zeit']))."' WHERE
				`user`='".$data['user']."' AND `start_zeit`='".$data['start_zeit']."' AND `ziel_zeit`='".
				$data['ziel_zeit']."' AND `troops`='".$data['troops']."';";
			$result3=mysql_query($sql3);

			$sql3="SELECT `name` FROM `tr".$round_id."_user` WHERE `id`='".$data['user']."';";
			$result3=mysql_query($sql3);
			$data3=mysql_fetch_array($result3);

			$sql3="INSERT INTO `tr".$round_id."_msgs` (`an`,`typ`,`neu`,`zeit`,`betreff`,`text`)
				VALUES ('".$data3['name']."','4','1','".$data['ziel_zeit']."','Dorf konnte nicht gegründet werden',
				'1".chr(13)."1::Ein Dorf welches Sie besiedeln wollten, wurde vorher schon besiedelt. Ihre Siedler kehren desshalb in euer Dorf zurück, ohne ein Dorf gegründet zu haben.');";
			$result3=mysql_query($sql3);
		}
	}
	if ($data['aktion']==2)	//Unterstützung
	{
		$sql3="DELETE FROM `tr".$round_id."_truppen_move` WHERE `user`='".$data['user']."' AND `ziel_x`='".$data['ziel_x']."'
				AND `ziel_y`='".$data['ziel_y']."'
				AND `ziel_zeit`='".$data['ziel_zeit']."' AND `truppen`='".$data['truppen']."';";
		$result3=mysql_query($sql3);

		$sql2="SELECT `troops` FROM `tr".$round_id."_truppen` WHERE `x`='".$data['ziel_x']."' AND `y`='".$data['ziel_y']."'
			AND `user`='".$data['user']."';";
		$result2=mysql_query($sql2);

		$ts=split(':',$data['truppen']);
		if (mysql_num_rows($result2)>0)
		{
			$data2=mysql_fetch_array($result2);
			$ts_dorf=split(':',$data2['troops']);

			for ($i=0;$i<=9;$i++)
				$ts_dorf[$i]+=$ts[$i];

			$ts_ds=implode(':',$ts_dorf);

			$sql2="UPDATE `tr".$round_id."_truppen` SET `troops`='$ts_ds' WHERE `x`='".$data['ziel_x']."'
				AND `y`='".$data['ziel_y']."' AND `user`='".$data['user']."';";
			$result2=mysql_query($sql2);

		}
		else
		{
			$sql2="INSERT INTO `tr".$round_id."_truppen` (`x`,`y`,`user`,`troops`) VALUES ('".$data['ziel_x']."','".$data['ziel_y'].
				"','".$data['user']."','".$data['truppen']."');";
			$result2=mysql_query($sql2);

		}
        $sql2="SELECT `name`,`volk` FROM `tr".$round_id."_user` WHERE `id`='".$data['user']."';";
        $result2=mysql_query($sql2);
        $data2=mysql_fetch_array($result2);
        $name=$data2['name']; $volk=$data2['volk'];

        $sql2="SELECT `name` FROM `tr".$round_id."_dorfer` WHERE `x`='".$data['start_x']."' AND `y`='".$data['start_y']."';";
        $result2=mysql_query($sql2);
        $data2=mysql_fetch_array($result2);
        $s_name=$data2['name'];

        $sql2="SELECT `name` FROM `tr".$round_id."_dorfer` WHERE `x`='".$data['ziel_x']."' AND `y`='".$data['ziel_y']."';";
        $result2=mysql_query($sql2);
        $data2=mysql_fetch_array($result2);
        $z_name=$data2['name'];

        if ($data['msg']==1)
        {
			$sql2="INSERT INTO `tr".$round_id."_msg` (`an`,`typ`,`neu`,`zeit`,`betreff`,`text`)
				VALUES ('".$name."','2','1','".$data['ziel_zeit']."','".$s_name." unterstützt ".
				$z_name."','4".chr(13)."1:Absender:".$name." aus Dorf ".$s_name.chr(13).
				"3:".$volk.chr(13)."4:Einheiten:".$data['truppen'].chr(13)."5:0');";
			$result2=mysql_query($sql2);
		}
	}
	if ($data['aktion']==3 or $data['aktion']==4)	//Angriff normal oder Raubzug
	{
		//Name und Volk des Angreiffers herausfinden
        $sql2="SELECT `name`,`volk` FROM `tr".$round_id."_user` WHERE `id`='".$data['user']."';";
        $result2=mysql_query($sql2);
        $data2=mysql_fetch_array($result2);
        $name=$data2['name']; $volk=$data2['volk'];

        //Name des Angreiffenden Dorfes
        $sql2="SELECT `name` FROM `tr".$round_id."_dorfer` WHERE `x`='".$data['start_x']."' AND `y`='".$data['start_y']."';";
        $result2=mysql_query($sql2);
        $data2=mysql_fetch_array($result2);
        $angreiffendes_dorf=$data2['name'];

		//Deff Truppen laden
		unset($deff_truppen);
		unset($deff_truppen_spieler);
		$sql2="SELECT tr".$round_id."_truppen.troops,tr".$round_id."_truppen.user,tr".$round_id."_user.volk
			FROM `tr".$round_id."_truppen`,`tr".$round_id."_user`
			WHERE tr".$round_id."_truppen.x='".$data['ziel_x']."' AND tr".$round_id."_truppen.y='".$data['ziel_y']."'
				AND tr".$round_id."_user.id=tr".$round_id."_truppen.user;";
		$result2=mysql_query($sql2);
		for ($i=1;$i<=mysql_num_rows($result2);$i++)
		{
			$data2=mysql_fetch_array($result2);

			$t=split(':',$data2['troops']);
			$v=$data2['volk'];
			for ($j=0;$j<=9;$j++)
			{
				//Deff Truppen gesamtzahl und zahl jedes Spielers speichern
				$deff_truppen[$j+1+$v*10-10]+=$t[$j];
				$deff_truppen_spieler[$data2['user']][$j+1+$v*10-10]+=$t[$j];
				$deff_truppen_string_start[$data2['user']].=$t[$j];
				if ($j<9) $deff_truppen_string_start[$data2['user']].=':';
			}
		}

		//Angriffstruppen laden
		$angriffs_truppen_09=split(':',$data['truppen']);
		for ($i=1;$i<=10;$i++)
			$angriffs_truppen[$i+$volk*10-10]=$angriffs_truppen_09[$i-1];

		//Kampfsim
		$neu_truppen=kampfsim($troops,$angriffs_truppen,$deff_truppen,$data['aktion']-2);

		//Angriffstruppen berechnen, string formen
		$anz_angreifer=0;
		$neu_truppen1_string='';
		$verluste_angreifer_string='';
		for ($i=1;$i<=10;$i++)
		{
			$anz_angreifer+=$neu_truppen[1][$i+$volk*10-10];
			$neu_truppen1_string.=$neu_truppen[1][$i+$volk*10-10];
			if ($i<10) $neu_truppen1_string.=':';
			$verluste_angreifer[$i]=$angriffs_truppen[$i+$volk*10-10]-$neu_truppen[1][$i+$volk*10-10];

			$verluste_angreifer_string.=$verluste_angreifer[$i];
			if ($i<10) $verluste_angreifer_string.=':';
		}
		if ($anz_angreifer>0)	//Zurück schicken
		{
			$neue_ziel_zeit=date('Y-m-d H:i:s',2*strtotime($data['ziel_zeit'])-strtotime($data['start_zeit']));

			$sql2="UPDATE `tr".$round_id."_truppen_move` SET `ziel_x`='".$data['start_x']."', `ziel_y`='".$data['start_y']."',
				`start_x`='".$data['ziel_x']."', `start_y`='".$data['ziel_y']."', `start_zeit`='".$data['ziel_zeit']."',
				`ziel_zeit`='$neue_ziel_zeit', `aktion`='2', `truppen`='$neu_truppen1_string', `msg`='0' ";
		}
		else		//oder löschen
			$sql2="DELETE FROM `tr".$round_id."_truppen_move` ";

		$sql2.=" WHERE `user`='".$data['user']."' AND `ziel_x`='".$data['ziel_x']."' AND `ziel_y`='".$data['ziel_y']."'
				AND `ziel_zeit`='".$data['ziel_zeit']."' AND `truppen`='".$data['truppen']."';";
		$result2=mysql_query($sql2);

		//Deff truppen berechnen
		$anzahl_deff=0;
		$sql2="SELECT tr".$round_id."_truppen.user,tr".$round_id."_user.volk,tr".$round_id."_user.name,
				tr".$round_id."_dorfer.user AS dorfuser,tr".$round_id."_dorfer.name AS dorfname
			FROM `tr".$round_id."_truppen`,`tr".$round_id."_user`,`tr".$round_id."_dorfer`
			WHERE tr".$round_id."_truppen.x='".$data['ziel_x']."' AND tr".$round_id."_truppen.y='".$data['ziel_y']."' AND
			tr".$round_id."_user.id=tr".$round_id."_truppen.user AND tr".$round_id."_truppen.x=tr".$round_id."_dorfer.x
				AND tr".$round_id."_truppen.y=tr".$round_id."_dorfer.y;";
		$result2=mysql_query($sql2);
		for ($i=1;$i<=mysql_num_rows($result2);$i++)
		{
			$data2=mysql_fetch_array($result2);

			$volk=$data2['volk'];
			$deff_truppen_string='';
			$deff_truppen_verluste_string='';
			$anz=0;
			for ($j=1;$j<=10;$j++)
			{
				//Deff Truppen prozentsatz der übriggebliebenen berechnen
				if ($deff_truppen[$j+$volk*10-10]==0)
					$prozent=0;
				else
					$prozent=$deff_truppen_spieler[$data2['user']][$j+$volk*10-10]/$deff_truppen[$j+$volk*10-10];

				$deff_truppen_einheit=round($prozent*$neu_truppen[2][$j+$volk*10-10],0);
				$anz+=$deff_truppen_einheit;
				$deff_truppen_string.=$deff_truppen_einheit;
				if ($j<10) $deff_truppen_string.=':';

				$verluste=$deff_truppen_spieler[$data2['user']][$j+$volk*10-10]-$deff_truppen_einheit;
				$deff_truppen_verluste_string.=$verluste;
				if ($j<10) $deff_truppen_verluste_string.=':';
			}
			if ($anz>0)
				$sql3="UPDATE `tr".$round_id."_truppen` SET `troops`='$deff_truppen_string' ";
			else
				$sql3="DELETE FROM `tr".$round_id."_truppen` ";
			$sql3.="WHERE `x`='".$data['ziel_x']."' AND `y`='".$data['ziel_y']."' AND `user`='".$data2['user']."';";
			$result3=mysql_query($sql3);

			//Nachrichten verschicken
			$anzahl_deff++;
			if ($data2['dorfuser']==$data2['user'])	//Besitzer des Dorfes
			{
				$temp3='<a href="spieler.php?name='.$data2['name'].'">'.$data2['name'].'</a> aus Dorf <a href="'.
					'karte.php?do=show&x='.$data['ziel_x'].'&y='.$data['ziel_y'].'">'.$data2['dorfname'].'</a>';
				$deff_string[$anzahl_deff]="1:Verteidiger:".$temp3.chr(13).
					"3:".$volk.chr(13)."4:Einheiten:".$deff_truppen_string_start[$data2['user']].chr(13).
					"4:Verluste:".$deff_truppen_verluste_string.chr(13);
				$name_des_angegriffenen=$data2['name'];
				$name_des_dorfes=$data2['dorfname'];
			}
			else	//Unterstützungen sonst
			{
				$temp4='<a href="spieler.php?name='.$data2['name'].'">'.$data2['name'].'</a>';
				$deff_string[$anzahl_deff]="1:Unterstützung:von ".$temp4.chr(13).
					"3:".$volk.chr(13)."4:Einheiten:".$deff_truppen_string_start[$data2['user']].chr(13).
					"4:Verluste:".$deff_truppen_verluste_string.chr(13);

				$sql3="INSERT INTO `tr".$round_id."_msg` (`an`,`typ`,`zeit`,`betreff`,`text`) VALUES
					( '".$data2['name']."','2','".$data['ziel_zeit']."','Unterstützung in ".$data2['dorfname'].
					" wurde angegriffen','4".chr(13)."1:".$name.":Hat eine Ihrer Unterstützungen angegriffen".chr(13).
					"3:".$volk.chr(13)."4:Einheiten:".$deff_truppen_string_start[$data2['user']].chr(13).
					"4:Verluste:".$deff_truppen_verluste_string."');";
				$result3=mysql_query($sql3);
			}

		}

		//Nachricht an den Angegriffenen
		$temp1='4';
		for ($j=1;$j<=$anzahl_deff;$j++)
			$temp1.=':4';

		$temp2='<a href="spieler.php?name='.$name.'">'.$name.'</a> aus Dorf <a href="karte.php?do=show&x='.
			$data['start_x'].'&y='.$data['start_y'].'">'.$angreiffendes_dorf.'</a>';

        $sql3="INSERT INTO `tr".$round_id."_msg` (`an`,`typ`,`zeit`,`betreff`,`text`) VALUES
            ( '$name_des_angegriffenen','3','".$data['ziel_zeit']."','$angreiffendes_dorf greift ".
        	$name_des_dorfes." an','$temp1".chr(13)."1:Angreifer:$temp2".chr(13).
            "3:$volk".chr(13)."4:Einheiten:".$data['truppen'].chr(13).
            "4:Verluste:$verluste_angreifer_string".chr(13);
        for ($j=1;$j<=$anzahl_deff;$j++)
        	$sql3.=$deff_string[$j];
        $sql3.="');";
		$result3=mysql_query($sql3);

		//Nachricht an den Angreiffer
		if ($data['aktion']==3) $betreff='Angriff';
		else $betreff='Raubzug';
		$betreff.=' auf '.$name_des_dorfes;
		if ($anz_angreifer==0) $text="5".chr(13)."1:Angreifer:Ihre Truppen".chr(13)."3:$volk".chr(13).
			"4:Einheiten:".$data['truppen'].chr(13)."4:Verluste:$verluste_angreifer_string".chr(13).
			"1:Info:Es sind keine Truppen zurückgekehrt";
		else
		{
			$text="$temp1".chr(13)."1:Angreifer:Ihre Truppen".chr(13)."3:$volk".chr(13).
				"4:Einheiten:".$data['truppen'].chr(13)."4:Verluste:$verluste_angreifer_string".chr(13);
        for ($j=1;$j<=$anzahl_deff;$j++)
        	$text.=$deff_string[$j];
		}
		$sql3="INSERT INTO `tr".$round_id."_msg` (`an`,`typ`,`zeit`,`betreff`,`text`) VALUES
			('$name','3',NOW(),'$betreff','$text');";
		$result3=mysql_query($sql3);
	}
}





//Ressourcen updaten
$neu_lager[0]=$lager[0]+$produktion[0]*$faktor;
$neu_lager[1]=$lager[1]+$produktion[1]*$faktor;
$neu_lager[2]=$lager[2]+$produktion[2]*$faktor;
$neu_lager[3]=$lager[3]+($produktion[3]-$troops_village['versorgung']-$lager[4])*$faktor;
if ($neu_lager[0]>$lager_grosse[0]) $neu_lager[0]=$lager_grosse[0];
if ($neu_lager[1]>$lager_grosse[0]) $neu_lager[1]=$lager_grosse[0];
if ($neu_lager[2]>$lager_grosse[0]) $neu_lager[2]=$lager_grosse[0];
if ($neu_lager[3]>$lager_grosse[1]) $neu_lager[3]=$lager_grosse[1];

$lagerstring=implode(':',$neu_lager);

//Ressourcen speichern
$sql="UPDATE `tr".$round_id."_dorfer` SET `lager`='$lagerstring', `update`=NOW(), `geb1`='$geb1string',
	`geb2`='$geb2string', `geb2t`='$geb2tstring' WHERE `x`='$dorfx' AND `y`='$dorfy';";
$result=mysql_query($sql);

//Kulturpunkte updaten
$faktor2=(time()-strtotime($spieler_data['update']))/(3600*24);	//Faktor in Produktionstagen
$neue_kp=$faktor2*$spieler_data['einwohner']/2;
$neu_kp=$spieler_data['kps']+$neue_kp;

//Spielerdaten speichern
$weaponsstring=implode(':',$weapons);
$armsstring=implode(':',$arms);

$sql="UPDATE `tr".$round_id."_user` SET `research`='$researchstring', `kps`='$neu_kp', `update`=NOW(),
	`weapons`='$weaponsstring', `arms`='$armsstring' WHERE `id`='".$dorf_data['user']."';";
$result=mysql_query($sql);
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function wahrscheinlichkeit($chance)  //Liefert mit der Wahrscheinlichkeit von $chance % eine 1 zurück
{
	$g=mt_rand(0,100);
	if ($g<100)
	{
		if ($g==0)	$h=mt_rand(1,999);
		else		$h=mt_rand(0,999);
	}
	$zahl=$g+$h*0.001;
	if ($zahl<=$chance) return 1;
	else	return 0;
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function zeit_dauer($stamp)
{
$h=floor($stamp/3600);
$m=floor(($stamp%3600)/60);
$s=floor($stamp-60*(60*$h+$m));
if ($m<10) $m='0'.$m;
if ($s<10) $s='0'.$s;
return $h.':'.$m.':'.$s;
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function load_troops_in_village($userid,$dorfx,$dorfy,$troops_typen=NULL)
{
global $round_id;
$verbrauch=0;

//Spieler Völker laden
$sql="SELECT `id`,`volk` FROM `tr".$round_id."_user`;";
$result=mysql_query($sql);
$spieler_anzahl=mysql_num_rows($result);
for ($i=1;$i<=$spieler_anzahl;$i++)
{
	$data=mysql_fetch_array($result);
	$user_volk[$data['id']]=$data['volk'];
	$user_id[$i]=$data['id'];
}
//Truppen von Dorf laden
$sql="SELECT * FROM `tr".$round_id."_truppen` WHERE `x`='$dorfx' AND `y`='$dorfy';";
$result=mysql_query($sql);
$anz=mysql_num_rows($result);
$troops_village['any']=0;
if ($anz==0)
{
	for ($i=1;$i<=30;$i++)
		$troops_village['own'][$i]=0;
	$troops_village['own']['versorgung']=0;
}
else
{
	$troops_village['own']['versorgung']=0;
	$troops_village['versorgung']=0;
    for ($i=1;$i<=$anz;$i++)
    {
        $data=mysql_fetch_array($result);
        $troops=explode(':',$data['troops']);
        $user=$data['user'];
        for ($j=1;$j<=10;$j++)
        {
            $troops_village['versorgung']+=$troops_typen[$j+($user_volk[$user]-1)*10]['versorgung']*$troops[$j-1];
            $troops_village['all'][$j+($user_volk[$user]-1)*10]+=$troops[$j-1];
            if ($user==$userid)
            {
                $troops_village['own'][$j+($user_volk[$user]-1)*10]+=$troops[$j-1];
                $troops_village['own']['versorgung']+=
                	$troops_typen[$j+($user_volk[$user]-1)*10]['versorgung']*$troops[$j-1];
            }
            $troops_village[$user][$j+($user_volk[$user]-1)*10]+=$troops[$j-1];
            $troops_village[$user]['versorgung']+=
            	$troops_typen[$j+($user_volk[$user]-1)*10]['versorgung']*$troops[$j-1];
        }
    }
    $troops_village['versorgung']=round($troops_village['versorgung']);
    $troops_village['own']['versorgung']=round($troops_village['own']['versorgung']);
    for ($i=1;$i<=$spieler_anzahl;$i++)
    {
        if (isset($troops_village[$user_id[$i]]['versorgung']))
            $troops_village[$user_id[$i]]['versorgung']=round($troops_village[$user_id[$i]]['versorgung']);
    }
}
if ($troops_village['versorgung']>0) $troops_village['any']=1;
return $troops_village;
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function serverzeit($load_time)
{
global $round_id;
$sql="SELECT `value` FROM `tr".$round_id."_diverses` WHERE `id`='rundenstart';";
$result=mysql_query($sql);
$data=mysql_fetch_array($result);

$d=floor((time()-strtotime($data['value']))/86400);
echo'<div id="ltime"><table width=350 style="margin:0px; padding:0px;"><tr height=5><td>Berechnung in <b>'.
	round((microtime()-$load_time)*1000).'</b> ms<br>Serverzeit: <span id="tp1" class="b">'.
	date('H:i:s',time()).'</span></td><td>Rundenlaufzeit:
	<span class="b">'.$d.'Tag</span>, <span id="tp2" class="b">'.
	zeit_dauer(time()-strtotime($data['value'])-$d*86400).'</span><br>&nbsp;</td></tr></tr></table></div>';
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function dorf_slots($dorf_data)
{
$geb2=split(':',$dorf_data['geb2']);
$geb2t=split(':',$dorf_data['geb2t']);
$slots=0;
for ($i=0;$i<=21;$i++)
{
	if ($geb2t[$i]==25 OR $geb2t[$i]==26)
	{
		if ($geb2[$i]>9) $slots++;
		if ($geb2t[$i]==26 AND $geb2[$i]>14) $slots++;
		if ($geb2[$i]>19) $slots++;
	}
}
return $slots;
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function versorgung_von_truppen($troops,$truppen,$volk=-1)
{
$versorung=0;
if ($volk!=-1)
{
	for ($i=0;$i<=9;$i++)
		$versorgung+=$truppen[$i]*$troops[$i+1+$volk*10-10]['versorgung'];
}
if ($volk==-1)
{
	for ($i=1;$i<=30;$i++)
		$versorgung+=$truppen[$i]*$troops[$i]['versorgung'];
}
return $versorgung;
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------
function nzf($stamp)
{
return date('H:i:s - d.m.Y',strtotime($stamp));
}
//---------------------------------------------------------------------------------------------------------------------
//OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
//---------------------------------------------------------------------------------------------------------------------

?>