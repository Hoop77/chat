<?php

require 'config.inc.php';
require 'common.inc.php';

if(!empty($_POST)) {
	if(empty($_POST['username']) || empty($_POST['password'])) {
		response_error("Please enter both a username and password");
	}

	$query = "SELECT 1 FROM users WHERE username = :username";
	$query_params = array(
		':username' => $_POST['username']
	);

	$stmt = exec_query($db, $query, $query_params, "Database Error1: Please try again!");
	$row = $stmt->fetch();
	if($row) {
		response_error('Error: username already exists!');
	}

	$query = "	INSERT INTO users (username, password)
				VALUES (:username, md5(:password))";
	$query_params = array(
		':username' => $_POST['username'],
		':password' => $_POST['password']
	);

	$stmt = exec_query($db, $query, $query_params, "Database Error2: Please try again!");

	$default_status_text = "Hello there!";
	$query = "	INSERT INTO status (`text`, datetime, user_id)
				VALUES (:text, :datetime, :user_id)";
	$query_params = array(
		':text' => $default_status_text,
		':datetime' => get_datetime(),
		':user_id' => get_id_by_username($db, $_POST['username'])
	);

	$stmt = exec_query($db, $query, $query_params, "Database Error3: Please try again!");

	response_success("User successfully added!");
}

?>

<form action="register.php" method="post">
	<input type="text" name="username" placeholder="username">
	<input type="password" name="password" placeholder="password">
	<input type="submit" value="Register">
</form>