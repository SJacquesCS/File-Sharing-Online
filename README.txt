Group Info:

GROUP #7

[LEADER]
Simon Jacques [27046677] 
si_jacqu@encs.concordia.ca 
s-jacques@live.com

Pierre-Olivier Jourdenais [26987540]
p_jourde@encs.concordia.ca 
pojourdenais@gmail.com

Jonathan Cardone [27317026] 
jo_cardo@encs.concordia.ca 
jonathan4210@gmail.com

Jonathan Linton [27388489] 
jo_lint@encs.concordia.ca 
jonny.linton@hotmail.com

Clément Hennebelle [27432917] 
c_henne@encs.concordia.ca 
clement.hennebelle@hotmail.fr



Database Info:

Putty Server: login.encs.concordia.ca
Database Access: mysql -h qoc353_1.encs.concordia.ca -u qoc353_1 -p qoc353_1
Website URL: https://qoc353_1.encs.concordia.ca/
Group Account: qoc353_1
Group Password: dbsu2016



List of files for submission:

INC/CSS Files:

head.inc
  - a common head for every webpage - defines the nav bar, containers and the header

footer.inc
  - a common foot for every webpage - defines containers and footer

fss.css


PHP Scripts:

config.php
  - Configures all of the necessary requirements for communication with the SQL server

create_account.php
  - Modify Students page: Allows a professor to add or remove Students from the class

fss_download1.php
  - Manage Files page: displays all of the available unique files for the UserÕs group from the Files table with buttons to take you to
    fss_download2.php
  - For professors - the option to Archive the course is also available for selection
  - If User is a group leader, a button with the option to rollback to a previous version is also displayed

fss_download2.php
  - For group leaders, displays all File Versions of the selected file and gives the option to download or delete
  - For non-leaders, displays all file versions and gives the option to download

fss_home.php
  - Displays user, course, and group information, available files.

fss_logout.php
  - destroys the current session of the User and navigates back to the login page.

fss_modify.php
  - Modify Groups page: allows a professor to add/remove students from a group, to rename a group, or to designate a group leader

fss_password.php
  - Checks the Users current password and allows them to set a new password according to requirements. Performs validation before accepting a password.

fss_stats.php
  - Displays the file statistics for a given Student in a group: uploads, downloads, and deletions.

fss_upload.php
  - Allows a Student in a group to upload a file of a particular assignment type to the FSS.

index.php
  - automatically navigates to the login page

login.php
  - Login Page: asks for ID and password. Checks if the combination is correct and in our database and begins the session if it is successful

validate.php
  - contains the isDangerous() function which checks if a given string is potentially dangerous to our site.


Extra Files:

background.jpg



System Users:

+----------+----------+
| sid      | password |
+----------+----------+
| 13484738 | pass123  |
| 17568295 | pass123  |
| 21243115 | pass123  |
| 22234343 | pass123  |
| 22343335 | pass123  |
| 22348844 | pass123  |
| 22839488 | pass123  |
| 22849999 | pass123  |
| 22938484 | pass123  |
| 22938493 | pass123  |
| 22939453 | pass123  |
| 23455667 | pass123  |
| 23456789 | pass123  |
| 24983728 | pass123  |
| 27046677 | pass123  |
| 27184615 | pass123  |
| 27384738 | pass123  |
| 27388489 | pass123  |
| 28374888 | pass123  |
| 28394839 | pass123  |
| 28494839 | pass123  |
| 28948393 | pass123  |
| 29138243 | pass123  |
| 29283948 | pass123  |
| 29384948 | pass123  |
| 29388849 | pass123  |
| 29581755 | pass123  |
| 29933441 | pass123  |
| 33242535 | pass123  |
| 38293883 | pass123  |
| 38294838 | pass123  |
| 38298473 | pass123  |
| 39284938 | pass123  |
| 39384932 | pass123  |
| 47246592 | pass123  |
| 47294656 | pass123  |
| 51917581 | pass123  |
| 54234233 | pass123  |
| 65826486 | pass123  |
| 88373723 | pass123  |
| 88888888 | pass123  |
| 93762976 | pass123  |
| 93838432 | pass123  |
| 99999999 | pass123  |
+----------+----------+



Implemented database features after demo:

- Added a trigger to check group capacity when assigning a student to that group.

- Entries from the del table are now deleted when files become archived.

- File size is now displayed for every file version.



Additional info on running the site:

The 3 types of users who can login are: Students, TAs, and Professors.

- Students can upload and manage files in the group they are in.

- TAs can view all the files within all the groups, archive all files, and view file statistics from any student.
(in our system: ID = 99999999, PWD = pass123)

- Professors have the most control. They can add new students, remove students, modify groups, modify assignments, view all the files within all the groups, archive all files, and view file statistics from any student.
(in our system: ID = 88888888, PWD = pass123)