<?php
if (!$execute) die('');
needed_login();
$stview=1;

Updater::Dorf($login_dorf);


$dx=$login_dorf->get('x');
$dy=$login_dorf->get('y');

$gebeude1=$login_dorf->gebeude1();

//Produktion laden
$produktion=$login_dorf->produktion();

//anklickbare Felder ermöglichen
Outputer::dorf1Karte();

//Überschrift Zustimmung und HD
echo'<div class="dname">
	<h1>'.$login_dorf->get('name');
$zustimmung=$login_dorf->get('zustimmung');
if ($zustimmung<100) {
	$c='color:#228B22;';
	if($zustimmung==100) $c='';
	if($zustimmung<=25) $c='color:#FF0000;';
	echo'<br><div style="font-size:9px;font-weight:bold;'.$c.'">Zustimmung: '.$zustimmung.'%</div>';
}
elseif ($login_dorf->get('grosse')==1) {
	echo'<br><div style="font-size:13px; color:#C0C0C0; ">(Hauptdorf)</div>';
}
echo'</h1>
	</div>';

//Feld anzeigen
echo'<div id="f'.Land::getByXY($dx,$dy)->get('typ').'">';

//Stufen der Rohstoffgebäude anzeigen
for ($i=1;$i<=18;$i++) {
	if($gebeude1[$i-1]>0) {
		echo'<img src="img/un/g/s/s'.$gebeude1[$i-1].'.gif"
			class="rf'.$i.'">';
	}
}
echo'</div>
	<img id="resfeld" usemap="#rx" src="img/un/a/x.gif">
	<div><div id="lplz1"></div>
	<div id="ltbw0">';


$x=$login_dorf->truppenBewegungen();
if (!empty($x)) {
	echo'<div class="f10 b">&nbsp;Truppenbewegungen:</div>
		<table class="f10"><tbody>';
	//Nach Ankunftszeit sortieren
	sortArray2($x,'first','asc');
	foreach($x as $typ => $array) {
		echo'<tr><td><a href="?page=build&gid=39">
			<img src="img/un/a/'.$typ.'.gif" title="'.$array['volltext'].'" border="0"></a></td>
			<td><b class="c'.$array['farbe'].' f10"
				align="right">» '.$array['anz'].'</b></td>
			<td><b class="c'.$array['farbe'].' f10">'.$array['text'].'</b></td>
			<td class="f10" align="center">in</td><td class="f10">
			<span id="timer'.$timerNr.'">'.
			zeit_dauer($array['first']-time()).'</span>
			Std.</td></tr>';
		$timerNr++;
	}
	echo'</tbody></table>';
}

?>

</div>
<div id="lrpr">


<?php //Produktion pro Stunde ausgeben
?>
<div class="f10 b">&nbsp;Produktion:</div>
<table>
<tr>
<td><img class="res" src="img/un/r/1.gif"></td>
<td>Holz:</td><td align="right"><b><?php echo $produktion[0]; ?>&nbsp;</b></td><td>pro Stunde</td>
</tr>
<tr>
<td><img class="res" src="img/un/r/2.gif"></td>
<td>Lehm:</td><td align="right"><b><?php echo $produktion[1]; ?>&nbsp;</b></td><td>pro Stunde</td>
</tr>
<tr>
<td><img class="res" src="img/un/r/3.gif"></td>
<td>Eisen:</td><td align="right"><b><?php echo $produktion[2]; ?>&nbsp;</b></td><td>pro Stunde</td>
</tr>
<tr>
<td><img class="res" src="img/un/r/4.gif"></td>
<td>Getreide:</td><td align="right"><b><?php
echo $produktion[3]-$login_dorf->versorgung(); ?>
&nbsp;</b></td><td>pro Stunde</td>
</tr>
</table></div>


<?php //Einheiten im Dorf
?>
<div id="ltrm"><div class="f10 b">&nbsp;Einheiten:</div>
<table class="f10"><tbody>
<?php
$users=Truppe::getUsersByXY($dx,$dy);
if (empty($users))
	echo'<tr><td class="s7">keine</td></tr>';
else {
	foreach($login_dorf->alleSoldaten() as $id => $anz) {
		if($anz>0) {
			echo'<tr><td><a href="?page=build&gid=39"><img class="unit"
				src="img/un/u/'.$id.'.gif" border="0"></a></td>
				<td align="right">&nbsp;<b>'.$anz.'</b>
				</td><td>';
			if ($id=='hero') {
				$name='Held';
				if ($anz>1) $name='Helden';
			}
			else {
				if($anz>1) $name=TruppenTyp::getById($id)->get('mehrzahl');
				else $name=TruppenTyp::getById($id)->get('name');
			}
			echo $name.'</td></tr>';
		}
	}
}
?>
</tbody></table>
</div>



<?php
Outputer::dorfAuftrage(1);
?>