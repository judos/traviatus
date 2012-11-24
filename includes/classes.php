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
		closedir($handle);
	}
	
	else
		x('Ordner mit Klassen kann nicht geöffnet werden.');
}

function include_classes() {
	global $classes;
	spl_autoload_register(function ($class) {
		include 'classes/' . $class . '.class.php';
	});
	include 'extern/Debug.class.php';
}

function global_save() {
	global $classes;
	foreach ($classes as $class) {
		if (($class::$save)==true) {
			$class::saveAll();
		}
	}
}
