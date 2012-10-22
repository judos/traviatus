<?php
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

?>