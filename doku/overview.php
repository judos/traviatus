<?php
if (!$execute) die('');

outgame_links();
outgame_blocks();
?>


<h2>Dokumentation:</h2>

<ul>
<?php
$arr=array('actions','classes','doku','includes','stammindex','template','views');
foreach($arr as $link) {
	echo'<li><a href="?page='.$link.'">'.ucwords($link).'</a></li>';
}
?>