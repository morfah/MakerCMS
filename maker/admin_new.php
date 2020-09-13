<?php
session_start(); // Always first

// Checks if admin logged in (session set)
if (! isset($_SESSION ['sess_user'])) {
	header("Location: index.php?sessiontimeout");
	exit();
}

require_once "../includes/config.php"; // Database and Site settings
require_once "classes/autoload.php"; // This autoload classes

//Headadmin and charset
$sql = "SELECT T1.id, T1.name, T3.charset, (SELECT COUNT(*) FROM permissions AS T2 WHERE T2.sid = 0 AND T2.permissions = T1.id AND T2.uid=".$_SESSION["sess_id"].") AS headadmin
		FROM permissions_extra AS T1, global AS T3 WHERE T1.id = 1 AND T3.id = 1";
$query = mysqli_query($conn, $sql);
$fetch = mysqli_fetch_array($query);
$headadmin = $fetch["headadmin"];
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "utf-8";

if (isset($_POST ["submit"]) && $headadmin) {
	$admin = new admin();
	if (isset($_POST ["username"])) $username = $_POST ["username"];
	else $username = '';
	if (isset($_POST ["password"])) $password = $_POST ["password"];
	else $password = '';

	$redirect = "admin.php";

	//make sure admin name is unique
	while (! $admin->UniqueAdminName($username)) {
		$username .= "_";
		$redirect = "admin.php?was_not_unique=$username";
	}
	//create admin
	$admin->CreateAdmin($username, $password);
	//error?
	if (! isset($admin->ErrorMsg)) header("Location: $redirect"); // redirect
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type"
	content="text/html; charset=<?php echo $charset ?>" />
<title>New admin</title>
<link rel="stylesheet" href="theme/css/stylemaker.css" type="text/css" />
</head>

<body>
<?php
if (!$headadmin){
?>
	<div class="infobox error">You dont have permission to be here!</div>
<?php
}
else{
?>
	<form action="admin_new.php" method="post">
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<th colspan="2">Create an Admin</th>
		</tr>
		<tr>
			<td colspan="2" class="sectionLinks">You can change admin-rights later
			(no rights by default)</td>
		</tr>
		<tr>
			<td class="relatedLinks">Username:</td>
			<td class="relatedLinks"><input type="text" name="username"
				style="width: 100%;" /></td>
		</tr>
		<tr>
			<td class="relatedLinks">Password:</td>
			<td class="relatedLinks"><input type="password" name="password"
				style="width: 100%;" /></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;<?php if(isset($admin->ErrorMsg)) echo $admin->ErrorMsg;?></td>
		</tr>
		<tr>
			<td><a href="admin.php" target="main">Go back to Admin list</a></td>
			<td align="right"><input type="submit" name="submit" value="Do it" /></td>
		</tr>
	</table>
	</form>
<?php
}
?>
</body>
</html>