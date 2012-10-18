<?

include('../functions.php');
connect();


$sql="DELETE FROM `tr_lander`;";
$result=mysql_query($sql);

for($x=1;$x<=21;$x++)
{
	for($y=1;$y<=21;$y++)
	{
		$oase=wahrscheinlichkeit(7);
		$aussehen=0;
		if ($oase==1)	$typ=mt_rand(1,12);
		else {$typ=mt_rand(1,6);$aussehen=mt_rand(1,9);}
		
		$sql="INSERT INTO `tr_lander` (`x`,`y`,`oase`,`typ`,`aussehen`) VALUES ('$x','$y','$oase','$typ','$aussehen');";
		$result=mysql_query($sql);
	}
}
?>