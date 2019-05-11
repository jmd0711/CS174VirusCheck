<?php
	//CS174 - Fabio Di Troia
	//Virus Check
	//by Jasper Matthew Dumdumaya and Trung Tran
	
	//------------------//
	//		Sign Up		//
	//------------------//
	
	//database login
	require_once 'login.php';
	$conn = new mysqli($hn, $un, $pw, $db);
	if ($conn->connect_error) die($conn->connect_error);
	
	//add user
	if (isset($_POST['add']) && isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
		$salt = "zr#b%";
		
		//sanitize inputs
		$email = mysql_entities_fix_string($conn, $_POST['email']);
		$username = mysql_entities_fix_string($conn, $_POST['username']);
		$password = mysql_entities_fix_string($conn, $_POST['password']);
		
		//hash and salt password
		$token = hash('ripemd128', "$salt$password");
		
		//add user to database
		add_user($conn, $email, $username, $token);
	}
	
	//get user inputs
	echo <<<_END
	<form action ="signup.php" method ="post"><pre>
	Email		<input type="text" name="email">
	Username	<input type="text" name="username">
	Password	<input type="password" name="password">
	<input type="submit" name="add" value="Sign Up">
	</pre></form>
_END;

	//go back to start page
	echo <<<_END
	<form action ="start.php" method ="post"><pre>
	<input type="submit" action="start.php" name="return" value="Return">
	</pre></form>
_END;
		
	//functions
	//TODO: print message if email or username is already taken
	function add_user($connection, $email, $username, $password) {
		$query = "INSERT INTO users VALUES (NULL, '$email', '$username', '$password')";
		$result = $connection->query($query);
		if (!$result) die($connection->error);
	}
	
	//sanitize functions
	function mysql_entities_fix_string($connection, $string) {
		return htmlentities(mysql_fix_string($connection, $string));
	}
	function mysql_fix_string($connection, $string) {
		if (get_magic_quotes_gpc()) $string = stripslashes($string);
			return $connection->real_escape_string($string);
	}
	
?>