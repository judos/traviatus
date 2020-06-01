<?php

function add_javascript($fileName) {
	global $javascripts;
	$javascripts.='<script src="++REL_PATH++js/'.$fileName.'.js" type=text/javascript></script>';
}


function toHtmlName($te) {
	$find=array('/',' ');
	$repl=array('','_');
	return str_replace($find,$repl,$te);
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
	return $spieler->getLink();
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

function now($dauer=0) {
	return date('Y-m-d H:i:s',time()+$dauer);
}

function needed_login() {
	global $login_user;
	if ($login_user===NULL) {
		gotoP('login');
	}
}

function msg_ok($msg) {
	return '<div class="msg_ok">'.$msg.'</div>';
}

function msg_error($msg) {
	return '<div class="msg_error">'.$msg.'</div>';
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
}

function serverzeit($load_time) {
	$start=Diverses::get('rundenstart');
	$d=floor((time()-strtotime($start))/86400);
	$calc_ms = round((microtime(true)-$load_time)*1000);
	
	$result= [
		'CALC_MS'=> $calc_ms,
		'CALC_TIME'=> date('H:i:s',time()),
		'CALC_RUNDAYS'=> $d,
		'CALC_RUNTIME'=> zeit_dauer(time()-strtotime($start)-$d*86400)
	];
	if ($result['CALC_MS']<0) $result['CALC_MS']+=1000;
	return $result;
}


function connect() {
	global $mysqli;
	require('db_config.php');
	$mysqli = new mysqli($host,$user,$pw,$db);
	if ($mysqli->connect_errno) {
			die("Verbindung fehlgeschlagen: " . $mysqli->connect_error);
	}
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
			$page_msg.='Falsche login Daten. Passwörter waren in der DB korrupt gespeichert, dort diesen Wert beim Benutzer speichern: '.$pw;
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
		if (isset($temps)) {
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