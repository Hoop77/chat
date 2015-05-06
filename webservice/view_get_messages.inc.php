<?php

if (!empty($_POST)) {
	// ensure post params are valid
	if(empty($_POST['chat_id']) || empty($_POST['timespan']))
		response_error("Error: Invalid post params!");

	$chat_id = $_POST['chat_id'];
	$timespan = $_POST['timespan'];

	$query = "	SELECT message, datetime, from_user_id, state
				FROM messages
				WHERE 	chat_id = :chat_id AND
						DATE_SUB(CURDATE(), INTERVAL :timespan DAY) <= datetime";

}

?>

<form action="index.php?view=get_messages" method="post">
	<input type="text" name="chat_id" placeholder="chat_id">
	<input type="text" name="timespan" placeholder="timespan (number of days)">
	<input type="submit" value="Get messages">
</form>