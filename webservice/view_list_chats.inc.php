<?php

$response['chats'] = array();

$query = "	SELECT chat_id
			FROM chats_has_users LEFT JOIN users ON user_id = id 
			WHERE username = :active_username";
$query_params = array(
	'active_username' => $active_username
);

$stmt = exec_query($db, $query, $query_params, "Database Error1: Please try again!");
$chats_resultset = $stmt->fetchAll();
foreach ($chats_resultset as $i => $chat) {
	$query = "	SELECT username
				FROM chats_has_users
				LEFT JOIN users ON user_id = id
				WHERE chat_id = :chat_id";
	$query_params = array(
		':chat_id' => $chat['chat_id']
	);

	$stmt = exec_query($db, $query, $query_params, "Database Error2: Please try again!");

	$usernames = array();
	$usernames_resultset = $stmt->fetchAll();
	foreach ($usernames_resultset as $j => $user) {
		array_push($usernames, $user['username']);
	}

	$query = "	SELECT *
				FROM messages
				LEFT JOIN users ON messages.from_user_id = users.id
				WHERE chat_id = :chat_id
				ORDER BY `datetime` DESC
				LIMIT 1";
	$query_params = array(
		':chat_id' => $chat['chat_id']
	);

	$stmt = exec_query($db, $query, $query_params, "Database Error3: Please try again!");

	$last_message_info = array();
	$last_message = $stmt->fetch();
	if($last_message) {
		$last_message_info['exists'] = 1; 
		$last_message_info['from_username'] = $last_message['username'];
		$last_message_info['message'] = $last_message['message'];
		$last_message_info['datetime'] = $last_message['datetime'];
		$last_message_info['state'] = $last_message['state'];
		}
	else {
		$last_message_info['exists'] = 0;
	}

	$chat_info = array();
	$chat_info['chat_id'] = $chat['chat_id'];
	$chat_info['usernames'] = $usernames;
	$chat_info['last_message_info'] = $last_message_info;

	array_push($response['chats'], $chat_info);
}

response_success("Success loading chat list!", "chats", $response['chats']);

?>