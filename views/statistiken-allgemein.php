<?php
if (!$execute) die('');
needed_login();
$stview=1;

$limit=20;

$dx=$login_dorf->get('x');
$dy=$login_dorf->get('y');


Outputer::statistikenMenu();

function table($title,$data) {
	echo'<table class="tbg" cellpadding="2" cellspacing="1">
		<tbody><tr class="rbg">
		<td colspan="2">'.$title.'</td>
		</tr>';
	foreach($data as $text => $value) {
		echo'<tr><td>'.$text.':</td>
			<td width="100">'.$value.'</td></tr>';
	}
	echo'</tbody></table><br>';
}


$data=array('Anzahl Spieler angemeldet'=>Spieler::anzahl(),
	'Dörfer insgesamt'=>Dorf::anzahl());
table('Allgemeine Statistiken',$data);

$volkerAnzahl=Spieler::statistikVolker();
$volkerNamen=explode(':',Diverses::get('volker')); //beginnt mit index 0 (Römer sind aber id=1 in der Statistik)
$data=array();
foreach ($volkerAnzahl as $nr => $value){
	$data[$volkerNamen[$nr-1]] = $value;
}

//$data=array('Römer'=>$volker[1],'Germanen'=>$volker[2],
//	'Gallier'=>$volker[3]);
table('Völker in Traviatus',$data);


echo'<div>';
?>