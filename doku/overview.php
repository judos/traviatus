<?php
if (!$execute) die('');

outgame_links();
outgame_blocks();
?>


<h2>Dokumentation:</h2>

<ul>
<?php

$exclude=array('index.php','overview.php' ,'.','..');

$dir=opendir('.');
while( ($file=readdir($dir)) !== false) {
	if (in_array($file,$exclude))
		continue;
	$link = substr($file,0,-4);
	echo '<li><a href="?page='.$link.'">'.ucwords($link).'</a></li>';
}

?>