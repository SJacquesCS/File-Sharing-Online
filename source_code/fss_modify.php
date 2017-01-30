<?php
	session_start();

	if(!isset($_SESSION['sid'])) //did not login - should not obtain access
	{
		require('fss_logout.php');
		exit;
	}
	else // user logged in - provide them with access
	{

		if (isset($_POST['addStudent'])) // adding a student to a group
		{
			if (isset($_POST['empty_users'])) {
				$sid = $_POST['empty_users'];
				if (isset($_POST['groups'])) {
					$gid = $_POST['groups'];
					require('config.php');
					$query = "	UPDATE Users
								SET gid = '" . $gid . "'
								WHERE sid = '" . $sid . "';"; // sets selected group id to chosen student

					$result = mysql_query($query, $link);

					if (!$result) { // Error
						mysql_close($link);
						 echo('<script type="text/javascript">alert("An error occured. Make sure there are no duplicates, or if the chosen group is full.");</script>');
					}
					else{ // Success
						mysql_close($link);
						echo ("<script language=\"javascript\"> alert(\"Student inserted to group successfully!\") </script>");
					}
				}
				else
					echo ("<script language=\"javascript\"> alert(\"You must select a group!\") </script>");
			}
			else
				echo ("<script language=\"javascript\"> alert(\"You must select a student to add!\") </script>");
		}

		if (isset($_POST['removeStudent'])) // removing a student from their assigned group
		{
			if (isset($_POST['enrolled_users'])) {
				$sid = $_POST['enrolled_users'];

				require('config.php');
				$query = "	UPDATE Users
							SET gid = 0
							WHERE sid = '" . $sid . "';"; // setting the group id to 0, meaning that student will no longer be in a group

				$result = mysql_query($query, $link);

				if (!$result) { // Error
					mysql_close($link);
					die('Could not get data: ' . mysql_error());
				}
				else { // Success
					echo ("<script language=\"javascript\"> alert(\"Student removed from group successfully!\") </script>");

					 // Additional step to remove person from leads as well!
					require('config.php');
					$query = "	SELECT *
								FROM leads
								WHERE sid = " . $sid . ";";

					$result = mysql_query($query, $link);
					$rows = mysql_numrows($result);
					if ($rows > 0)
					{
						require('config.php');
						$query = "	DELETE FROM leads
									WHERE sid = " . $sid . ";";

						$result = mysql_query($query, $link);
					}
					mysql_close($link);
				}
			}
			else
				echo ("<script language=\"javascript\"> alert(\"You must select a student to remove!\") </script>");
		}

		if (isset($_POST['renameGroup'])) // renaming a group
		{
			if (isset($_POST['group_names'])) {
				$gid = $_POST['group_names'];

				$newGName = $_POST['newGName']; // empty text field bypasses isset, so using if-cond here!

				if ($newGName == "")
					echo ("<script language=\"javascript\"> alert(\"You must choose a name to rename group!\") </script>");

				// check if the input strings could be dangerous
				require('validate.php');
				if(isDangerous($newGName))
				{
					//mysql_close($link);
					echo ("<script language=\"javascript\"> alert(\"Invalid input!!\") </script>");
					unset($_POST['renameGroup']);
					require('fss_modify.php');
					exit;
				}

				else
				{
					require('config.php');
					$query = "	UPDATE Groups
								SET gname = '" . $newGName . "'
								WHERE gid = '" . $gid . "';"; // setting the chosen group name to the new name

					$result = mysql_query($query, $link);

					if (!$result) { // Error
						mysql_close($link);
						 echo('<script type="text/javascript">alert("An error occured. Make sure there are no duplicates");</script>');
					}
					else { // Success
						echo ("<script language=\"javascript\"> alert(\"Group renamed successfully!\") </script>");
						mysql_close($link);
					}
				}
			}
			else
				echo ("<script language=\"javascript\"> alert(\"You must select a group to rename!\") </script>");
		}

		if (isset($_POST['newLeader'])) // assigns a leader to a group
		{
			if (isset($_POST['non_group_leaders']))
				$sid = $_POST['non_group_leaders'];
			else
				echo ("<script language=\"javascript\"> alert(\"You must select a student to make leader!\") </script>");

			require('config.php');
			$query = "	SELECT g.gid
						FROM Groups g, Users u
						WHERE u.gid = g.gid
							AND u.sid = " . $sid . ";";

			$result = mysql_query($query, $link);
			$gid = mysql_result($result, 0, 'gid'); // 0 because there should be only 1 result!

			require('config.php');
			$query = "	INSERT INTO leads
						VALUES(	'" . $sid . "',
								'" . $gid . "');"; // making chosen student the leader of their group

			$result = mysql_query($query, $link);

			if (!$result) { // Error
				mysql_close($link);
				 echo('<script type="text/javascript">alert("An error occured. Make sure there are no duplicates");</script>');
			}
			else { // Success
				echo ("<script language=\"javascript\"> alert(\"Student successfully became leader!\") </script>");
				mysql_close($link);
			}
		}

		include("head.inc");
?>

<!--
MODIFY A GROUP

ONLY PROF HAS PERMISSION FOR THIS PAGE

Programmed by: JONNY LINTON & JONATHAN CARDONE
ID# 27388489 & 27317026
-->

<!--****************************   CONTENT SECTION   ***********************************-->
		<div class="content">
		<?php
			if($_SESSION['status'] == 1) // if the user is a professor, allow them to see content
			{
		?>
		<!-- ****************** PROFESSOR-ONLY CONTENT ZONE START **************************** -->

			<h2 class="centerheader2">Modify Groups</h2>
			<div class="modify">
				<h3>Assign student to a group:</h3>
				<form method="post" action="fss_modify.php">
					<select name="empty_users" style="width:140px;">
						<option value="none" disabled selected="selected">Select a student</option>
						<?php
							$query = "	SELECT sid, fname, lname
										FROM Users
										WHERE status = 2
											AND gid = 0;"; // displaying students without a group

							require('config.php');
							$result = mysql_query($query, $link);
							$rows = mysql_numrows($result);
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
					<select name="groups" style="width:140px;">
						<option value="none" disabled selected="selected">Select a group</option>
						<?php
							$query = "	SELECT gid
										FROM Groups;";

							require('config.php');
							$result = mysql_query($query, $link);
							$rows = mysql_numrows($result);
							for ($i = 1; $i < $rows; $i++)
							{
								$gid = mysql_result($result, $i, 'gid');
								echo ("<option value = " . $gid . "> " . $gid);
							}
							mysql_close($link);
						?>
					</select>
					<br/><br/>
					<input class ="button" type="submit" name="addStudent" value="Add"/>
				</form>
				<br/>
				<h3>Remove student from a group:</h3>
				<form method="post" action="fss_modify.php">
					<select name="enrolled_users" style="width:140px;">
						<option value="none" disabled selected="selected">Select a student</option>
						<?php
							$query = "	SELECT sid, fname, lname
										FROM Users
										WHERE status = 2
											AND gid <> 0;"; // displaying students that are in a group

							require('config.php');
							$result = mysql_query($query, $link);
							$rows = mysql_numrows($result);
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
				<h3>Rename a group:</h3>
				<form method="post" action="fss_modify.php">
					<select name="group_names" style="width:140px;">
						<option value="none" disabled selected="selected">Select a group</option>
						<?php
							$query = "	SELECT gid, gname
										FROM Groups;";

							require('config.php');
							$result = mysql_query($query, $link);
							$rows = mysql_numrows($result);
							for ($i = 1; $i < $rows; $i++)
							{
								$gid = mysql_result($result, $i, 'gid');
								$gname = mysql_result($result, $i, 'gname');
								echo ("<option value = " . $gid . "> " . $gid . ' - ' . $gname);
							}
							mysql_close($link);
						?>
					</select>
					<br/><br/>
					<input type="text" name="newGName" placeholder="Type new name" style="width:136px;"/>
					<br/><br/>
					<input class ="button" type="submit" name="renameGroup" value="Rename"/>
				</form>



				<br/>
				<h3>Define group leader:</h3>
				<form method="post" action="fss_modify.php">
					<select name="non_group_leaders" style="width:140px;">
						<option value="none" disabled selected="selected">Select a student</option>
						<?php
							$query = "	SELECT u.sid, u.fname, u.lname, u.gid
										FROM Users u
										WHERE u.status = 2
											AND u.gid <> 0
											AND NOT EXISTS(
												SELECT l.sid
												FROM leads l
												WHERE u.sid = l.sid)
											AND NOT EXISTS(
												SELECT ll.gid
												FROM leads ll
												WHERE u.gid = ll.gid);"; // displaying students who are eligible to become leaders of their groups

							require('config.php');
							$result = mysql_query($query, $link);
							$rows = mysql_numrows($result);
							for ($i = 0; $i < $rows; $i++)
							{
								$sid = mysql_result($result, $i, 'u.sid');
								$gid = mysql_result($result, $i, 'u.gid');
								$fname = mysql_result($result, $i, 'u.fname');
								$lname = mysql_result($result, $i, 'u.lname');
								echo ("<option value = " . $sid . "> " . '(' . $gid . ') ' . $sid . ' - ' . $fname. ' ' . $lname);
							}
							mysql_close($link);
						?>
					</select>
					<br/><br/>
					<input class ="button" type="submit" name="newLeader" value="Make Leader"/>
					<br/>
				</form>

			</div>
			<div class="list2">
				<h3>Currently Enrolled Students:</h3>
					<table class = "enrolled_students">
						<tr>
							<th>Group ID</th>
							<th>Student ID</th>
							<th>Name</th>
						</tr>
						<?php
							$query = "	SELECT gid, sid, fname, lname
										FROM Users
										WHERE status = 2
											AND gid <> 0
										ORDER BY gid;"; // displaying students who are enrolled in a group

							require('config.php');
							$result = mysql_query($query, $link);
							$rows = mysql_numrows($result);
							for ($i = 0; $i < $rows; $i++)
							{
								echo("<tr>");
									$gid = mysql_result($result, $i, 'gid');
									echo("<td>" . $gid . "</td>");
									$sid = mysql_result($result, $i, 'sid');
									echo("<td>" . $sid . "</td>");
									$full_name = mysql_result($result, $i, 'fname') . " " . mysql_result($result, $i, 'lname');
									echo("<td>" . $full_name . "</td>");
								echo("</tr>");
							}
							mysql_close($link);
						?>
					</table>
				</div>

		<!-- ****************** PROFESSOR-ONLY CONTENT ZONE END*************************** -->

		<?php
			}
			else //if not professor - deny access.
			{
		?>

		<!-- ****************** NON-PROFESSOR CONTENT ZONE START*************************** -->

			<h2 class="centerheader">Access Denied!</h2>

		<!-- ****************** NON-PROFESSOR CONTENT ZONE END*************************** -->

		<?php
			}
		?>
		</div>
<!--****************************   CONTENT SECTION END   *******************************-->

<?php
	include("footer.inc");
	} // end of else
?>