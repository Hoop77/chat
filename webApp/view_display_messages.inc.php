<style type="text/css">

.chat-container {
	width: 400px;
}

.chat-container .message-container {
	width: 100%;
	text-align: left;
	margin-bottom: 5px;
}

.chat-container .message-container.own {
	text-align: right;
}

.chat-container .message-container .wrapper {
	display: inline-block;
	max-width: 300px;
	margin: 0;
	padding-left: 4px;
	padding-right: 4px;
	padding-top: 2px;
	padding-bottom: 2px;
	text-align: left;
	border: 1px solid black;
}

.chat-container .message-container .wrapper .message {
	margin-top: 2px;
	margin-bottom: 2px;
}

.chat-container .message-container .wrapper hr {
	margin-top: 2px;
	margin-bottom: 2px;
}

.chat-container .message-container .wrapper .datetime {
	margin-top: 2px;
	margin-bottom: 0px;
	font-size: 10pt;
	text-align: right;
}

</style>

<?php

// check input
if(!empty($_GET['chat_id']))
	$active_chat_id = intval($_GET['chat_id']);
else
	$active_chat_id = -1;

if($active_chat_id != -1) {
	// check if chat exists
	$query = "SELECT * FROM chats WHERE id = :active_chat_id";
	$query_params = array(
		':active_chat_id' => $active_chat_id
	);

	$stmt = exec_query($db, $query, $query_params);
	$row = $stmt->fetch();
	// chat exists
	if($row) {
		// insert new message to database
		if(!empty($_POST['message'])) {
			$message = $_POST['message'];
			$datetime = get_datetime();
			
			$query = "	INSERT INTO messages (message, datetime, from_user_id, chat_id)
						VALUES (:message, :datetime, :from_user_id, :chat_id)";
			$query_params = array(
				':message' => $message,
				':datetime' => $datetime,
				':from_user_id' => $active_user_id,
				':chat_id' => $active_chat_id
			);

			$stmt = exec_query($db, $query, $query_params);
			header('Location: index.php?view=show_chat&chat_id=' . $active_chat_id);
		}

		// display messages
		$query = "	SELECT * 
					FROM messages 
					WHERE chat_id = :active_chat_id
					ORDER BY `datetime` ASC";
		$query_params = array(
			':active_chat_id' => $active_chat_id
		);

		$stmt = exec_query($db, $query, $query_params);
		$messages_resultset = $stmt->fetchAll();
		foreach ($messages_resultset as $i_row => $row) {
			$username = get_username_by_id($row['from_user_id'], $db);
			$datetime = $row['datetime'];
			$message = $row['message'];
?>
<!-- HTML BEGIN -->
			<div class="chat-container">
				<div class="message-container <?php if($row['from_user_id'] == $active_user_id) echo "own"; ?>">
					<div class="wrapper">
						<p class="message"><?php echo $message ?></p>
						<hr>
						<p class="datetime"><?php print_datetime($datetime, HOURS_MINUTES); echo " - " . $username; ?></p>
					</div>
				</div>
			</div>
<!-- HTML END -->
<?php
		}
	}
}

?>