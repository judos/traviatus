<?php

function init_classes_var() {
	global $classes;
	global $path;
	if ($handle = opendir($path.'classes/')) {
		$classes=array();
		while (false !== ($file = readdir($handle))) {
		  if ($file!='.' and $file!='..' and substr($file,-10)=='.class.php')
			$classes[]=substr($file,0,-10);
		}
		//sort($classes);
		closedir($handle);
	}
	
	else
		x('Ordner mit Klassen kann nicht geöffnet werden.');
}


function include_classes($rel_path='') {
	global $classes;
	foreach ($classes as $class) {
		include($rel_path.'classes/'.$class.'.class.php');
	}
	include($rel_path.'extern/Debug.class.php');
}

function global_save() {
	global $classes;
	foreach ($classes as $class) {
		if (($class::$save)==true) {
			$class::saveAll();
		}
	}
}
