<?php

//Array $chancen: $key => $chance [0,1]
//Ein key wird zufällig ausgewählt mit der Wahrscheinlichkeit von $chance
//Dieser wird zurückgegeben
function wkeitVerteilung($chancen) {
	$zahl=mt_rand(0,99)+0.001*mt_rand(0,999);
	$i=0;
	foreach($chancen as $key => $chance) {
		if ($zahl<=$chance) return $key;
		$zahl-=$chance;
	}
	x('wkeitVerteilungs funktion hat keinen key gefunden.');
}

//Liefert mit der Wahrscheinlichkeit von $chance % eine 1 zurück
function wahrscheinlichkeit($chance) {
	if ($chance==100) return true;
	$zahl=mt_rand(0,99)+0.001*mt_rand(0,999);
	if ($zahl<=$chance) return true;
	return 0;
}

function make_save_int($int) {
	return (int)$int;
}

function roundTo($value,$smallestPart) {
	return $smallestPart*round($value/$smallestPart,0);
}
