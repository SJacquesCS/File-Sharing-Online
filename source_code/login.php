<?php session_start();

	if(isset($_POST["submit"]))
	{
		$sid = $_POST["sid"];
	   	$password = $_POST["password"];

		// check if the input strings could be dangerous
		require('validate.php');
		if(isDangerous($sid) || isDangerous($password))
		{
			echo ("<script language=\"javascript\"> alert(\"Invalid input!!\") </script>");
			unset($_POST['submit']);
			require('fss_logout.php');
			exit;
		}


	   	require('config.php');

		$query = "	SELECT *
					FROM Users
					WHERE sid='" . $sid . "'
						AND password='" . $password . "';";
						
		
		$result = mysql_query($query, $link);
		global $failedLogin;

		if (!$result) //error
		{
		   die('Could not get data: ' . mysql_error());
		} 
		else if(mysql_num_rows($result) == 0) //student is not in datebase
		{
			$failedLogin = true;
		}
		else //student is in the database
		{ 
			$failedLogin = false;
			$value = mysql_fetch_assoc($result);
	       
	        // Load session variables with User information
			$_SESSION['sid'] = $value['sid'];
			$_SESSION['fname'] = $value['fname'];
			$_SESSION['lname'] = $value['lname'];
			$_SESSION['password'] = $value['password'];
			$_SESSION['status'] = $value['status'];
			$_SESSION['gid'] = $value['gid'];
			$_SESSION['joinDate'] = $value['joinDate'];
			
			// Select all files to be deleted
			$query = "	SELECT FileVersions.del_date, FileVersions.fid, FileVersions.path
						FROM FileVersions;";
						
			$result = mysql_query($query, $link);
			$numOfRows = mysql_num_rows($result);
			$date_time = date("Y-m-d h-i-s"); // Save upload date
			
			if(!$result) {
				die(mysql_error());
			}
			else {
				for ($i = 0; $i < $numOfRows; $i++) {
					$del_date = mysql_result($result, $i, 'del_date');
					$del_date_plus = date("Y-m-d h-i-s", strtotime($del_date . ' +1 minute'));
					
					if ($del_date != NULL && $date_time >= $del_date_plus) {
						$fid = mysql_result($result, $i, 'fid');
						$path = mysql_result($result, $i, 'path');
						$query = "	SELECT FileVersions.path, FileVersions.up_by,
										Files.file_name
									FROM FileVersions, Files
									WHERE FileVersions.fid = '" . $fid . "'
										AND Files.fid = FileVersions.fid;";
										
						$result = mysql_query($query, $link);
						$numOfRows = mysql_num_rows($result);
						
						if(!$result) {
							die(mysql_error());
						}
						else {
							if ($numOfRows = 1) {
								$query = "	DELETE FROM Files
											WHERE Files.fid = '" . $fid . "';";
								$result2 = mysql_query($query);
								
								if (!$result2) {
									die(mysql_error());
								}
							}
							else {
								$query = "	DELETE FROM FileVersions
											WHERE FileVersions.del_date = '" . $del_date . "';";
											
								$result = mysql_query($query);
								
								if(!$result) {
									die(mysql_error());
								}
							}
							unlink($path);
						}
					}
				}
			}

			require('fss_home.php');
			exit;
		}
	}
?>

<!DOCTYPE >
<html lang = "en">
    <head>
	    <meta charset = "utf-8" />
		<title>Login</title>
		<link rel="stylesheet" type="text/css" href="fss.css" />
		<!-- 
		<script type="text/javascript" src=""></script>
		-->
    </head>    
<body onload = "document.getElementById('button').focus();">
	<fieldset class = "centerheader1 login">
		<h2>Welcome!</h2>
		<form method="post" action="login.php">
			Student ID:
			<input type="text" name="sid" id ="sid" placeholder= "Enter your Student ID"/>
			</br>
			Password:
			<input type="password" name="password" id ="password" placeholder= "Enter your Password"/>
			<br/><br/>
			<input id="button" type="submit" name="submit" value="Log-in"/>
			<?php
				if($failedLogin)
					echo "<div id='failedLogin'><p style='color:red;'>Error! Please Enter Valid Credentials!</p></div>";
			?>
		</form>
	<br/>
	</fieldset>
</body>
</html>






