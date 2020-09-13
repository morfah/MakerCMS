<?php
session_start(); // Always first

// Checks if admin logged in (session set)
if (!isset($_SESSION['sess_user'])){
  header("Location: index.php?sessiontimeout");
  exit;
}

require_once "../includes/config.php"; // Database and Site settings

// Headadmin and charset
$sql = "SELECT T1.id, T1.name, T3.charset, (SELECT COUNT(*) FROM permissions AS T2 WHERE T2.sid = 0 AND T2.permissions = T1.id AND T2.uid=".$_SESSION["sess_id"].") AS headadmin
		FROM permissions_extra AS T1, global AS T3 WHERE T1.id = 1 AND T3.id = 1";
$query = mysqli_query($conn, $sql);
$fetch = mysqli_fetch_array($query);
$headadmin = $fetch["headadmin"];
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "utf-8";
if ($headadmin) $colspan = 9;
else $colspan = 8;
$version = file_get_contents(".version");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
<title>Menu</title>
<link rel="stylesheet" href="theme/css/stylemaker.css" type="text/css" />
</head>
<body>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr><th colspan="<?php echo $colspan?>"><?php echo $sitename?> - Maker v<?php echo $version?></th><td class="sectionLinks" align="center" width="7%"><a href="index.php?logout=" target="_top">Logout</a></td></tr>
	<tr>
		<td class="relatedLinks"><a href="global.php" target="main">Site Settings</a></td>
<?php if ($headadmin){ ?>
		<td class="relatedLinks"><a href="sectionsettings.php" target="main">Section Settings</a></td>
<?php }?>
		<td class="relatedLinks"><a href="admin.php" target="main">Admins</a></td>
		<td class="relatedLinks"><a href="news.php" target="main">News</a></td>
		<td class="relatedLinks"><a href="quote.php" target="main">Quotes</a></td>
		<td class="relatedLinks"><a href="notepad.php" target="main">Your notepad</a></td>
		<td class="relatedLinks"><a href="password.php" target="main">Change your password</a></td>
		<td class="relatedLinks"><a href="trashcan.php" target="main">Trashcan</a></td>
		<td class="relatedLinks"><a href="stats.php" target="main">Statistics</a></td>
<?php if ($headadmin){ ?>
		<td class="relatedLinks"><a href="phpinfo.php" target="main">PHP Info</a></td>
<?php }?>
	</tr>
</table>
</body>
</html>
