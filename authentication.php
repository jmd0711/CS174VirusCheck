<?php
	
	require_once 'login.php';
	$conn = new mysqli($hn, $un, $pw, $db);
	if ($conn->connect_error) die($conn->connect_error);

	if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
		$un_temp = mysql_entities_fix_string($conn, $_SERVER['PHP_AUTH_USER']);
		$pw_temp = mysql_entities_fix_string($conn, $_SERVER['PHP_AUTH_PW']);
		$query = "SELECT * FROM users WHERE username = '$un_temp'";
		$result = $conn->query($query);
		if (!$result) die($conn->error);
		elseif ($result->num_rows) {
			$row = $result->fetch_array(MYSQLI_NUM);
			$result->close();
			$salt = "zr#b%";
			$token = hash('ripemd128', "$salt$pw_temp");

			if ($token == $row[3]) {
				session_start();
				$_SESSION['username'] = $un_temp;
				$_SESSION['password'] = $pw_temp;
				$_SESSION['email'] = $row[1];
				echo "You are now logged in as $row[1]";
				die("<p><a href=start.php>Click here to continue</a></p>");
			}
			else {
				http_response_code(401);
				echo "Invalid username or password";
				echo "<br><a href = 'start.php'>Return to Sign In page.</a>";
			}
			
		} 
		else {
			http_response_code(401);
			echo "Invalid username or password";
			echo "<br><a href = 'start.php'>Return to Sign In page.</a>";
		}
	} else {
		header('WWW-Authenticate:Basic realm ="Restricted Section"');
		header('HTTP/1.0 401 Unauthorized');
		die("Please enter your username and password");
	}
	$conn->close();
	
	function mysql_entities_fix_string($connection, $string) {
		return htmlentities(mysql_fix_string($connection, $string));
	}
	function mysql_fix_string($connection, $string) {
		if (get_magic_quotes_gpc()) $string = stripslashes($string);
			return $connection->real_escape_string($string);
	}
?>