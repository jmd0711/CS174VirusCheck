<?php
	//CS174 - Fabio Di Troia
	//Virus Check
	//by Jasper Matthew Dumdumaya and Trung Tran
	
	//------------------//
	//	Setup Users		//
	//------------------//
	
	//**********************************************//
	//	Only run once to set up tables for database	//
	//**********************************************//
	
	//database login
	require_once 'login.php';
	$conn = new mysqli($hn, $un, $pw, $db);
	if ($conn->connect_error) die($conn->connect_error);
	
	// $query = "CREATE TABLE users (
	// 	ID	smallint(6) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	// 	email VARCHAR(32) NOT NULL UNIQUE,
	// 	username VARCHAR(32) NOT NULL UNIQUE,
	// 	password VARCHAR(32) NOT NULL
	// )";
	$query = "CREATE TABLE IF NOT EXISTS Users (
		ID SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
		Username VARCHAR(50) NOT NULL UNIQUE,
		Password VARCHAR(255) NOT NULL,
		Email VARCHAR(50) NOT NULL UNIQUE, 
		Clearance CHAR(1) NOT NULL);";
	
	$result = $conn->query($query);
	if (!$result) die($conn->error);
	
	// $query = "CREATE TABLE data (
	// 	EntryID smallint(6) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	// 	username VARCHAR(32) NOT NULL,
	// 	title VARCHAR(32) NOT NULL,
	// 	text blob NOT NULL
	// )";
	$query = "CREATE TABLE IF NOT EXISTS MalwareDex (
		Name VARCHAR(50) NOT NULL UNIQUE,
		Signature BLOB NOT NULL,
		Date TIMESTAMP);";
	
	$result = $conn->query($query);
	if (!$result) die($conn->error);

	$query = "CREATE TABLE IF NOT EXISTS Limbo (
		User VARCHAR(50) NOT NULL,
		Name VARCHAR(50) NOT NULL UNIQUE,
		Signature BLOB NOT NULL,
		Date TIMESTAMP);";

	$result = $conn->query($query);
	if (!$result) die($conn->error);

	$salt = "zr#b%";
	$admin_username = 'ADMIN'; 
	$password = 'thepass';
	$token = hash('ripemd128', "$salt$password");
	$clearance_level = '3';
	$email = 'admin@mail.com';

	$query = "SELECT * FROM Users WHERE Username = 'ADMIN';";
	$result = $conn->query($query);
	if(!$result) die($conn->error);
	$rows = $result->num_rows;
	if($rows == 0){
		$query = "INSERT INTO Users VALUES (NULL,'$admin_username', '$token','$email','$clearance_level')";
		$result = $conn->query($query);
		if (!$result) die($conn->error);
	}
	//$result->close();
	$conn->close();
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