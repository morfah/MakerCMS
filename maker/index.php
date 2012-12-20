<?php
if (!file_exists("../includes/config.php")){
	require_once "../includes/error.php";
	errorpage(1001, $_SERVER["SCRIPT_FILENAME"], $_SERVER['REQUEST_URI']."includes/config.php");
	exit;
}

session_start(); // This should always be first (high up) on the page

require_once "../includes/config.php"; // Database connection
require_once "../includes/validate.php"; // Validation Functions
// timezone and charset
$sql = "SELECT timezone,charset FROM `global` WHERE global.id=1";
$query = mysql_query($sql, $conn);
$fetch = mysql_fetch_array($query);
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "utf-8";
if ($fetch["timezone"]!="") date_default_timezone_set($fetch["timezone"]);

// Logging in
if (isset($_POST['submit'])){
  if (valid($_POST['username'], 1, 20)){ 
	  $sql = "SELECT * FROM maker WHERE username='" . $_POST['username'] . "' AND password='" . md5($_POST['password']) . "'";
	  $result = mysql_query($sql);
	 
	  // If username and/or password wasn't found
	  // send an error to the form
	  if (mysql_num_rows($result) == 0){
		header("Location: index.php?badlogin=1");
		exit;
	  }
	 
	  // Set session with unique index
	  $_SESSION['sess_id'] = mysql_result($result, 0, 'id');
	  $_SESSION['sess_user'] = $_POST['username'];
	  $_SESSION['sess_pass'] = md5($_POST['password']);
	  // Updating "last_login" 
	  $sql2 = "UPDATE maker SET last_login = '" . date('Y-m-d H:i:s') . "' WHERE id=" . $_SESSION['sess_id'];
	  @mysql_query($sql2) or die("error...");
	  header("Location: frames.php"); // Redirecting to frames.php
	  exit;
  } 
  else
  	header("Location: index.php?badlogin=");
}
 
// Logging out
if (isset($_GET['logout'])){
  session_unset();
  session_destroy();
  header("Location: index.php");
  exit;
}

if (isset($_SESSION['sess_user'])){
	  header("Location: frames.php");
	  exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
	<meta name="Author" content="Kristian 'morfar' Johansson" />
	<title>Maker</title>
	<link rel="stylesheet" href="theme/css/stylemaker.css" type="text/css" />
	<link rel="icon" href="theme/img/icon.png" type="image/png" />
<style type="text/css">
<!--
body {
	background-color: #FFFFFF;
	margin: 100px;
	text-align:center;
}
.loginheader{border:1px solid black;color:#000;background-color:#fff;}
.errorbox{border:#CC3333 5px solid;width:800px;margin: 40px auto;}
.header{color:#FFFFFF; background-color:#CC3333; font-size:16px; font-weight:bold; padding-left:5px; padding-bottom:5px;}
.explaination{color:inherit;background-color:#FFF;padding:5px; border-bottom:#000000 1px dashed;}
.solution{color:inherit;background-color:#FFF;padding:5px;}
-->
</style>
<script src="javascript/cookies.js" language="javascript" type="text/javascript"></script>
<script type="text/javascript">
function javascript_cookies_check() {
	document.getElementById("submit").disabled = false;
	
	// Cookie check
	createCookie('cookie_check','enabled',1);
	if (readCookie('cookie_check')==null) {
		document.getElementById("cookie_error").style.display = "block";
		document.getElementById("submit").disabled = true;
	}
	eraseCookie('cookie_check');
}
</script>
</head>
<body onload="javascript_cookies_check()">

<?php
 
// If not logged in show the form, if logged in show 'logout' link
if (!isset($_SESSION['sess_user'])){
	// Show error message if login was incorrect
	if (isset($_GET['badlogin'])){?>
		<div class="infobox centerbox error">Wrong username or password!<br />Try again!</div>
	<?php }
	else if (isset($_GET['sessiontimeout'])){?>
		<div class="infobox centerbox warning">The login session is over. Please login here again.<br />
		Your last action was NOT saved.</div>
	<?php }
?> 
<p>&nbsp;</p>
<form action="index.php" method="post" target="_top" id="loginform">
<table align="center" border="0" cellspacing="0" cellpadding="0">
<tr><th colspan="2"><?php echo $sitename?> - Administration</th></tr>
<tr><td class="relatedLinks">Username:</td><td class="relatedLinks"><input name="username" type="text" id="username" tabindex="1" /></td></tr>
<tr><td class="relatedLinks">Password:</td><td class="relatedLinks"><input name="password" type="password" id="password" tabindex="2" /></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2" align="right"><input name="submit" type="submit" id="submit" tabindex="3" value="Login" disabled="disabled" /></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><a href="<?php echo  $url?>" class="login"><?php echo $sitename?> site</a></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
</table>
</form>
<noscript>
<div class="errorbox" id="javascript_error" style="display:block;">
<div class="header">Javascript</div>
<div class="explaination">Javascript needs to be enabled for this site.</div>
<div class="solution">
	Troubleshooting:
	<ul>
		<li>Check your browser configuration or browser extensions. Make sure you enable Javascript support for at least this site.</li>
	</ul>
</div>
</div>
</noscript>
<div class="errorbox" id="cookie_error" style="display:none;">
<div class="header">Cookies</div>
<div class="explaination">Cookies needs to be enabled for this site.</div>
<div class="solution">
	Troubleshooting:
	<ul>
		<li>Check your browser configuration or browser extensions. Make sure you enable Cookies for at least this site.</li>
	</ul>
</div>
</div>
<?php
}
?>
</body>
</html>