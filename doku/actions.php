<?php
if (!$execute) die('');

outgame_links();
outgame_blocks();
?>

<h2>Actions:</h2><br />

<?php
$text= <<<TEXT
Ein Link mit ?do=irgendwas wird bei anklicken automatisch an die actions weitergeleitet.
Im actions Ordner sollte daher eine Datei mit dem gleichen Namen sein.
In der Datei z.B: actions/allianz.php kann dann die Aktion ausgeführt werden.
Alle Variablen die in \$_GET gespeichert sind, werden an das Action-skript
weitergeleitet und sind weiterhin über \$_GET verfügbar.

Um von der Action-skript Datei aus Variabeln zurück geben zu können, können folgende Variabeln benützt werden:


TEXT;

$table=<<<TABLE
<table class="tbg" cellpadding="2" cellspacing="1">
<tr class="cbg1"><td colspan="2">Übertragung der Variabeln</td></tr>
<tr class="s7"><td>\$_GET[\$x]<br />Ausnahme: \$_GET['do']</td><td>Via URL</td></tr>
<tr class="s7"><td>\$msg</td><td>Via URL</td></tr>
<tr class="s7"><td>\$var1 - \$var4</td><td>Via Datenbank</td></tr>
</table><br>
TABLE;

$text2=<<<TEXT2
Nachdem die Aktion abgearbeitet wurde, wird automatisch zur normalen View Seite weitergeleitet.
D.h. dem Aktions-skript bleibt zur Erfolgs- oder Fehlermeldung einzig die Rückgabe via der Variabeln.

Zu Debugging Zwecken sollte im Stammindex die automatische Weiterleitung verhindert werden.
Ansonsten sieht man Fehlermeldungen die mit der Funktion x() erzeugt werden nicht.
Dazu einfach die Variable \$action_forwarding im Stammindex auf false setzen.

Falls in einer Actionsdatei ein Objekt einer beliebigen Klasse verändert wird, muss am Ende global_save() aufgerufen werden damit die Objekte ihre Änderungen auch in der DB sichern.
TEXT2;

echo t($text).$table.t($text2);
?>
<br /><br />
<a href="?">Zurück</a>