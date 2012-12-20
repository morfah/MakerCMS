<?php
session_start(); // Always first

// Checks if admin logged in (session set)
if (!isset($_SESSION['sess_user'])){
  header("Location: index.php?sessiontimeout");
  exit;
}

require_once "../includes/config.php"; // Database connection

// Headadmin, timezone and charset
$sql = "SELECT T1.id, T1.name, T3.charset, T3.timezone, (SELECT COUNT(*) FROM permissions AS T2 WHERE T2.sid = 0 AND T2.permissions = T1.id AND T2.uid=".$_SESSION["sess_id"].") AS headadmin
		FROM permissions_extra AS T1, global AS T3 WHERE T1.id = 1 AND T3.id = 1";
$query = mysql_query($sql, $conn);
$fetch = mysql_fetch_array($query);
$headadmin = $fetch["headadmin"];
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "utf-8";
if ($fetch["timezone"]!="") date_default_timezone_set($fetch["timezone"]);

// Saving.
if (isset($_POST["savesection"])){
	require_once "../includes/validate.php";
	$date = date("Y-m-d H:i:s");

	// Start alot of checks (to silence the "undefined notice message" in PHP)
	if (isset ($_GET["sid"])) $sid = $_GET["sid"];
	else $sid = "";

	if (isset ($_POST["header"])) {
		$header = htmlentities($_POST["header"],ENT_QUOTES,$charset);
		if ($_POST["header"]=="") $header = "noname $date";
	}
	else $header = "noname $date";

	if (isset ($_POST["vis"])) $vis = $_POST["vis"];
	else $vis = "0";
	if (isset ($_POST["dis"])) $dis = $_POST["dis"];
	else $dis = "0";


	//New Section
	if ($sid == "") {
		$startedby = $_SESSION["sess_id"];
		$sql = "INSERT INTO sections (visible, disabled, startedby, startedby_date, header) VALUES ($vis, $dis, $startedby, '$date', '$header')";
		$what = "Posted";
	}
	// Updating Existing Section
	else {
		$updatedby = $_SESSION["sess_id"];
		$sql = "UPDATE sections SET visible=$vis, disabled=$dis, updatedby=$updatedby, updatedby_date='$date', header='$header' WHERE sid=$sid";
		$what = "Updated";
	}

	@mysql_query($sql, $conn) or die("<b>A fatal MySQL error occurred</b>.\n<br />\nError: (" . mysql_errno() . ") " . mysql_error());

	// This assigns the correct order number and add adminrights for this newly added section.
	if ($what == "Posted"){
		$sql = "SELECT sid FROM sections WHERE deleted=0";
		$query = mysql_query($sql, $conn);
		$rows = mysql_num_rows($query);
		$sql = "SELECT * FROM sections ORDER BY sid DESC LIMIT 0,1";
		$query = mysql_query($sql, $conn);
		$db = mysql_fetch_array($query);
		$sidmax = $db["sid"];
		$sql = "UPDATE `sections` SET `order` = $rows WHERE `sid` = $sidmax";
		@mysql_query($sql, $conn) or die("<b>A fatal MySQL error occurred</b>.\n<br />\nError: (" . mysql_errno() . ") " . mysql_error());
		header("Location: editors.php?sid=$sidmax&refresh=yes"); // Open this section in Editor
	}
}
// Moving to trashcan.
if (isset ($_POST["delete"])) {
	if (isset ($_GET["sid"])) $sid = $_GET["sid"];
	else $sid = "";
	$sql = "UPDATE `sections` SET `deleted` = 1, `order` = 0 WHERE `sid` = $sid";
	@mysql_query($sql, $conn) or die("<b>A fatal MySQL error occurred</b>.\n<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	$what = "Deleted";
	//echo "Deletion SQL: <br />\n$sql<br /><br />\nRe-ordering SQLS: <br />\n";
	// Re-Order current alive pages. This fixes wholes in order.
	$sql = "SELECT * FROM `sections` WHERE `deleted` = 0";
	$rows = mysql_num_rows(mysql_query($sql, $conn));
	for ($i=1;$i<=$rows;$i++){
		$sql_sid = "SELECT `sid` FROM `sections` WHERE `deleted` = 0 ORDER BY `order` ASC LIMIT ".($i-1).",1";
		$fetch_sid = mysql_fetch_array(mysql_query($sql_sid, $conn));
		$sql = "UPDATE `sections` SET `order` = $i WHERE `sid` = ".$fetch_sid["sid"];
		@mysql_query($sql, $conn) or die("<b>A fatal MySQL error occurred</b>.\n<br />\nError: (" . mysql_errno() . ") " . mysql_error());
		//echo "$sql<br />\n";
	}
	//exit();
	header("Location: trashcan.php?fromeditor=yes");
}

// Need the page params so we can return to this section after saving.
$param = "";
// SID is set. We are therefore editing a section.
if (isset($_GET["sid"])){
	// Need the page params so we can return to this section after saving.
	$param = "?sid=".$_GET["sid"];
	$edit=true;

	//started by
	$sql = "SELECT maker.username AS startedby_name, sections.startedby AS startedby FROM maker, sections WHERE maker.id = startedby AND sections.sid = ".$_GET["sid"];
	$query = mysql_query($sql, $conn);
	$datas2 = mysql_fetch_array($query);
	if (!isset($datas2["startedby_name"])) $datas2["startedby_name"] = "&lt;unknown&gt;";

	//updated by
	$sql = "SELECT maker.username AS updatedby_name, sections.startedby AS updatedby FROM maker, sections WHERE maker.id = updatedby AND sections.sid = ".$_GET["sid"];
	$query = mysql_query($sql, $conn);
	$datas3 = mysql_fetch_array($query);
	if (!isset($datas3["updatedby_name"])) $datas3["updatedby_name"] = "&lt;unknown&gt;";

	// Fetching the section, if the admin has permission to it.
    $sql = "SELECT sections.*
    FROM sections, permissions
    WHERE sections.sid = ".$_GET["sid"]."
    AND ((
              permissions.uid = ".$_SESSION['sess_id']." AND permissions.sid = 0 AND permissions.permissions = 1
    		) OR (
              permissions.uid = ".$_SESSION['sess_id']." AND permissions.sid = sections.sid AND permissions.permissions = 1
        ))
        GROUP BY sections.sid";

	$query = mysql_query($sql, $conn);
	$datas = mysql_fetch_array($query);
}
else
	$edit=false;

$title = "Section editor"; // Title

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
<title><?php echo $title;?></title>
<link rel="stylesheet" href="theme/css/stylemaker.css" type="text/css" />
<script type="text/javascript" src="javascript/overlib/Mini/overlib_mini.js"><!-- overLIB (c) Erik Bosrup --></script>
<script type="text/javascript">
function refresh_ce(){
	setTimeout('parent.content_editor.location = "content_editor.php"',100);
}
</script>
</head>
<body>
<?php if (isset($what) or isset($_GET["refresh"])){?>
<script type="text/javascript">refresh_ce();</script>
<?php }?>
<?php if (($edit && mysql_num_rows($query) == 0) || !$headadmin){?>
<div class="infobox error">This section does not exist, or you don't have permission to edit it.</div>
<?php }else{?>
<script type="text/javascript">
	var ol_hpos = LEFT;
	var ol_width = "350";
	var ol_fgcolor = "#ffffcc";
	var ol_bgcolor = "#666666";
</script>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<form action="editors.php<?php echo $param?>" method="post">
<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
	<tr><th colspan="2"><?php echo $title; ?></th></tr>
	<tr><td class="sectionLinks" colspan="2"><?php if ($edit){?>Editing section id: <?php echo $_GET["sid"]; } else {?>Posting a new section<?php }?></td></tr>
	<tr>
		<td class="relatedLinks" style="width:90px;">Section Name:</td>
		<td class="relatedLinks">
			<input name="header" type="text" <?php if ($edit) {?>value="<?php echo $datas["header"];?>"<?php }?> maxlength="50" size="50" onmouseover="return overlib('You &lt;b&gt;must&lt;/b&gt; type a Section Name.&lt;br /&gt;This is the name which will be shown in menus.');" onmouseout="return nd();" style="width:100%;" />
		</td>
	</tr>
	<tr>
		<td class="relatedLinks" align="right">
		<span onmouseover="return overlib('&lt;span class=yes&gt;&lt;b&gt;Visible&lt;/b&gt;&lt;/span&gt; means that this section and it\'s pages &lt;b&gt;will&lt;/b&gt; show up in menu for end-users.');" onmouseout="return nd();">
			<label for="visyes"><span class="yes">Visible</span></label>
			<input name="vis" id="visyes" type="radio" value="1" <?php if ($edit && $datas["visible"]==1) {?>checked="checked"<?php } else if (!$edit) {?>checked="checked"<?php }?> />
		</span>
		</td>
		<td class="relatedLinks" align="left">
		<span onmouseover="return overlib('&lt;span class=no&gt;&lt;b&gt;Hidden&lt;/b&gt;&lt;/span&gt; means that this section and it\'s pages will &lt;b&gt;not&lt;/b&gt; show up in menu for end-users.');" onmouseout="return nd();">
			<label for="visno"><span class="no">Hidden</span></label>
			<input name="vis" id="visno" type="radio" value="0" <?php if ($edit && $datas["visible"]!=1) {?>checked="checked"<?php }?> />
		</span>
		</td>
	</tr>
	<tr>
		<td class="relatedLinks" align="right">
		<span onmouseover="return overlib('&lt;span class=yes&gt;&lt;b&gt;Enabled&lt;/b&gt;&lt;/span&gt; means that section and it\'s pages can be accessed on the site and is not hidden from sitemap and search.');" onmouseout="return nd();">
			<label for="disno"><span class="yes">Enabled</span></label>
			<input name="dis" id="disno" type="radio" value="0" <?php if ($edit && $datas["disabled"]!=1) {?>checked="checked"<?php } else if (!$edit) {?>checked="checked"<?php }?> />
		</span>
		</td>
		<td class="relatedLinks" align="left">
		<span onmouseover="return overlib('&lt;span class=no&gt;&lt;b&gt;Disabled&lt;/b&gt;&lt;/span&gt; means that section and it\'s pages &lt;b&gt;can\'t&lt;/b&gt; be accessed on the site and is hidden from sitemap and search.');" onmouseout="return nd();">
			<label for="disyes"><span class="no">Disabled</span></label>
			<input name="dis" id="disyes" type="radio" value="1" <?php if ($edit && $datas["disabled"]==1) {?>checked="checked"<?php }?> />
		</span>
		</td>
	</tr>

<?php if ($edit) {?>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2">Originally written by <b><?php echo $datas2["startedby_name"]; ?></b>&nbsp;(<?php echo $datas["startedby_date"]; ?>)</td></tr>
<?php if ($datas["updatedby"]!="") {?>
	<tr><td colspan="2">Last updated by <b><?php echo $datas3["updatedby_name"]; ?></b>&nbsp;(<?php echo $datas["updatedby_date"]; ?>)</td></tr>
<?php } if ($datas["deleted"]==1) {?>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2"><span class="no"><b>Moved to <a href="trashcan.php">Trashcan</a></b></span></td></tr>
<?php }}?>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td align="right" colspan="2"><input name="savesection" type="submit" value="Save" /><?php if ($edit && $headadmin && $datas["deleted"]!=1) {?>&nbsp;&nbsp;&nbsp;<input name="delete" type="submit" value="Delete section" /><?php }?></td></tr>
	<tr><td colspan="2" align="right"><?php if (isset($_POST["savesection"]) || isset($_POST["delete"])): ?><br /><div class="infobox saved">Saved <?php echo date("Y-m-d H:i:s");?></div><?php endif;?></td></tr>
</table>
</form>
<?php }?>
</body>
</html>