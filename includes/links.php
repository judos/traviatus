<?php

function add_link($eintrag,$url='',$htmlatts=array()) {
	global $links;
	global $path;
	if ($url=='') {
		$links.='<a href="'.$path.'?page='.$eintrag.'">'.
			ucwords($eintrag).'</a>';
	}
	else {
		$add='';
		foreach($htmlatts as $key => $value)
			$add.=" $key=\"$value\"";
		$links.='<a onfocus="this.blur();" href="'.$url.'"'.$add.'>'.$eintrag.'</a>';
	}
}

function outgame_links() {
	add_link('login');
	add_link('anleitung');
	if (Diverses::get('register')) add_link('anmelden');
	add_link('PhpMyadmin','/phpmyadmin/',array('target'=>'_blank'));
}