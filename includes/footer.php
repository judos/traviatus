<?php

$footer='by judos 2012, visit
	<a href="http://www.travian.de">www.travian.de</a><br />
	See <a href="'.$path.'doku/">Doku</a> for further Help<br />';

$ver=@db_getVersion();
$u=$ver['used'];
$c=$ver['current'];

if ($u==$c)
	$footer.='<a href="?page=db">DB version uptodate.</a>';
elseif ($u<$c or !isset($c))
	$footer.='<a href="?page=db" style="font-size:48pt; background:red;">DB version outdated! fix here</a>';
elseif ($u>$c)
	$footer.='<a href="?page=db" style="font-size:48pt; background:red;">DB version newer than backup. fix here</a>';

?>