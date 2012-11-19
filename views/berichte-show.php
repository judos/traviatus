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
echo $bericht->toHtml();
?>

</td></tr>
</tbody></table>
<div>