<?php

if(!empty($_POST)) {
	// ensure that at least 'add_usernames'-parameter is given
	if(empty($_POST['add_usernames']))
		response_error("Error: Invalid post params!");

	// asuming that chat does not already exists
	$chat_already_exists = false;

	// put input usernames in a list
	$str_add_usernames = preg_replace('/\s+/', '', $_POST['add_usernames']);
	$add_usernames = explode(',', $str_add_usernames);

	// get usernames who do not exist in database
	$not_existing_usernames = get_not_existing_usernames($db, $add_usernames, "Database Error2: Please try again!");

	// username(s) do not exist
	if(!empty($not_existing_usernames)) {
		// concat not existing usernames to one string
		$str_not_existing_usernames = list_strings($not_existing_usernames);
		response_error("Error: Username(s) do not exist: " . $str_not_existing_usernames);
	}

	// only active username given
	if(count($add_usernames) == 1) {
		if($add_usernames[0] === $active_username)
			response_error("Error: No chat members given!");
	}

	// all usernames exist
	// put active username in list if not done yet
	if(!string_exists_in_strings($active_username, $add_usernames)) {
		array_push($add_usernames, $active_username);
	}

	// check group checkbox
	if(empty($_POST['is_group']))
		$is_group = false;
	else
		$is_group = true;

	// so if more than 2 users it's automatically a group
	if(count($add_usernames) > 2) {
		$is_group = true;
	}

	// ----------- GROUP -----------
	if($is_group) {
		// group requires group name
		if(!empty($_POST['group_name']))
			$group_name = $_POST['group_name'];
		else
			response_error("Error: Group requires a name!");

		// create new group
		// insert in table chats
		$query = "INSERT INTO chats (is_group, group_name) VALUES (1, :group_name)";
		$query_params = array(
			':group_name' => $group_name
		);
		
		$stmt = exec_query($db, $query, $query_params, "Database Error3: Please try again!");
		$new_chat_id = $db->lastInsertId();
		foreach ($add_usernames as $i => $add_username) {
			$user_id = get_id_by_username($db, $add_username);

			$query = "INSERT INTO chats_has_users (chat_id, user_id) VALUES (:new_chat_id, :user_id)";
			$query_params = array(
				':new_chat_id' => $new_chat_id,
				':user_id' => $user_id
			);

			$stmt = exec_query($db, $query, $query_params, "Database Error4: Please try again!");
		}

		response_success("New group was added!");
	}
	// ----------- NOT GROUP -----------
	else {
		// check if this chat already exists
		// get all chats of active users which are not groups

		if($add_usernames[0] != $active_username) $add_username = $add_usernames[0];
		if($add_usernames[1] != $active_username) $add_username = $add_usernames[1];

		$query = "	SELECT chat_id
					FROM chats_has_users
					LEFT JOIN users ON chats_has_users.user_id = users.id
					LEFT JOIN chats ON chats_has_users.chat_id = chats.id
					WHERE user_id = :active_user_id AND is_group = 0";
		$query_params = array(
			':active_user_id' => $active_user_id
		);

		$stmt = exec_query($db, $query, $query_params, "Database Error5: Please try again!");
		$active_chats_resultset = $stmt->fetchAll();
		$chat_already_exists = false;
		foreach ($active_chats_resultset as $i_active_chat => $active_chat) {
			// check if chat members equal input members
			$query = "	SELECT username
						FROM chats_has_users
						LEFT JOIN users ON chats_has_users.user_id = users.id
						WHERE chat_id = :active_chat_id AND user_id != :active_user_id";
			$query_params = array(
				':active_chat_id' => $active_chat['chat_id'],
				':active_user_id' => $active_user_id
			);

			$stmt = exec_query($db, $query, $query_params, "Database Error6: Please try again!");
			$chat_members_resultset = $stmt->fetchAll();

			// should it be that there are more than a single user in this chat just ignore it
			if(count($chat_members_resultset) > 1) {
				continue;
			}

			if($chat_members_resultset[0]['username'] == $add_username) {
				$chat_already_exists = true;
			}
		}

		if($chat_already_exists) {
			response_error("Error: Chat already exists!");
		}

		// create new chat
		// insert in table chats
		$query = "INSERT INTO chats (is_group) VALUES (:is_group)";
		$query_params = array(
			':is_group' => $is_group
		);
		
		$stmt = exec_query($db, $query, $query_params, "Database Error5: Please try again!");
		$new_chat_id = $db->lastInsertId();

		// add active user to the chat
		$query = "INSERT INTO chats_has_users (chat_id, user_id) VALUES (:new_chat_id, :active_user_id)";
		$query_params = array(
			':new_chat_id' => $new_chat_id,
			':active_user_id' => $active_user_id
		);

		$stmt = exec_query($db, $query, $query_params, "Database Error6: Please try again!");

		// add entered user to the chat
		$add_user_id = get_id_by_username($db, $add_username);

		$query = "INSERT INTO chats_has_users (chat_id, user_id) VALUES (:new_chat_id, :add_user_id)";
		$query_params = array(
			':new_chat_id' => $new_chat_id,
			':add_user_id' => $add_user_id
		);

		$stmt = exec_query($db, $query, $query_params, "Database Error7: Please try again!");

		response_success("New chat was added!");
	}
}

?>

<form action="index.php?view=add_chat" method="post">
	<input type="text" name="add_usernames" placeholder="add_usernames">
	<label for="cb-is-group">
		<input id="cb-is-group" type="checkbox" name="is_group">
		Group
	</label>
	<input type="text" name="group_name" placeholder="group_name">
	<input type="submit" value="Add new chat">
</form>
