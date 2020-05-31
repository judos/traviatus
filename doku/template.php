<?php
if (!$execute) die('');

outgame_links();
outgame_blocks();

echo'<h2>Template:</h2><br>';
$t=<<<TEXT
Das Html wird zu einem grossen Teil direkt aus dem Template entnommen. Dieses findet sich unter 'template/std.html'.
Im Template gibt es viele Teile die nachtr�glich durch PHP gef�llt werden. Dazu sind Tags der Form ++NAME_DES_TAGS++ im Template eingebaut.
Im Template gibt es ebenfalls Bl�cke die durch Html-Kommentare der folgenden Form umschlossen sind:
&lt;!--block:menu-->
&lt;!--/menu-->
Diese k�nnen von PHP mit den Funktionen showblock(\$name) und hideblock(\$name) angezeigt oder ausgeblendet werden.

Ebenfalls sehr viel HTML wird von der Klasse Outputer ausgegeben. Diese bietet Methoden zum Anzeigen von Men�s sowie den �bersichten Dorf1, Dorf2 und auch einzelnen Buttons an.
Das Anzeigen der ingame Links, Blocks und das Men� (oberer Balken) bietet ebenfalls diese Klasse �ber die Methoden links(),blocks(),menu() an.
Da jedoch Ingame immer wieder die gleichen Funktionen f�r das Anzeigen aufgerufen werden m�ssen existiert auch ein PHP-Template f�r den Ingame Bereich.

Siehe im Folgenden die Datei 'template/std-view.php'. Diese tut folgendes:
<ol><li>Pr�ft login, leitet ggf. nach '?page=login' um</li>
<li>Gibt links, blocks und menu aus (via Outputer Klasse)</li>
<li>Benutzt den Tag ++PAGE++ wo der Inhalt dann effektiv eingef�llt wird</li>
<li>Gibt D�rfer und das Lager aus</li>
</ol>
TEXT;

echo t($t);
?>

<a href="?">Zur�ck</a>