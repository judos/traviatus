<?php
if (!$execute) die('');
needed_login();
$stview=1;

echo'<h1>Nachrichten</h1>';
Outputer::nachrichtenMenu();



$id=$_GET['id'];
$typ=$_GET['t'];

$nachricht=Nachricht::getById($id);
if ($nachricht===NULL) {
	$msg='Nachricht wurde nicht gefunden';
	gotoP('nachrichten');
}
$nachricht->read();

?>
<form method="post"
	action="?page=nachrichten-send&id=<?php echo $id; ?>">
<table class="f10" background="img/de/msg/block_bg22.gif"
	cellpadding="0" cellspacing="0" width="440">
<tbody><tr><td colspan="5"><img src="img/de/msg/block_bg21.gif"
	border="0" height="41" width="440"></td></tr>

<tr>

<td width="12"></td>
<td rowspan="2" width="86">
<?php
$x='a';
if ($typ==1) $x='b';
echo'<img src="img/de/msg/block_bg24'.$x.'.gif" border="0"
	height="34" width="77">';
?>
</td>
<td background="img/de/msg/underline.gif" width="230">
<?php
echo $nachricht->get('von');
?>
</td>
<td class="right" width="100">
<?php
echo date('d.m.y',strtotime($nachricht->get('zeit')));
?>
</td>
<td width="12"></td>
</tr>

<tr>
<td width="12"></td>
<td background="img/de/msg/underline.gif" width="230">
<?php
echo $nachricht->get('betreff');
?>
</td>
<td class="right" width="100">
<?php
echo date('H:i:s',strtotime($nachricht->get('zeit')));
?>
</td>
<td width="12"></td>
</tr>

<tr><td colspan="5"><div>
<img src="img/un/a/x.gif" border="0" height="6" width="440">
</div>
<img src="img/de/msg/block_bg25.gif" border="0" height="18"
	width="440">
</td></tr>

<tr>
<td width="12">
<img src="img/un/a/x.gif" border="0" height="250" width="1"></td>
<td colspan="3" background="img/de/msg/underline.gif" valign="top">
<?php
echo str_replace(chr(13),'<br>',$nachricht->get('text'));
?>
</td>
<td width="12"></td>
</tr>
<tr>
<td colspan="5" align="center">
<?php
echo'<input name="id" value="'.$id.'" type="hidden">';
if ($typ==0) {
?>
<input value="" name="s1" src="img/de/b/ant1.gif"
	onMouseDown="btm1('s1','','img/de/b/ant2.gif',1)"
	onMouseOver="btm1('s1','','img/de/b/ant3.gif',1)"
	onMouseUp="btm0()" onMouseOut="btm0()" border="0"
	height="20" type="image" width="80">
<?php } ?>
</td>
</tr>
<tr>
<td colspan="5"><img src="img/de/msg/block_bg23.gif"
	border="0" height="18" width="440"></td>
</tr>

</tbody></table>
</form>

<div>