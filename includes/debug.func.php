<?php

function xOn() {
	global $error_log_on;
	$error_log_on=true;
}
function xOff() {
	global $error_log_on;
	echo'fooo';
	$error_log_on=false;
}

function dumpShort($x) {
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
	xx('ERROR'.$errno.' '.$errstr);
}
//set_error_handler('handleError');

//Zum debuggen verwendet
//Call this function to get an error log with stacktrace in the bottom panel
function xx() {
	global $error_log_on;
	if (isset($error_log_on) && $error_log_on==false)
		return;
	global $error, $error_output_fatal;
	echo $error;
	$error='';
	$error_output_fatal=true;
	x(func_get_args());
}
function x() {
	global $error_log_on;
	if (isset($error_log_on) && $error_log_on==false)
		return;
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
		$tmp.='<span style="color:'.$c.'; display: block;">';
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
	//if (preg_match('/.+index.php/i',$trace[0]['file']))
	//	unset($trace[0]);
	$firstKey=array_keys($trace);
	if (sizeof($firstKey)>0) {
		$firstKey=$firstKey[0];
		if (!isset($trace[$firstKey]['file']) and !isset($trace[$firstKey]['line']))
			unset($trace[$firstKey]);
	}
	foreach($trace as $key=>$value){
		unset($trace[$key]['args']);
		unset($trace[$key]['object']);
	}
	$traceTable='<table border="1" style="background-color: #d6ffef; border: 1px solid #aaa; padding: 7px; text-align: left; border-collapse:collapse;" cellspacing="2" cellpadding="5">';
	$traceTable.='<tr style="font-weight:bold"><td>Stack</td><td>File</td><td>Line</td><td>Call</td></tr>';
	foreach($trace as $nr => $value){
		$function = @$value['class'].' '.@$value['type'].' '.@$value['function'];
		$file=str_replace('D:\\julian\\web php,js\\traviatus2012\\','',@$value['file']);
		
		$traceTable.='<tr><td>'.$nr.'</td><td><a href="file:///'.@$value['file'].'">'.$file.'</a></td>'.
			'<td style="color:blue;text-align:right;">'.@$value['line'].'</td><td style="color:green;font-weight:bold;">'.$function.'</td></tr>';
	}
	$traceTable.='</table>';

	$scrollDiv='<div style="height: auto !important; max-height: 150px; '.
		'margin: 10px; overflow:scroll; border:inset lightgray;overflow-x: hidden;display: inline-block">';
	
	$tmp.=$scrollDiv.$traceTable.'</div></div>';
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