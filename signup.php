<?php
	
	require_once 'login.php';
	$conn = new mysqli($hn, $un, $pw, $db);
	if ($conn->connect_error) die($conn->connect_error);
	
	if (isset($_POST['add']) && isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
		$salt = "zr#b%";
		$email = mysql_entities_fix_string($conn, $_POST['email']);
		$username = mysql_entities_fix_string($conn, $_POST['username']);
		$password = mysql_entities_fix_string($conn, $_POST['password']);
		$token = hash('ripemd128', "$salt$password");
		add_user($conn, $email, $username, $token);
	}
	
	echo <<<_END
	<form action ="signup.php" method ="post"><pre>
	Email		<input type="text" name="email">
	Username	<input type="text" name="username">
	Password	<input type="password" name="password">
	<input type="submit" name="add" value="Sign Up">
	</pre></form>
_END;

	echo <<<_END
	<form action ="start.php" method ="post"><pre>
	<input type="submit" action="start.php" name="return" value="Return">
	</pre></form>
_END;
		
	function add_user($connection, $email, $username, $password) {
		$query = "INSERT INTO users VALUES (NULL, '$email', '$username', '$password')";
		$result = $connection->query($query);
		if (!$result) die($connection->error);
		
		/*$query = "CREATE TABLE $username (
			email VARCHAR(32) NOT NULL,
			username VARCHAR(32) NOT NULL UNIQUE,
			password VARCHAR(32) NOT NULL
		)";
	
		$result = $conn->query($query);
		if (!$result) die($conn->error);*/
	}
	
	function mysql_entities_fix_string($connection, $string) {
		return htmlentities(mysql_fix_string($connection, $string));
	}
	function mysql_fix_string($connection, $string) {
		if (get_magic_quotes_gpc()) $string = stripslashes($string);
			return $connection->real_escape_string($string);
	}
	
?>