<?php
if (!$execute) die('');
needed_login();
$stview=1;

Updater::dorf($login_dorf);

$ally=Allianz::getById($login_user->get('ally'));
if ($ally===NULL) gotoP('dorf2');

echo'<h1>'.$ally->get('name').'</h1>';
Outputer::allianzMenu();


echo'<br />Wird nicht implementiert werden vorläufig.';





?>