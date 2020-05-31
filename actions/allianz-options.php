<?php


if ($_GET['do']=='leave') {
	$ally=Allianz::getById($login_user->get('ally'));
	$login_user->set('ally',0);
	$login_user->set('ally_rang',0);
	$highest=$login_dorf->highest();
	$msg=$ally->anzMitglieder();
	if ($ally->anzMitglieder()==0) {
		$ally->auflosen();
	}
	else {
		$ally->insertNews('[spieler]'.$login_user->get('id').'[/spieler] hat die Allianz verlassen.');
	}

	if ($highest[18]>0) {
		$hgid=$login_dorf->highestGid();
		gotoP('build&gid='.$hgid[18]);
	}
}
else {
	//Hat User berechtigung dies zu tun?
	$do=$_GET['do'];

	$ally=Allianz::getById($login_user->get('ally'));
	if ($ally===NULL) gotoP('dorf2');
	$rang=$ally->getRang($login_user->get('ally_rang'));
	if ($rang===NULL) gotoP('allianz');

	//ähnliche Rechte die nicht in der db gespeichert sind
	$hash=array('user_ausladen'=>'user_einladen',
							'rang_delete'=>'rang_vergeben',
							'rang_new'=>'rang_vergeben');

	//User hat genug Rechte
	if (@$rang->get($do)==1 or @$rang->get($hash[$do])==1) {

		if ($do==='ally_auflosen') {
			$mitglieder=$ally->mitglieder();
			foreach($mitglieder as $mitglied) {
				$mitglied->set('ally',0);
				$mitglied->set('ally_rang',0);
			}
			$ally->auflosen();
		}
		if ($do==='user_einladen') {
			$user=Spieler::getByName($_POST['name']);
			if ($user===NULL)
				$msg='Spieler nicht gefunden';
			else{
				$msg=$ally->spielerEinladen($user);
				$news='[spieler]'.$login_user->get('id').'[/spieler] hat [spieler]'.$user->get('id').
					'[/spieler] in die Allianz eingeladen.';
				$ally->insertNews($news);
			}
		}
		if ($do==='user_ausladen') {
			$user=Spieler::getById($_GET['id']);
			$ally->spielerAusladen($user);
			$news='[spieler]'.$login_user->get('id').'[/spieler] hat die Einladung von [spieler]'.$user->get('id').
					'[/spieler] zurückgezogen.';
				$ally->insertNews($news);
		}
		if ($do==='user_entlassen') {
			$user=Spieler::getById($_POST['id']);
			$msg=$ally->spielerEntlassen($user);
		}

		if ($do==='beschreibung_andern') {
			$ally->set('beschreibung',$_POST['beschreibung']);
			$ally->set('beschreibung2',$_POST['beschreibung2']);
			if ($_POST['name']!='')
				$ally->set('name',$_POST['name']);
			else
				$msg='Bitte Name eingeben.';
			if ($_POST['tag']!='')
				$ally->set('tag',$_POST['tag']);
			else
				$msg='Bitte Tag eingeben.';
		}
		if ($do==='rang_new') {
			$name=$_POST['name'];
			$rechte=AllianzRang::rechte();
			$neue_rechte=array();
			foreach($rechte as $nr=>$value) {
				$neue_rechte[$nr]=(@$_POST['r'.$nr]==='on');
			}
			$ally->createRang($name,$neue_rechte);
			$msg='Posten erstellt.';
			$_GET['i']='new';
		}
		if ($do==='rang_delete') {
			$rang_id=$_POST['posten'];
			$rang=$ally->getRang($rang_id);
			if ($rang!==NULL) {
				$rang->delete();
				$mitglieder=$ally->mitglieder();
				foreach($mitglieder as $spieler) {
					if ($spieler->get('ally_rang')==$rang_id)
						$spieler->set('ally_rang',0);
				}
				$msg='Posten gelöscht.';
			}
			else
				$msg='Posten nicht gefunden.';
			$_GET['i']='del';
		}
		if ($do==='rang_vergeben') {
			$spieler=Spieler::getById($_POST['id']);
			$rang=$ally->getRang($_POST['posten']);
			if ($rang!==NULL) {
				$spieler->set('ally_rang',$rang->get('rang_id'));
				$msg='Posten vergeben.';
			}
			else
				$msg='Posten nicht gefunden.';
			$_GET['i']='vergabe';
		}
	}
}