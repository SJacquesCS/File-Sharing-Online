<?php
	session_start();

	if(!isset($_SESSION['sid'])) //did not login - should not obtain access
	{
		require('fss_logout.php');
		exit;
	}
	else // user logged in - provide them with access
	{
		if(isset($_POST["addStudent"])) //Action when form is submitted
		{
			require('config.php');

			//C ollect form info
			$sid_add = $_POST["sid"];
			$fname_add = $_POST["fname"];
			$lname_add = $_POST["lname"];
			$password_add = "pass123"; // Default password
			$pattern = "/\d{8}/";
			$pattern2 = "/^[a-zA-Z-]+$/";
			
			if (preg_match($pattern, $sid_add) != 1 || preg_match("/[0]/", $sid_add) == 1) // Check validity of the password
			{
				mysql_close($link);
				echo ("<script language=\"javascript\"> alert(\"The student ID you entered is incorrect. Student IDs must be composed of 8 (non-zero) digits.\") </script>");
				unset($_POST['addStudent']);
				require('create_account.php');
				exit;
			}

			if (preg_match($pattern2, $fname_add) != 1 || preg_match($pattern2, $lname_add) != 1 ) // Check validity of names
			{
				mysql_close($link);
				echo ("<script language=\"javascript\"> alert(\"Invalid input - only letters (and hyphens) allowed in names.\") </script>");
				unset($_POST['addStudent']);
				require('create_account.php');
				exit;
			}

			// check if the input strings could be dangerous
			require('validate.php');
			if(isDangerous($fname_add) || isDangerous($lname_add))
			{
				mysql_close($link);
				echo ("<script language=\"javascript\"> alert(\"Invalid input!!\") </script>");
				unset($_POST['addStudent']);
				require('create_account.php');
				exit;
			}
			
			// Enter Student's info into the Database
			$query = "	INSERT INTO Users(sid, fname, lname, password)
						VALUES (	" . $sid_add . ",
									'" . $fname_add . "',
									'" . $lname_add . "',
									'" . $password_add . "');";

			$result = mysql_query($query, $link);

			if (!$result) // Error
			{
			    echo('<script type="text/javascript">alert("An error occured. Make sure there are no duplicates");</script>');
			   mysql_close($link);
			}
			else // Insert succeeded
			{
				mysql_close($link);
				echo ("<script language=\"javascript\"> alert(\"Student added successfully!\") </script>");
			}

		}

		// <!-- ******************   REMOVE SECTIO START   *************************** -->
		
		if (isset($_POST['removeStudent']))
		{
			if (isset($_POST['enrolled_users']))
				$sid = $_POST['enrolled_users'];
			else{
				echo ("<script language=\"javascript\"> alert(\"You must select a student to remove!\") </script>");
				unset($_POST['removeStudent']);
				require('create_account.php');
				exit;
			}
			
			// delete student in database
			require('config.php');
			$query = "	DELETE
						FROM Users
						WHERE sid = '" . $sid . "';";
			$result = mysql_query($query, $link);

			if (!$result) { // Error
			   die('Could not get data: ' . mysql_error());
			}
			else { // Success
				echo ("<script language=\"javascript\"> alert(\"Student removed successfully!\") </script>");
			}
			mysql_close($link);
		}
		
		// <!-- ******************   REMOVE SECTIO END   *************************** -->

		include("head.inc");
?>

<!-- ******************   ID CHECK SECTION START   *************************** -->

<script type="text/javascript">
	
	function check() { //checks for validity of student ID
	
		var output = document.getElementById("jsout");
		var sid = document.getElementById("sid");
		
		output.style.color="red";
		
		if (sid.value == "") {
		
			output.innerHTML = "";
			output.style.visibility = "hidden";
		}
		else if (sid.value.length != 8) {
			
			output.style.visibility = "visible";
			output.innerHTML = "Student ID must be exactly 8 characters long."
		}
		else if (sid.value.search(/\d{8}/) == -1) {
			
			output.style.visibility = "visible";
			output.innerHTML = "Student ID must be entirely composed of digits."
		}
		else {
			
			output.innerHTML = "";
			output.style.visibility = "hidden";
		}
	}
</script>

<!-- ******************   ID CHECK SECTION END   *************************** -->

<!--
HOME

Programmed by: JONNY LINTON & SIMON JACQUESa
ID# 27388489 & 27046677
-->

<!--****************************   CONTENT SECTION   ***********************************-->
		<div class="content">

		<?php
			if($_SESSION['status'] == 1) // if the user is a professor, allow them to see content
			{
		?>

		<!-- ****************** PROFESSOR-ONLY CONTENT ZONE START **************************** -->

			<h2 class="centerheader2">Add/Remove Students</h2>

			<form method="post" action="create_account.php">
				<br/><h3>Add a Student:</h3><br/>
				Student ID: <input type="text" name="sid" id ="sid" placeholder= "Enter Student ID" oninput="check()"/><br/>
				First Name: <input type="text" name="fname" id ="fname" placeholder= "Enter First Name"/><br/>
				Last Name: <input type="text" name="lname" id ="lname" placeholder= "Enter Last Name"/><br/>
				<br/><p id="jsout"></p>
				* Password will be set to "pass123" by default! <br/>
				* Student will not be placed in any group by default!<br/>
				<br/><input class="button" type="submit" name="addStudent" value="Add"/>
			</form>
			<br/><br/>
			<h3>Remove A Student:</h3>
			<form method="post" action="create_account.php">
				<select name="enrolled_users" style="width:140px;">
					<option value="none" disabled selected="selected">Select a student</option>
					<?php
						// Get students information for removal
						$query = "	SELECT sid, fname, lname
									FROM Users
									WHERE status = 2;";
						require('config.php');
						$result = mysql_query($query, $link);
						$rows = mysql_numrows($result);
						
						// Loop for all students and display them
						for ($i = 0; $i < $rows; $i++)
						{
							$sid = mysql_result($result, $i, 'sid');
							$fname = mysql_result($result, $i, 'fname');
							$lname = mysql_result($result, $i, 'lname');
							echo ("<option value = " . $sid . "> " . $sid . ' - ' . $fname . ' ' . $lname);
						}

						mysql_close($link);
					?>
				</select>
				<br/><br/>
				<input class = "button" type="submit" name="removeStudent" value="Remove"/>
			</form>
			<br/>
			<div class="list3">
				<h3>Currently Enrolled Students:</h3>
					<table class = "enrolled_students">
						<tr>
							<th>Student ID</th>
							<th>Name</th>
						</tr>
						<?php
							// List all students currently enrolled in a class
							$query = "	SELECT sid, fname, lname
										FROM Users
										WHERE status = 2
										ORDER BY sid;";
							require('config.php');
							$result = mysql_query($query, $link);
							$rows = mysql_numrows($result);
							
							// Loop for all students and display them
							for ($i = 0; $i < $rows; $i++)
							{
								$sid = mysql_result($result, $i, 'sid');
								$full_name = mysql_result($result, $i, 'fname') . " " . mysql_result($result, $i, 'lname');
								echo("	<tr>");
								echo("		<td>" . $sid . "</td>");
								echo("		<td>" . $full_name . "</td>");
								echo("	</tr>");
							}
							mysql_close($link);
						?>
					</table>
			</div>
		<!-- ******************   PROFESSOR-ONLY CONTENT ZONE END   *************************** -->

		<?php
			}
			else //if not professor - deny access.
			{
		?>

		<!-- ******************   NON-PROFESSOR CONTENT ZONE START   *************************** -->

			<h2 class="centerheader">Access Denied!</h2>

		<!-- ******************   NON-PROFESSOR CONTENT ZONE END   *************************** -->

		<?php
			}
		?>
		</div>
<!--****************************   CONTENT SECTION END   *******************************-->
<?php
		include("footer.inc");
	} // end of else
?>