<?php

require 'common.inc.php';
require 'config.inc.php';

if(!empty($_POST['active_username']) && !empty($_POST['password'])) {
	validate_credentials($db, $_POST['active_username'], $_POST['password']);

	$query = "	SELECT * 
				FROM status
				LEFT JOIN users ON status.user_id = users.id
				WHERE username = :username";
	$query_params = array(
		':username' => $_POST['active_username']
	);

	$stmt = exec_query($db, $query, $query_params, "Database Error1: Please try again!");
	$status = $stmt->fetch();
	if($status) {
		$status_info = array();
		$status_info['text'] = $status['text'];
		$status_info['datetime'] = $status['datetime'];
		response_success("Status received!", "status_info", $status_info);
	}
	else {
		response_error("Failed to load status!");
	}
}

?>

<form action="get_status.php" method="post">
	<input type="text" name="active_username" placeholder="active_username">
	<input type="password" name="password" placeholder="password">
	<input type="submit" value="Get status">
</form>