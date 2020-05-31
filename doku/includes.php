<?php
if (!$execute) die('');

outgame_links();
outgame_blocks();

echo'<h2>Includes:</h2><br>';
$t=<<<TEXT
Alle nicht klassen bezogenen Funktionen werden im Ordner 'includes' gespeichert.

Jedes Skript in diesem Ordner wird manuell vom Index per include() eingebunden.
Siehe index.php ab ca. Zeile Nr. 11.


TEXT;

echo t($t);
?>
<a href="?">Zurück</a>
