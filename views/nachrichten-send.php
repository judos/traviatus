<?php
if (!$execute) die('');
needed_login();
$stview=1;

echo'<h1>Nachrichten</h1>';
Outputer::nachrichtenMenu();


$text='';
$betreff='';
if (isset($_GET['name']))
	$an=$_GET['name'];
else
	$an='';

if (isset($msg)) {
	$text=$var2;
	$betreff=$var1;
	$an=$var3;
}

if (isset($_GET['id'])) {	//Antworten auf eine Nachricht
	$nachricht=Nachricht::getById($_GET['id']);
	$an=$nachricht->get('an');
	$betreff=$nachricht->get('betreff');
	$text=chr(13).'____________'.chr(13).
		$an.' schrieb:'.chr(13).chr(13).$nachricht->get('text');
}

if (isset($msg))
	echo $msg;
?>

<form method="post" action="?page=nachrichten-send&do=send"
	accept-charset="UTF-8" name="msg">
<table class="f10" background="img/de/msg/block_bg22.gif"
	cellpadding="0" cellspacing="0" width="440">
<tbody><tr><td colspan="4"><img src="img/de/msg/block_bg21.gif"
	border="0" height="41" width="440"></td></tr>

<tr>

<td width="12"></td>
<td rowspan="2" width="86"><img src="img/de/msg/block_bg24b.gif"
	border="0" height="34"></td>
<?php
echo'<td width="330"><input name="an" value="'.$an.'" size="40"
	maxlength="20" style="border: 0px none ;
	background-image: url(img/de/msg/underline.gif);" type="text">
	</td>';
?>
<td width="12"></td>
</tr>

<tr>
<td width="12"></td>
<?php
echo'<td width="330"><input name="betreff" value="'.$betreff.'"
	size="40" maxlength="35" style="border: 0px none;
	background-image: url(img/de/msg/underline.gif);" type="text">
	</td>';
?>
<td width="12"></td>
</tr>

<tr><td colspan="4"><img src="img/de/msg/block_bg25.gif"
	border="0" height="18" width="440"></td></tr>

<tr>
<td width="12"><img src="img/un/a/x.gif" border="0"
	height="250" width="1"></td>
<td colspan="2" align="center">
<textarea name="text" id="igm" cols="55" rows="15" class="f10"
	style="background-image: url(img/de/msg/underline.gif);">
<?php echo $text; ?>
</textarea></td>

<td width="12"></td>
</tr>
<tr><td colspan="4" align="center">
<input value="" name="s1" src="img/de/b/snd1.gif"
	onMouseDown="btm1('s1','','img/de/b/snd2.gif',1)"
	onMouseOver="btm1('s1','',img/de/b/snd3.gif',1)"
	onMouseUp="btm0()" onMouseOut="btm0()"
	onClick="return urlaub()" border="0" height="20"
	type="image" width="80"></td></tr>
<tr><td colspan="4">
<img src="img/de/msg/block_bg23.gif" border="0" height="18"
	width="440">
</td></tr>
</tbody></table>
</form>
<div>