<?php

function mysql_query($sql) {
	global $mysqli;
	$statement = $mysqli->prepare($sql);
	$statement->execute();
	$result = $statement->get_result();
	return [
		'result' => $result,
		'rows' => $statement->affected_rows,
		'error' => $statement->error
	];
}

function mysql_num_rows($result) {
	return $result['rows'];
}

function mysql_fetch_assoc($result) {
	$r = $result['result'];
	return $r->fetch_assoc();
}

function mysql_fetch_array($result) {
	return $result['result']->fetch_array();
}