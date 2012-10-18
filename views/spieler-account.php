<?php
if (!$execute) die('');
needed_login();
$stview=1;


Outputer::profilMenu();

if (isset($_GET['msg'])) echo $_GET['msg'].'<br>';

?>

<form action="?page=spieler-account&do=change_account" method="post">

<table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr>
<td class="rbg" colspan="3">Direkt Links</td></tr>
<tr><td class="s7" colspan="3">Eigene Links:</td></tr>
<tr><td>Nr.</td><td>Linkname</td><td>Linkziel</td></tr>
<tr><td class="s7" colspan="3">Links zu wichtigen Gebäuden:</td></tr>
<?php
$gebs=array(16,19,20,21,17,37,25,24);
foreach($gebs as $gebId) {
	$gebName=GebeudeTyp::getById($gebId)->get('name');
	if ($gebId==25) $gebName.='/ Palast';
	$value=$login_user->getKonfig('geb_'.$gebId);
	$c='';
	if ($value==1) $c='checked';
	echo'<tr><td><input type="checkbox" name="g'.$gebId.'" value="1" '.$c.' /></td>
		<td colspan="2" class="s7">'.ucwords($gebName).'</td></tr>';
}
?>
</tbody></table>

<p></p>

<table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr>
<td class="rbg" colspan="2">Passwort ändern</td>
</tr>

<!--
<tr class="s7"><td width="50%">Altes Passwort:</td>
<td width="50%"><input class="fm f110" name="pw1" maxlength="20" type="password"></td>
</tr>-->

<tr class="s7"><td>Neues Passwort:</td>
<td><input class="fm f110" name="pw2" maxlength="20" type="password"></td>

</tr>


<tr class="s7"><td>Wiederholung:</td>
<td><input class="fm f110" name="pw3" maxlength="20" type="password"></td>
</tr></tbody></table>
<p align="center">
<?php
Outputer::button('s1','s');
?>
</p>
</form>

<div>