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
DOWNLOAD A FILE

Programmed by: JONNY LINTON & SIMON JACQUES
ID# 27388489 & 27046677
-->

<?php
	// <!--****************************   DOWNLOAD SECTION START   ***********************************-->
	
	if(isset($_POST['linkbutton'])) {
		$file_id = $_SESSION['choice']; // Store file id
		$sid = $_SESSION['sid']; // Store student id
		$ip = $_SERVER['REMOTE_ADDR']; // Store ip address
		$row = ($_POST['linkbutton']); // Store row where file will be located in query
		$row = trim($row, 'Link ');
		$row = ((int)$row - 1);
		
		// Select all file versions information
		$query = "	SELECT FileVersions.up_date, FileVersions.path, FileVersions.fid, FileVersions.path,
						Users.sid
					FROM FileVersions, Users
					WHERE FileVersions.fid= " . $file_id . "
						AND Users.sid = FileVersions.up_by;";
			
		require('config.php');
			
		$result = mysql_query($query, $link); // Store the resulting query from server into variable
		
		if (!$result) { // Error
			die('Could not get data: '.mysql_error());
			mysql_close($link);
		} 
		else if(mysql_numrows($result) == 0) { // No files found
			echo('<script type="text/javascript">alert("No files");</script>');
		}
		$date = mysql_result($result, $row, 'up_date'); // Store file upload date
		$up_by = mysql_result($result, $row, 'sid'); // Store file uploader
		$path = mysql_result($result, $row, 'path'); // Store path where file was uploaded
		
		// Add stored information into downloads table
		$sql = "	INSERT INTO down (down_by, up_date, fid, up_by, ip_address)
					VALUES ('" . $sid . "',
							'" . $date . "',
							'" . $file_id . "',
							'" . $up_by . "',
							'" . $ip . "');";
		$result = mysql_query($sql); // Store the resulting query from server into variable

		if (!$result) { // Error
			echo('<script type="text/javascript">alert("An error occured. Make sure there are no duplicates");</script>');
		}
		echo("<script type='text/javascript'> window.location.href = '".$path."';</script>");
		unset($_SESSION["choice"]);
		mysql_close($link);
		exit;
	}
	
	// <!--****************************   DOWNLOAD SECTION END   ***********************************-->
	
	
	// <!--****************************   DELETE SECTION START   ***********************************-->
	
	if(isset($_POST['dltbutton'])) {
		$file_id = $_SESSION['choice']; // Store file id
		$sid = $_SESSION['sid']; // Store student id
		$ip = $_SERVER['REMOTE_ADDR']; // Store ip address
		$row = ($_POST['dltbutton']); // Store row where file will be located in query
		$row = trim($row, 'Delete ');
		$row = ((int)$row - 1);
		
		// Select all file versions information
		$query = "	SELECT FileVersions.up_date, FileVersions.path, FileVersions.file_size,
						Files.file_name, Files.ass_code,
						Users.sid
					FROM FileVersions, Users, Files
					WHERE FileVersions.fid= " . $file_id . "
						AND Users.sid = FileVersions.up_by
						AND Files.fid = FileVersions.fid;";
			
		require('config.php');
			
		$result = mysql_query($query, $link); // Store the resulting query from server into variable
		
		if (!$result) { // Error
			echo('<script type="text/javascript">alert("An error occured. Make sure to select something available");</script>');
		} 
		else {
			$date = mysql_result($result, $row, 'up_date'); // Store file upload date
			$up_by = mysql_result($result, $row, 'sid'); // Store file uploader
			$path = mysql_result($result, $row, 'path'); // Store path to uploaded file
			$size = mysql_result($result, $row, 'file_size'); // Store size of file
			$name = mysql_result($result, $row, 'file_name'); // Store name of file
			$ass = mysql_result($result, $row, 'ass_code'); // Store assignment code of file
			
			// Add stored information into deletion table
			$sql = "	INSERT INTO del(del_by, up_date, fid, up_by, ip_address, file_name, ass_code)
						VALUES ('" . $sid . "',
								'" . $date . "',
								'" . $file_id . "',
								'" . $up_by . "',
								'" . $ip . "',
								'" . $name . "',
								'" . $ass . "');";
								
			$result2 = mysql_query($sql); // Store the resulting query from server into variable
			
			if (!$result2) {
				echo('<script type="text/javascript">alert("An error occured. Make sure there are no duplicates");</script>');
			}
			else {
				// Set deletion date
				$query = "	UPDATE FileVersions
							SET FileVersions.del_date = '" . $date . "'
							WHERE FileVersions.path = '" . $path . "';";
							
				$result = mysql_query($query);
			}
		}
		mysql_close($link);
		unset($_SESSION["choice"]);
		require('fss_home.php');
		exit;
	}
	
	// <!--****************************   DELETE SECTION END   ***********************************-->
	
	
	// <!--****************************   ROLLBACK SECTION END   ***********************************-->
	
	if (isset($_POST['rollbkbutton'])) {
		$row = ($_POST['rollbkbutton']); // Store row where file will be located in query
		$row = trim($row, 'Version ');
		$file_id = $_SESSION['choice']; // Store file id
		$gid = $_SESSION['gid']; // Store group id
					
					
		// Select all file versions information
		$query = "	SELECT DISTINCT FileVersions.up_date, FileVersions.path,
						Files.file_name
					FROM FileVersions, Files
					WHERE FileVersions.fid= '" . $file_id . "'
						AND FileVersions.gid = '" . $gid . "'
						AND Files.fid = FileVersions.fid
					ORDER BY FileVersions.up_date DESC;";
					
		require('config.php');
		$result = mysql_query($query, $link);
		
		if (!$result) {
			die('Could not get data: ' . mysql_error());
		}
		else {
			$path = mysql_result($result, $row, 'path'); // Store path to uploaded file
			$name = mysql_result($result, $row, 'file_name'); // Store name of file
			$date_time = date("Y-m-d h-i-s"); // Store current date
			$target_path = "upload/" . $date_time . $name; // Create rollback path
			
			if (rename($path, $target_path)) { // Change path to file
				echo("<script language=\"javascript\"> alert(\"File successfully rolled back to previous version\") </script>");
				
				// Update path to file and set date to rollback date
				$query = "	UPDATE FileVersions
							SET FileVersions.path = '" . $target_path . "',
								FileVersions.up_date = '" . $date_time . "'
							WHERE FileVersions.path = '" . $path . "';";
				
				if(!mysql_query($query)) {
					echo('<script type="text/javascript">alert("An error occured. Make sure there are no duplicates");</script>');
				}
			}
			else {
				echo("<script language=\"javascript\"> alert(\"Could not rollback to previous version, please try again\") </script>");
			}
		}
		unset($_SESSION["choice"]);
		mysql_close($link);
	}
	
	// <!--****************************   ROLLBACK SECTION END   ***********************************-->
	
	include("head.inc");
?>

<!--****************************   CONTENT SECTION   ***********************************-->

		<div class="content">
			<?php
				echo("<h2 class='centerheader2'>Manage Files</h2>");
				
				// Get class' ending date
				$query = "	SELECT Info.endDate
							FROM Info;";
							
				require('config.php');
				
				$result = mysql_query($query, $link);
				$endDate = mysql_result($result, 0, 'endDate'); // Store end date
				$date = date("Y-m-d h-i-s"); // Store current date
				
				if ($date >= $endDate) { // Compare both date to see if class has ended
					echo("<h3>This class is over. You don't have access to these files anymore</h3>
						</div>");
					include("footer.inc");
					exit;
				}
						
				// <!--****************************   DOWNLOAD CONTENT START   ***********************************-->
				
				// Store session variables
				$page = $_SESSION['page'];
				$gid = $_SESSION['gid'];
				$file_id = $_SESSION['choice'];
				$sid = $_SESSION['sid'];
				
				if($page == 0) {			
					
					// Select all file name
					$query = "	SELECT file_name
								FROM Files
								WHERE Files.fid = '" . $file_id . "';";
					
					require('config.php'); // Fetch database server info
					
					$result = mysql_query($query, $link); // Store the resulting query from server into variable
					
					if (!$result) { // Error
					   die('Could not get data: '.mysql_error());
					} 
					else if(mysql_numrows($result) == 0) { // No files found
					}
					else { // Files found. Get file name
						$file_name = mysql_result($result, 0, 'file_name');
					}
					
					echo("<h3>Versions Available for File " . $file_id . " [" . $file_name . "]</h3>");
					
					if ($_SESSION['status'] == 2) { // If user is not a professor
						
						// Check if student is a leader
						$query1 = "	SELECT sid
									FROM leads
									WHERE gid = " . $gid . ";";
						
						$result1 = mysql_query($query1, $link);
						$numOfRows = mysql_num_rows($result1);
						if ($numOfRows > 0) {
							$sid1 = mysql_result($result1, 0, 'sid');
						}
						
						// Select all Versions of chosen file
						$query = "	SELECT DISTINCT FileVersions.up_date, FileVersions.path, FileVersions.file_size,
										Users.lname, Users.fname
									FROM FileVersions, Users
									WHERE FileVersions.fid= '" . $file_id . "'
										AND FileVersions.gid = '" . $gid . "'
										AND Users.sid = FileVersions.up_by
									ORDER BY FileVersions.up_date DESC;";
						
						$result = mysql_query($query, $link); // Store the resulting query from server into variable
						
						if ($numOfRows > 0 && $sid == $sid1) {
							$numOfRows = mysql_numrows($result); // Get number of rows
						}
						else {						
							$numOfRows = 1; // Get number of rows
						}
					}
					else {
						
						// Select all versions of a given file
						$query = "	SELECT DISTINCT FileVersions.up_date, FileVersions.path, FIleVersions.file_size,
										Users.lname, Users.fname
									FROM FileVersions, Users
									WHERE FileVersions.fid= '" . $file_id . "'
										AND Users.sid = FileVersions.up_by
									ORDER BY FileVersions.up_date DESC;";
						
						$result = mysql_query($query, $link); // Store the resulting query from server into variable
						$numOfRows = mysql_numrows($result); // Get number of rows
					}
					
					if (!$result) { // Error
					   die('Could not get data: '.mysql_error());
					} 
					else if(mysql_numrows($result) == 0) { // No files found
					}
					else { // Files found. Create table
					
						// Check if current user is leader of group
						$query = "	SELECT *
									FROM leads
									WHERE leads.sid = " . $_SESSION['sid'] . ";";
									
						$result2 = mysql_query($query, $link); // Store the resulting query from server into variable
						$numOfRows2 = mysql_numrows($result2); // Get number of rows
						
						if ($numOfRows2 > 0) {
							$leader = true;
						}
						else {
							$leader = false;
						}
						
						print('	<table class="download2 null">
									<tr>
										<th>Upload Date</th>
										<th>By</th>
										<th>Size</th>
										<th>Download</th>');
						if ($leader && $gid != NULL) {
							print('		<th>Modify</th>');
						}
						print(		'</tr>');
						for ($i = 0; $i < $numOfRows; $i++) { // Loops for every row generated by query
							print('	<tr>
										<td>'); // Print upload date
											$date = mysql_result($result, $i, 'up_date');
											print($date);
							print('	 </td>
										<td>'); // Print complete name of uploader
											$fname = mysql_result($result, $i, 'fname');
											$lname = mysql_result($result, $i, 'lname');
											print($fname . " " . $lname);
							print('		</td>
										<td>'); // Print file size
											$size = mysql_result($result, $i, 'file_size');
											print($size . "KB");
							print('		</td>
										<td>'); // Print file path, where file can be downloaded
							print("			<form method='post' action='fss_download_2.php'>
												<input class='button' value='Link " . ($i + 1) . "' name='linkbutton' type='submit'/>
											</form>");
							
							if ($leader && $gid != NULL) {
								print('	</td>
										<td>');
								// Print possible file deletion
								print("		<form method='post' action='fss_download_2.php'>
												<input class='button' value='Delete " . ($i + 1) . "' name='dltbutton' type='submit'/>
											</form>");
							}
							print('		</td>
									</tr>');											
						}
						print('	</table>');
						mysql_close($link);
					}
					// <!--****************************   DOWNLOAD CONTENT END   ***********************************-->
				}
				else if ($page == 1) {
					
					// <!--****************************   ROLLBACK CONTENT START   ***********************************-->
				
					$query = "SELECT file_name FROM Files WHERE Files.fid = '" . $file_id . "';";
					
					require('config.php'); // Fetch database server info
					
					$result = mysql_query($query, $link); // Store the resulting query from server into variable
					
					if (!$result) { // Error
					   die('Could not get data: '.mysql_error());
					} 
					else if(mysql_numrows($result) == 0) { // No files found
					}
					else { // Files found. Get file name
						$file_name = mysql_result($result, 0, 'file_name');
					}
					
					echo("<h3>Rollbacks Available for File " . $file_id . " [" . $file_name . "]</h3>");
					
					// Select all versions of a given file
					$query = "	SELECT DISTINCT FileVersions.up_date, FileVersions.path, FileVersions.file_size,
									Users.lname, Users.fname
								FROM FileVersions, Users
								WHERE FileVersions.fid= '" . $file_id . "'
									AND FileVersions.gid = '" . $gid . "'
									AND Users.sid = FileVersions.up_by
								ORDER BY FileVersions.up_date DESC;";
					
					$result = mysql_query($query, $link); // Store the resulting query from server into variable
					$numOfRows = mysql_numrows($result); // Get number of rows
					
					if (!$result) { // Error
					   die('Could not get data: '.mysql_error());
					} 
					else if($numOfRows == 0) { // No files found
					}
					else if($numOfRows == 1) {
						echo("Only one version available, no rollback possible");
					}
					else { // Files found. Create table
						
						print('	<table class="download2 null">
									<tr>
										<th>Upload Date</th>
										<th>By</th>
										<th>Rollback</th>										
									</tr>');
						for ($i = 1; $i < $numOfRows; $i++) { // Loops for every row generated by query
							print('	<tr>
										<td>'); // Print upload date
											$date = mysql_result($result, $i, 'up_date');
											print($date);
							print('	 </td>
										<td>'); // Print complete name of uploader
											$fname = mysql_result($result, $i, 'fname');
											$lname = mysql_result($result, $i, 'lname');
											print($fname . " " . $lname);
							print("		</td>
										<td>
											<form method='post' action='fss_download_2.php'>
												<input class='button' value='Version " . $i . "' name='rollbkbutton' type='submit'/>
											</form>");
							print('		</td>
									</tr>');											
						}
						print('	</table>');
						mysql_close($link);
					}
				}
				
				// <!--****************************   DOWNLOAD CONTENT END   ***********************************-->
				
				unset($_SESSION["page"]);
			?>
		</div>
		
<!--****************************   CONTENT SECTION END   *******************************-->

<?php
	include("footer.inc");
	} // end of else
?>