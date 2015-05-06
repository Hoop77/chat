<?php

$query = "	SELECT * 
			FROM status
			LEFT JOIN users ON status.user_id = users.id
			WHERE user_id = :user_id";
$query_params = array(
	':user_id' => $active_user_id
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

?>