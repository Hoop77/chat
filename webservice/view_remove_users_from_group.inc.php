<?php

if(!empty($_POST)) {
	// ensure post params are set
	if(empty($_POST['remove_usernames']) || empty($_POST['chat_id']))
		response_error("Error: Invalid post params!");

	// put input usernames in a list
	$str_remove_usernames = preg_replace('/\s+/', '', $_POST['remove_usernames']);
	$remove_usernames = explode(',', $str_remove_usernames);

	// get usernames who do not exist in database
	$not_existing_usernames = get_not_existing_usernames($db, $remove_usernames, "Database Error1: Please try again!");

	// username(s) do not exist
	if(!empty($not_existing_usernames)) {
		// concat not existing usernames to one string
		$str_not_existing_usernames = list_strings($not_existing_usernames);
		response_error("Username(s) do not exist in group: " . $str_not_existing_usernames);
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

	$group_removed = false;

	$query = "	SELECT username
				FROM chats_has_users
				INNER JOIN users ON user_id = id
				WHERE chat_id = :chat_id";
	$query_params = array(
		':chat_id' => $chat_id
	);

	$stmt = exec_query($db, $query, $query_params, "Database Error3: Please try again!");
	$users_in_group = $stmt->fetchAll();

	$removed_usernames = array();
	foreach ($remove_usernames as $i_remove_username => $remove_username) {
		$query = "	SELECT 1
					FROM chats_has_users
					LEFT JOIN users ON user_id = id
					WHERE chat_id = :chat_id AND username = :remove_username";
		$query_params = array(
			':chat_id' => $chat_id,
			':remove_username' => $remove_username
		);

		$stmt = exec_query($db, $query, $query_params, "Database Error3: Please try again!");
		$row = $stmt->fetch();
		if($row) {
			// add removed username to output list
			array_push($removed_usernames, $remove_username);
			// remove username from remaining remove list
			if(($key = array_search($remove_username, $remove_usernames)) !== false) {
				unset($remove_usernames[$key]);
			}
		}

		$query = "	DELETE chats_has_users
					FROM chats_has_users
					INNER JOIN users ON user_id = id
					WHERE chat_id = :chat_id AND username = :remove_username";
		$query_params = array(
			':chat_id' => $chat_id,
			':remove_username' => $remove_username
		);

		$stmt = exec_query($db, $query, $query_params, "Database Error3: Please try again!");

		// see how many members are remaining in the group
		$query = "	SELECT 1
					FROM chats_has_users
					WHERE chat_id = :chat_id";
		$query_params = array(
			':chat_id' => $chat_id
		);

		$stmt = exec_query($db, $query, $query_params, "Database Error4: Please try again!");			
		$row = $stmt->fetchAll();

		$len = count($row);
		if($len == 0) {
			$query = "	DELETE FROM chats
						WHERE id = :chat_id";
			$query_params = array(
				':chat_id' => $chat_id
			);						

			$stmt = exec_query($db, $query, $query_params, "Database Error5: Please try again!");
			$group_removed = true;
		}
	}

	$result['removed_usernames'] = $removed_usernames;
	$result['not_in_group_usernames'] = $remove_usernames;
	$result['group_removed'] = $group_removed;
	response_success("Success! removed users: " . list_strings($removed_usernames) 
												. " not in group: " . list_strings($remove_usernames) 
												. " group removed: " . ($group_removed ? "1" : "0"), 
							"result", $result);
}

?>

<form action="index.php?view=remove_users_from_group" method="post">
	<input type="text" name="remove_usernames" placeholder="remove_usernames">
	<input type="text" name="chat_id" placeholder="chat_id">
	<input type="submit" value="Delete from group">
</form>