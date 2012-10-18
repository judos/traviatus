<html>
<body>

<?
if (isset($_GET['do']))
{
echo'Werte:<br>';

$gf=0;
for ($i=1;$i<=5;$i++)
{	
	$h=$_POST[$i.'h'];
	$m=$_POST[$i.'m'];
	$s=$_POST[$i.'s'];
	$wert[$i]=$h*3600+$m*60+$s;
	if ($i>1) $diff[$i]=$wert[$i]-$wert[$i-1];
	if ($i>2)
	{
		$fak[$i]=$diff[$i]/$diff[$i-1];
		$gf+=$fak[$i];
	}
}

$nr1=$wert[1];
$nr2=$wert[2]-$wert[1];
$gf=round($gf/3,3);

echo'<input value="'.$nr1.':'.$nr2.':'.$gf.'"><br><br><br>';
}
?>

<form method=post action="bauzeit.php?do=calc">
<table>
<?
for ($i=1;$i<=5;$i++)
	echo'<tr><td>Stufe '.$i.': <input name="'.$i.'h">:<input name="'.$i.'m">:<input name="'.$i.'s"></td></tr>';
?>
</table>
<input type=submit value="berechnen">
</form>

</body>
</html>