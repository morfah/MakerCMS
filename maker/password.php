<?php
session_start(); // Always first

// Checks if admin logged in (session set)
if (!isset($_SESSION['sess_user'])){
  header("Location: index.php?sessiontimeout");
  exit;
}

require_once "../includes/config.php"; // Database and Site settings
// charset
$sql = "SELECT global.charset FROM global WHERE global.id=1";
$query = mysqli_query($conn, $sql);
$fetch = mysqli_fetch_array($query);
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "utf-8";

if (isset($_POST["change"])){
	// Start alot of checks (to silence the "undefined notice message" in PHP)
	if (isset ($_POST["oldpassword"])) $oldpassword = md5($_POST["oldpassword"]);
	else $oldpassword = "";
	if (isset ($_POST["newpassword"])) $newpassword = md5($_POST["newpassword"]);
	else $newpassword = "";
	if (isset ($_POST["newpasswordagain"])) $newpasswordagain = md5($_POST["newpasswordagain"]);
	else $newpasswordagain = "";

	if ($oldpassword==$_SESSION["sess_pass"] && $newpassword == $newpasswordagain && strlen($_POST["newpassword"]) > 5) {
		$_SESSION["sess_pass"] = $newpasswordagain;
		$sql = "UPDATE maker SET password='" . $newpasswordagain . "' WHERE id=" . $_SESSION['sess_id'];
		@mysqli_query($conn, $sql) or die("<strong>A fatal MySQL error occurred</strong>.\n<br>\nError: (" . mysqli_connect_errno() . ") " . mysqli_connect_error());
		$what = "ok";
	}
	else
		$what = "error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
<title>Password changer</title>
<link rel="stylesheet" href="theme/css/stylemaker.css" type="text/css" />
</head>
<body>

<form action="password.php" method="post">
<table border="0" cellpadding="0" cellspacing="0">
	<tr><th colspan="2">Change your password</th></tr>
	<tr><td class="relatedLinks">Old password: </td><td class="relatedLinks"><input type="password" name="oldpassword" /></td></tr>
	<tr><td class="relatedLinks">New password: </td><td class="relatedLinks"><input type="password" name="newpassword" /></td></tr>
	<tr><td class="relatedLinks">New password again: </td><td class="relatedLinks"><input type="password" name="newpasswordagain" /></td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr align="right"><td colspan="2"><input name="change" type="submit" value="Change" /></td></tr>
	<tr><td colspan="2" align="right">
<?php
	if (isset($what)){
		if ($what == "ok"){
?>
			<div class="infobox saved">Your password has been changed!</div>
<?php
		}
		else{
?>
			<div class="infobox error">You did something wrong.<br>Make sure you typed everything correctly.<br><em>Also, new passwords are required to be at least 6 characters long.</em></div>
<?php
		}
	}
?>
	</td></tr>
</table>
</form>
</body>
</html>
