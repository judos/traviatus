<?php
error_reporting(E_ALL | E_WARNING);
$load_time=microtime();

//Falls ein externer Index (wie doku) diesen Index benützt muss $path gesetzt sein
//um den Pfad der existierenden Links (z.b. Menu links) gültig anzuzeigen.
if (!isset($path)) $path='';
$anz=substr_count($path,'/');
$path=str_repeat('../',$anz);

//Includes
include "includes/functions.php";
include "includes/arrays.func.php";
include "includes/debug.func.php";
include "includes/arrayobjects.func.php";
include "includes/math.func.php";
include "includes/string.func.php";
include "includes/object.func.php";
include "includes/vector4.php";
include "includes/configs.php";
include "includes/links.php";
include "includes/classes.php";
include "includes/backup_db.php";
connect();

//Die Funktion handleError verwenden um Fehler etc. abzufangen.
set_error_handler('handleError');

//variablen zurücksetzen
$action_forwarding=false;  //Ob nach einem Action automatisch weitergeleitet wird
                           //Spiel-Einstellung: true
$error_output_fatal=true; //Falls ein Fatal_Error im PHP existiert sollte dies auf
                           // True umgeschalten werden um alle Fehler anzuzeigen
						   //Spiel-Einstellung: false
$title='Traviatus R'.ROUND_ID.' ('.VERSION.')'; //Titel der Runde

$blocks_shown=array();     //HTML-Template-Blocks die angezeigt wurden
$blocks_hidden=array();    //HTML-Template-Blocks die verborgen wurden
$body_onload='';           //Javascript das beim Laden ausgeführt wird
$error='';                 //Hier werden Fehlermeldungen gespeichert
$error_count=0;            //Hier werden Fehler gezählt
$javascripts='';           //Zusätzliche javascripts in html-code
$links='';
$login_dorf=NULL;
$login_user=NULL;
$menu='';
$page_msg='';//Beim login mit falschen Daten wird eine Nachricht darin gespeichert
$stview=0;
$template='std';
$timerNr=1;
$tooltip=1;
unset($var1,$var2,$var3,$var4);

//Classes
global $classes;
init_classes_var();
include_classes();
include "includes/footer.php";

//Aufgerufene Seite auslesen
if(!isset($_GET['page'])) {
	$page='login';
}
else {
	$page=make_save_text($_GET['page']);
}
unset($_GET['page']);

//Einloggen, ausloggen, login testen
global_actions();

//Actions ausführen
if (isset($_GET['do'])) {
	if (file_exists('actions/'.$page.'.php')) {
		unset($msg);
		unset($var1,$var2,$var3,$var4);
		require('actions/'.$page.'.php');
		unset($_GET['do']);
		//global_save is executed in gotoP()
		if($action_forwarding)
			gotoP($page);
	}
}


if (isset($_GET['temp'])) {
	$temp=Temp::load($_GET['temp']);
	$var1=$temp->var1;
	$var2=$temp->var2;
	$var3=$temp->var3;
	$var4=$temp->var4;
}


//Html vom template abrufen
$html=file_get_contents('template/'.$template.'.html',true);


//Testen ob Seite existiert
$page_path='views/'.$page.'.php';
if (isset($path) and $path!='') $page_path=$page.'.php';
if (!file_exists($page_path)) {
	$page_path='views/404.php';
}


//Seite einbinden
if (isset($_GET['msg']))
	$msg=$_GET['msg'];
ob_start();
$execute=true;
try {
	require($page_path);
} catch(Exception $e) {
	x('Ein Fehler ist aufgetreten:',$e->getMessage());
}
unset($execute);
$content=ob_get_contents();
ob_end_clean();


//Std View einbinden
if (file_exists('template/std-view.php') and $stview==1) {
	ob_start();
	require('template/std-view.php');
	$content2=ob_get_contents();
	ob_end_clean();
	$content=str_replace('++PAGE++',$content,$content2);
}


//Alles speichern
global_save();

//Inhalt vorbereiten
$body_onload.='start();';
if ($body_onload!='') {
	$body_onload='onload="'.$body_onload.'"';
}

//Links ersetzen
$html=str_replace('++LINKS++',$links,$html);
$html=str_replace('++BODY_ON_LOAD++',$body_onload,$html);
$html=str_replace('++MENU++',$menu,$html);
$html=str_replace('++TITLE++',$title,$html);
$html=str_replace('++CONTENT++',$content,$html);
$html=str_replace('++FOOTER++',$footer,$html);
$rel_path_escape='';
if (isset($path) and $path!='') $rel_path_escape='../';
$html=str_replace('++JAVASCRIPTS++',$javascripts,$html);
$html=str_replace('++REL_PATH++',$rel_path_escape,$html);

if (in_array('servertime',$blocks_shown)) {
	$x=serverzeit($load_time);
	foreach($x as $key => $var) {
		$html=str_replace('++'.$key.'++',$var,$html);
	}
}

prooveblocks();
showblock('error');
if ($error!='') $error='Errors and notifications ('.$error_count.'): '.$error;
$html=str_replace('++ERROR++',$error,$html);



if ($template===false)
	$html=$content;
$html=replace_special_chars($html);

if (!isset($script) or $script==0) {
	echo $html;
}


?>