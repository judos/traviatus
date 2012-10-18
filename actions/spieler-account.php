<?php

if ($_GET['do']=='change_account') {
	$msg='';
	if ($_POST['pw2']!='' or $_POST['pw3']!='') {
		if ($_POST['pw2']==$_POST['pw3']) {
			$login_user->set('pw',md5($_POST['pw2']));
			$msg=msg_ok('Passwort geändert!');
		}
		else {
			$msg.=msg_error('Passwort Wiederholung stimmt nicht!');
		}
	}
	
	$gebs=array(16,19,20,21,17,37,25,24);
	$changed=false;
	foreach($gebs as $gebId) {
		$value=@$_POST['g'.$gebId];
		if ($value===NULL)
			$value=0;
		else
			$changed=true;
		
		$login_user->setKonfig('geb_'.$gebId,$value);
		
	}
	if ($changed) {
		$msg.=msg_ok('Konfiguration geändert');
		$login_user->save();
	}
}