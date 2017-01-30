<?php
	session_start();
	
	if(!isset($_SESSION['sid'])) //did not login - should not obtain access
	{
		require('fss_logout.php');
		exit;
	}
	else // user logged in - provide them with access
	{

		if(isset($_POST["submit"])) //Action when form is submitted
		{
			
			//Collect form info
			$oldPassword = $_POST['pass0'];
			$sid = $_SESSION["sid"];
			$newPassword = $_POST["pass1"];
			$newPassword2 = $_POST["pass2"];
			$pattern = "/^(?=.*\d)(?=.*[a-z])[0-9a-zA-Z]{6,}$/";
			
			if($newPassword != $newPassword2 || $oldPassword != $_SESSION['password'] || preg_match($pattern, $newPassword) != 1) // check if the password is invalid
			{
				echo ("<script language=\"javascript\"> alert(\"Invalid Input! Please try again.\") </script>");
				unset($_POST['submit']);
				require('fss_password.php');
				exit;
			}
			
			// check if the input strings could be dangerous
			require('validate.php');
			if(isDangerous($newPassword) || isDangerous($newPassword2) || isDangerous($oldPassword) || isDangerous($sid))
			{
				echo ("<script language=\"javascript\"> alert(\"Invalid input!!\") </script>");
				unset($_POST['submit']);
				require('fss_password.php');
				exit;
			}

			require('config.php');

			//Enter Student's password into the Database
			$query = "	UPDATE Users
						SET password = '".$newPassword."'
						WHERE sid = ".$sid.";";

			$result = mysql_query($query, $link);

			if (!$result) //Error
			{
			   mysql_close($link);
			   die('Could not enter data: '.mysql_error());
			} 
			else //Insert succeeded
			{
				mysql_close($link);
				echo ("<script language=\"javascript\"> alert(\"Password changed successfully!\") </script>");
				$_SESSION['password'] = $newPassword;
			}
			
		}
			
		include("head.inc");
?>
		<script type="text/javascript"> 
			function checkPwd(str) {
			    if (str.length < 6) {
			        return("Password must be at least 6 characters!");
			    } else if (str.length > 20) {
			        return("Password cannot be longer than 20 characters!");
			    } else if (str.search(/\d/) == -1) {
			        return("Password must contain a number!");
			    } else if (str.search(/[a-zA-Z]/) == -1) {
			        return("Password must contain a letter!");
			    } else if (str.search(/[\!\@\#\$\%\^\&\*\(\)\_\+]/) != -1) {
			        return("Password cannot contain special characters!");
			    }
			    return "yes";
			}
			
			function checkPass(){
				//alert("Inside checkPass()");
				var pass1 = document.getElementById("pass1").value;
				var pass2 = document.getElementById("pass2").value;

				var output = document.getElementById("jsOut");

				if(pass1.localeCompare(pass2) == 0) // if passwords are equal
				{
					var match = checkPwd(pass2)
					var pattern = /^(?=.*\d)(?=.*[a-z])[0-9a-zA-Z]{6,}$/;

					if(pattern.test(pass2)) // if password is in correct format
					{
						//pass1 is identical to pass2
						output.style.color = "blue";
						output.style.fontWeight = "bold";
						output.innerHTML = "Password is Valid!";
					}
					else
					{
						// not in correct format
						output.style.color = "orange";
						output.style.fontWeight = "bold";
						output.innerHTML = match;
						//output.innerHTML = "Password must be at least 6 characters of numbers and letters only!";
					}
				}
				else
				{
					output.style.color = "red";
					output.style.fontWeight = "bold";
					output.innerHTML = "Passwords DO NOT Match!";
				}
			}
		</script>
<!-- 
CHANGE PASSWORD

Programmed by: JONNY LINTON & SIMON JACQUESa
ID# 27388489 & 27046677
--> 

<!--****************************   CONTENT SECTION   ***********************************-->
		<div class="content">
			<h2 class="centerheader2">Change Your Password</h2>

		<form method="post" action="fss_password.php">
 		<br/>

 		<!-- Enter old password - and check? -->
 		Current Password:
		<input type="password" name="pass0" id ="pass0" placeholder= "Enter your old password"/>
		<br/><br/>

		New Password:
		<input type="password" name="pass1" id ="pass1" placeholder= "Enter your new password" oninput="checkPass()"/>
		<br/><br/>
	
		Re-Type New Password:
		<input type="password" name="pass2" id ="pass2" placeholder= "Re-Enter your new password" style="width:185px;" oninput="checkPass()"/>
		<p id="jsOut"></p>
		<br/>


		<input class="button" type="submit" name="submit" value="Change Password"/>

		</form>
	

		</div>
<!--****************************   CONTENT SECTION END   *******************************-->
<?php
	include("footer.inc");
	} // end of else
?>