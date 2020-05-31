<?php

//beschreibung speichern
if ($_GET['do']=='cd') {
	$login_user->set('besch',$_POST['besch']);
	$login_dorf->set('name',$_POST['dname']);
	$login_user->setKonfig('berichte',$_POST['handel']);
	gotoP('spieler');
}
