<?php

if(!empty($_POST)) {
	// ensure post params are valid
	if(empty($_POST['chat_id']) || empty($_POST['message']))
		response_error("Error: Invalid post params!");

	$chat_id = $_POST['chat_id'];
	$message = $_POST['message'];

	$query = "	INSERT INTO messages
				(message, datetime, from_user_id, chat_id)
				VALUES (:message, :datetime, :from_user_id, :chat_id)";
	$query_params = array(
		":message" => $message,
		":datetime" => get_datetime(),
		":from_user_id" => $active_user_id,
		":chat_id" => $chat_id
	);

	exec_query($db, $query, $query_params, "Database Error1: Please try again!");

	response_success("Message was send!");
}

?>

<form action="index.php?view=add_message" method="post">
	<input type="text" name="chat_id" placeholder="chat_id">
	<input type="text" name="message" placeholder="message">
	<input type="submit" value="Send message">
</form>