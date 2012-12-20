<?php
session_start(); // Always first

// Checks if admin logged in (session set)
if (!isset($_SESSION['sess_user'])){
  header("Location: index.php?sessiontimeout");
  exit;
}

require_once "../includes/config.php"; // Database and Site settings

if (isset($_POST["submit"])){
	if (isset($_POST["site_url"])) $site_url = $_POST["site_url"];
	else $site_url = "";
	if (isset($_POST["site_name"])) $site_name = $_POST["site_name"];
	else $site_name = "";
	if (isset($_POST["meta_author"])) $meta_author = $_POST["meta_author"];
	else $meta_author = "";
	if (isset($_POST["meta_description"])) $meta_description = $_POST["meta_description"];
	else $meta_description = "";
	if (isset($_POST["meta_keywords"])) $meta_keywords = $_POST["meta_keywords"];
	else $meta_keywords = "";
	if (isset($_POST["charset"])) $charset = $_POST["charset"];
	else $charset = "utf-8";
	if (isset($_POST["timezone"])) $timezone = $_POST["timezone"];
	else $timezone = date_default_timezone_get();

	$sql = "UPDATE global SET site_url='$site_url', site_name='$site_name', meta_author='$meta_author', meta_description='$meta_description', meta_keywords='$meta_keywords', charset='$charset', timezone='$timezone' WHERE id=1";
	@mysql_query($sql, $conn) or die(mysql_error());
	// This line must be before all references to date() to silence strict php notices.
	//date_default_timezone_set("$timezone");

	$saved = true;
}

$sql = "SELECT T1.id, T1.name, T3.*, (SELECT COUNT(*) FROM permissions AS T2 WHERE T2.sid = 0 AND T2.permissions = T1.id AND T2.uid=".$_SESSION["sess_id"].") AS headadmin
		FROM permissions_extra AS T1, global AS T3 WHERE T1.id = 1 AND T3.id = 1";
$query = mysql_query($sql, $conn);
$fetch = mysql_fetch_array($query);
if ($fetch["timezone"]!="") date_default_timezone_set($fetch["timezone"]);
unset($sql);

/*if (isset($fetch["site_url"])) $site_url = $fetch["site_url"];
else $site_url = "";
if (isset($fetch["site_name"])) $site_name = $fetch["site_name"];
else $site_name = "";*/
// Take url and sitename from config.php for now
$site_url = $url;
$site_name = $sitename;
if (isset($fetch["meta_author"])) $meta_author = $fetch["meta_author"];
else $meta_author = "";
if (isset($fetch["meta_description"])) $meta_description = $fetch["meta_description"];
else $meta_description = "";
if (isset($fetch["meta_keywords"])) $meta_keywords = $fetch["meta_keywords"];
else $meta_keywords = "";
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "utf-8";
if ($fetch["timezone"]!="") $timezone = $fetch["timezone"];
else $timezone = date_default_timezone_get();
$headadmin = $fetch["headadmin"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
<title>Global Site Settings</title>
<link rel="stylesheet" href="theme/css/stylemaker.css" type="text/css" />
</head>
<body>

<?php
if (!$headadmin): $inputsettings="disabled=\"disabled\""; else: $inputsettings=""; endif;
?>

<form action="global.php" target="main" method="post">
<table border="0" cellpadding="0" cellspacing="0">
	<tr><th colspan="2">Global Site Settings</th></tr>
	<tr><td class="relatedLinks">Site URL:* </td><td class="relatedLinks"><input disabled="disabled" type="text" name="site_url" size="80" maxlength="80" value="<?php echo $site_url; ?>" /></td></tr>
	<tr><td class="relatedLinks">Site Name:* </td><td class="relatedLinks"><input disabled="disabled" type="text" name="site_name" size="80" maxlength="100" value="<?php echo $site_name; ?>" /></td></tr>
	<tr><td class="relatedLinks">Author: </td><td class="relatedLinks"><input <?php echo $inputsettings;?> type="text" name="meta_author" size="80" maxlength="100" value="<?php echo $meta_author; ?>" /></td></tr>
	<tr><td class="relatedLinks">Description: </td><td class="relatedLinks"><input <?php echo $inputsettings;?> type="text" name="meta_description" size="80" maxlength="500" value="<?php echo $meta_description; ?>" /></td></tr>
	<tr><td class="relatedLinks">Keywords: </td><td class="relatedLinks"><input <?php echo $inputsettings;?> type="text" name="meta_keywords" size="80" maxlength="500" value="<?php echo $meta_keywords; ?>" /></td></tr>
	<tr><td class="relatedLinks">Charset: </td><td class="relatedLinks"><input <?php echo $inputsettings;?> type="text" name="charset" size="80" maxlength="20" value="<?php echo $charset; ?>" /></td></tr>
	<tr><td class="relatedLinks">Timezone: </td><td class="relatedLinks">
		<select <?php echo $inputsettings;?> name="timezone">
		<?php
		$timezone_identifiers = DateTimeZone::listIdentifiers();
		for($i=0;$i<count($timezone_identifiers);$i++) {?>
			<option value="<?php echo $timezone_identifiers[$i];?>" <?php if ($timezone_identifiers[$i]==$timezone):?>selected="selected"<?php endif;?>><?php echo $timezone_identifiers[$i]; ?></option>
		<?php }?>
		</select>
	</td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<?php if($headadmin):?>
	<tr align="right"><td colspan="2"><input type="submit" name="submit" value="Save" /></td></tr>
	<tr><td colspan="2" align="right"><?php if(isset($saved)) :?><br /><div class="infobox saved">Saved <?php echo date("Y-m-d H:i:s");?></div><?php endif;?></td></tr>
	<?php endif;?>
</table>
</form>

<br /><br />
<div class="infobox">
	<?php if($headadmin):?>* Change this setting in 'includes/config.php' instead.<br />
	<?php else:?>Only Headadmins can change global site settings.<br />If you see something wrong here contact a Headadmin.<br /><?php endif;?>
</div>

</body>
</html>