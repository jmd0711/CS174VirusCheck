<?php
	//CS174 - Fabio Di Troia
	//Virus Check
	//by Jasper Matthew Dumdumaya and Trung Tran
	
	//------------------//
	//		Start		//
	//------------------//

	//database login
	require_once 'login.php';
	$conn = new mysqli($hn, $un, $pw, $db);
	if ($conn->connect_error) die($conn->connect_error);
	
	//start session
	//TODO: session security
	session_start();
	if(isset($_SESSION['username'])) {
		//user start page
		$email = $_SESSION['email'];
		$username = $_SESSION['username'];
		$password = $_SESSION['password'];
		
		//end session on logout
		if(isset($_POST['logout'])) {
			destroy_session_and_data();
			header("refresh:0;");
		}
		
		echo "Welcome back $username.<br>";
		
		echo <<<_END
		<form method = "post">
			<input type="submit" name="logout" value="Logout">
		</form>
_END;
		
		//add file to database
		if (isset($_POST['title']) && isset($_FILES['text'])) {
			if ($_FILES['text']['type'] != "text/plain") { //Check for correct file type
                    echo "Only .txt files are allowed";
                    die;
            }
			$username = mysql_entities_fix_string($conn, $username);
			$title = mysql_entities_fix_string($conn, $_POST['title']);
			$rawText = file_get_contents($_FILES['text']['tmp_name']);
			$text = mysql_entities_fix_string($conn, $rawText);
			$query = "INSERT INTO data VALUES" .
				"(NULL, '$username', '$title', '$text')";
			$result = $conn->query($query);
			if (!$result) echo "INSERT failed: $query<br>" . $conn->error . "<br><br>";
		}
		
		//add file form TODO: replace with virus check
		echo <<<_END
<html>
<head>
    <title>Please fill out this form</title>
</head>
<body>
    <form enctype="multipart/form-data" action="start.php" method="POST"><pre>
    Enter text in the text box <input type="text" name = "title">
    
    Choose your file <input type="file" name = "text">
    
    <input type="submit" value= "ADD RECORD">
    </pre></form>
</body>
</html>
_END;
		
		//print txt filed owned by user
		$query = "SELECT * FROM data WHERE username = '$username'";
		$result = $conn->query($query);
		if (!$result) die($conn->error);
	 
		$rows = $result->num_rows;
	
		for ($i = 0; $i < $rows; ++$i) {
	 
			$result->data_seek($i);
			$row = $result->fetch_array(MYSQLI_NUM);
			echo <<<_END
<pre>
$row[2]
	$row[3]
_END;

		}
	}
	//no user logged in
	else echo "Please <a href = 'authentication.php'>login</a> or <a href = 'signup.php'>sign up.</a>";
	
	//functions
	function destroy_session_and_data() {
		$_SESSION = array();
		setcookie(session_name(), '', time() - 2592000, '/');
		session_destroy();
		http_response_code(401);
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