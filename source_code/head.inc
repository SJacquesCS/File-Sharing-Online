
<!DOCTYPE html>
	<!-- 
		Programmed by: JONNY LINTON
		ID# 27388489
	--> 
	<html lang = "en">
	<head>
		<meta charset = "utf-8" />
		<title>FSS</title>
		<link rel="stylesheet" type="text/css" href="fss.css" />
		<script type="text/javascript" src="fss.js"></script>
	</head>
	<body>
		<div class="container">
			<div class="header">
				<h1>File Sharing System</h1>
				<h2>COMP 353 Concordia University</h2>
			</div>
			
			<div class="nav">
				<ul class="navlist">
					<li><a href="fss_home.php">Home</a></li>
					<?php
					
						// TA Nav Bar
						if($_SESSION['status'] == 0)
						{
							?>
							<li><a href="fss_download_1.php">Manage Files</a></li>
							<li><a href="fss_stats.php">File Statistics</a></li>
							<?php
						}

						// Professor Nav Bar
						if($_SESSION['status'] == 1) 
						{
							?>
							<li><a href="create_account.php">Modify Students</a></li>
							<li><a href="fss_modify.php">Modify Groups</a></li>
							<li><a href="fss_upload.php">Modify Assignments</a></li>
							<li><a href="fss_download_1.php">Manage Files</a></li>
							<li><a href="fss_stats.php">File Statistics</a></li>
							<?php
						}
						
						// Student Nav Bar
						if($_SESSION['status'] == 2) 
						{
							if($_SESSION['gid'] != 0 && $_SESSION['gid'] != NULL) // if student is in a group
							{
								?>
								<li><a href="fss_upload.php">Upload Files</a></li>
								<li><a href="fss_download_1.php">Manage Files</a></li>
								<?php
							}
						}
						
						// Global Nav Bar
						?>
						<li><a href="fss_password.php">Change Password</a></li>
						<li><a href="fss_logout.php">Logout</a></li>
				</ul>
			</div>