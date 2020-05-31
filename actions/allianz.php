<?php


if ($_GET['do']=='new') {
	if ($login_user->get('ally')==0) {
		$highest=$login_dorf->highest();
		if ($highest[18]>1) {
			$ally=Allianz::create($_POST['tag'],$_POST['name']);
			$rang=$ally->createRang('Gründer','all_rights');
			$login_user->set('ally',$ally->get('id'));
			$login_user->set('ally_rang',$rang->get('rang_id'));
		}
	}
}

if ($_GET['do']=='accept_invitation') {
	$einladungen=$login_user->allianzEinladungen();
	$id=$_GET['id'];
	$allys=arrayObjectsContaining($einladungen,'id',$id);
	if (!empty($allys)) {
		$ally=$allys[0];
		$login_user->set('ally',$ally->get('id'));
		$login_user->set('ally_rang',0);
		$login_user->loscheEinladung('all');
		$news='[spieler]'.$login_user->get('id').'[/spieler] ist in die Allianz eingetreten.';
		$ally->insertNews($news);
		gotoP('allianz');
	}
}

if ($_GET['do']=='decline_invitation') {
	$einladungen=$login_user->allianzEinladungen();
	$id=$_GET['id'];
	$login_user->loscheEinladung($id);
	$ally=Allianz::getById($id);
	if ($ally!==NULL) {
		$news='[spieler]'.$login_user->get('id').'[/spieler] hat die Einladung abgelehnt.';
		$ally->insertNews($news);
	}

	gotoP('build&highest=18');
}