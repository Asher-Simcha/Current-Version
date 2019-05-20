<?php
/*
* Title: Check Version with server if 1st of the month
* Author: Asher Simcha
* Filename: check_version.php
* Description: Check to make sure the user has the most up to date version of the software. Using a cookie, so repetitive checking does happen, this will only slow the process once 3 times a month
* Version: 0.0.1
* Date: 05-17-2019
* Last Modified: 02-19-2019
*/
// start your php script here

// web-server location https://asher-simcha.github.io/help/index.html
//

// if the date is 1 10 20 check to make sure the software is up to date.
// add cookie. 
$outofdate=NULL;
/*
EXTRA NOTES:
=	// this is equals (if it existed before erase the value and this is the new value)
.=  // this is append the variable

example:
* $a = "Hello";
* output of $a would be Hello
* $a .= " World"
* output of $a would be Hello World
* $a = "Test"
* output of $a would be Test
** TAKE_NOTE: if you want to append you first have to declare the variable as NULL at least.
** 		I like to use NULL so I can if NOT null then display for example
**		$outofdate=NULL;
**		if ($outofdate != NULL) { echo "Hello World"; }
**		output Nothing because $a was NULL
**		$outofdate="test";
**		if ($outofdate != NULL) { echo "Hello World"; }
**		output Hello World ... because $a was NOT null.

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
///$serverread = file_get_contents("$SERVER");

$resultsDealing=NULL;
$COOKIENAME="Version";

$COOKIEEXPIRES= time() + (31 * 3600);
$thisDate = date('md');
//echo "thisDate $thisDate<br>";


// dates to check if software is up to date.
$Date1 = 1;	// check on the first of the month
$Date2 = 10; // check on the 10th of the month
$Date3 = 20; // check on the 20th of the month

$xdays=9; // if cookie is older than x days check to see if the version is uptodate

// if out of date COOKIEMESSAGE is
$outOfDateMessage  = "Time to update this program!! It is out of date!!!<br>";
$outOfDateMessage .= "Please go to: <a target='_new' href='$MAINSITE'>Asher Simcha's Help Program</a><br>";
$outOfDateMessage .= "And Update your Software<br>";


// internal variables
$COOKIEMESSAGEEXPIRED=0;
$testDone=0;
$results=0;
$outofdate=NULL;
function checkVersion($filenameCheckVersion, $SERVERCHECKVERSION) {
	$serverread = NULL;
	$clientread = NULL;
	// read cookie 
	// if cookie date is more than 31 days old check version.
	//echo "In function CheckVersion<br>";
	//echo "Yes it's time to check if your program is uptodate or not<br>";
	//download the file from the server
	$serverread = file_get_contents("$SERVERCHECKVERSION"); // download the file from the internet into a variable.
	$clientread = file_get_contents("$filenameCheckVersion"); // copy the local file into a variable.
	//echo "serverread $serverread<br>";
	//echo "clientread $clientread<br>";
	
	if ($serverread == NULL) {
		// Server Location NOT found
		// exiting error code 16 ....look at the top for further explanation
		return 16; 
	}
	if ($clientread == NULL) {
		// Client Configuration file is missing.
		// exiting error code 17 ....look at the top for further explanation
		return 17;
	}
	// now compare the 2 variables to see if they match
	// strpos ($line, $pattern), so the SERVER is the file to open and Client is the pattern
	if (strpos($serverread, $clientread) !== false) {
		//echo "found a match";
		// exiting error code 0 ...look at the top for further explanation
		return 0;
	} else {
		//echo "no match found Time to update";
		// when comparing the 2 files they did not match
		// it's time to update your program
		// exiting error code 1 ....look at the top for further explanation
		return 1;
	}
}

function DealingWithResults($results, $thisDate, $COOKIENAME, $COOKIEEXPIRES, $COOKIEMESSAGEEXPIRED, $outOfDateMessage) {
	if ($results >= 1) {
		// Your Program is out of date.:(
		$COOKIEMESSAGE = $COOKIEMESSAGEEXPIRED;
		setcookie("$COOKIENAME","$COOKIEMESSAGE", $COOKIEEXPIRES);
		return $outOfDateMessage;
	} else {
		// Your program is Up to date.:)
		$COOKIEMESSAGE=$thisDate;
		setcookie("$COOKIENAME","$COOKIEMESSAGE", $COOKIEEXPIRES);
		return NULL;
	}	
}	



//echo "<b>START</b> If cookie is or is not set START line:" . __LINE__ . " file: " . basename(__FILE__) . "<br>";
// if the cookie does not exist checkVersion write cookie
if (!isset($_COOKIE["$COOKIENAME"])) {
	// cookie does NOT exist
	//echo " line:" . __LINE__ . " file: " . basename(__FILE__) . "<br>";
	
	$results=checkVersion($filenameCheckVersion, $SERVERCHECKVERSION);
	$resultsDealing=DealingWithResults($results, $thisDate, $COOKIENAME, $COOKIEEXPIRES, $COOKIEMESSAGEEXPIRED, $outOfDateMessage);
	if ($resultsDealing != NULL) {
		// Your program is out of date notify the user
		$outofdate .= $resultsDealing;
	}
} else {
	// cookie exist
	
	// read the cookie
	$cookieResults=$_COOKIE["$COOKIENAME"];
	// subtract todays date from the cookie date
	$finalResult=$thisDate-$cookieResults;
	// if finalResult is greater or equal to $xdays days old, check the version again. 
	if ($finalResult >= $xdays ) {
		$results=checkVersion($filenameCheckVersion, $SERVERCHECKVERSION);
		if ($results >= 1) {
			// message to the user that the program is out of date
			$outofdate .= $outOfDateMessage;
			// set cookie to what ever value $COOKIEMESSAGEEXPIRED is.
			$COOKIEMESSAGE = $COOKIEMESSAGEEXPIRED;
			setcookie("$COOKIENAME","$COOKIEMESSAGE", $COOKIEEXPIRES);
		} else {
			// Your program is Up to date!!!!
			// set cookie to todays date.
			$COOKIEMESSAGE="$thisDate";
			setcookie("$COOKIENAME","$COOKIEMESSAGE", $COOKIEEXPIRES);
		} 
	} else {
		// this will speed it up if it has already check the file for the day it will not do it again
		// because the cookie is good enough
		$testDone=1;
	}
}
//echo "<b>END</b> of if cookie is or is not set END line:" . __LINE__ . " file: " . basename(__FILE__) . "<br>";

// $testDone if the date is 1, 10, or 20 then 
if ($testDone == 0) {
	if ($thisDate == $Date1 || $thisDate == $Date2 || $thisDate == $Date3) {
		$results=checkVersion($filenameCheckVersion, $SERVERCHECKVERSION);
		if ($results >= 1) {
			$outofdate .= $outOfDateMessage;
			$COOKIEMESSAGE = $COOKIEMESSAGEEXPIRED;
			setcookie("$COOKIENAME","$COOKIEMESSAGE", $COOKIEEXPIRES);
		} else {
			$COOKIEMESSAGE="$thisDate";
			setcookie("$COOKIENAME","$COOKIEMESSAGE", $COOKIEEXPIRES);
		} 
	}
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
<center><h1>Check Version</h1></center>
<center><?php if ($outofdate != NULL) { echo "<b>$outofdate</b><br>"; } ?></center>
<?php if ($outofdate == NULL) { echo "<b>Your System is Up to date! :)</b>"; } ?>

<p>This is an example of how you create a version control for php, if you distribute php code this is a great idea!</p>
</body>
</html>
