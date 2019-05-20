<?php
/*
* Title: Check Version with server if 1st of the month
* Author: Asher Simcha
* Description: Check to make sure the user has the most up to date version of the software. checks on the 1st, 10th, and 20th of the month
* Version: 0.0.1
* Date: 05-17-2019
* Last Modified: 02-19-2019

# // filename: checkVersion.php
# // Copyright (C) 2019 aka Asher Simcha 
# // This library is free software; you can redistribute it and/or modify it under the
# // terms of the The 3-Clause BSD License as published by the
# // Open Source Initiative; version 3
# 
# // This library is distributed in the hope that it will be useful,
# // but WITHOUT ANY WARRANTY; without even the implied warranty of
# // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# // The 3-Clause BSD License for more details.
# 
# // You should have received a copy of The 3-Clause BSD License
# // see the files The 3-Clause BSD License.txt respectively.  If not, see
# // <https://opensource.org/licenses/BSD-3-Clause/>.
*/
$message=NULL;
/*
# exiting codes
# 0 both versions match... You have the most uptodate version
# 1 the versions do NOT match... You need to upgrade your system
# 15 url does NOT exits
# 16 the SERVER's version control file is missing, most likely write permission issues
# 17 the CLIENT's version control file is missing
*/
$filenameCheckVersion="Version.inf";
$SERVERCHECKVERSION="https://asher-simcha.github.io/help/index.html";
$MAINSITE="https://github.com/Asher-Simcha/help";
$results=0;
function checkVersion($filenameCheckVersion, $SERVERCHECKVERSION) {
	$serverread = NULL;
	$clientread = NULL;
	//echo "In function CheckVersion<br>";

	//echo "Yes it's time to check if your program is uptodate or not<br>";
	//download the file from the server
	$serverread = file_get_contents("$SERVERCHECKVERSION"); // download the file from the internet into a variable.
	$clientread = file_get_contents("$filenameCheckVersion"); // copy the local file into a variable.
	//echo "serverread $serverread<br>";
	//echo "clientread $clientread<br>";

	if ($serverread == NULL) {
		// Server Location NOT found
		// exiting
		return 16;
	}
	if ($clientread == NULL) {
		// Client Configuration file is missing.
		// exiting
		return 17;
	}
	// now compare the 2 variables to see if they match
	// strpos ($line, $pattern), so the SERVER is the file to open and Client is the pattern
	if (strpos($serverread, $clientread) !== false) {
		//echo "found a match";
		return 0;
	} else {
		//echo "no match found Time to update";
		// when comparing the 2 files they did not match
		// it's time to update your program
		return 1;
	}
	
}
$thisDate = date('d');
//echo "thisDate $thisDate<br>";
if ($thisDate == 1 || $thisDate == 10 || $thisDate == 20) {
	// if the date is the 1st 10th or 20th it will check the date
	$results=checkVersion($filenameCheckVersion, $SERVERCHECKVERSION);
}
// now the return status if greater than 1 your program is out of date, or something is
// wrong and you need to update the program
if ($results >= 1) {
	$message .= "Time to update Your software is out of date!!<br>";
	$message .= "Please go to: $MAINSITE<br>";
	$message .= "And Update your software";
} 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding"><meta charset="utf-8">
<title>Check Verison</title>
<script type="text/javascript"></script>
<style>
body { background-color: Gainsboro; }
</style>
</head>
<body id="body">

<?php if ($message == NULL) { echo $message; } ?>
<?php echo "the results are: $results<br>"; ?>

</body>
</html>
