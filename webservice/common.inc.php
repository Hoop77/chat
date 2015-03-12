<?php

function get_datetime() {
	date_default_timezone_set('Europe/Berlin');
	return date('Y-m-d H:i:s', time());
}

function response_error($message) {
	$response["success"] = 0;
	$response["message"] = $message;
	die(json_encode($response));
}

function response_success($message, $data_tag = '', $data = null) {
	$response["success"] = 1;
	$response["message"] = $message;
	if($data_tag != "" && $data != null) {
		$response[$data_tag] = $data;
	}
	echo json_encode($response);
}

function list_strings($strings) {
	$result = "";
	foreach ($strings as $i => $string) {
		if($result != "")
			$result .= ", ";
		$result .= "'" . $string . "'";
	}

	return $result;
}

function string_exists_in_strings($string, $strings) {
	$result = false;
	foreach ($strings as $i => $str) {
		if($string == $str) {
			$result = true;
			break;
		}
	}

	return $result;
}

function exec_query($db, $query, $query_params, $error_message="") {
	try {
		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);
		return $stmt;
	}
	catch(PDOException $ex) {
		echo $ex->getMessage();
		response_error($error_message);
	}

	return NULL;
}

function get_username_by_id($db, $id) {
	$query = "SELECT username FROM users WHERE id = :id";
	$query_params = array(
		':id' => $id
	);

	$stmt = exec_query($db, $query, $query_params);
	$row = $stmt->fetch();
	if($row)
		return $row['username'];
	else
		return NULL;
}

function get_id_by_username($db, $username) {
	$query = "SELECT id FROM users WHERE username = :username";
	$query_params = array(
		':username' => $username
	);

	$stmt = exec_query($db, $query, $query_params);
	$row = $stmt->fetch();
	if($row)
		return $row['id'];
	else
		return NULL;	
}

function get_not_existing_usernames($db, $usernames, $error_message) {
	// get usernames who do not exist in database
	$not_existing_usernames = array();
	foreach ($usernames as $i_username => $username) {
		// check if user exits
		$query = "SELECT * FROM users WHERE username = :username";
		$query_params = array(
			':username' => $username
		);

		$stmt = exec_query($db, $query, $query_params, $error_message);
		$user = $stmt->fetch();
		if(!$user) {
			array_push($not_existing_usernames, $username);
			continue; 
		}
	}

	return $not_existing_usernames;
}

function validate_credentials($db, $username, $password) {
	$query = "SELECT * FROM users WHERE username = :username AND password = md5(:password)";
	$query_params = array(
		':username' => $username,
		':password' => $password
	);

	$stmt = exec_query($db, $query, $query_params, "Database Error0: Please try again!");
	$row = $stmt->fetch();
	if(!$row)
		response_error("Error: Invalid credentials!");
}

?>