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
	}
}
?>

<!-- MAIN HTML -->
<br>
<?php if($active_chat_id != -1): ?>
	<form action="index.php?view=show_chat&chat_id=<?php echo $active_chat_id ?>" method="post">
		<textarea type="message" name="message" placeholder="Type in your message." row="50" cols="50"></textarea>
		<br>
		<input type="submit" value="Send Message" id="viewTarget">
	</form>
<?php else: ?>
	Error: Chat does not exist!
<?php endif; ?>