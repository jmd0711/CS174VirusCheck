<?php
	
	require_once 'login.php';
	$conn = new mysqli($hn, $un, $pw, $db);
	if ($conn->connect_error) die($conn->connect_error);
	
	$query = "CREATE TABLE users (
		ID	smallint(6) NOT NULL PRIMARY KEY AUTO_INCREMENT,
		email VARCHAR(32) NOT NULL UNIQUE,
		username VARCHAR(32) NOT NULL UNIQUE,
		password VARCHAR(32) NOT NULL
	)";
	
	$result = $conn->query($query);
	if (!$result) die($conn->error);
	
	$query = "CREATE TABLE data (
		EntryID smallint(6) NOT NULL PRIMARY KEY AUTO_INCREMENT,
		username VARCHAR(32) NOT NULL,
		title VARCHAR(32) NOT NULL,
		text blob NOT NULL
	)";
	
	$result = $conn->query($query);
	if (!$result) die($conn->error);
	/*
	$salt = "zr#b%";
	$email = "abc@email.com";
	$user = "admin";
	$password = "password";
	$token = hash('ripemd128', "$salt$password");
	add_user($conn, $email, $user, $token);
		
	function add_user($connection, $email, $username, $password) {
		$query = "INSERT INTO users VALUES ('$email', '$username', '$password')";
		$result = $connection->query($query);
		if (!$result) die($connection->error);
	}*/
	
?>