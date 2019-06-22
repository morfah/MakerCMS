<?php
session_start(); // Always first

// Checks if admin logged in (session set)
if (!isset($_SESSION['sess_user'])){
	header("Location: index.php?sessiontimeout");
	exit;
}

require_once "../includes/config.php"; // Database and Site settings

// Headadmin, timezone and charset
$sql = "SELECT T1.id, T1.name, T3.charset, T3.timezone, (SELECT COUNT(*) FROM permissions AS T2 WHERE T2.sid = 0 AND T2.permissions = T1.id AND T2.uid=".$_SESSION["sess_id"].") AS headadmin
		FROM permissions_extra AS T1, global AS T3 WHERE T1.id = 1 AND T3.id = 1";
$query = mysqli_query($conn, $sql);
$fetch = mysqli_fetch_array($query);
$headadmin = $fetch["headadmin"];
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "utf-8";
if ($fetch["timezone"]!="") date_default_timezone_set($fetch["timezone"]);

if (isset($_GET["was_not_unique"])) $AdminNameWasNotUnique = $_GET["was_not_unique"];
else $AdminNameWasNotUnique = "";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
<title>User Admin</title>
<link rel="stylesheet" href="theme/css/stylemaker.css" type="text/css" />
</head>

<body>

<table border="0" cellpadding="0" cellspacing="0">
<tr><th colspan="5">Admin list</th></tr>
<tr>
	<td colspan="4" class="sectionLinks">Admins</td>
	<td class="sectionLinks">Last login <span style="font-weight:normal;">(Timezone: <?php echo date_default_timezone_get();?>)</span></td>
</tr>

<?php
$sql = "SELECT T3.id, T3.username, T3.last_login, (SELECT COUNT(*) FROM permissions AS T2 WHERE T2.sid = 0 AND T2.permissions = T1.id AND T2.uid=T3.id) AS headadmin FROM permissions_extra AS T1, maker AS T3 WHERE T1.id = 1 ORDER BY headadmin DESC";
$query=mysqli_query($conn, $sql);
$rows = mysqli_num_rows($query);

for ($i=0;$i<$rows;$i++){
	$fetch=mysqli_fetch_array($query);
?>
<tr>
	<td class="relatedLinks">
<?php
	// Headadmins can click on admins to change their permissions.
	// $AdminNameWasNotUnique is set when a new admin was created but their name was not unique. Their name turns red just to show that.
	if ($headadmin){
?>
		<a href="admin_rights.php?id=<?php echo $fetch["id"]?>" target="main"><span <?php if ($AdminNameWasNotUnique==$fetch["username"]):?>class="NameWasNotUnique"<?php endif;?>><?php echo $fetch["username"]?></span></a>
<?php
	}
	else{
?>
		<?php echo $fetch["username"]?>
<?php
	}
?>
	</td>
	<td class="relatedLinks">(id: <?php echo $fetch["id"]?>)</td>
	<td class="relatedLinks"><?php if ($fetch["headadmin"]):?>(Headadmin)<?php else:?>&nbsp;<?php endif;?></td>
	<td class="relatedLinks">
<?php
		// Headadmins can delete other (head)admins.
		if ($headadmin AND $_SESSION["sess_id"] != $fetch["id"]){
?>
			<form method="post" action="admin_rights.php?id=<?php echo $fetch["id"]?>" onsubmit="javascript:return confirm('Do you really want to remove this Admin forever?');">
				<input type="submit" name="delete" value="Remove Admin" />
			</form>
<?php 
		}
		else{
?>
			&nbsp;
<?php 
		}
?>
	</td>
	<td class="relatedLinks"><?php echo $fetch["last_login"]?></td>
</tr>
<?php
} //end for loop
if ($headadmin){
?>
	<tr><td colspan="5">&nbsp;</td></tr>
	<tr><td colspan="5"><form method="post" action="admin_new.php"><input type="submit" value="Create Admin" /></form></td></tr>
<?php 
}
?>
</table>
</body>
</html>
