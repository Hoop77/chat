<?php

if(!empty($_POST)) {

	$username = $_POST['username'];
	$password = $_POST['password'];

	$query = "	SELECT * 
				FROM users 
				WHERE username = :username AND password = md5(:password)";

	$query_params = array(
		':username' => $username,
		':password' => $password	
	);

	$stmt = exec_query($db, $query, $query_params, "Database Error1: Please try again!");
	$row = $stmt->fetch();
	if(!$row) {
		response_error("Error: Invalid Credentials!");
	}

	$user_id = $row['id'];

	$query = "INSERT INTO sessions (user_id, session_id) VALUES (:user_id, :session_id)";
	$query_params = array(
		':user_id' => $user_id,
		':session_id' => $session_id
	);

	exec_query($db, $query, $query_params, "Database Error2: Please try again!");

	response_success("Login successful!");
}

?>

<form action="index.php?view=login" method="post">
	<input type="text" name="username" placeholder="username">
	<input type="password" name="password" placeholder="password">
	<input type="submit" value="login">
</form>