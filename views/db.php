<?php
if (!$execute) die('');

outgame_links();
outgame_blocks();

$ver=@db_getVersion();
$u=$ver['used'];
$c=$ver['current'];

if (isset($msg))
	echo msg_ok($msg).'<br /><br />';

echo'<div style="border:1px solid gray; padding:5px;">';


if ($u<$c or !isset($u)){
	echo'<form method="post" action="?page=db&do=update">
		Gesamte DB zerstören und neuste DB importieren:<br />
		<span style="color:red;">(WARNUNG: Damit werden alle Daten der
		bisherigen DB gelöscht, dafür ist die Struktur der DB
		aber wieder auf dem neusten Stand)</span><br /><br />';
	Outputer::button('ok','ok',array('align'=>'right'));
	echo'</form><br />';
}
elseif($u==$c){
	echo'Datenbank ist auf dem neusten Stand. (Version '.$u.')<br />
		Bei Änderungen der Struktur der DB <u>immer</u> die Version
		der DB mit folgendem Button anpassen:<br /><br />
		<form method="post" action="?page=db&do=backup">';
		Outputer::button('sp','s',array('align'=>'right'));
	echo'</form><br />';
}
elseif($u>$c){
	echo'Deine DB scheint neuer zu sein als das letzte Backup.<br />
		Ein neues Backup wird gerade angefertigt. Bitte danach
		falls die Dateien verteilt sind die Dateien committen.';
	db_saveNewVersion();
}
echo'</div>';

if ($page_msg!='') {
	echo '<span style="color:red;">'.$page_msg.'</span>';
}
?>