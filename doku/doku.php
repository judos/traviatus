<?php
if (!$execute) die('');

outgame_links();
outgame_blocks();

echo'<h2>Doku:</h2><br>';
$t=<<<TEXT
Die Dokumentation ist in ihrem eigenen Ordner 'doku' gespeichert.

Der index legt dabei nur den relativen Pfad zum Stammverzeichnis fest und ruft dann den Index darin auf.


TEXT;

echo t($t);
?>
<a href="?">Zurück</a>