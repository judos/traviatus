<?php
if (!$execute) die('');

outgame_links();
outgame_blocks();

if (!isset($_GET['s'])) {
	echo'<h2>Klassen:</h2><br>';
	$c=$classes;
	sort($c);
	$t= <<<TEXT
Jede Klasse muss mit der Endung '.class.php' im Ordner 'classes' gespeichert sein.

Um zu gewährleisten dass alle Änderungen einer Klasse in der Datenbank gesichert werden muss eine Methode 'saveAll()' in jeder Klasse vorhanden sein.
Diese wird automatisch für jede Klasse ausgeführt nachdem die Seite vollständig geladen und angezeigt wurde.


Index der Klassen:

TEXT;
	
	echo t($t).'<table style="width:100%;">';
	$h=ceil(sizeof($c)/2);
	for($i=0;$i<$h;$i++) {
		echo'<tr>';
		for($j=0;$j<=$h;$j+=$h)
			if(isset($c[$i+$j]))
			echo'<td><a href="?page=classes&s='.$c[$i+$j].'">'.$c[$i+$j].'</a></td>';
		echo'</tr>';
	}
	echo'</table><br> <a href="?">Zurück</a>';
}

else {
	$klasse=$_GET['s'];
	echo'<h2>Klasse "'.$klasse.'":</h2><br/>';
	$inhalt=@file_get_contents('../classes/'.$klasse.'.class.php');
	if ($inhalt===FALSE) {
		echo'Datei wurde nicht gefunden.';
	}
	else {
	  preg_match_all('/(\w+) (static)? ?function (\w+)\((.*)\)/i',$inhalt,$functions);
		//$inhalt_linien=explode(chr(13),$inhalt);

    $func=$functions[0];
    foreach($func as $index=> $all) {
      $lineNr=strCountBefore($inhalt,chr(13),$all)+1;
      //$pos=strpos($inhalt,$all);
      //$pos2=strpos($inhalt,'return ',$pos);
      //x1($pos2);
      $matches=array();
      foreach($functions as $i=>$arr) {
        if ($i!=0)
          $matches[]=$arr[$index];
      }
      echo $lineNr.': ';
      $tag='';
      $tag2='';
      if ($matches[0]=='public') { $c='#008000'; $te='+ '; }
      if ($matches[0]=='protected') { $c='#A0A000;'; $te='# '; }
      if ($matches[0]=='private') { $c='red'; $te='- '; }
      if ($matches[1]=='static') { $tag.='<u>'; $tag2.='</u>'; }

      echo'<span style="color:'.$c.';">'.$tag.$te.'<b>'.$matches[2].'</b> ('.$matches[3].')'.$tag2.'</span>';
      //x1($matches);
      echo'<br>';
    }


	}
	echo'<br><a href="?page=classes">Zurück</a>';

}
?>