<?php
if (!$execute) die('');

outgame_links();
outgame_blocks();

echo'<h2>Weltwunder:</h2><br>';
$t=<<<TEXT
In der Tabelle L�nder ist ein Attribut ww gespeichert, welches festlegt ob im Dorf ein Weltwunder gebaut werden kann oder nicht.

Die Karte zeigt selbst�ndig die WW-D�rfer speziell gekennzeichnet an. 
Wenn ein WW-Dorf eingenommen wird, wird das Hauptgeb�ude auf dem Feld 23 (statt 26) erstellt.

Die Felder im Zentrum des Dorfes (25,26,29,30,33 siehe classes/WW.php) werden automatisch ausgeblendet.

Der Weltwunderbauplatz wird als Feld 26 definiert (standardm�ssig das einzelne oben im Zentrum) und ist um einiges gr�sser.


TEXT;

echo t($t);
?>
<a href="?">Zur�ck</a>
