<?php
if (!$execute) die('');
needed_login();
$stview=1;
?>

<h1>Berichte</h1>

<?php

Outputer::berichteMenu();
if (isset($_GET['t']))
	$tab=(int)$_GET['t'];
else
	$tab=0;

if (isset($_GET['s'])){
	$start=(int)$_GET['s'];
	if ($start<0) $start=0;
}
else
	$start=0;

if (isset($msg))
	echo $msg;
echo'<form method="post"
	action="?page=berichte&do=del&t='.$tab.'" name="msg">';
?>
<p></p><table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr class="rbg"><td colspan="2">Betreff:</td>
	<td>Gesendet</td></tr>

<?php

$berichte=Bericht::getAll($tab);
$perPage = Diverses::get('berichte_pro_seite');

if (empty($berichte)) {
	echo'<tr><td colspan="4" class="c">Es sind keine Berichte
		vorhanden</td></tr>';
}
else {
	$nr=1;
	$start2=$start;
	foreach ($berichte as $id => $bericht) {
		if ($start2>0) $start2--;
		else {
			echo'<tr>
				<td width="22"><input name="n'.$nr.'" value="'.$id.'"
					type="checkbox"></td>
				<td class="s7"><a href="?page=berichte-show&keyid='.
					$id.'">'.$bericht->get('betreff').'</a> ';
			if ($bericht->get('neu')==1) echo'(neu)';
			echo'</td>'.
				'<td nowrap="nowrap" width="125">'.
					date('d.m.y H:i',strtotime($bericht->get('zeit'))).
				'</td>
				</tr>';
			if ($nr==$perPage) break;
			$nr++;
		}
	}
}

$anz=sizeof($berichte);

echo'<tr class="rbg">';
if ($anz>0)
	echo'<td align="center"><input id="s10" name="s10"
		onclick="Allmsg(this.form);" align="bottom"
		type="checkbox"></td>';
echo'<td class="s7" '.(($anz==0)?'colspan=2':'').'>';
if ($anz>0)
	Outputer::button('del','delete');

echo'</td><td class="r7">';

if ($start>0)
	echo'<a href="?page=berichte&t='.$tab.'&s='.($start-$perPage).'">«</a>';
else
	echo'<span class="c"><b>«</b></span>';

if ($start+$perPage<$anz)
	echo'<a href="?page=berichte&t='.$tab.'&s='.($start+$perPage).'">»</a>';
else
	echo'<span class="c"><b>»</b></span>';
?>

</td></tr>
</tbody></table>
</form>
<div>