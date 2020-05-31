<?php
if (!$execute) die('');
needed_login();
$stview=1;

if (isset($_GET['s'])){
	$start=(int)$_GET['s'];
	if ($start<0) $start=0;
}
else
	$start=0;

if (isset($_GET['t']))
	$tab=(int)$_GET['t'];
else
	$tab=0;

$username=$login_user->get('name');

echo'<h1>Nachrichten</h1>';
Outputer::nachrichtenMenu();


if (isset($msg))
	echo'<p class="f10">'.$msg.'</p>';

?>
<!-- Form zum löschen von Nachrichten -->
<form method="post"
	action="?page=nachrichten&do=del&t=<?php echo $tab?>" name="msg">
<table cellspacing="1" cellpadding="2" class="tbg">
<tr class="rbg">
<td width="22"></td>
<td>Betreff</td><td>
<?php
if ($tab==0) echo'Absender';
else echo'Empfänger';
?>
</td>
<td width="125">Gesendet</td>
</tr>
<?php
$person=($tab==0)?'von':'an';

$nachrichten=Nachricht::getAll($tab);

if (empty($nachrichten))
	echo'<tr><td colspan="4" class="c" align="center">'.
		'Es sind keine Nachrichten im Posteingang vorhanden</td></tr>';
else {
	$nr=1;
	$start2=$start;
	foreach ($nachrichten as $id => $nachricht) {
		if ($start2>0) $start2--;
		else {
			echo'<tr>
				<td><input type="Checkbox" name="n'.$nr.'"
					value="'.$id.'"></td>
				<td class="s7"><a href="?page=nachrichten-show&id='.
					$id.'&t='.$tab.'">'.$nachricht->get('betreff').'</a> ';
			if($tab==0) {
				if ($nachricht->neu()) echo'<span class="c">(neu)</span>';
			}
			else {
				if ($nachricht->get('von')=='@ally')
					echo'';
				elseif ($nachricht->neu())
					echo'<span class="c">(ungelesen)</span>';
				elseif ($nachricht->deleted())
					echo'<span class="c">(gelöscht)</span>';
			}

			echo'</td><td style="text-align:left;">&nbsp;';

			if ($nachricht->get('von')=='@ally'){
				$vor='';
				$nach='';
			}else{
				$vor='<a href="?page=spieler&name='.$nachricht->get('von').'">';
				$nach='</a>';
			}
			echo $vor.$nachricht->get('von').$nach.'</a></td>
				<td>'.date('d.m.y H:i',
					strtotime($nachricht->get('zeit'))).'</td>
				</tr>';
			$nr++;
		}
	}
}


//Vor und zurück
$anz=sizeof($nachrichten);

echo'<tr class="rbg">
	<td align="center">';
if ($anz>0)
	echo'<input id="s10" name="s10"
		onClick="Allmsg(this.form);" align="bottom" type="checkbox">';
echo'</td>
	<td class="s7"colspan="2">';
if ($anz>0)
	Outputer::button('delmsg','delete');

echo'</td>
	<td align="right">';

if ($start>0)
	echo'<a href="?page=nachrichten&t='.$tab.'&s='.
		($start-10).'"><b>&laquo;</b></a>';
else
	echo'<span class="c"><b>&laquo;</b></span>';
if ($start+10<$anz)
	echo'<a href="?page=nachrichten&t='.$tab.'&s='.
		($start+10).'"><b>&raquo;</b></a>';
else
	echo'<span class="c"><b>&raquo;</b></span>&nbsp;';
?>
</td>
</tr>
</table>
</form>
<div>