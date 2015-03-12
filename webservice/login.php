<?php

require 'config.inc.php';
require 'common.inc.php';

if(!empty($_POST)) {
	$query = "	SELECT * 
				FROM users 
				WHERE username = :username AND password = md5(:password)";

	$query_params = array(
		':username' => $_POST['username'],
		':password' => $_POST['password']	
	);

	$stmt = exec_query($db, $query, $query_params, "Database Error1: Please try again!");
	$row = $stmt->fetch();
	if(!$row) {
		response_error("Error: Invalid Credentials!");
	}

	response_success("Login successful!");
}

?>

<form action="login.php" method="post">
	<input type="text" name="username" placeholder="username">
	<input type="password" name="password" placeholder="password">
	<input type="submit" value="login">
</form>