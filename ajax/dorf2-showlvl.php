<?php


set_include_path('../');
$path='ajax/';
$script=TRUE;



require("../index.php");

$anzeigen=$_POST['show'];
$login_user->setKonfig('dorf2_stufen_anzeige',$anzeigen);

global_save();

?>