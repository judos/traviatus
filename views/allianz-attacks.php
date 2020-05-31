<?php
if (!$execute) die('');
needed_login();
$stview=1;

Updater::dorf($login_dorf);

$ally=Allianz::getById($login_user->get('ally'));
if ($ally===NULL) gotoP('dorf2');

echo'<h1>'.$ally->get('name').'</h1>';
Outputer::allianzMenu();

if (!isset($_GET['s'])) {
	echo'<table cellpadding="2" cellspacing="1" class="tbg"><tbody>
	  <tr class="rbg"><td colspan="3">Allianz Kämpfe</td></tr>
	  <tr>
		<td>Ereignis</td>
		<td>Datum</td>
	  </tr>';

	$attacks=$ally->getKampfe();
	if (empty($attacks)){
		echo'<tr class="s7"><td colspan="2" style="color:#AAAAAA;">Zurzeit gibt es keine Kämpfe</td></tr>';
	}
	foreach($attacks as $entry) {
		echo'<tr class="s7"><td><a href="?page=allianz-attacks&s='.$entry->get('keyid').'">'.$entry->get('betreff').'</td><td>'.zeitAngabe($entry->get('datetime'),TRUE).'</a></td></tr>';
	}
	echo'</tbody></table>';
}
else {
	$s=$_GET['s'];
	$bericht=AllianzBericht::getByAllyId($ally,$s);
	echo'<table cellpadding="2" cellspacing="1" class="tbg"><tbody>
		<tr class="rbg"><td class="s7">Betreff:</td><td class="s7">'.$bericht->get('betreff').'</td></tr>
		<tr><td class="s7">Zeit / Datum:</td><td class="s7">'.$bericht->get('datetime').'</td></tr>
		<tr><td colspan="2">'.$bericht->toHtml().'</td></tr>
		</table><br>
		<a href="?page=allianz-attacks">-> Zurück</a>';
}





?>