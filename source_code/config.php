<?php
	$server_name = 'qoc353_1.encs.concordia.ca'; // Store server
	$user = 'qoc353_1'; // Log-in username
	$pass = 'dbsu2016'; // Log-in password
	$database = 'qoc353_1'; // Store database
	$link = mysql_connect($server_name, $user, $pass); // Connect to server
	mysql_select_db($database); // Access database

	// Check if connection works
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
?>