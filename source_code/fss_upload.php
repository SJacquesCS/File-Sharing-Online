<?php
	session_start();
		
	if(!isset($_SESSION['sid']) || $_SESSION['gid'] == 0 && $_SESSION['gid'] != NULL) //did not login - should not obtain access
	{
		require('fss_logout.php');
		exit;
	}
	else // user logged in - provide them with access
	{
?>

<!-- 
UPLOAD A FILE

Programmed by: JONNY LINTON, SIMON JACQUES & JONATHAN CARDONE
ID# 27388489, 27046677 & 27317026
--> 

<?php
	// <!--****************************   INSTRUCTOR OPTIONS START   ***********************************-->
	if (isset($_POST['add']))
	{
		if (isset($_POST['newWork']) && $_POST['newWork'] != NULL && $_POST['newWork'] != '') {
			$newWorkType = $_POST['newWork'];

			// check if the input strings could be dangerous
			require('validate.php');
			if(isDangerous($newWorkType))
			{
				//mysql_close($link);
				echo ("<script language=\"javascript\"> alert(\"Invalid input!!\") </script>");
				unset($_POST['add']);
				require('fss_upload.php');
				exit;
			}

			// Add $newWorkType into ass_code attribute in Assignment table
			require('config.php');
			$query = "	INSERT INTO Assignment
						VALUES ('" . $newWorkType . "');";
						
			$result = mysql_query($query, $link);

			if (!$result) { // Error
			   mysql_close($link);
			   echo('<script type="text/javascript">alert("An error occured. Make sure there are no duplicates");</script>');
			} 
			else { // Success
				echo ("<script language=\"javascript\"> alert(\"Homework Type Successfully Inserted!\") </script>");
				mysql_close($link);
			}
		}
		else { // If missing name
			echo ("<script language=\"javascript\"> alert(\"Please Specify which Assignment to Add!\") </script>");
		}
	}
	
	if(isset($_POST['remove']))
	{
		if (isset($_POST['removeWork'])) {
			$removeWorkType = $_POST['removeWork'];

			// Remove $removeWorkType from ass_code attribute in Assignment table
			require('config.php');
			$query = "	DELETE
						FROM Assignment
						WHERE ass_code = '" . $removeWorkType . "';";
						
			$result = mysql_query($query, $link);

			if (!$result) { // Error
			   mysql_close($link);
			   die('Could not delete data: ' . mysql_error());
			} 
			else { // Success
				echo ("<script language=\"javascript\"> alert(\"Homework Type Successfully Removed!\") </script>");
				mysql_close($link);
			}
		}
		else { // If missing selection
			echo ("<script language=\"javascript\"> alert(\"Please Specify which Assignment to Remove!\") </script>");
		}
	}
	
	if(isset($_POST['rename'])) {
		if(isset($_POST['renameWork'])) {
			$renameWork = $_POST['renameWork'];
			$newName = $_POST['newName'];	
			
			// check if the input strings could be dangerous
			require('validate.php');
			if(isDangerous($newName))
			{
				//mysql_close($link);
				echo ("<script language=\"javascript\"> alert(\"Invalid input!!\") </script>");
				unset($_POST['rename']);
				require('fss_upload.php');
				exit;
			}

			require('config.php');
			$sql = "	UPDATE Assignment
						SET ass_code = '" . $newName . "'
						WHERE ass_code = '" . $renameWork . "';";
						
			$result = mysql_query($sql, $link);
			
			if (!$result) { // Error
			   mysql_close($link);
			   die('Could not update data: ' . mysql_error());
			} 
			else { // Success
				echo ("<script language=\"javascript\"> alert(\"Homework Type Successfully Renamed!\") </script>");
				mysql_close($link);
			}
		}
		else {
			echo("<script language=\"javascript\"> alert(\"Please Choose an assignment to rename first!\") </script>");
		}
	}
	
	// <!--****************************   INSTRUCTOR OPTIONS END   ***********************************-->

	
	// <!--****************************   STUDENT OPTIONS START   ***********************************-->
	
	if(isset($_POST['upload']))
	{
		// Store file variables
		$file = $_FILES['file']; // Store file information into an array
		$f_name = $file['name']; // Save file name
		$gid = $_SESSION['gid'];
		$size = ($file['size'] / 1024); // Save size of file [in KB]
		$date_time = date("Y-m-d h-i-s"); // Save upload date
		$temp_name = $file['tmp_name']; // Save temp name
		$ext = pathinfo($f_name, PATHINFO_EXTENSION); // Save extension of file
		$ip = $_SERVER['REMOTE_ADDR'];
		$pattern1 = "/[^ ]{1,}/";
		$pattern2 = "/[^ ]{1,}\.[a-zA-Z0-9]{1,}/";
		
		if(isset($_POST['work'])) {
			$code = $_POST['work'];
		}
		
		if (preg_match($pattern1, $f_name) != 1 || preg_match($pattern2, $f_name) != 1) {
		
			echo ("<script language=\"javascript\"> alert(\"The file your tried to upload has an empty name. Uploaded files must have a name.\") </script>");
			unset($_POST['upload']);
			require('fss_upload.php');
			exit;
		}
		
		$query = "	SELECT Info.maxFiles
					FROM Info;";
					
		require('config.php');
		
		$result = mysql_query($query, $link);
		$maxFiles = mysql_result($result, 0, 'maxFiles');
		
		$query = "	SELECT Groups.current_size
					FROM Groups
					WHERE Groups.gid = '" . $gid . "';";
					
		$result = mysql_query($query, $link);
		$currentFiles = mysql_result($result, 0, 'current_size');
		
		if (($currentFiles + $size) > $maxFiles) {
			echo ("<script language=\"javascript\"> alert(\"Your group's file storage is full. Please ask your leader to remove files before uploading new ones\") </script>");
		}
		else {
			// Create a query to check if the file has previous versions
			$query = "	SELECT Files.fid
						FROM Files
						WHERE Files.file_name = '" . $f_name . "'
							AND Files.ass_code = '" . $code . "'
							AND Files.gid = '" . $gid . "';";

			require('config.php');
			
			$result = mysql_query($query, $link); // Store the resulting query from server into variable
			
			if (!$result) { // Error
				mysql_close($link);
				die('Could not get data: ' . mysql_error());
			}			
			else if (mysql_num_rows($result) == 0) { // Check for duplicates
				echo("<script language=\"javascript\"> alert('File successfully uploaded without duplicates') </script>");
				// Add new file in Files table
				$sql = "INSERT INTO Files(ass_code, gid, file_name)
						VALUES(	'" . $code . "',
								'" . $gid . "',
								'" . $f_name . "');";
				
				mysql_query($sql);
			}
			else
				echo("<script language=\"javascript\"> alert(\"File successfully uploaded with duplicates as a new version\") </script>");
			
			// Create the same query as above to fetch the id of the file
			$query = "	SELECT Files.fid
						FROM Files
						WHERE Files.file_name = '" . $f_name . "'
							AND Files.ass_code = '" . $code . "'
							AND Files.gid = '" . $gid . "';";
								
			$result = mysql_query($query, $link); // Store the resulting query from server into variable
			
			if (!$result) { // Error
				die('Could not get data: ' . mysql_error());
				mysql_close($link);
			}
			else {
				$fid = mysql_result($result, 0, 'fid'); // Save the file id in a variable
				
				$target_path = "upload/" . $date_time . $f_name;			
				
				move_uploaded_file($temp_name, $target_path);
				chmod($target_path, 0664);
				
				// Add new version into FileVersions table
				$sql = "	INSERT INTO FileVersions (fid, up_date, up_by, ass_code, gid, file_size, file_type, path, ip_address)
							VALUES(	'" . $fid . "',
									'" . $date_time . "',
									'" . $_SESSION['sid'] . "',
									'" . $code . "',
									'" . $_SESSION['gid'] . "',
									'" . $size . "',
									'" . $ext . "',
									'" . $target_path . "',
									'" . $ip . "');";
								
				$result = mysql_query($sql);
				
				if (!$result) { // Error
					die('Could not get data: ' . mysql_error());
				}	
				
				$query = "	SELECT current_size
							FROM Groups
							WHERE Groups.gid = '" . $_SESSION['gid'] . "';";

				require('config.php');
				
				$result = mysql_query($query, $link); // Store the resulting query from server into variable
				
				if (!$result) { // Error
					mysql_close($link);
					die('Could not get data: ' . mysql_error());
				}			
				else {
					$current_size = mysql_result($result, 0, 'current_size');
					$current_size += $size;
					
					$sql = "UPDATE Groups
							SET current_size = '" . $current_size . "'
							WHERE gid = '" . $_SESSION['gid'] . "';";

					require('config.php');
					
					mysql_query($sql);
				}
			}
		}
		mysql_close($link);
	}
	
	// <!--****************************   STUDENT OPTIONS END   ***********************************-->
	
	//load contents from the Assignment table into $work array
	require('config.php');
	$query = "	SELECT ass_code
				FROM Assignment";
				
	$result = mysql_query($query, $link);
	$numOfRows = mysql_num_rows($result);

	if (!$result) { // Error
	   mysql_close($link);
	   die('Could not get data: ' . mysql_error());
	} 
	else if($numOfRows == 0) { // No files found
		echo('No assignments have been inserted!');
	}
	else { // Data found -- load work array
		for($j=0; $j < $numOfRows; $j++)
		{
			$work[$j] = mysql_result($result, $j);
		}
	}
	mysql_close($link);
	include("head.inc");
?>

<script type="text/javascript">

	function checkFile () {
		
		var output = document.getElementById("jsout");
		var file = document.getElementById("file");
		
		if (file.value.replace(/.*[\/\\]/, '').search(/[^ ]{1,}/) == -1 || file.value.replace(/.*[\/\\]/, '').search(/[^ ]{1,}\.[a-zA-Z0-9]{1,}/) == -1) {
			
			output.style.visibility = "visible";
			output.innerHTML = "Your file must have a name.";
			output.style.color = "red";
		}
		else {
			
			output.style.visibility = "hidden";
		}
	}

</script>

<!--****************************   CONTENT SECTION   ***********************************-->
<div class="content">
	<?php
		if($_SESSION['status'] == 2) // if the user is a student, allow them to see content
		{
			echo("<h2 class='centerheader2'>Upload a File</h2>");
			$query = "	SELECT Info.endDate
						FROM Info;";
						
			require('config.php');
			
			$result = mysql_query($query, $link);
			$endDate = mysql_result($result, 0, 'endDate');
			$date = date("Y-m-d h-i-s");
			
			if ($date >= $endDate) {
				echo("<h3>This class is over. You can't upload files anymore</h3>
					</div>");
				include("footer.inc");
				exit;
			}
	?>
	<!-- ****************** STUDENT-ONLY CONTENT ZONE START **************************** -->
	
			<h3>Location:</h3>				
			<form action="fss_upload.php" method="post" enctype="multipart/form-data">
				<select name="work">
					<?php
						for ($i = 0; $i < sizeOf($work); $i++) // select homework type that is being uploaded
						{
							if ($i == 0) {
								echo ("<option value = " . $work[$i] . " selected = 'selected'> " . $work[$i]); 
							}
							else {
								echo ("<option value = " . $work[$i] . "> " . $work[$i]); 
							}
							// the data from this selection will be passed in $_POST['work'] upon submission
						}
					?>
				</select>
				<br/>
				<h3>Select your file:</h3>
				<input type="file" name="file" id="file" onchange="checkFile()"/><br/><br/>
				<p id="jsout"></p>
				<input class="button" type="submit" name="upload" value="Upload File">
				<input class="button"type="reset" name ="reset" value="Reset">
			</form>
			
	<!-- ****************** STUDENT-ONLY CONTENT ZONE END ****************************** -->
	
	<?php 
		}
		else if ($_SESSION['status'] == 0 || $_SESSION['status'] == 1) //if not student - deny access to file upload.
		{
	?>		
	
	<!-- ****************** NON-STUDENT CONTENT ZONE START **************************** -->
	
			<h2 class="centerheader2">Modify Assignments</h2>
			
			<div class="modify">
				<h3>Add an assignment:</h3>
				<form method="post" action="fss_upload.php">
					<input type="text" name="newWork" placeholder="Type new assignment name" style="width:170px;"/><br/><br/>
					<input class="button" type="submit" name="add" value="Add"/>
				</form>
				<br/><h3>Remove an assignment:</h3>
				<form method="post" action = "fss_upload.php">
					<select name ="removeWork" style="width:175px;">
						<option value="none" disabled selected>Select an assignment</option>
						<?php
							foreach ($work as $key => $value) {
								echo("<option value = " . $value . ">" . $value);
							}
						?>
					</select><br/><br/>
					<input class="button" type="submit" name="remove" value="Remove">
				</form>
				<br/><h3>Rename an assignment:</h3>
				<form method="post" action="fss_upload.php">
					<select name ="renameWork" style = "width:175px;">
						<option value="none" disabled selected>Select an assignment</option>
						<?php
							foreach($work as $key => $value) {
								echo("<option value = " . $value . ">" . $value);
							}
						?>
					</select>
					<br/><br/>
					<input type = "text" name ="newName" placeholder="Type new name" style="width:170px;"/><br/><br/>
					<input class="button" type="submit" name="rename" value="Rename">
				</form>
			</div>
			<div class="list">
				<h3>Current assignments:</h3>
				<table>
					<tr>
						<th>Name</th>
						<th>Files</th>
					</tr>
					<?php
						for ($l = 0; $l < count($work); $l++) {
							require('config.php');
							echo("	<tr>
										<td>" . $work[$l] . "</td>");
							$query = "	SELECT COUNT(*)
										FROM FileVersions
										WHERE FileVersions.ass_code = '" . $work[$l] . "';";
							$result = mysql_query($query, $link);

							if (!$result) { // Error
							   mysql_close($link);
							   die('Could not get data: ' . mysql_error());
							} 
							else { // Data found -- load work array
								$file_num = mysql_result($result, 0);
								echo("	<td>" . $file_num . "</td>
									</tr>");
								mysql_close($link);
							}
						}
					?>
				</table>
			</div>

	<!-- ****************** NON-STUDENT CONTENT ZONE END **************************** -->
	
	<?php
		}
	?>
</div>
	
<!--****************************   CONTENT SECTION END   *******************************-->

<?php
	include("footer.inc");
	} // end of else
?>