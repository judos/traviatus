<?php
$username=$login_user->get('name');

if ($_GET['do']=='send') {
	$an=$_POST['an'];
	$betreff=$_POST['betreff'];
	$text=$_POST['text'];
	
	if ($an=='@ally') {
		$ally=Allianz::getById($login_user->get('ally'));
		if ($ally===NULL) {
			$msg='Du bist in keiner Allianz!';
			$var1=$betreff;
			$var2=$text;
			$var3=$an;
		}
		else {
			$mitglieder=$ally->mitglieder();
			$betreff='[Ally] ';
			foreach($mitglieder as $spieler) {
				if ($spieler!==$login_user)
					Nachricht::sendTo($spieler->get('name'),$betreff,$text,array(true,false));
			}
			//Nur eine Kopie davon im Postausgang speichern
			Nachricht::sendTo('@ally',$betreff,$text,array(false,true));
			$msg='Gesendet! (An alle ausser an dich)';
			$page='nachrichten';
		}
	}
	else {
		if (Nachricht::sendTo($an,$betreff,$text)) {
			$msg='Gesendet!';
			$page='nachrichten';
		}
		else {
			$page='nachrichten-send';
			$msg='Dieser User existiert nicht!';
			$var1=$betreff;
			$var2=$text;
			$var3=$an;
		}
	}
}