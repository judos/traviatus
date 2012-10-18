<?php
if (!$execute) die('');
needed_login();
$stview=1;

if (!isset($_GET['keyid'])) gotoP('berichte');
$keyid=(int)$_GET['keyid'];


$bericht=Bericht::getById($keyid);
if ($bericht===NULL) {
	$msg='Bericht wurde nicht gefunden.';
	gotoP('berichte');
}
$bericht->read();

?>

<h1>Berichte</h1>

<?php
Outputer::berichteMenu();
?>

<table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr class="rbg">
<td class="s7">Betreff:</td>
<td class="s7"><?php echo $bericht->get('betreff'); ?></td>
</tr>

<tr>
<td class="s7 b">Gesendet:</td>
<td class="s7">am
<?php
echo date('d.m.y',strtotime($bericht->get('zeit'))).' um '.
	date('H:i:s',strtotime($bericht->get('zeit')));
?>
<span> Uhr</span></td>
</tr>

<tr valign="middle">
<td colspan="2" valign="middle"><p>
</p>

<?php
$alles=explode(chr(13),$bericht->get('text'));
echo'<table class="tbg" cellpadding="2" cellspacing="1"><tbody>';
for ($teil=0;$teil<sizeof($alles);$teil++) {
	if ($teil>0) echo'<br>';
	$zeile=explode(':',$alles[$teil]);

	if ($zeile[0]==0) { //Reiner Text
		echo'<tr class="cbg1"><td width="100%" colspan="11">'.
			$zeile[1].'</td></tr>';
	}
	if ($zeile[0]==1)	{ //Text
		echo'<tr class="cbg1">';
		if ($zeile[1]=='Angreiffer')
			echo'<td width="21%" class="c2 b">'.$zeile[1].'</td>';
		elseif($zeile[1]=='Verteidiger')
			echo'<td width="21%" class="c1 b">'.$zeile[1].'</td>';
		elseif($zeile[1]=='Unterstützung')
			echo'<td width="21%" class="c1 b">'.$zeile[1].'</td>';
		echo'<td></td><td colspan=10 class="b">'.$zeile[2].'</td></tr>';
	}
	if ($zeile[0]==2) {	//Rohstoffe
		for ($i=1;$i<=4;$i++)
			if ($zeile[$i]=='') $zeile[$i]=0;
		echo'<tr><td width="100" class="left">&nbsp;Rohstoffe</td><td class="s7">'.
			'<img class="res" src="img/un/r/1.gif">'.$zeile[1].' | '.
			'<img class="res" src="img/un/r/2.gif">'.$zeile[2].' | '.
			'<img class="res" src="img/un/r/3.gif">'.$zeile[3].' | '.
			'<img class="res" src="img/un/r/4.gif">'.$zeile[4].
			'</td></tr>';
	}
	if ($zeile[0]==3)	{ //Bilder von Einheiten
		echo'<tr class="unit"><td>&nbsp;</td>';
		$v=($zeile[1]-1)*10;
		for ($j=1;$j<=10;$j++)
			echo'<td><img src="img/un/u/'.($j+$v).'.gif" title="'.
				$troops[$j+$v]['name'].'"></td>';
		echo'</tr>';
	}
	if ($zeile[0]==4) {	//Text und Einheitenanzahl
		echo'<tr><td>'.$zeile[1].'</td>';
		for ($j=1;$j<=10;$j++) {
			if ($zeile[$j+1]>0) echo'<td>'.$zeile[$j+1].'</td>';
			else	echo'<td class="c">0</td>';
		}
		echo'</tr>';
	}
	if ($zeile[0]==5)	{ //Unterhalt
		echo'<tr class="cbg1"><td>Unterhalt</td>
			<td class="s7" colspan="10">'.$zeile[1].
				'<img class="res" src="img/un/r/4.gif">pro Stunde</td>
				</tr>';
	}

	if ($zeile[0]==100) {	//Neue Tabelle beginnen
		echo'</tbody></table>';
		if ($teil<sizeof($alles)-1)
			echo'<table class="tbg" cellpadding="2" cellspacing="1">
				<tbody>';
	}
}
echo'</tbody></table>';
?>

</td></tr>
</tbody></table>
<div>