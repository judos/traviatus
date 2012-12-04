<?php
if (!$execute) die('');
needed_login();
$stview=1;

$s=saveGet('s',4);

$c=' class="selected"';
echo'<h1>Admintools</h1>
  <p class="txt_menue">
    <a href="?page=admintools" '.($s==4?$c:'').'>Todo Liste</a> |
    <a href="?page=admintools&s=3"'.($s==3?$c:'').'>BUGs melden</a> | 
    <a href="?page=admintools&s=1"'.($s==1?$c:'').'>Neue Entwicklung</a> |
    <a href="?page=admintools&s=2"'.($s==2?$c:'').'>User bearbeiten</a> |
    <a href="?page=admintools&s=5"'.($s==5?$c:'').'>Terraforming</a>
  </p>';


if ($s==4) { //TODO's
	$sql="SELECT COUNT(*) as anz1 FROM tr".ROUND_ID."_todo WHERE status='finished';";
	$result=mysql_query($sql);
	$data=mysql_fetch_assoc($result);
	$anz1=$data['anz1'];
	$sql="SELECT COUNT(*) as anz2 FROM tr".ROUND_ID."_todo;";
	$result=mysql_query($sql);
	$data=mysql_fetch_assoc($result);
	$anz2=$data['anz2'];
	$fortschritt=@roundTo($anz1/$anz2*100,1);
	if ($anz2==0)
		$fortschritt=100;
	
	echo'<table class="tbg" cellpadding="2" cellspacing="1">
		<tbody><tr class="rbg"><td colspan=3><b>TODO Liste: (Fortschritt: '.$fortschritt.'%)</b>
		</td></tr>
		<tr><td>Erfasst:</td><td>Text:</td><td>Status:</td></tr>';
	$sql="SELECT * FROM tr".ROUND_ID."_todo
		WHERE status=''
		ORDER BY erfasst ASC;";
	$result=mysql_query($sql);
	$nr=0;
	$max=10;
	if (isset($_GET['show']) and $_GET['show']=='all') $max=10000;
	while (($data=mysql_fetch_array($result)) && (++$nr)<=$max) {
		$link='?page=admintools&s=4&keyid='.$data['keyid'];
		echo'<tr><td class="s7 f8">'.date('H:i d.m.Y',
				strtotime($data['erfasst'])).'</td>
			<td class="s7 f8">'.t($data['text']).'</td>
			<td class="s7 f8"><a href="'.$link.'&do=finishtodo">fertig</a>
				&nbsp;&nbsp;&nbsp;&nbsp;<a style="color:red;"
					href="'.$link.'&do=deltodo">X</a><br>
				<a href="'.$link.'&do=later" style="color:green;">später</a>
			</td></tr>';
	}
	if ($nr==$max+1) {
		echo'<tr><td colspan="3"><a href="?page=admintools&'.
			's=4&show=all">Alle zeigen</a></td></tr>';
	}
	//Neu hinzufügen
	echo'<form method="post"
		action="?page=admintools&s=4&do=newtodo">
		<tr><td class="s7 f8">Jetzt</td><td class="s7 f8">
		<textarea name="text" style="width:95%;" rows="5">'.
		'</textarea></td>
		<td class="s7 f8">
			<input type="submit" value="speichern" /></td>
		</tr></form>';
	echo'</tbody></table>';
	//fertig gestellte sachen
	echo'<br><table class="tbg" cellpadding="2" cellspacing="1">
		<tbody><tr class="rbg"><td colspan=4>
		<b>Fertig gestellte TODO\'s:</b></td></tr>
		<tr><td>fertig gestellt:</td>
		<td>Text:</td><td>Status:</td><td>Version:</td></tr>';
	$sql="SELECT * FROM tr".ROUND_ID."_todo
		WHERE status!=''
		ORDER BY fertig DESC;";
	$result=mysql_query($sql);
	$nr=0;
	while (($data=mysql_fetch_array($result)) && (++$nr)<20) {
		echo'<tr><td class="s7 f8">'.date('H:i d.m.Y',
				strtotime($data['fertig'])).'</td>
			<td class="s7 f8">'.t($data['text']).'</td>
			<td class="s7 f8">'.$data['status'].'</td>
			<td class="s7 f8">'.$data['implemented_in'].'</td></tr>';
	}
	echo'</tbody></table>';
}

if ($s==5) { //Terraforming
	?>
	<form method="post" action="?page=admintools&do=terra&s=5">
	<table><tr>
	<td>Grösse:</td>
	<td><input class="fm" name="sx" size="10" /> /
		<input class="fm" name="sy" size="10" /></td>
	</tr>
	<tr>
	<td>Wkeit Oasen:</td>
	<td><input class="fm" name="oasen" /> %</td>
	</tr><tr>
	<td>Normale Anteile:</td>
	<td><?php echo round(100/Land::anzLandTypen(),1); ?> %</td>
	</tr><tr>
	<td>Anteil 15er:</td>
	<td><input class="fm" name="15er" /> %</td>
	</tr><tr>
	<td>Anteil 9er:</td>
	<td><input class="fm" name="9er" /> %</td>
	</tr><tr>
	<td>Anteil 1er:</td>
	<td><input class="fm" name="1er" /> %</td>
	</tr></table>
	<?php
	Outputer::button('ok','ok');
	if (isset($msg))
		echo'<br><br><p>'.$msg.'</p>';
}


if ($s==3) { //Report Bugs
	if (isset($msg))
		echo msg_ok($msg);
?>
<form action="?page=admintools&s=3&do=newbug" method=post>
<table><tr><td>Titel/Name:</td>
<td><input class="fm" name="titel"></td></tr>
<tr><td>Text und Beschreibung:</td>
<td><textarea cols=39 rows=5 name="text"></textarea></td></tr>
<tr><td><input type=submit value="Speichern"></td></tr>
</table>
</form>
<br>

<?php
$sql="SELECT * FROM `tr".ROUND_ID."_bugs`
	ORDER BY `status` ASC, `zeit` DESC;";
$result=mysql_query($sql);
if (mysql_num_rows($result)>0) {
	echo'<table class="tbg" cellpadding="2" cellspacing="1">
		<tbody><tr class="rbg"><td colspan=3><b>Bekannte BUGs:</b>
		</td></tr>
		<tr><td>Name:</td><td>Beschreibung:</td><td>Status:</td></tr>';
	while ($data=mysql_fetch_array($result)) {
		echo'<tr><td class="s7 f8">'.$data['titel'].'</td>
			<td class="s7 f8">'.$data['text'].'</td>
			<td class="s7 f8">'.$data['status'].'</td></tr>';
	}
	echo'</tbody></table>';
}

}

if ($s==1) { //Bericht senden
?>

<form action="?page=admintools&do=sendreport&s=1" method="post">
<table>
<tr><td>Betreff:</td><td><input class="fm" name="betreff" /></td></tr>
<tr><td>Typ:</td><td><input class="fm" name="typ" /></td></tr>
<tr><td>Text:</td></tr>
<tr><td><textarea cols="29" rows="9" name="text">
1::Titel
0:Text
</textarea></td></tr>
<tr><td><input type="submit" value="Senden" /></td></tr>
</table>
</form>
<?php
}

if ($s==2) { //User bearbeiten
	echo'<form action="?page=admintools&s=2&do=deluser" method="post">
    User löschen: <select name="uid">';
	$sql="SELECT id,name FROM tr".ROUND_ID."_user ORDER BY name ASC;";
	$result=mysql_query($sql);
	while ($data=mysql_fetch_assoc($result)) {
		echo'<option value="'.$data['id'].'">'.$data['name'].'</option>';
	}
	echo'</select>  <input type="submit" value="Löschen"></form>';
}

echo'<div>';