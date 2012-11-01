<?php

function dumpS($x) {
	return substr(dump($x),0,15);
}

function dump($x) {
	ob_start();
	var_dump($x);
	$content=ob_get_contents();
	ob_end_clean();
	return $content;
}

//Alle Fehler und PHP Notizen abfangen und selber auf der Seite ausgeben
function handleError($errno, $errstr, $errfile, $errline, array $errcontext){
	// error was suppressed with the @-operator
	if (0 === error_reporting())
		return false;
		//Daten an die Fehlerfunktion weitergeben (function x() siehe includes/functions.php)
	x('ERROR'.$errno.' '.$errstr);
}
//set_error_handler('handleError');

//Zum debuggen verwendet
//Call this function to get an error log with stacktrace in the bottom panel
function xx() {
	global $error, $error_output_fatal;
	echo $error;
	$error='';
	$error_output_fatal=true;
	x(func_get_args());
}
function x() {
	global $error;
	global $error_count;
	global $error_output_fatal;
	
	$tmp='';
	$nr=0;
	$array = func_get_args();
	$c='DDD';
	$tmp.='<div style="border:1px #999 solid; background-color:#'.$c.'; '.
		'padding:5px 10px 5px 10px; margin:3px 30px 0px 10px;" align="center">';
	foreach($array as $var) {
		if ($nr==0) $c='#000000';
		if ($nr==1) $c='#00A000';
		if ($nr==2) $c='#999999';
		if ($nr==3) $c='#0000A0';
		if ($nr==4) $c='#B0B000';
		if ($nr==5) $c='#A00000';
		$nr= ($nr+1)%6;
		$tmp.='<span style="color:'.$c.';">';
		if (is_object($var) and is_callable(array($var,'toString')))
			$tmp.=$var->toString();
		elseif (is_object($var) and is_callable(array($var,'__toString')))
			$tmp.=$var->__toString();
		else {
			if (is_arrayObjects($var)) {
				$tmp.='ArrayObjects ('.sizeof($var).')';
				if (!empty($var)) $tmp.=':';
				foreach($var as $key=>$object) {
					$tmp.='<br><table><tr><td valign="top"><p style="padding:5px; margin:3px 0px 0px 10px;">['.$key.'] =></p></td><td>';
					$tmp.=$object;
					$tmp.='</td></tr></table>';
				}
			}
			else{
				if (is_string($var) and substr($var,0,5)=='ERROR'){
					$var=explode(' ',$var,2);
					$nr=substr($var[0],5);
					$tmp.='<span style="color:red;font-weight:bold;">'.FriendlyErrorType($nr).'</span> '.
						dump($var[1]);
				}
				else
					$tmp.='<span style="color:black;font-weight:bold;">UserOutput:</span> '.
						dump($var);
			}
		}
		$tmp.='</span><br><br>';
	}
	$tmp=substr($tmp,0,-8);
  
  //add stack trace to error log
	$trace=debug_backtrace();
	if (preg_match('/.+index.php/i',$trace[0]['file']))
		unset($trace[0]);
	$firstKey=array_keys($trace);
	$firstKey=$firstKey[0];
	if (!isset($trace[$firstKey]['file']) and !isset($trace[$firstKey]['line']))
		unset($trace[$firstKey]);
	foreach($trace as $key=>$value){
		unset($trace[$key]['args']);
		unset($trace[$key]['object']);
	}
	$h='<div style="background-color: #d6ffef; border: 1px solid #bbb; margin: 30px; padding: 7px; text-align: left">';
	foreach($trace as $nr => $value){
		$h.=$nr.' '.@$value['file'].' <span style="color:blue; margin-left:10px;">line '.@$value['line'].'</span><br />'.
			'&nbsp;&nbsp;&nbsp;<span style="color:green; font-weight:bold;">'.@$value['class'].' '.@$value['type'].' '.@$value['function'].
			'</span><br /><br />';
	}
	$h.='</div></div>';
	$tmp.=$h;
	if ($error_output_fatal)
		echo $tmp;
	else
		$error.=$tmp;
	
	$error_count++;
}


function FriendlyErrorType($type){
    $return ="";
    if($type & E_ERROR) // 1 //
        $return.='& E_ERROR ';
    if($type & E_WARNING) // 2 //
        $return.='& E_WARNING ';
    if($type & E_PARSE) // 4 //
        $return.='& E_PARSE ';
    if($type & E_NOTICE) // 8 //
        $return.='& E_NOTICE ';
    if($type & E_CORE_ERROR) // 16 //
        $return.='& E_CORE_ERROR ';
    if($type & E_CORE_WARNING) // 32 //
        $return.='& E_CORE_WARNING ';
    if($type & E_CORE_ERROR) // 64 //
        $return.='& E_COMPILE_ERROR ';
    if($type & E_CORE_WARNING) // 128 //
        $return.='& E_COMPILE_WARNING ';
    if($type & E_USER_ERROR) // 256 //
        $return.='& E_USER_ERROR ';
    if($type & E_USER_WARNING) // 512 //
        $return.='& E_USER_WARNING ';
    if($type & E_USER_NOTICE) // 1024 //
        $return.='& E_USER_NOTICE ';
    if($type & E_STRICT) // 2048 //
        $return.='& E_STRICT ';
    if($type & E_RECOVERABLE_ERROR) // 4096 //
        $return.='& E_RECOVERABLE_ERROR ';
    if($type & E_DEPRECATED) // 8192 //
        $return.='& E_DEPRECATED ';
    if($type & E_USER_DEPRECATED) // 16384 //
        $return.='& E_USER_DEPRECATED ';
    return substr($return,2);
} 