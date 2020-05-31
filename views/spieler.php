<?php
if (!$execute) die('');
needed_login();
$stview=1;

//Name des Spielers
$name=@$_GET['name'];
if (!isset($name)) $name=$login_user->get('name');
$show_user=Spieler::getByName($name);



//Spielerprofil Menü
echo'<h1>Spieler Profil</h1>';
if ($name==$login_user->get('name')) {
	Outputer::profilMenu(false);
}
echo'<p></p>';

if ($show_user===NULL) {
	echo'<div class="msg_error">Spieler nicht gefunden.</div>';
}
else {

//Dörfer laden
$dorfer=$show_user->dorfer();
?>


<table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr>
<td class="rbg" colspan="3">Spieler
<?php echo $show_user->get('name'); ?></td>
</tr>

<tr>
<td colspan="2" width="50%">Details:</td>
<td width="50%">Beschreibung:</td>
</tr>
<tr><td colspan="2"></td><td></td></tr>
<tr><td class="s7">Rang:</td><td class="s7">
<?php echo $show_user->rang(); ?></td>

<td rowspan="7" class="slr3">
<?php echo t($show_user->get('besch')); ?>
</td></tr>
<tr class="s7"><td>Volk:</td><td>
<? // ?>
<?php echo Outputer::volk($show_user->get('volk')); ?></td></tr>
<?php
$allyid=$show_user->get('ally');
if ($allyid>0) {
	$ally=Allianz::getById($allyid);
	echo'<tr class="s7"><td>Allianz:</td><td><a href="?page=allianz&id='.$allyid.'">'.$ally->get('tag').'</a></td></tr>';
}
?>
<tr class="s7"><td>Dörfer:</td><td>
<?php echo sizeof($dorfer); ?></td></tr>

<tr class="s7"><td>Bevölkerung:</td><td>
<?php echo $show_user->get('einwohner'); ?></td></tr>
<tr><td></td><td></td></tr>
<tr class="s7"><td colspan="2">
<?php
if ($name==$login_user->get('name'))
	echo'<a href="?page=spieler-profil">» Profil bearbeiten</a>';
else
	echo'<a href="?page=nachrichten-send&name='.$name.'">'.
		'» Nachricht schreiben</a>';
?>
</td></tr>
</tbody></table>
<p></p>

<table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr>
	<td class="rbg" colspan="3">Dörfer:</td>
</tr>
<tr>
	<td width="50%">Name</td>
	<td width="25%">Einwohner</td>
	<td width="25%">Koordinaten</td>
</tr>
<?php
foreach($dorfer as $dorf) {
	echo'<tr><td class="s7"><a href="?page=karte&x='.$dorf->get('x').
		'&y='.$dorf->get('y').'">'.$dorf->get('name').'</a>';
	if ($dorf->get('grosse')==1) echo'<span class="c"> (Hauptdorf)</span>';
	echo'</td><td>'.$dorf->get('einwohner').'</td>
		<td>('.$dorf->get('x').'|'.$dorf->get('y').')</td></tr>';
}
echo'</tbody></table>';

echo'<div>';

}