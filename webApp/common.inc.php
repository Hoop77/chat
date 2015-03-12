<?php

function get_datetime() {
	date_default_timezone_set('Europe/Berlin');
	return date('Y-m-d H:i:s', time());
}

define("HOURS_MINUTES", 0);

function print_datetime($datetime, $type) {
	$split = explode(" ", $datetime);
	$date = explode("-", $split[0]);
	$time = explode(":", $split[1]);

	$year = $date[0];
	$month = $date[1];
	$day = $date[2];

	$hours = $time[0];
	$minutes = $time[1];
	$seconds = $time[2];

	switch ($type) {
		case HOURS_MINUTES:
			echo $hours . ":" . $minutes;
			break;
		
		default:
			echo $datetime;
			break;
	}
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
		if($string === $str) {
			$result = true;
			break;
		}
	}

	return $result;
}

function exec_query($db, $query, $query_params) {
	try {
		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);
		return $stmt;
	}
	catch(PDOException $ex) {
		die('Failed to run query: ' . $ex->getMessage());
	}

	return NULL;
}

function get_username_by_id($id, $db) {
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

function get_id_by_username($username, $db) {
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

function get_not_existing_usernames($add_usernames, $active_user_id, $db) {
	$result = array();

	foreach ($add_usernames as $i_add_username => $add_username) {
		// check if user exits
		$query = "SELECT * FROM users WHERE username = :username";
		$query_params = array(
			':username' => $add_username
		);

		$stmt = exec_query($db, $query, $query_params);
		$row = $stmt->fetch();
		if(!$row) {
			array_push($result, $add_username);
			continue; 
		}
		
		if($active_user_id != -1) {
			// check if user is not active user
			if($row['id'] === $active_user_id) {
				continue;
			}
		}	
	}

	return $result;
}

function create_new_chat($add_usernames, $active_user_id, $db) {
	// check if this chat already exists
	// get all chats of active users
	$query = "SELECT chat_id FROM chats_has_users WHERE user_id = :active_user_id";
	$query_params = array(
		':active_user_id' => $active_user_id
	);

	$stmt = exec_query($db, $query, $query_params);
	$active_chats_resultset = $stmt->fetchAll();
	foreach ($active_chats_resultset as $i_row_chat_id => $row_chat_id) {
		// check if chat members equal input members
		$query = "SELECT user_id FROM chats_has_users WHERE chat_id = :chat_id";
		$query_params = array(
			'chat_id' => $row_chat_id['chat_id']
		);

		$stmt = exec_query($db, $query, $query_params);
		$chat_members_resultset = $stmt->fetchAll();
		// count corresponding users
		$equal_users = 0;
		foreach ($chat_members_resultset as $i_row_user_id => $row_user_id) {
			$member_username = get_username_by_id($row_user_id['user_id'], $db);
			if($member_username != NULL) {
				// username found
				if(string_exists_in_strings($member_username, $add_usernames)) {
					$equal_users++;
				}
				// username not found -> chat doesn't already exists
				else {
					break;
				}
			}
		}

		if($equal_users == count($add_usernames)) {
			$chat_already_exists = true;
			break;
		}
	}

	if(!$chat_already_exists) {
		// create new chat

		if($active_user_id != -1) {
			// put active username in list if not done yet
			$active_username = get_username_by_id($active_user_id, $db);
			if(!string_exists_in_strings($active_username, $add_usernames)) {
				array_push($add_usernames, $active_username);
			}
		}

		// insert in table chats
		$query = "INSERT INTO chats () VALUES ()";
		$query_params = array();

		$stmt = exec_query($db, $query, $query_params);
		$new_chat_id = $db->lastInsertId();
		foreach ($add_usernames as $i => $add_username) {
			$user_id = get_id_by_username($add_username, $db);

			$query = "INSERT INTO chats_has_users (chat_id, user_id) VALUES (:chat_id, :user_id)";
			$query_params = array(
				':chat_id' => $new_chat_id,
				':user_id' => $user_id
			);

			$stmt = exec_query($db, $query, $query_params);
		}
	}

	return $chat_already_exists;
}

?>