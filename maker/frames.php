<?php
session_start(); // Always first


// Checks if admin logged in (session set)
if (! isset($_SESSION ['sess_user'])) {
  header("Location: index.php?sessiontimeout");
  exit();
}

require_once "../includes/config.php"; // Database connection
require_once "classes/autoload.php"; // autoload classes

// charset
$sql = "SELECT global.charset,global.version FROM global WHERE global.id=1";
$query = mysql_query($sql, $conn);
$fetch = mysql_fetch_array($query);
if ($fetch ["charset"] != "") $charset = $fetch ["charset"];
else $charset = "utf-8";
$version = file_get_contents(".version");

// Testing if we need to upgrade the database.
$Upgrade = new upgrade();
$Upgrade->database($fetch ["version"], $version);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type"
	content="text/html; charset=<?php echo $charset?>" />
<title><?php echo $sitename?> - Maker v<?php echo $version?>. Logged in as <?php echo $_SESSION['sess_user']?></title>
<link rel="icon" href="theme/img/icon.png" type="image/png" />
</head>
<frameset rows="55,*">
	<frame src="menu.php" name="left" scrolling="no" noresize="noresize" />
	<frameset cols="260,*">
		<frame src="content_editor.php" name="content_editor" scrolling="yes" noresize="noresize" />
		<frame src="notepad.php" name="main" scrolling="auto" noresize="noresize" />
	</frameset>
	<noframes>
	<body>
	Your browser does not support &quot;frames&quot;, please upgrade your
	browser.
	<br />
	Or be a smart person to download and use
	<a href="http://www.mozilla.org/products/firefox/" target="_blank">Firefox</a>
	</body>
	</noframes>
</frameset>
</html>
