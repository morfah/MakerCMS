<?php
session_start(); // Always first


// Checks if admin logged in (session set)
if (! isset($_SESSION ['sess_user'])) {
	header("Location: index.php?sessiontimeout");
	exit();
}

require_once "../includes/config.php"; // Database and Site settings
require_once "classes/autoload.php"; // This autoloads classes

// Headadmin, charset and the username for the admin we are editing
$sql = "SELECT T1.id, T1.name, T3.charset, T4.username, (SELECT COUNT(*) FROM permissions AS T2 WHERE T2.sid = 0 AND T2.permissions = T1.id AND T2.uid=".$_SESSION["sess_id"].") AS headadmin
		FROM permissions_extra AS T1, global AS T3, maker as T4 WHERE T1.id = 1 AND T3.id = 1 AND T4.id =" . $_GET ["id"];
$query = mysql_query($sql, $conn);
$fetch = mysql_fetch_array($query);
$headadmin = $fetch["headadmin"];
$username = $fetch ["username"];
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "utf-8";

if (isset($_POST ["submit"]) && $headadmin) {
  $admin = new admin();

  //Changed name? Ensure name is unique
  if ($_POST ["username"] != $_POST ["username_before"]) {
    while (! $admin->UniqueAdminName($_POST ["username"])) {
      $_POST ["username"] .= "_";
      $AdminNameWasNotUnique = true;
    }
    $admin->RenameAdmin($_GET ["id"], $_POST ["username"]);
    $username = $_POST ["username"];
  }

  // Save Permissions
  // First we clear everything for this user
  if ($_GET["id"] != $_SESSION["sess_id"])
    $admin->ClearPermissions($_GET["id"]);
  // extra permissions
  for ($i=0;$i<=$_POST["num_extra_permissions"];$i++) {
    if(isset($_POST["pid,$i"]) && $_GET["id"] != $_SESSION["sess_id"])
      $admin->AddPermissions($_GET["id"], 0, $_POST["pid,$i"]);
  }
  // section permissions
  for ($i=0;$i<=$_POST["num_sections"];$i++) {
    if(isset($_POST["sid,$i"]) && $_GET["id"] != $_SESSION["sess_id"])
      $admin->AddPermissions($_GET["id"], $_POST["sid,$i"], 1);
  }

  $saved = true;

} else if (isset($_POST ["delete"]) && $headadmin) {
  $admin = new admin();
  $admin->DeleteAdmin($_GET ["id"]);
  header("Location: admin.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type"
	content="text/html; charset=<?php echo $charset ?>" />
<title>Admin rights</title>
<link rel="stylesheet" href="theme/css/stylemaker.css" type="text/css" />
</head>

<body>
<?php
if (!$headadmin){
?>
	<div class="infobox error">You do not have permission to be here!</div>
<?php
}
else{
?>
	<form method="post" action="admin_rights.php?id=<?php echo $_GET["id"]?>">
	<table border="0" cellpadding="0" cellspacing="0">
		<tr><th colspan="4">Admin Options</th></tr>
		<tr>
			<td colspan="4" class="sectionLinks">
				<input type="text" name="username" value="<?php echo $username?>" style="width: 94%;" <?php if (isset($AdminNameWasNotUnique)):?> class="NameWasNotUnique" <?php endif;?> />
				<input type="hidden" name="username_before" value="<?php echo $username?>" />
			</td>
		</tr>
		<tr><td colspan="4">&nbsp;</td></tr>
<?php
		// EXTRA PERMISSIONS
		$sql = "SELECT T1.id, T1.name, (SELECT COUNT(*) FROM permissions AS T2 WHERE T2.sid = 0 AND T2.permissions = T1.id AND T2.uid=".$_GET["id"].") AS granted
				FROM permissions_extra AS T1";
		$query = mysql_query($sql, $conn);
		$rows = mysql_num_rows($query);

		for ($i=0;$i<$rows;$i++){
		$fetch=mysql_fetch_array($query);
?>
			<tr>
				<td colspan="2" class="relatedLinks"><strong><?php echo $fetch["name"]; ?></strong></td>
				<td colspan="2" class="relatedLinks">
					<input type="checkbox" name="pid,<?php echo $i;?>" value="<?php echo $fetch["id"];?>"
					<?php if ($fetch["granted"] > 0):?>checked="checked" <?php endif; if ($_SESSION["sess_id"]==$_GET["id"]):?>disabled="disabled"<?php endif;?> />
				</td>
			</tr>
<?php 
		} //end for loop
?>
		<tr><td colspan="4"><input type="hidden" name="num_extra_permissions" value="<?php echo $rows ?>" /></td></tr>
		<tr><td colspan="4" class="sectionLinks">Section Permissions:</td></tr>
<?php
		// SECTION PERMISSIONS
		$sql = "SELECT T1.sid, T1.header, T1.deleted, (SELECT COUNT(*) FROM permissions AS T2 WHERE T2.sid = T1.sid AND T2.uid=".$_GET["id"].") AS granted
				FROM sections AS T1 ORDER BY T1.deleted ASC, T1.order ASC";
		$query = mysql_query($sql, $conn);
		$rows = mysql_num_rows($query);
		for ($i=1;$i<=$rows;$i++){
		$fetch=mysql_fetch_array($query);
?>
			<tr>
				<td class="relatedLinks"><?php echo $fetch["header"]?></td>
				<td class="relatedLinks">(sid: <?php echo $fetch["sid"]?>)</td>
				<td class="relatedLinks">
					<input name="sid,<?php echo $i;?>" type="checkbox" value="<?php echo $fetch["sid"];?>"
					<?php if ($fetch["granted"] > 0):?>checked="checked" <?php endif; if ($_SESSION["sess_id"]==$_GET["id"]):?>disabled="disabled"<?php endif;?> />
				</td>
				<td class="relatedLinks"><?php if ($fetch["deleted"]):?><span class="no"><strong>Trashcan</strong></span><?php else:?>&nbsp;<?php endif;?></td>
			</tr>
<?php
		} //end for loop
?>
		<tr><td colspan="4"><input type="hidden" name="num_sections" value="<?php echo $rows ?>" /></td></tr>
		<tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td colspan="2">
<?php
			if ($_SESSION["sess_id"] != $_GET["id"]){
?>
				<input type="submit" name="delete" value="Remove Admin" onclick="javascript:return confirm('Do you really want to remove this Admin forever?');" />
<?php 
			}
			else{
?>
				&nbsp;
<?php 
			}
?>
		</td>
		<td colspan="2" align="right" valign="top">
			<input type="submit" name="submit" value="Save" />
		</td>
		</tr>
		<tr><td colspan="4" align="right"><?php if (isset($saved)):?><br /><div class="infobox saved">Saved <?php echo date("Y-m-d H:i:s");?></div>&nbsp;<?php endif;?></td></tr>
	</table>
	</form>
<?php
}
?>
<br /><br /><p><a href="admin.php" target="main">Go back to Admin list</a></p>
</body>
</html>
