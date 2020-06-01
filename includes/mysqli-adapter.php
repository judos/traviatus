<?php

function mysql_query($sql) {
	global $mysqli;
	global $mysqli_id;
	$statement = $mysqli->prepare($sql);
	$statement->execute();
	$result = $statement->get_result();
	$mysqli_id = $mysqli->insert_id;
	return [
		'result' => $result,
		'statement' => $statement,
	];
}

function mysql_num_rows($data) {
	return $data['statement']->affected_rows;
}

function mysql_fetch_assoc($data) {
	return $data['result']->fetch_assoc();
}

function mysql_fetch_array($data) {
	return $data['result']->fetch_array();
}

function mysql_insert_id() {
	global $mysqli_id;
	return $mysqli_id;
}