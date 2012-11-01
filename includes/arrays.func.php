<?php


function array_insert(&$array,$pos,$val) {
	$array2 = array_splice($array,$pos);
	$array[] = $val;
	$array = array_merge($array,$array2);
}


//Sort an array of arrays bi a specific attribute and an order
//possible orders are asc and desc
function sortArray2(&$arr,$att,$order) {
	global $compare_att;
	global $compare_order;
	$compare_att=$att;
	$compare_order=$order;
	uasort($arr,'sortArray2_compare_att');
}
function sortArray2_compare_att($a, $b) {
	global $compare_att,$compare_order;
	if ($compare_order=='asc')
		return strnatcmp($a[$compare_att],$b[$compare_att]);
	else
		return strnatcmp($b[$compare_att],$a[$compare_att]);
}


function array_add($arr1,$arr2) {
	foreach($arr1 as $id => $nr) {
		if (isset($arr1[$id])) {
			$arr1[$id]+=$arr2[$id];
		}
	}
	foreach($arr2 as $id => $nr) {
		if (!isset($arr1[$id])) {
			$arr1[$id]=$nr;
		}
	}
	return $arr1;
}
