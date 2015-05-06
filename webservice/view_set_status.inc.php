<?php

if(!empty($_POST)) {
	// ensure post params are set
	if(empty($_POST['text']))
		response_error("Error: Invalid post params");

	$new_status = $_POST['text'];

	$query = "	UPDATE status
				SET `text` = :text,
					`datetime` = :datetime
				WHERE user_id = :user_id";
	$query_params = array(
		':text' => $new_status,
		':datetime' => get_datetime(),
		':user_id' => $active_user_id
	);

	$stmt = exec_query($db, $query, $query_params, "Database Error1: Please try again!");
	response_success("Status set!");
}

?>

<form action="index.php?view=set_status" method="post">
	<input type="text" name="text" placeholder="new status">
	<input type="submit" value="Set status">
</form>