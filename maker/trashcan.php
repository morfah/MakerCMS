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
if ($headadmin) $colspan = 4;
else $colspan = 2;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
<title>Menu</title>
<link rel="stylesheet" href="theme/css/stylemaker.css" type="text/css" />
<script type="text/javascript">
function refresh_ce(){
	setTimeout('parent.content_editor.location = "content_editor.php"',100);
}
</script>
</head>
<body>
<?php
if (isset($_GET["fromeditor"])) echo "<script type=\"text/javascript\">refresh_ce();</script>\n";

if ($headadmin){
	if (isset($_POST["submit_restoresection"])){
		$sid = $_GET["sid"];
		$sql = "SELECT * FROM `sections` WHERE `deleted` = 0";
		$rows = mysqli_num_rows(mysqli_query($conn, $sql));
		$sql = "UPDATE `sections` SET `deleted` = 0, `order` = ".($rows+1)." WHERE `sid` = $sid";
		@mysqli_query($conn, $sql) or die("<strong>A fatal MySQL error occurred</strong>.\n<br>\nError: (" . mysqli_connect_errno() . ") " . mysqli_connect_error());
		echo "<script type=\"text/javascript\">refresh_ce();</script>\n";
	}
	else if (isset($_POST["submit_deletesection"])){
		$sid = $_GET["sid"];
		$sql = "DELETE sections FROM sections WHERE sections.sid=$sid";
		@mysqli_query($conn, $sql) or die("<strong>A fatal MySQL error occurred</strong>.\n<br>\nError: (" . mysqli_connect_errno() . ") " . mysqli_connect_error());
		echo "<script type=\"text/javascript\">refresh_ce();</script>\n";
	}
	else if (isset($_POST["submit_restorepage"])){
		$id=$_GET["id"];
		$sid=$_GET["sid"];
		if ($_POST["submit_restorepage"] > 0)
			$sql = "SELECT * FROM `content` WHERE `sid` = ".$_POST["submit_restorepage"]." AND `deleted` = 0";
		else
			$sql = "SELECT * FROM `content` WHERE `sid` = $sid AND `deleted` = 0";
		//echo $sql . "<br>";
		$rows = mysqli_num_rows(mysqli_query($conn, $sql));
		//echo "rows = $rows <br>";
		$sql = "UPDATE `content` SET `deleted` = 0, `order` = ".($rows+1)." WHERE `id` = $id AND `sid` = $sid";
		//echo $sql . "<br>";
		@mysqli_query($conn, $sql) or die("<strong>A fatal MySQL error occurred</strong>.\n<br>\nError: (" . mysqli_connect_errno() . ") " . mysqli_connect_error());
		//Are we moving the page to a diffrent section?
		if ($_POST["submit_restorepage"] > 0){
			$sql = "UPDATE `content` SET `sid` = ".$_POST["submit_restorepage"]." WHERE `id` = $id AND `sid` = $sid";
			//echo $sql;
			@mysqli_query($conn, $sql) or die("<strong>A fatal MySQL error occurred</strong>.\n<br>\nError: (" . mysqli_connect_errno() . ") " . mysqli_connect_error());
		}
		echo "<script type=\"text/javascript\">refresh_ce();</script>\n";
	}
	else if (isset($_POST["submit_deletepage"])){
		$id=$_GET["id"];
		$sid=$_GET["sid"];
		$sql = "DELETE FROM content WHERE id=$id AND sid=$sid";
		@mysqli_query($conn, $sql) or die("<strong>A fatal MySQL error occurred</strong>.\n<br>\nError: (" . mysqli_connect_errno() . ") " . mysqli_connect_error());
		echo "<script type=\"text/javascript\">refresh_ce();</script>\n";
	}
}
?>
<table border="0" cellpadding="0" cellspacing="0">
	<tr><th colspan="<?php echo $colspan?>">Trashcan</th></tr>
	<tr><td class="sectionLinks" colspan="<?php echo $colspan?>">Deleted sections</td></tr>
<?php
$sql = "SELECT * FROM sections WHERE deleted=1";
$query = mysqli_query($conn, $sql);
$rows = mysqli_num_rows($query);

for ($i=0;$i<$rows;$i++){
	$fetch=mysqli_fetch_array($query); ?>
	<tr>
		<td class="relatedLinks"><a href="editors.php?sid=<?php echo $fetch["sid"]?>"><?php echo $fetch["header"]?></a></td>
		<td class="relatedLinks">(sid: <?php echo $fetch["sid"]?>)</td>
<?php if ($headadmin){?>
		<td class="relatedLinks"><form method="post" action="trashcan.php?sid=<?php echo $fetch["sid"]?>"><input type="submit" name="submit_restoresection" value="Restore" /></form></td>
		<td class="relatedLinks"><form method="post" action="trashcan.php?sid=<?php echo $fetch["sid"]?>" onsubmit="javascript:return confirm('Do you really want to remove this section forever?');"><input type="submit" name="submit_deletesection" value="Delete" /></form></td>
<?php }?>
	</tr>
<?php } if ($rows < 1) {?>
	<tr><td colspan="<?php echo $colspan?>">&nbsp;<em>None</em></td></tr>
<?php }?>
	<tr><td colspan="<?php echo $colspan?>">&nbsp;</td></tr>
	<tr><td class="sectionLinks" colspan="<?php echo $colspan?>">Deleted pages</td></tr>
<?php
$sql = "SELECT url,sid,id,header FROM content WHERE deleted=1";
$query = mysqli_query($conn, $sql);
$rows = mysqli_num_rows($query);

for ($i=0;$i<$rows;$i++){
	$fetch=mysqli_fetch_array($query); ?>
	<tr>
		<td class="relatedLinks"><a href="editor<?php if ($fetch["url"]==1) echo "u"?>.php?sid=<?php echo $fetch["sid"]?>&amp;id=<?php echo $fetch["id"]?>"><?php echo $fetch["header"]?> <?php if ($fetch["url"]==1):?> <img class="url" src="theme/img/url.png" alt="URL" width="10" height="10" /><?php endif;?></a></td>
		<td class="relatedLinks">(sid: <?php echo $fetch["sid"]?>) (id: <?php echo $fetch["id"]?>)</td>
<?php if ($headadmin){?>
		<td class="relatedLinks"><form method="post" action="trashcan.php?sid=<?php echo $fetch["sid"]?>&amp;id=<?php echo $fetch["id"]?>"><input type="submit" name="submit_restorepage" value="Restore" /></form></td>
		<td class="relatedLinks"><form method="post" action="trashcan.php?sid=<?php echo $fetch["sid"]?>&amp;id=<?php echo $fetch["id"]?>" onsubmit="javascript:return confirm('Do you really want to remove this page forever?');"><input type="submit" name="submit_deletepage" value="Delete" /></form></td>
<?php }?>
	</tr>
<?php } if ($rows < 1) {?>
	<tr><td colspan="4">&nbsp;<em>None</em></td></tr>
<?php }?>
	<tr><td colspan="4">&nbsp;</td></tr>
	<tr>
	  <td class="sectionLinks" colspan="4">Pages without section</td></tr>
<?php
//$sql = "SELECT content.url,content.sid,content.id,content.header FROM content WHERE content.sid NOT IN (SELECT sections.sid FROM sections GROUP BY sections.sid)";
$sql = "SELECT content.url,content.sid,content.id,content.header FROM content WHERE content.sid NOT IN (SELECT sections.sid FROM sections GROUP BY sections.sid) OR content.sid IN (SELECT sections.sid FROM sections WHERE sections.deleted=1 GROUP BY sections.sid)";
$query = mysqli_query($conn, $sql);
$rows = mysqli_num_rows($query);

for ($i=0;$i<$rows;$i++){
	$fetch=mysqli_fetch_array($query); ?>
	<tr>
		<td class="relatedLinks"><a href="editor<?php if ($fetch["url"]==1) echo "u"?>.php?sid=<?php echo $fetch["sid"]?>&amp;id=<?php echo $fetch["id"]?>"><?php echo $fetch["header"]?> <?php if ($fetch["url"]==1):?> <img class="url" src="theme/img/url.png" alt="URL" width="10" height="10" /><?php endif;?></a></td>
		<td class="relatedLinks">(sid: <?php echo $fetch["sid"]?>) (id: <?php echo $fetch["id"]?>)</td>
<?php if ($headadmin){?>
		<td class="relatedLinks">
		<form method="post" action="trashcan.php?sid=<?php echo $fetch["sid"]?>&amp;id=<?php echo $fetch["id"]?>" id="movepage_<?php echo $fetch["sid"]?>_<?php echo $fetch["id"]?>">
    <select onchange="javascript:if(confirm('Do really you want to move the page?')) document.getElementById('movepage_<?php echo $fetch["sid"]?>_<?php echo $fetch["id"]?>').submit(); else document.getElementById('movepage_<?php echo $fetch["sid"]?>_<?php echo $fetch["id"]?>_select').selectedIndex = 0;" name="submit_restorepage" id="movepage_<?php echo $fetch["sid"]?>_<?php echo $fetch["id"]?>_select">
			<option selected="selected">Move to section:</option>
<?php
$sql2 = "SELECT sid,header FROM sections WHERE deleted=0";
$query2 = mysqli_query($conn, $sql2);
$rows2 = mysqli_num_rows($query2);
for ($i2=0;$i2<$rows2;$i2++){
	$fetch2=mysqli_fetch_array($query2);
?>
			<option value="<?php echo $fetch2["sid"] ?>"><?php echo $fetch2["header"]?></option>
<?php
}
?>
		</select></form></td>
		<td class="relatedLinks"><form method="post" action="trashcan.php?sid=<?php echo $fetch["sid"]?>&amp;id=<?php echo $fetch["id"]?>" onsubmit="javascript:return confirm('Do you really want to remove this page forever?');"><input type="submit" name="submit_deletepage" value="Delete" /></form></td>
<?php }?>
	</tr>
<?php } if ($rows < 1):?>
	<tr><td colspan="4">&nbsp;<em>None</em></td></tr>
<?php endif;?>
	<tr><td colspan="4">&nbsp;</td></tr>
<?php if(!$headadmin):?>
	<tr><td colspan="4"><em>Only Headadmins can restore and delete from Trashcan.</em></td></tr>
<?php endif;?>
</table>
<p>&nbsp;</p>
</body>
</html>
