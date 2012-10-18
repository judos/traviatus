<?php
if (!$execute) die('');
needed_login();
$stview=1;

Updater::dorf($login_dorf);

$ally=Allianz::getById($login_user->get('ally'));
if ($ally===NULL) gotoP('dorf2');

echo'<h1>'.$ally->get('name').'</h1>';
Outputer::allianzMenu();


echo'<table cellpadding="2" cellspacing="1" class="tbg"><tbody>
  <tr class="rbg"><td colspan="3">Allianz Ereignisse</td></tr>
  <tr>
    <td>Ereignis</td>
    <td>Datum</td>
  </tr>';

$news=$ally->getNews();
if (empty($news)){
	echo'<tr class="s7"><td colspan="2" style="color:#AAAAAA;">Zurzeit gibt es keine Neuigkeiten</td></tr>';
}
foreach($news as $entry) {
	echo'<tr class="s7"><td>'.insert_tra_tags($entry['news']).'</td><td>'.zeitAngabe($entry['datum'],TRUE).'</td></tr>';
}
echo'</tbody></table>';





?>