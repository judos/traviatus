<?php
if (!$execute) die('');
needed_login();
$stview=1;

Outputer::profilMenu();

?>


<form action="?page=spieler-profil&do=cd" method="post">

<p></p><table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr>
<td class="rbg" colspan="3">Spieler
<?php echo $login_user->get('name'); ?></td>
</tr>

<tr>
<td colspan="2" width="50%">Details:</td>
<td width="50%">Beschreibung:</td>
</tr>

<tr><td colspan="2"></td><td></td></tr>

<tr class="s7">
<td height="20">Dorfname:</td><td>
<?php echo'<input name="dname" value="'.$login_dorf->get('name').'" size="20" maxlength="20" class="fm" type="text">'; ?>
</td>
<td rowspan="3">
<textarea name="besch" cols="27" rows="12" class="f10 fm_text">
<?php echo $login_user->get('besch'); ?>
</textarea>
</td>
</tr>

<tr class="s7"><td height="20">Nachrichten:</td>
<td>
<?php echo'<input type=checkbox name="handel" value="1"';
if ($login_user->getKonfig('berichte')==1) echo' checked';
echo'>&nbsp;Handelsberichte speichern</td></tr>';
?>

<tr class="s7"><td colspan="2"</td></tr>
</tbody></table>

<p align="center">
<?php
Outputer::button('s1','s');
?>
</p>
</form>

<div>