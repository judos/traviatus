<?php
if (!$execute) die('');

outgame_links();
outgame_blocks();

echo'<h2>Weltwunder:</h2><br>';
$t=<<<TEXT
In der Tabelle Länder ist ein Attribut ww gespeichert, welches festlegt ob im Dorf ein Weltwunder gebaut werden kann oder nicht.

Die Karte zeigt selbständig die WW-Dörfer speziell gekennzeichnet an. 
Wenn ein WW-Dorf eingenommen wird, wird das Hauptgebäude auf dem Feld 23 (statt 26) erstellt.

Die Felder im Zentrum des Dorfes (25,26,29,30,33 siehe classes/WW.php) werden automatisch ausgeblendet.

Der Weltwunderbauplatz wird als Feld 26 definiert (standardmässig das einzelne oben im Zentrum) und ist um einiges grösser.


TEXT;

echo t($t);
?>
<a href="?">Zurück</a>
