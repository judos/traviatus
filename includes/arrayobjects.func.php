<?php


//returns whether this is an array which only contains objects
function is_arrayObjects($arr) {
	if (empty($arr)) return false;
	if (!is_array($arr)) return false;
	foreach($arr as $object) {
		if (!is_object($object))
			return false;
	}
	return true;
}


//Delete all Objects in the array which have set the attribute to the given value
function arrayObjectsDelete(&$arr,$att,$value) {
	foreach($arr as $key => $object) {
		if ($object->get($att)==$value) {
			unset($arr[$key]);
		}
	}
}

//Sums up the values of a certain attribute of an array of objects.
function arrayObjectsSum($arr,$att) {
	global $arrayObjectsSum_result;
	global $arrayObjectsSum_att;
	$arrayObjectsSum_att=$att;
	$arrayObjectsSum_result=0;
	array_walk($arr,'arrayObjectsSum_walk');
	$result=$arrayObjectsSum_result;
	unset($arrayObjectsSum_result);
	return $result;
}
function arrayObjectsSum_walk($object) {
	global $arrayObjectsSum_att;
	global $arrayObjectsSum_result;
	$arrayObjectsSum_result+=$object->get($arrayObjectsSum_att);
}



//Sort an array of objects by a specific attribute and an order
//possible orders are asc and desc
function arrayObjectsSort(&$arr,$att,$order) {
	global $compare_att;
	global $compare_order;
	$compare_att=$att;
	$compare_order=$order;
	usort($arr,'arrayObjectsSort_compare_att');
	return $arr;
}
function arrayObjectsSort_compare_att($a, $b) {
	global $compare_att,$compare_order;
	$av=$a->get($compare_att);
	$bv=$b->get($compare_att);
	if (is_string($av)) {
		if ($compare_order=='asc')
			return strnatcmp($av,$bv);
		else
			return strnatcmp($bv,$av);
	}
	elseif (is_int($av)) {
		if ($compare_order=='asc')
			return $av-$bv;
		else
			return $bv-$av;
	}
}

//Looks if an array of objects contain at least one object where
// a certain attribute has the given value
function arrayObjectsContains($arr,$att,$value) {
	global $arrayObjectsContain_result;
	global $arrayObjectsContain_att,$arrayObjectsContain_value;
	$arrayObjectsContain_att=$att;
	$arrayObjectsContain_value=$value;
	$arrayObjectsContain_result=array();
	array_walk($arr,'arrayObjectsContain_walk');
	$result=$arrayObjectsContain_result;
	unset($arrayObjectsContain_result);
	return !empty($result);
}

//TODO: test this function and replace old version
function arrayObjectsContains2($arr,$att,$value) {
	$result = arrayObjectsContaining2($arr,$att,$value);
	return !empty($result);
}

//returns all objects that have the attribute with the given value
function arrayObjectsContaining($arr,$att,$value) {
	global $arrayObjectsContain_result;
	global $arrayObjectsContain_att,$arrayObjectsContain_value;
	$arrayObjectsContain_att=$att;
	$arrayObjectsContain_value=$value;
	$arrayObjectsContain_result=array();
	if (!is_array($arr)) return array();
	array_walk($arr,'arrayObjectsContain_walk');
	$result=$arrayObjectsContain_result;
	unset($arrayObjectsContain_result);
	return $result;
}

//TODO: test this function and replace old version
function arrayObjectsContaining2($arr,$att,$value) {
	$result=array();
	foreach($arr as $object) {
		if ($object->get($att)==$value)
			$result[]=$object;
	}
	return $result;
}

//TODO: remove this function if 2nd implementation works
function arrayObjectsContain_walk($object) {
	global $arrayObjectsContain_att,$arrayObjectsContain_value;
	global $arrayObjectsContain_result;
	if ($object->get($arrayObjectsContain_att)==$arrayObjectsContain_value)
		array_push($arrayObjectsContain_result,$object);
}



?>