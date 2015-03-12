<?php

require 'common.inc.php';
require 'config.inc.php';

if(	!empty($_POST['active_username']) &&
	!empty($_POST['password']) &&
	!empty($_POST['add_usernames']) &&
	!empty($_POST['chat_id'])) {

	// checking if active username is valid
	validate_credentials($db, $_POST['active_username'], $_POST['password']);

	// put input usernames in a list
	$str_add_usernames = preg_replace('/\s+/', '', $_POST['add_usernames']);
	$add_usernames = explode(',', $str_add_usernames);

	// get usernames who do not exist in database
	$not_existing_usernames = get_not_existing_usernames($db, $add_usernames, "Database Error1: Please try again!");

	// username(s) do not exist
	if(!empty($not_existing_usernames)) {
		// concat not existing usernames to one string
		$str_not_existing_usernames = list_strings($not_existing_usernames);
		response_error("Username(s) do not exist: " . $str_not_existing_usernames);
	}

	// input chat id
	$chat_id = $_POST['chat_id'];

	// check if this chat exists and is a group
	$query = "	SELECT 1
				FROM chats
				WHERE id = :chat_id AND is_group = 1";
	$query_params = array(
		':chat_id' => $chat_id
	);

	$stmt = exec_query($db, $query, $query_params, "Database Error2: Please try again!");
	$row = $stmt->fetch();
	if(!$row) {
		response_error("Error: Either not group or chat_id is not valid!");
	}

	// get all current user from this chat
	$query = "	SELECT username
				FROM chats_has_users
				LEFT JOIN users ON user_id = id
				WHERE chat_id = :chat_id";
	$query_params = array(
		':chat_id' => $chat_id
	);

	$stmt = exec_query($db, $query, $query_params, "Database Error3: Please try again!");
	$user_resultset = $stmt->fetchAll();
	$added_usernames = array();
	$already_existing_usernames = array();
	// check if there are any users
	foreach ($add_usernames as $i_add_useranem => $add_username) {
		$user_already_exists = false;
		foreach ($user_resultset as $i_user => $user) {
			if($add_username == $user['username']) {
				$user_already_exists = true;
				array_push($already_existing_usernames, $add_username);
				break;
			}
		}

		if(!$user_already_exists) {
			$user_id = get_id_by_username($db, $add_username);

			$query = "	INSERT INTO chats_has_users (chat_id, user_id)
						VALUES (:chat_id, :user_id)";
			$query_params = array(
				':chat_id' => $chat_id,
				':user_id' => $user_id
			);

			$stmt = exec_query($db, $query, $query_params, "Database Error4: Please try again");
			array_push($added_usernames, $add_username);
		}
	}

	$result['added_usernames'] = $added_usernames;
	$result['already_existing_usernames'] = $already_existing_usernames;
	response_success("Success! Added users: " . list_strings($added_usernames) . " Already in group: " . list_strings($already_existing_usernames), 
							"result", $result);
}

?>

<form action="add_user_to_group.php" method="post">
	<input type="text" name="active_username" placeholder="active_username">
	<input type="password" name="password" placeholder="password">
	<input type="text" name="add_usernames" placeholder="add_usernames">
	<input type="text" name="chat_id" placeholder="chat_id">
	<input type="submit" value="Add user to group">
</form>