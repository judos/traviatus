<?php

if (!$execute) die('');
$stview=0;
$template=0;

echo'Cronjob 5 Min<br>';

if ($_GET['user']!='cronuser' or $_GET['pw']!='updater_cron1234')
	die('Wrong Username or PW');
echo'<br>';

//Alle Dörfer updaten
echo'Alle Dörfer updaten:<br>';
$koords_dorfer=Dorf::getAllKoords();
foreach($koords_dorfer as $x => $arr) {
	foreach($arr as $y => $value) {
		if ($value) {
			$dorf=Dorf::getByXY($x,$y);
			echo $dorf.'<br>';
			Updater::dorf($dorf);
		}
	}
}
echo'<br>';

//Natur updaten
echo'Natur updaten:<br>';
var_dump(Updater:natur1h());


?>