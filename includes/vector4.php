<?php


//Um Kosten zu berechnen. Z.B: $arr = Kosten einer Einheit
//  $faktor = Anzahl Einheiten
function vector4mul($arr,$factor) {
	for ($i=0;$i<4;$i++)
		$arr[$i]*=$factor;
	return $arr;
}

//Z.B: Zum testen ob genug Platz für die Kosten eines
// Gebäudeausbaus ist. -> vector4gt($lager,$kosten)
function vector4gt($arr1,$arr2) {
	for ($i=0;$i<4;$i++)
		if ($arr1[$i]<$arr2[$i]) return false;
	return true;
}



//Summe aller Elemente
function vector4sum($arr) {
	return array_sum($arr);
}