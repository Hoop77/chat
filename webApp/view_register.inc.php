<?php

if (!empty($_POST)) {
    // check if empty username or password
    if (empty($_POST['username']) || empty($_POST['password'])) {
        die('Username or password cannot be empty!');
    }
    
    $query        = " SELECT 1 FROM users WHERE username = :username";
    $query_params = array(
        ':username' => $_POST['username']
    );
    
    $stmt = exec_query($db, $query, $query_params);    

    $row = $stmt->fetch();
    // check if username already exists
    if ($row) {
        die('Username already exists!');
    }
    
    // insert new user into database
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $query = "INSERT INTO users ( username, password ) VALUES ( :username, :password ) ";
    $query_params = array(
        ':username' => $username,
        ':password' => $password
    );

    exec_query($db, $query, $query_params);

    // get new user's id
    $query = " SELECT id FROM users WHERE username = :username";
    $query_params = array(
        ':username' => $username
    );

    $stmt = exec_query($db, $query, $query_params);
    $row = $stmt->fetch();
    $user_id = $row['id'];

    // insert empty status
    $text = "Hello, I'm using Philipp's new chat system!";

    $query = "INSERT INTO `status` ( `text`, `datetime`, user_id ) VALUES ( :text, :datetime, :user_id ) ";
    $query_params = array(
        ':text' => $text,
        ':datetime' => get_datetime(),
        ':user_id' => $user_id
    );

    $stmt = exec_query($db, $query, $query_params);

?> <!-- HTML -->

    New user '<?php echo $username ?>' registered.
    <br><br>
    <a href="index.php" style="display=block">Login</a>

<?php    

} else {
?>
	<h2>Register</h2>
	<form action="index.php?view=register" method="post"> 
	    Username:<br /> 
	    <input type="text" name="username" placeholder="username" /> 
	    <br /><br /> 
	    Password:<br /> 
	    <input type="password" name="password" placeholder="password" /> 
	    <br /><br /> 
	    <input type="submit" value="Register New User" /> 
	</form>
<?php
}

?>