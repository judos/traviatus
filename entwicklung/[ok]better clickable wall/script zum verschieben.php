<html>
<body>
<form method="post" action="?do=update">
<table>
	<?php
	$s=$_POST['coords'];
	$x=(int)$_POST['x'];
	$y=(int)$_POST['y'];
	echo'
	<tr><td>Coords vom Html:</td><td><input name="coords" size="100" value="" /></td></tr>
	<tr><td>X-Veränderung:</td><td><input name="x" value="'.$x.'" /></td></tr>
	<tr><td>Y-Veränderung:</td><td><input name="y" value="'.$y.'" /></td></tr>
	';
	?>
</table>
<input type="submit" value="Berechnen" />
</form>

<?php
if (isset($_GET['do']) and $_GET['do']=='update'){
	$s=explode(',',$s);
	for($i=0;$i<sizeof($s);$i++){
		if ($i % 2 ==0)
			$s[$i]+=$x;
		else
			$s[$i]+=$y;
	}
	$s_new=implode(',',$s);
	echo'Neue coords: <input value="'.$s_new.'" size="100" />';
}
?>
</body>
</html>