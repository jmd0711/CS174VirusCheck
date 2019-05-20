<?php
	//CS174 - Fabio Di Troia
	//Virus Check
	//by Jasper Matthew Dumdumaya and Trung Tran
	
	//------------------//
	//		Start		//
	//------------------//

	//database login
	require_once 'login.php';
	require_once 'setupusers.php';
	$conn = new mysqli($hn, $un, $pw, $db);
	if ($conn->connect_error) die($conn->connect_error);
	
	//start session
	//TODO: session security
	session_start();
	if (!isset($_SESSION['initiated'])) {
		session_regenerate_id();
		$_SESSION['initiated'] = 1;
	}
	if (isset($_SESSION['username'])) {
		//user start page
		if ($_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])) different_user();
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
		if (isset($_FILES['text1'])) {
		
		// $data1 = file_get_contents($_FILES['text1']['tmp_name']);
		// $bytes1 = unpack("H*",$data1);
		// print_r($bytes1);
		// echo("<br>");
		// $data2 = file_get_contents($_FILES['text2']['tmp_name']);
		// $bytes2 = unpack("H*", $data2);
		// print_r($bytes2);

		$data1 = file_get_contents($_FILES['text1']['tmp_name']);
		//$data1 = mysql_entities_fix_string($conn,$data1);
		$bytes1 = unpack("H*",$data1);
		//print_r($bytes1);

		$query = "SELECT * FROM MalwareDex";
		$result = $conn->query($query);
		if(!$result)die($conn->error);
		$rows = $result->num_rows;
		$clean = true;
		for ($i = 0; $i < $rows; ++$i) {
			$result->data_seek($i);
			$row = $result->fetch_array(MYSQLI_NUM);
			if(strpos($bytes1[1],$row[1])!== false){
				echo("Your file was infected by the Malware: ".$row[0]."<br>");
				$clean = false;
				}
			}
			if($clean){
			echo("It's clean");
			}
		}


		//Proposing a new Malware to be added to Limbo
		if(isset($_POST['malwareName']) && isset($_FILES['malwareFile'])) {
			$user = mysql_entities_fix_string($conn, $username);
			$name = mysql_entities_fix_string($conn,$_POST['malwareName']);
			$rawText = file_get_contents($_FILES['malwareFile']['tmp_name']);
			$text = mysql_entities_fix_string($conn, $rawText);
			$signature = unpack("H*",$text)[1];
			$query = "INSERT INTO Limbo VALUES" .
			"('$user', '$name','$signature',Null)";
			$result = $conn->query($query);
			if (!$result) echo("INSERT failed: $query<br>" . $conn->error . "<br><br>");
			//$result->close();
		}

		//Adding a proposed Malware from Limbo to the MalwareDex
		if(isset($_POST['chosenMalware'])){
			$chosen = mysql_entities_fix_string($conn,$_POST['chosenMalware']);
			$query = "INSERT INTO MalwareDex ".
			"SELECT Name,Signature,Date FROM Limbo WHERE Name = '$chosen';"; 
			
			$result = $conn->query($query);
			echo("query submitted");
			if(!$result) echo ("Failed to add to MalwareDex" . $conn->error . "<br>");
			//$result->close();
		}
	
	
	$query = "SELECT * FROM Users WHERE Username = '$username';";
	$result = $conn->query($query);
	if (!$result) die($conn->error);
	elseif ($result->num_rows) {
		$row = $result->fetch_array(MYSQLI_NUM);
		$result->close();
		$clearance = $row[4];
		$sess_name = $row[1];
		
	}

		
		//add file form TODO: replace with virus check
		echo <<<_END
<html>
<head>
    <title>Please fill out this form</title>
</head>
<body>
    <form enctype="multipart/form-data" action="start.php" method="POST"><pre>
    Choose your file <input type="file" name = "text1">    
    
    <input type="submit" value= "ADD RECORD">
	</pre></form>
_END;
		if($clearance > 1){
		echo <<<_END
	Here is where you can add a potential Malware into Limbo.
	<form enctype="multipart/form-data" action="start.php" method="POST"><pre>
	Enter the name of the Malware <input type="text" name="malwareName">

	Choose the malware file <input type="file" name="malwareFile">

	<input type="submit" value="ADD to Limbo">
	</pre></form>
_END;
		if($clearance > 2){
			
		echo <<<_END
	And here is where you can choose which Malware from Limbo you would 
	like to add to our MalwareDex!
	<form action='start.php' method="POST"><pre>
	Enter the name of the Malware <input type="text" name="chosenMalware">
	<input type="submit" value="ADD to MalwareDex">
		</pre></form>
_END;
		}
		}
		echo <<<_END
		<span style="color: blue; font-size: 20px;">User submitted malwares are listed below:</span>
</body>
</html>
_END;
		
		//print contents of malware data tables
		$query = "SELECT * FROM Limbo ;";
		$result = $conn->query($query);
		if (!$result) die($conn->error);
	 
		$rows = $result->num_rows;
	
		for ($i = 0; $i < $rows; ++$i) {
	 
			$result->data_seek($i);
			$row = $result->fetch_array(MYSQLI_NUM);
			
			echo <<<_END
<pre>
	Proposed by user: $row[0]
	Name of malware: $row[1]		
	Malware Signature: $row[2]
	Date proposed: $row[3]

_END;

		}

		$query = "SELECT * FROM MalwareDex ;";
		$result = $conn->query($query);
		if (!$result) die($conn->error);
		$rows_md = $result->num_rows;
		echo <<<_END

<span style="color: red; font-size: 30px;">Contents of MalwareDex are listed below:</span>
</body>
</html>		
_END;

	for ($i = 0; $i < $rows_md; ++$i) {

		$result->data_seek($i);
		$row = $result->fetch_array(MYSQLI_NUM);

		echo <<<_END
	<pre>
	Name of malware: $row[0]		
	Malware Signature: $row[1]
	Date added: $row[2]
	</pre>
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
	
	//session
	function different_user() {
		destroy_session_and_data();
		echo "An unexpected error has occured.<br>";
	}
?>