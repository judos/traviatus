<?php

function saveObject($object,$alternative) {
	if ($object==null)
		return $alternative;
	return $object;
}