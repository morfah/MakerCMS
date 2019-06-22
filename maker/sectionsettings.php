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

// How many non-trashcanned sections do we have?
$sql = "SELECT * FROM sections WHERE deleted = 0";
$rows = mysqli_num_rows(mysqli_query($conn, $sql));

// Saving.
if ($headadmin && isset($_POST["submit"])){
	$date = date('Y-m-d H:i:s');
	$updatedby = $_SESSION["sess_id"];
	//echo "Saving section settings. Section by section.<br />\n";
	for ($i=1;$i<=$rows;$i++) {
		$sql = "SELECT * FROM sections WHERE deleted = 0 LIMIT ".($i-1).",1";
		$fetch = mysqli_fetch_array(mysqli_query($conn, $sql));
		$sid = $fetch["sid"];
		if (isset($_POST["order"."$sid"])) $order = $_POST["order"."$sid"];
		else $order = 0;
		if (isset($_POST["vis"."$sid"])) $vis = $_POST["vis"."$sid"];
		else $vis = 0;
		if (isset($_POST["dis"."$sid"])) $dis = $_POST["dis"."$sid"];
		else $dis = 0;
		$sql2 = "UPDATE sections SET `order`=$order, visible=$vis, disabled=$dis, updatedby='$updatedby', updatedby_date='$date' WHERE sid=$sid";
		//echo "$sql2<br />\n";
		@mysqli_query($conn, $sql2) or die("<b>A fatal MySQL error occurred</b>.\n<br />\nError: (" . mysqli_connect_errno() . ") " . mysqli_connect_error());
	}
	$what = "ok";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
<title>Section editor</title>
<link rel="stylesheet" href="theme/css/stylemaker.css" type="text/css" />
<script type="text/javascript">
function refresh_ce(){
	setTimeout('parent.content_editor.location = "content_editor.php"',100);
}
</script>
</head>
<body>
<?php if (isset($what)){?>
<script type="text/javascript">refresh_ce();</script>
<?php
}
if (!$headadmin){
?>
<div class="infobox error">You do not have permission to be here!</div>
<?php }else{?>
<form action="sectionsettings.php" method="post">
<table border="0" cellpadding="0" cellspacing="0" style="text-align:center;">
	<tr><th colspan="4">Section Settings</th></tr>
	<tr>
	<td class="sectionLinks">Name</td>
	<td class="sectionLinks">Order</td>
	<td class="sectionLinks">Visible</td>
	<td class="sectionLinks">Disabled</td>
	</tr>
<?php
for ($i=1;$i<=$rows;$i++) {

$sql = "SELECT * FROM sections WHERE deleted = 0 ORDER BY `order` LIMIT ".($i-1).",1";
$query = mysqli_query($conn, $sql);
$datas=mysqli_fetch_array($query);

?>
	<tr>
	<td class="relatedLinks"><a href="editors.php?sid=<?php echo $datas["sid"]?>" title="More options"><?php echo $datas["header"] ?></a></td>
	<td class="relatedLinks">
	<select name="order<?php echo $datas["sid"]?>">
<?php for ($u=1; $u<=$rows; $u++){?>
		<option value="<?php if ($datas["order"]==0): echo "0"; else: echo $u; endif;?>" <?php if ($u==$datas["order"]){ echo "selected=\"selected\""; } if ($datas["order"]==0){ echo " disabled=\"disabled\"";}?>><?php if ($datas["order"]==0): echo "0"; else: echo $u; endif;?></option>
<?php } ?>
	</select>
	</td>
	<td class="relatedLinks"><input name="vis<?php echo $datas["sid"]?>" type="checkbox" value="1" <?php if ($datas["visible"]==1){ echo "checked=\"checked\""; } ?>/></td>
	<td class="relatedLinks"><input name="dis<?php echo $datas["sid"]?>" type="checkbox" value="1" <?php if ($datas["disabled"]==1){ echo "checked=\"checked\""; } ?>/></td>
	</tr>
<?php
}
?>
	<tr><td colspan="4">&nbsp;</td></tr>
	<tr><td colspan="4" align="right"><input name="submit" type="submit" value="Save" /></td></tr>
	<tr><td colspan="4" align="right"><br />
<?php
	if (isset($what)){
		if ($what == "ok"){
?>
			<div class="infobox saved">Saved <?php echo date("Y-m-d H:i:s");?></div>
<?php
		}
	}
?>
	</td></tr>
</table>
</form>

<br /><br />
<div class="infobox">
  Order: Order of appearance shown in menus<br /><br />
  Visible: Will it be shown in menus?<br /><br />
  Disabled: Can't be accessed on the site and hidden from sitemap and search.
</div>

<?php }?>
</body>
</html>
