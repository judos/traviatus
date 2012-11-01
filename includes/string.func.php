<?php

function savePost($varName,$standardValue){
	if (isset($_POST[$varName]))
		return $_POST[$varName];
	else
		return $standardValue;
}

function saveGet($varName,$standardValue){
	if (isset($_GET[$varName]))
		return $_GET[$varName];
	else
		return $standardValue;
}

function strcount($haystack, $needle, $offset=0) {
	$count=0;
	if ($offset > strlen($haystack))
		trigger_error("strcount(): Offset not contained in string.", E_USER_WARNING);
	for ($count=0; (($pos = strpos($haystack, $needle, $offset)) !== false); $count++) {
		$offset = $pos + strlen($needle);
	}
	return $count;
}

function strCountBefore($haystack,$needleBefore,$needle) {
	$pos=strpos($haystack,$needle);
	$count=strcount(substr($haystack,0,$pos+1),$needleBefore);
	return $count;
}

function strRepeatSep($str,$multiplier,$sep='') {
	return implode($sep, array_fill(0,$multiplier,$str));
}

function curPageUrl() {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".
			$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL.=$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}


function replace_special_chars($te) {
	$find=array('ä','ö','ü','Ä','Ö','Ü',chr(228),chr(246),
				chr(252),chr(196),chr(214),chr(220),'»','«',chr(223),
				'ß','Ø','´');
	$rep=array('&auml;','&ouml;','&uuml;','&Auml;','&Ouml;',
			   '&Uuml;','&auml;','&ouml;','&uuml;','&Auml;',
				'&Ouml;','&Uuml;','&raquo;','&laquo;','&szlig;',
				'&szlig;','&Oslash;','&rsquo;');
	$te=str_replace($find,$rep,$te);

	$find=array('Ã¤','Ã¶','Ã¼','ÃŸ','Ãœ','Â','Ã˜');
	$rep=array('&auml;','&ouml;','&uuml;','&szlig;','&Uuml;','','&Oslash;');
	$te=str_replace($find,$rep,$te);
	
	return $te;
}

function t($te) {
	return str_replace(chr(13),'<br>',$te);
}

function make_save_text($text) {
	$text = htmlspecialchars($text, ENT_QUOTES);
	return $text;
}