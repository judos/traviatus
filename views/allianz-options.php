<?php
if (!$execute) die('');
needed_login();
$stview=1;

$ally=Allianz::getById($login_user->get('ally'));
if ($ally===NULL) gotoP('dorf2');
$rang=$ally->getRang($login_user->get('ally_rang'));

echo'<h1>'.$ally->get('name').'</h1>';
Outputer::allianzMenu();

if (@$_GET['s']==='leave') {
	echo'<p>Bist du sicher, dass du die Allianz verlassen möchtest?</p>
		<a href="?page=allianz-options&do=leave" style="color:red;">
			Verlassen</a>&nbsp;
		<a href="?page=allianz-options">Abbrechen</a>
		';
}
elseif (isset($_GET['s'])) {
	if ($rang===NULL or $rang->get($_GET['s'])==0) unset($_GET['s']);
	if ($_GET['s']=='ally_auflosen') {
		echo'<p>Bist du sicher, dass du die <u>Allianz auflösen</u> möchtest?
			<br>Alle Mitglieder werden aus der Allianz entlassen und die
			Allianz mitsamt Forum und allen Einstellungen gelöscht.</p>
		<a href="?page=allianz-options&do=ally_auflosen" style="color:red;">
			Allianz Auflösen</a>&nbsp;
		<a href="?page=allianz-options">Abbrechen</a>
		';
	}
	if ($_GET['s']=='user_einladen') {
		echo'<form method="post" action="?page=allianz-options&'.
				'do=user_einladen&s=user_einladen">
			<table cellpadding="2" cellspacing="1" class="tbg"
				style="width:50%;"><tbody>
			<tr class="rbg">
				<td colspan="2">Spieler in Allianz einladen</td></tr>
			<tr><td>Name:</td><td><input class="fm" name="name" /></td></tr>
			</table>';
		echo'<p>'.@$msg.'</p>';
		Outputer::button('ok','ok');

		$eingeladen=$ally->spielerEingeladen();
		if (!empty($eingeladen)) {
			echo'<br><br><table cellpadding="2" cellspacing="1" class="tbg"
					style="width:50%;"><tbody>
				<tr class="rbg"><td colspan="2">Bereits eingeladen:</td></tr>';
			foreach($eingeladen as $spieler) {
				echo'<tr><td width="10%"><a href="?page=allianz-options&'.
					'do=user_ausladen&s=user_einladen&id='.$spieler->get('id').'">
					<img src="img/un/a/del.gif">
					</a></td><td class="s7"><a href="?page=spieler&name='.
					$spieler->get('name').'">'.$spieler->get('name').'</a></td>
					</tr>';
			}
			echo'</table>';
		}
	}
	if ($_GET['s']=='user_entlassen') {
		echo'<form method="post" action="?page=allianz-options&'.
				'do=user_entlassen&s=user_entlassen">
			<table cellpadding="2" cellspacing="1" class="tbg"
				style="width:50%;"><tbody>
			<tr class="rbg">
				<td colspan="2">Spieler entlassen</td></tr>
			<tr><td>Name:</td><td><select class="fm"
				name="id" style="width:100%;">';
		$mitglieder=$ally->mitglieder();
		foreach($mitglieder as $spieler) {
			echo'<option value="'.$spieler->get('id').'">'.
				$spieler->get('name').'</option>';
		}
		echo'</select></td></tr></table><p>'.@$msg.'</p>';
		Outputer::button('ok','ok');
	}
	if ($_GET['s']=='rundmail') {
		gotoP('nachrichten-send&name=@ally');
	}
	if ($_GET['s']=='beschreibung_andern') {
		require_once('ally/beschreibungAndern.php');
	}
	if ($_GET['s']==='rang_vergeben') {
		require_once('ally/rangVergeben.php');
	}
}


if (!isset($_GET['s'])) {
	echo'<table cellpadding="2" cellspacing="1" class="tbg"
		style="width:50%;"><tbody>
		<tr class="rbg"><td colspan="3">Optionen</td></tr>';

	$rechte=AllianzRang::rechte();
	$namen=AllianzRang::rechteNamen();

	$links=@array_combine($rechte,$namen);
	if ($links===false) {
		x('Namen der Rechte prüfen. Anzahl sollte mit den tinyint(1) Feldern
			der Tabelle tr_ally_range Übereinstimmen!');
	}
	if (is_array($links)) {
		foreach($links as $option=>$name) {
			if ($rang!==NULL and $rang->get($option)==1) {
				echo'<tr><td class="s7">
					<a href="?page=allianz-options&s='.$option.'">'.
					$name.'</a></td></tr>';
			}
		}
	}
	unset($links);
	echo'<tr><td class="s7"><a href="?page=allianz-options&s=leave">
		Allianz verlassen</a></td></tr>';

	echo'</table>';
}






?>