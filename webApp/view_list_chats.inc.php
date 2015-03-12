<?php

$not_existing_usernames = array();
$chat_already_exists = false;

if(!empty($_POST['add_usernames'])) {
	// put input usernames in a list
	$str_add_usernames = preg_replace('/\s+/', '', $_POST['add_usernames']);
	$add_usernames = explode(',', $str_add_usernames);

	// get usernames who do not exist in database
	$not_existing_usernames = get_not_existing_usernames($add_usernames, $active_user_id, $db);

	if(empty($not_existing_usernames)) {
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

			// put active username in list if not done yet
			$active_username = get_username_by_id($active_user_id, $db);
			if(!string_exists_in_strings($active_username, $add_usernames)) {
				array_push($add_usernames, $active_username);
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
	}
}

$query = "SELECT chat_id FROM chats_has_users WHERE user_id = :user_id";
$query_params = array(
	':user_id' => $active_user_id
);

$stmt = exec_query($db, $query, $query_params);
$active_chats_resultset = $stmt->fetchAll();
if(!empty($active_chats_resultset)) {
	foreach ($active_chats_resultset as $i_row_chat_id => $row_chat_id) {
		$chat_id = $row_chat_id['chat_id'];

		// store all chat member names
		$chat_members = "";
		// query all chat member users from this chat
		$query = "SELECT user_id FROM chats_has_users WHERE chat_id = :chat_id";
		$query_params = array(
			':chat_id' => $chat_id
		);

		$stmt = exec_query($db, $query, $query_params);
		$chat_members_resultset = $stmt->fetchAll();
		foreach ($chat_members_resultset as $i_row_user_id => $row_user_id) {
			// get user name through id
			$query = "SELECT username FROM users WHERE id = :user_id AND id != :active_user_id";
			$query_params = array(
				':user_id' => $row_user_id['user_id'],
				':active_user_id' => $active_user_id
			);

			$stmt = exec_query($db, $query, $query_params);
			$row_username = $stmt->fetch();
			if($row_username) {
				$username = $row_username['username'];
				$chat_members .= $username . " ";
			}
		}

		?>
			<a href="index.php?view=show_chat&chat_id=<?php echo $chat_id; ?>" style="display: block;"><?php echo $chat_members; ?></a>
		<?php
	}
}

?>

<br><br>
<form action="index.php?view=list_chats" method="post">
	Enter names of users followed by a comma.<br><br>
<?php if(empty($not_existing_usernames)): ?>
	<input type="text" name="add_usernames">
	<br><br>
	<input type="submit" value="Create new chat">
	<br><br>
	<?php if(!empty($_POST) && !$chat_already_exists): ?>
		New chat created!
	<?php elseif(!empty($_POST && $chat_already_exists)): ?>
		Error: Chat already exists!
	<?php endif; ?>

<?php else: ?>
	<input type="text" name="add_usernames" value="<?php if(!empty($_POST)) echo $_POST['add_usernames']; ?>">
	<br><br>
	<input type="submit" value="Create new chat">
	<br><br>
<?php echo 'Error: username(s) do not exit: ' . list_strings($not_existing_usernames); ?>
<?php endif; ?>

</form>