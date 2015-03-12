<?php
$query = "SELECT username FROM users WHERE id = :id";
$query_params = array(
	':id' => $active_user_id
);

// get username
$stmt = exec_query($db, $query, $query_params);

$row = $stmt->fetch();
if($row) {
	$active_user_name = $row['username'];
}

// get status
$query = "SELECT * FROM status WHERE user_id = :user_id";
$query_params = array(
	':user_id' => $active_user_id
);

$stmt = exec_query($db, $query, $query_params);

$row = $stmt->fetch();
if($row) {
	$active_status_text = $row['text'];
}

?>

<h2>Welcome <?php echo $active_user_name ?>!</h2>

<p><span style="font-weight: bold;">Current Status:</span> <?php echo $active_status_text ?></p>

<button onclick="location.href='index.php?view=edit_status'">Edit status</button>
<br><br>
<button onclick="location.href='index.php?view=list_chats'">List chats</button>
<br><br>
<a href="index.php?logout=1">Logout</a>