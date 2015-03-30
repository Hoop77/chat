<?php

require 'common.inc.php';
require 'config.inc.php';

if(!empty($_POST['active_username']) && !empty($_POST['password']) && !empty($_POST['text'])) {
	validate_credentials($db, $_POST['active_username'], $_POST['password']);

	$new_status = $_POST['text'];

	$query = "	UPDATE status
				SET `text` = :text,
					`datetime` = :datetime
				WHERE user_id = :user_id";
	$query_params = array(
		':text' => $new_status,
		':datetime' => get_datetime(),
		':user_id' => get_id_by_username($db, $_POST['active_username'])
	);

	$stmt = exec_query($db, $query, $query_params, "Database Error1: Please try again!");
	response_success("Status set!");
}

?>

<form action="set_status.php" method="post">
	<input type="text" name="active_username" placeholder="active_username">
	<input type="password" name="password" placeholder="password">
	<input type="text" name="text" placeholder="new status">
	<input type="submit" value="Set status">
</form>