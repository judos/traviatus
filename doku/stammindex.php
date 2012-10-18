<?php
if (!$execute) die('');

outgame_links();
outgame_blocks();

echo'<h2>Stammindex:</h2><br>'.
	'Der Stammindex organisiert die ganze Software in folgender Reihenfolge:';

$does=array(
	'Error reporting für PHP wird festgelegt.',
	'Pfad des Skript wird übernommen und relativer Pfad ausgerechnet',
	'Alle Include Dateien werden manuell eingebunden',
	'Verbindung zur SQL Datenbank wird hergestellt',
	'Alle Fehler werden an den internen Fehlerbehandler weitergeleitet (Funktion x())',
	'Wichtigste Variabeln werden initialisiert',
	'Klassen werden eingebunden',
	'Aktuell anzuzeigende Seite wird ermittelt, Login und logout vollzogen',
	'Actions werden an die Skripte im actions Ordner weitergeleitet',
	'Template wird abgerufen',
	'View Seite wird aufgerufen oder 404 falls diese nicht existiert',
	'Std-view prüft login und gibt standard Ingame layout aus',
	'Alle Klassen werden zum Speichern aufgefordert',
	'Html wird aufbereitet und eingefüllt',
	'Blöcke vom Template werden geprüft und angezeigt/verborgen',
	'Html wird ausgegeben (mit/ohne template), falls die Seite kein reines Skript war'
	);

echo'<ol>';
foreach($does as $v)
	echo'<li>'.$v.'</li>';
echo'</ol>';
?>
<a href="?">Zurück</a>
