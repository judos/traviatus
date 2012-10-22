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

function toHtmlName($te) {
	$find=array('/',' ');
	$repl=array('','_');
	return str_replace($find,$repl,$te);
}

function strcount($haystack, $needle, $offset=0) {
	$count=0;
  if ($offset > strlen($haystack)) trigger_error("strcount(): Offset not contained in string.", E_USER_WARNING);
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

//Array $chancen gibt an wie wahrscheinlich der key sein soll
//Key der gewählten Elements wird zurück gegeben.
function wkeitVerteilung($chancen) {
	$zahl=mt_rand(0,99)+0.001*mt_rand(0,999);
	$i=0;
	foreach($chancen as $key => $chance) {
		if ($zahl<=$chance) return $key;
		$zahl-=$chance;
	}
	x('wkeitVerteilungs funktion hat keinen key gefunden.');
	new Errorlog('wkeitVerteilungs funktion hat
	              keinen key gefunden.');
}

//Liefert mit der Wahrscheinlichkeit von $chance % eine 1 zurück
function wahrscheinlichkeit($chance) {
	if ($chance==100) return true;
	$zahl=mt_rand(0,99)+0.001*mt_rand(0,999);
	if ($zahl<=$chance) return true;
	return 0;
}


//Ersetzt in Travian texte [spieler]id[/spieler] und [allianz]id[/allianz]
function insert_tra_tags($text) {
  $text=preg_replace('/\[url=(.+)\](.+)\[\/url\]/i','<a href="http://\1">\2</a>',$text); //[url=link]text[/link]
  $text=preg_replace('/\[url\](.+)\[\/url\]/i','<a href="http://\1">\1</a>',$text); //[url]link[/link]
  $text=preg_replace('/\[b\](.+)\[\/b\]/i','<b>\1</b>',$text); //[b]text[/b]
  $text=preg_replace('/\[u\](.+)\[\/u\]/i','<u>\1</u>',$text); //[u]text[/u]
  $text=preg_replace('/\[i\](.+)\[\/i\]/i','<i>\1</i>',$text); //[i]text[/i]
  $text=preg_replace('/\[center\](.+)\[\/center\]/i','<center>\1</center>',$text); //[center]text[/center]
  $text=preg_replace('/\[img\](.+)\[\/img\]/','<img src="\1" />',$text); //[img]link[/img]
	$text=preg_replace_callback("/\[spieler\]([^\[\]]+)\[\/spieler\]/i","insert_tra_tags_spieler",$text);
	$text=preg_replace_callback("/\[allianz\]([^\[\]]+)\[\/allianz\]/i","insert_tra_tags_ally",$text);
	return $text;
}
function insert_tra_tags_ally($treffer) {
	$ally=Allianz::getById($treffer[1]);
	if ($ally===NULL) return '<i>&raquo;Allianz nicht gefunden&laquo;</i>';
	return '<a href="?page=allianz&id='.$ally->get('id').'">'.$ally->get('name').'</a>';
}
function insert_tra_tags_spieler($treffer) {
	$spieler=Spieler::getById($treffer[1]);
	if ($spieler===NULL) return '<i>&raquo;Spieler nicht gefunden&laquo;</i>';
	return '<a href="?page=spieler&name='.$spieler->get('name').'">'.$spieler->get('name').'</a>';
}

//Ersetzt ++Name++ durch Wert aus der diversus Tabelle
function insert_div($text) {
	//Search something like "++HELLO++" and replace it with what
	// insert_div_callback returns on the input
	$x=preg_replace_callback("/\+\+(.+)\+\+/","insert_div_callback",$text);
	return $x;
}
function insert_div_callback($treffer) {
	return Diverses::get($treffer[1]);
}



function array_insert(&$array,$pos,$val) {
	$array2 = array_splice($array,$pos);
	$array[] = $val;
	$array = array_merge($array,$array2);
}



//Sort an array of arrays bi a specific attribute and an order
//possible orders are asc and desc
function sortArray2(&$arr,$att,$order) {
	global $compare_att;
	global $compare_order;
	$compare_att=$att;
	$compare_order=$order;
	uasort($arr,'sortArray2_compare_att');
}
function sortArray2_compare_att($a, $b) {
	global $compare_att,$compare_order;
	if ($compare_order=='asc')
		return strnatcmp($a[$compare_att],$b[$compare_att]);
	else
		return strnatcmp($b[$compare_att],$a[$compare_att]);
}


function now($dauer=0) {
	return date('Y-m-d H:i:s',time()+$dauer);
}

function needed_login() {
	global $login_user;
	if ($login_user===NULL) {
		gotoP('login');
	}
}

//Zum debuggen verwendet
//Call this function to get an error log with stacktrace in the bottom panel
function xx() {
	$array = func_get_args();
	foreach($array as $var)
		var_dump($var);
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
					$tmp.=$object->toString();
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
		$h.=$nr.' '.$value['file'].' <span style="color:blue; margin-left:10px;">line '.$value['line'].'</span><br />'.
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


function msg_ok($msg) {
	return '<div class="msg_ok">'.$msg.'</div>';
}

function msg_error($msg) {
	return '<div class="msg_error">'.$msg.'</div>';
}

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


function array_add($arr1,$arr2) {
	foreach($arr1 as $id => $nr) {
		if (isset($arr1[$id])) {
			$arr1[$id]+=$arr2[$id];
		}
	}
	foreach($arr2 as $id => $nr) {
		if (!isset($arr1[$id])) {
			$arr1[$id]=$nr;
		}
	}
	return $arr1;
}


function strRepeatSep($str,$multiplier,$sep='') {
	return implode($sep, array_fill(0,$multiplier,$str));
}

function outgame_blocks() {
	showblock('footer');
	hideblock('servertime');
	hideblock('menu');
}

function prooveblocks() {
	global $html,$error;
	$pos=0;
	while(strpos($html,'<!--block:',$pos)!==false) {
		$pos=strpos($html,'<!--block:',$pos);
		$pos2=strpos($html,'-->',$pos);
		$start=$pos+strlen('<!--block:');
		$len=$pos2-$start;
		$name=substr($html,$start,$len);
		if ($name!='error') {
			$error.='Block not hidden or shown: '.$name.'<br />';
			hideblock($name);
		}
		$pos=$pos+1;
	}
}

function hideblock($name) {
	global $html,$blocks_hidden;
	$pos=strpos($html,'<!--block:'.$name.'-->');
	$pos2=strpos($html,'<!--/'.$name.'-->',$pos);
	if ($pos!==false && $pos2!==false) {
		$pos3=$pos2+strlen('<!--/'.$name.'-->');
		$html=substr($html,0,$pos).substr($html,$pos3);
	}
	array_push($blocks_hidden,$name);
}

function showblock($name) {
	global $html,$blocks_shown;
	$html=str_replace('<!--block:'.$name.'-->','',$html);
	$html=str_replace('<!--/'.$name.'-->','',$html);
	array_push($blocks_shown,$name);
}


function zeitAngabe($stamp,$date=FALSE) {
	if ($date) $stamp=strtotime($stamp);
	$word='';
	if (date('d.m.Y',time())==date('d.m.Y',$stamp))
		$word='heute';
	if (date('d.m.Y',time()+86400)==date('d.m.Y',$stamp))
		$word='morgen';
	if ($word=='') $word='am '.date('d.m.Y',$stamp);
	return $word.' um '.date('H:i',$stamp).' Uhr';
}

function zeit_dauer($stamp) {
	$h=floor($stamp/3600);
	$m=floor(($stamp%3600)/60);
	$s=floor($stamp-60*(60*$h+$m));
	if ($m<10) $m='0'.$m;
	if ($s<10) $s='0'.$s;
	return $h.':'.$m.':'.$s;

	//return date('H:i:s',$stamp);
}

function serverzeit($load_time) {
	$start=Diverses::get('rundenstart');
	$d=floor((time()-strtotime($start))/86400);
	$result=array('CALC_MS'=> round((microtime()-$load_time)*1000),
		'CALC_TIME'=> date('H:i:s',time()),
		'CALC_RUNDAYS'=> $d,
		'CALC_RUNTIME'=> zeit_dauer(time()
			-strtotime($start)-$d*86400) );
	if ($result['CALC_MS']<0) $result['CALC_MS']+=1000;
	return $result;
}


function connect() {
	$co='req';
	//zur Datenbank verbinden
	if ($co=='online') {
		$host='however.ch:3306';
		$user='web375';
		$pw='';
		$db='usr_web375_7';
	}
	elseif ($co=='local'){
		$host='localhost';
		$user='root';
		$pw='localsqlpw';
		$db='traviatus';
	}
	elseif ($co=='req'){
		require('db_config.php');
	}
	$link = mysql_connect($host,$user,$pw);
	if (!$link)
		die('Verbindung nicht möglich : ' . mysql_error());
	if(!mysql_select_db($db))
		die('Fehler Datenbank konnte nicht ausgewÃ¤hlt werden.');
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


function special_chars_wrong_codec($te) {
	global $error,$page,$msg;
	if (strpos($te,'Ã¤')!==FALSE or strpos($te,'Ã¶')!==FALSE or strpos($te,'Ã¼')!==FALSE or strpos($te,'ÃŸ')!==FALSE
		or strpos($te,'Ãœ')!==FALSE or strpos($te,'Â»')!==FALSE) {
		$para='?page='.$page;
		if ($msg!='') $para.='&msg='.$msg;
		foreach($_GET as $key => $value) {
			if ($key!='sschars')
				$para.='&'.$key.'='.$value;
		}
		$link='';
		if (!isset($_GET['sschars'])) $link='&sschars=show">Zeigen';
		else $link='">Verstecken';
		x('Warnung: Auf dieser Seite gibt es Sonderzeichen, die nicht im richtigen Zeichensatz codiert sind.
			<a href="'.$para.$link.'</a>');
	}
}


function replace_special_chars($te) {
	$find=array('ä','ö','ü','Ä','Ö','Ü',chr(228),chr(246),
				chr(252),chr(196),chr(214),chr(220),'»','«',chr(223),
				'ß','Ø');
	$rep=array('&auml;','&ouml;','&uuml;','&Auml;','&Ouml;',
			   '&Uuml;','&auml;','&ouml;','&uuml;','&Auml;',
				'&Ouml;','&Uuml;','&raquo;','&laquo;','&szlig;',
				'&szlig;','&Oslash;');
	$te=str_replace($find,$rep,$te);
	if (!isset($_GET['sschars'])) {
		$find=array('Ã¤','Ã¶','Ã¼','ÃŸ','Ãœ','Â','Ã˜');
		$rep=array('&auml;','&ouml;','&uuml;','&szlig;','&Uuml;','','&Oslash;');
		$te=str_replace($find,$rep,$te);
	}
	return $te;
}

function t($te) {
	return str_replace(chr(13),'<br>',$te);
}

function make_save_text($text) {
	$text = htmlspecialchars($text, ENT_QUOTES);
	return $text;
}

function make_save_int($int) {
	return (int)$int;
}

function roundTo($value,$smallestPart) {
	return $smallestPart*round($value/$smallestPart,0);
}

function global_actions() {
	global $login_user,$login_dorf,$page,$page_msg,$html;
	if (isset($_GET['do']) and $_GET['do']=='logout') {
		setcookie('uid','',time()-3600);
		setcookie('pid','',time()-3600);
		setcookie('nid','',time()-3600);
		$_COOKIE['uid']='';
		$_COOKIE['pid']='';
		$_COOKIE['nid']='';
		setcookie('dorfx','',time()-3600);
		setcookie('dorfy','',time()-3600);
		$_COOKIE['dorfx']='';
		$_COOKIE['dorfy']='';
		$page='logout';
	}
	elseif (isset($_GET['do']) and $_GET['do']=='login') {
		unset($_GET['do']);
		unset($_SERVER['QUERY_STRING']);
		$name=$_POST['name'];
		$pw=md5($_POST['pw']);

		$user=Spieler::checkLogin($name,$pw);
		if ($user===NULL) {
			$page='login';
			$page_msg.='Falsche login Daten.';
		}
		else {
			setcookie('uid',$user->get('id'));
			setcookie('pid',$user->get('pw'));
			setcookie('nid',$user->get('name'));
			$_COOKIE['uid']=$user->get('id');
			$_COOKIE['pid']=$user->get('pw');
			$_COOKIE['nid']=$user->get('name');
			$login_user=$user;
			$login_dorf=$user->startDorf();
			gotoP('dorf1');
		}
	}
	else {
		if (isset($_COOKIE['nid'])) {
			$login_user=Spieler::checkLogin($_COOKIE['nid'],
											$_COOKIE['pid']);
			if ($login_user!==NULL) {
				$login_dorf=Dorf::check_change_Dorf();
			}
		}
	}
}

function gotoP($page) {
	global $msg;
	global $var1,$var2,$var3,$var4;
	static $called=false;
	if (!$called) {
		$para='';
		unset($temps);
		if ($var1.$var2.$var3.$var4!='') $temps=true;
		if ($temps) {
			$temp=new Temp($var1,$var2,$var3,$var4);
			$para='&temp='.$temp->id;
		}
		if ($msg!='') $para.='&msg='.$msg;
		foreach($_GET as $key => $value) {
			$para.='&'.$key.'='.$value;
		}
		global_save();
		$called=true;
		header("Location: ?page=$page".$para);
		die('');

	}
}

?>