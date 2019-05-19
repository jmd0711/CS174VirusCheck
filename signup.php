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
	$email = $username = $password = "";
	
	if (isset($_POST['email']))
		$email = mysql_fix_string($conn, $_POST['email']);
	if (isset($_POST['username']))
		$username = mysql_fix_string($conn, $_POST['username']);
	if (isset($_POST['password']))
		$password = mysql_fix_string($conn, $_POST['password']);
	
	$fail = validate_email($email);
	$fail .= validate_username($username);
	$fail .= validate_password($password);
	
	echo "<!DOCTYPE html>\n<html><head><title>An Example Form</title>";
	
	if ($fail == "") {
		echo "</head><body>Form data succesfuly validated!</body></html>";
		
		//hash and salt password
		$salt = "zr#b%";
		$token = hash('ripemd128', "$salt$password");
		
		//add to database table user
		if (isset($_POST['contributor'])) {
			//add user with clearance 2
			add_contributor($conn, $email, $username, $token);
		} else {
			//add user with clearance 1
			add_user($conn, $email, $username, $token);
		}
	}
	
	//Sign up form and Client side validation
	echo <<<_END
	<style>
		.signup {
			border: 1px solid #999999;
			font: normal 14px helvetica; color: #444444;
		}
	</style>
	
	<script>
		function validate(form) {
			fail = validateEmail(form.email.value)
			fail += validateUsername(form.Username.value)
			fail += validatePassword(form.password.value)
			
			if (fail == "") {
				return true
			} else {
				alert(fail)
				return false
			}
			
		function validateEmail(field) {
			if (field == "") return "No Email was entered.\n"
			else if (!/\S+@\S+\.\S+/.test(field))
				return "The Email address is invalid.\n"
			return ""
		}
		
		function validateUsername(field) {
			if (field == "") return "No Username was entered.\n"
			else if (field.length < 5)
				return "Usernames must be at least 5 characters.\n"
			else if (/[^a-zA-Z0-9_-]/.test(field))
				return "Only letters, numbers, - and _ are allowed in Usernames.\n"
			return ""
		}
			
		function validatePassword(field) {
			if (field == "") return "No Password was entered.\n"
			else if (field.length < 6)
				return "Passwords must be at least 6 characters.\n"
			else if (!/[a-z]/.test(field) || ! /[A-Z]/.test(field) || !/[0-9]/.test(field))
				return "Passwords require one each of a-z, A-Z and 0-9.\n"
			return ""
		}
	</script>
</head>
<body>

	<table border= "0" cellpadding="2" cellspacing="5" bgcolor="#eeeeee">
		<th colspan="2" align="center">Signup Form</th>
		
			<tr><td colspan="2">Sorry, the following errors were found<br>
				in your form: <p><font color=red size = 1><i>$fail</i></font></p>
			</td></tr>
			
		<form method="post" action="signup.php" onSubmit="return validate(this)">
			<tr><td>Email</td>
				<td><input type="text" maxlength="32" name="email">
			<tr><td>Username</td>
				<td><input type="text" maxlength="32" name="username">
			<tr><td>Password</td>
				<td><input type="password" maxlength="32" name="password">
			<tr><td><input type="checkbox" name="contributor"> Sign me up as a contributor
			</td></tr><tr><td colspan="2" align="center"><input type="submit" value="sign up"></td></tr>
			</td></tr><tr><td colspan="2" align="center"><a href="start.php" class="button--style-red">return</a></td></tr>
		</form>
	</table>
</body>
</html>
_END;

	function add_user($connection, $email, $username, $password) {
		$clearance_level ='1';
		$query = "INSERT INTO Users VALUES (NULL,'$username', '$password','$email','$clearance_level')";
		$result = $connection->query($query);
		if (!$result) die($connection->error);
	}

	function add_contributor($connection, $email, $username, $password)
	{
		$clearance_level = '2';
		$query = "INSERT INTO Users VALUES (NULL,'$username', '$password','$email','$clearance_level')";
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
	
	//Server side validation
	function validate_email($field) {
		if ($field == "") return "No Email was entered<br>";
		else if (!preg_match("/\S+@\S+\.\S+/", $field))
			return "The Email address is invalid<br>";
		return "";
	}
	
	function validate_username($field) {
		if ($field == "") return "No Username was entered<br>";
		else if (strlen($field) < 5)
			return "Usernames must be at least 5 characters<br>";
		else if (preg_match("/[^a-zA-Z0-9_-]/", $field))
			return "Only letters, numbers, - and _ are allowed in Usernames.<br>";
		return "";
	}
			
	function validate_password($field) {
		if ($field == "") return "No Password was entered<br>";
		else if (strlen($field) < 6)
			return "Passwords must be at least 6 characters<br>";
		else if (!preg_match("/[a-z]/", $field) || !preg_match("/[A-Z]/", $field) || !preg_match("/[0-9]/", $field))
			return "Passwords require one each of a-z, A-Z and 0-9<br>";
		return "";
	}
		

	
?>