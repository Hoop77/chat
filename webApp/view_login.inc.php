<?php

if (!empty($_POST['login'])) {
    //gets user's info based off of a username.
    $query = "SELECT * FROM users WHERE username = :username";
    
    $query_params = array(
        ':username' => $_POST['username']
    );
    
    try {
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        // For testing, you could use a die and message. 
        //die("Failed to run query: " . $ex->getMessage());
        
        //or just use this use this one to product JSON data:
        $response["success"] = 0;
        $response["message"] = "Database Error1. Please Try Again!";
        die(json_encode($response));
        
    }
    
    $correct_credentials = false;
    
    //fetching all the rows from the query
    $row = $stmt->fetch();
    if ($row) {
        // compare password
        $password = md5($_POST['password']);
        if ($password === $row['password']) {
            $correct_credentials = true;
        }
    }
    
    if ($correct_credentials) {
        // insert session id
        $query = "INSERT INTO sessions (user_id, session_id) VALUES(:id, :session_id)";
        $query_params = array(
            ':id' => $row['id'],
            ':session_id' => $session_id
        );

        exec_query($db, $query, $query_params);

        header('Location: index.php?view=home');

    } else {
        die('Wrong credentials entered!');
    }
} else {
?>
		<h2>Login</h2> 
		<form action="index.php?view=login" method="post"> 
		    Username:<br /> 
		    <input type="text" name="username" placeholder="username" /> 
		    <br /><br /> 
		    Password:<br /> 
		    <input type="password" name="password" placeholder="password"/> 
		    <br /><br /> 
		    <input type="submit" name="login" value="Login" /> 
		</form> 
		<a href="index.php?view=register">Register</a>
	<?php
}

?> 
