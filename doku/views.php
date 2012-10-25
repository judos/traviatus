<?php
if (!$execute) die('');

outgame_links();
outgame_blocks();
?>

<h2>Views:</h2><br>

Eine Datei im Ordner 'views' wird direkt als eine Seite interpretiert. 
Angezeigt wird die Seite wenn der Link '?page=name' dem Namen der Seite entspricht.
<br><br>
Jeder View wird empfohlen folgende Aufrufe am Anfang zu t�tigen:

<style type="text/css">
table.grau { border:lightgray 1px solid; border-collapse:collapse; margin:5px; }
.grau td { border:1px solid lightgray; }
</style>

<table class="grau" cellpadding="5" border="1">
	<tr><td width="220">if (!$execute) die('');</td>
		<td>Die Seite sollte nur �ber den Index aufgerufen werden k�nnen</td></tr>
	<tr><td>needed_login();</td>
		<td>Falls nur eingeloggte Benutzer die Seite sehen sollen</td></tr>
	<tr><td>$stview=1;</td>
		<td>Falls die Standard Anzeige verwendet werden soll (Links,Men�,Rohstoff Anzeige)</td></tr>
	<tr><td>echo'&lt;h1>Titel der Seite&lt;/h1>';</td>
		<td>Sollte die erste Ausgabe der Seite sein.</td></tr>
</table>

<a href="?">Zur�ck</a>