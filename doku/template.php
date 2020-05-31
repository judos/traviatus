<?php
if (!$execute) die('');

outgame_links();
outgame_blocks();

echo'<h2>Template:</h2><br>';
$t=<<<TEXT
Das Html wird zu einem grossen Teil direkt aus dem Template entnommen. Dieses findet sich unter 'template/std.html'.
Im Template gibt es viele Teile die nachträglich durch PHP gefüllt werden. Dazu sind Tags der Form ++NAME_DES_TAGS++ im Template eingebaut.
Im Template gibt es ebenfalls Blöcke die durch Html-Kommentare der folgenden Form umschlossen sind:
&lt;!--block:menu-->
&lt;!--/menu-->
Diese können von PHP mit den Funktionen showblock(\$name) und hideblock(\$name) angezeigt oder ausgeblendet werden.

Ebenfalls sehr viel HTML wird von der Klasse Outputer ausgegeben. Diese bietet Methoden zum Anzeigen von Menüs sowie den Übersichten Dorf1, Dorf2 und auch einzelnen Buttons an.
Das Anzeigen der ingame Links, Blocks und das Menü (oberer Balken) bietet ebenfalls diese Klasse über die Methoden links(),blocks(),menu() an.
Da jedoch Ingame immer wieder die gleichen Funktionen für das Anzeigen aufgerufen werden müssen existiert auch ein PHP-Template für den Ingame Bereich.

Siehe im Folgenden die Datei 'template/std-view.php'. Diese tut folgendes:
<ol><li>Prüft login, leitet ggf. nach '?page=login' um</li>
<li>Gibt links, blocks und menu aus (via Outputer Klasse)</li>
<li>Benutzt den Tag ++PAGE++ wo der Inhalt dann effektiv eingefüllt wird</li>
<li>Gibt Dörfer und das Lager aus</li>
</ol>
TEXT;

echo t($t);
?>

<a href="?">Zurück</a>