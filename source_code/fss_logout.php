<?php
	session_start();
	
	if($_SESSION["sid"]){
		//echo ("<script language=\"javascript\"> alert(\"Logging-out...\") </script>"); // this is output
		
		// just to be safe -- eliminate the variables
		unset($_SESSION["sid"]); 
		unset($_SESSION["fname"]);
		unset($_SESSION["lname"]);
		unset($_SESSION["password"]);
		unset($_SESSION["status"]);
		unset($_SESSION["gid"]);

		session_destroy();

		require('index.php'); // return to login page
		exit;
	}
	else{
		//echo ("<script language=\"javascript\"> alert(\"You haven't Logged-In yet!\") </script>");
		require('index.php');
		exit;
	}

?>
