<?php


$stview=1;


$text='Hi mein Profil, hier mal ein kurzer Travian text zu [Spieler]judos[/spieler] hihihi hahaha
	und hier noch [Spieler]asdf jklö[/spieler]<br>
	Allianz [Allianz]asdf[/allianz] <br>
	[Allianz]qwer[/allianz]';

echo insert_tra_tags($text);




/*
$ally=Allianz::getById(17);

x($ally->getNews());

$ally->insertNews('Ich ist in die Allianz eingetreten');

x($ally->getNews());

*/


/*
x(Updater::natur1h());



$t='Hallo zusammen Hallo
Hallo Hallo

Tschüüs Hallo Hallo';

$anz=strcountBefore($t,chr(13),'Tschüüs');
x($anz);


*/



/*echo "'&auml;','&ouml;','&uuml;','&Auml;','&Ouml;',
			   '&Uuml;','&auml;','&ouml;','&uuml;','&Auml;',
				'&Ouml;','&Uuml;','&raquo;','&laquo;','&szlig;',
				'&szlig;','&Oslash;'";


*/



/*
echo'<table><tr><td>Name</td><td>Kosten</td><td>Werte</td>
	<td>Faktor</td></tr>';
for ($i=1;$i<=30;$i++) {
	$typ=TruppenTyp::getById($i);
	$kosten=$typ->baukosten();
	$totkosten=array_sum($kosten);
	$werte=$typ->werte();
	$totwert=array_sum($werte);
	$faktor=$totwert/$totkosten;
	echo'<tr><td>'.$typ->get('name').'</td>
		<td>'.$totkosten.'</td>
		<td>'.$totwert.'</td>
		<td>'.$faktor.'</td></tr>';
}

echo'</table>';
*/
?>