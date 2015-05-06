<?php

if(!empty($_POST)) {
	// ensure post params are set
	if(empty($_POST['group_id']) || empty($_POST['new_group_name']))
		response_error("Error: Invalid post params!");

	$group_id = $_POST['group_id'];
	$new_group_name = $_POST['new_group_name'];

	$query = "SELECT * FROM chats WHERE id = :group_id";
	$query_params = array(
		":group_id" => $group_id
	);

	$stmt = exec_query($db, $query, $query_params, "Database Error1: Please try again!");

	$chat = $stmt->fetch();
	if(!$chat)
		response_error("Error: Chat doesn't exist!");
	
	// is not group?
	if($chat['is_group'] == 0)
		response_error("Error: Chat isn't group!");

	$old_group_name = $chat['group_name'];

	$query = "	UPDATE chats
				SET group_name = :new_group_name
				WHERE id = :group_id";
	$query_params = array(
		":new_group_name" => $new_group_name,
		":group_id" => $group_id
	);

	exec_query($db, $query, $query_params, "Database Error2: Please try again!");

	$group_name_info = array(
		"old_group_name" => $old_group_name,
		"new_group_name" => $new_group_name
	);

	response_success("Group name changed!", "group_name_info", $group_name_info);
}

?>

<form action="index.php?view=set_group_name" method="post">
	<input type="text" name="group_id" placeholder="group_id">
	<input type="text" name="new_group_name" placeholder="new group name">	
	<input type="submit" value="Set group name">
</form>