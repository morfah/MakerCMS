<?php
session_start(); // Always first

// Checks if admin logged in (session set)
if (!isset($_SESSION['sess_user'])){
	header("Location: index.php?sessiontimeout");
	exit;
}

require_once "../includes/config.php"; // Database connection

// Headadmin and charset
$sql = "SELECT T1.id, T1.name, T3.charset, (SELECT COUNT(*) FROM permissions AS T2 WHERE T2.sid = 0 AND T2.permissions = T1.id AND T2.uid=".$_SESSION["sess_id"].") AS headadmin
		FROM permissions_extra AS T1, global AS T3 WHERE T1.id = 1 AND T3.id = 1";
$query = mysqli_query($conn, $sql);
$fetch = mysqli_fetch_array($query);
$headadmin = $fetch["headadmin"];
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "utf-8";

if (isset($_GET["ascend"]) AND $_GET["ascend"] > 1){
	$sql = "UPDATE `content` SET `order`=".$_GET["ascend"]." WHERE `order`=".($_GET["ascend"] - 1)." AND `sid`=".$_GET["sid"].";";
	@mysqli_query($conn, $sql) or die("error while transfering");
	$sql = "UPDATE `content` SET `order`=".($_GET["ascend"] - 1)." WHERE `id`=".$_GET["id"]." AND `sid`=".$_GET["sid"].";";
	@mysqli_query($conn, $sql) or die("error while transfering");
}
elseif (isset($_GET["descend"])){
	$sql = "SELECT COUNT(*) AS `rows` FROM `content` WHERE `sid`=".$_GET["sid"]." GROUP BY `sid`;";
	$db = mysqli_fetch_array(mysqli_query($conn, $sql));
	$rows = $db["rows"];
	if ($_GET["descend"] != $rows){
		$sql = "UPDATE `content` SET `order`=".$_GET["descend"]." WHERE `order`=".($_GET["descend"] + 1)." AND `sid`=".$_GET["sid"].";";
		@mysqli_query($conn, $sql) or die("error while transfering");
		$sql = "UPDATE `content` SET `order`=".($_GET["descend"] + 1)." WHERE `id`=".$_GET["id"]." AND `sid`=".$_GET["sid"].";";
		@mysqli_query($conn, $sql) or die("error while transfering");
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
<title>Content editor</title>
<link rel="stylesheet" href="theme/css/stylemaker.css" type="text/css" />
<style type="text/css">
<!--
	.toggle{color:#005FA9;background-color:inherit;border-bottom:#005FA9 1px dashed;font-size:9px;cursor:pointer;}
	.toggle:hover{border-bottom:#005FA9 1px solid;}
-->
</style>
<script src="javascript/cookies.js" language="javascript" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
<!--
var ToggleTextFirst = "Show pages";
var ToggleTextSecond = "Hide pages";
var ToggleLayerFirst = "none";
var ToggleLayerSecond = "block";
function togglelayer(layerid){
	var ToggleLayer = document.getElementById("submenu" + layerid);
	ToggleLayer.style.display = (ToggleLayer.style.display == ToggleLayerFirst) ? ToggleLayerSecond : ToggleLayerFirst;
	var ToggleText = document.getElementById("toggle" + layerid);
	ToggleText.firstChild.nodeValue = (ToggleLayer.style.display != ToggleLayerFirst) ? ToggleTextSecond : ToggleTextFirst;
	// Save hide/show settings in a cookie for 7 days.
	createCookie('submenu'+layerid,ToggleLayer.style.display,7);
}
//-->
</script>
</head>
<body>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<th align="left" style="padding-left:5px">Content editor</th>
		<th align="right" style="padding:0 2px 0 0;margin:0;">
			<form action="content_editor.php" method="get"><input type="submit" value="Refresh" style="font-size:80%" /></form>
		</th>
	</tr>
<?php
$sql = "SELECT sections.*
FROM sections, permissions
WHERE sections.deleted != 1
AND ((
		permissions.uid = ".$_SESSION['sess_id']." AND permissions.sid = 0 AND permissions.permissions = 1
		) OR (
		permissions.uid = ".$_SESSION['sess_id']." AND permissions.sid = sections.sid AND permissions.permissions = 1
	))
    ORDER BY sections.`order`";
$query = mysqli_query($conn, $sql) or die($sql);
$rows = mysqli_num_rows($query);

for ($i=0;$i<$rows;$i++){
$fetch=mysqli_fetch_array($query);
$tempsid = $fetch["sid"];
?>
	<!--SECTION BEGIN-->
	<tr>
		<td class="sectionLinks" align="left">
			<a href="editors.php?sid=<?php echo $tempsid;?>" target="main"><span class="section">
					<?php if ($fetch["disabled"]==1):?><span class="disabled"><?php echo $fetch["header"];?></span>
					<?php elseif ($fetch["visible"]!=1):?><span class="not_visible"><?php echo $fetch["header"];?></span>
					<?php else: echo $fetch["header"]; ?>
					<?php endif;?>
			</span></a>
		</td>
		<td class="sectionLinks" align="right">
			<span id="toggle<?php echo $tempsid;?>" class="toggle" title="Shows or Hide the pages in this section." onclick="togglelayer('<?php echo $tempsid;?>');">
				<?php if (isset($_COOKIE["submenu".$tempsid]) && $_COOKIE["submenu".$tempsid]=="block"): ?>Hide<?php else: ?>Show<?php endif; ?> pages
			</span>
		</td>
	</tr>
	<tr><td colspan="2">
	<div id="submenu<?php echo $tempsid;?>" style="display:<?php if (isset($_COOKIE["submenu".$tempsid])): echo $_COOKIE["submenu".$tempsid]; else: ?>none;<?php endif;?>">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
<?php
		$sql2 = "SELECT id,sid,`order`,header,url,visible,disabled FROM content WHERE sid=".$tempsid." AND deleted=0 ORDER BY `order` ASC, `header` ASC";
		$query2 = mysqli_query($conn, $sql2);
		$rows2 = mysqli_num_rows($query2);
		for ($i2=0;$i2<$rows2;$i2++){
		$fetch2=mysqli_fetch_array($query2);
?>
			<tr>
				<!-- Page/Url -->
				<td class="relatedLinks"><?php //echo $fetch2["order"]. " - ";?>
					<a href="editor<?php if ($fetch2["url"]==1) echo "u"?>.php?sid=<?php echo $fetch2["sid"];?>&amp;id=<?php echo $fetch2["id"];?>" target="main">
						<?php if ($fetch2["disabled"]==1):?><span class="disabled"><?php echo $fetch2["header"];?></span>
						<?php elseif ($fetch2["visible"]==1):?><span class="visible"><?php echo $fetch2["header"];?></span>
						<?php else: echo $fetch2["header"];?>
						<?php endif;?>
						<?php if ($fetch2["url"]==1):?><img class="url" src="theme/img/url.png" alt="URL" width="10" height="10" /><?php endif;?>
					</a>
				</td>
				<!-- Ascend -->
				<td style="width:15px;border-bottom:1px solid #ccc;">
					<a href="?ascend=<?php echo $fetch2["order"];?>&amp;id=<?php echo $fetch2["id"];?>&amp;sid=<?php echo $fetch2["sid"];?>">
						<img class="arrows" src="theme/img/arrow_up.gif" alt="Ascend" title="Ascend &quot;<?php echo $fetch2["header"]?>&quot;" width="11" height="9" />
					</a>
				</td>
				<!-- Descend -->
				<td style="width:15px;border-bottom:1px solid #ccc;">
					<a href="?descend=<?php echo $fetch2["order"];?>&amp;id=<?php echo $fetch2["id"];?>&amp;sid=<?php echo $fetch2["sid"];?>">
						<img class="arrows" src="theme/img/arrow_down.gif" alt="Descend" title="Descend &quot;<?php echo $fetch2["header"]?>&quot;" width="11" height="9" />
					</a>
				</td>
			</tr>
<?php
		} //end for loop
?>
			<tr>
				<td colspan="3" style="padding: 5px 2px;">
					<form action="editor.php?sid=<?php echo $fetch["sid"];?>" method="post" target="main">
						<input class="newpage" type="submit" value="Create a page for &quot;<?php echo $fetch["header"]?>&quot;" style="margin:0 0 5px;" />
					</form>
					<form action="editoru.php?sid=<?php echo $fetch["sid"];?>" method="post" target="main">
						<input class="newpage" type="submit" value="Create an URL for &quot;<?php echo $fetch["header"]?>&quot;" />
					</form>
				</td>
			</tr>
		</table>
	</div>
	</td></tr>
	<tr><td class="relatedLinks" colspan="2">&nbsp;</td></tr>
	<!--SECTION END-->
<?php
} //end for loop
if ($headadmin){
?>
	<tr><td class="sectionLinks" align="center" colspan="2"><a href="editors.php" target="main" title="Add a section">Create a new section</a></td></tr>
<?php
}
?>
</table>

<br /><br />
<div class="infobox centerbox">&copy; 2005-2011 Kristian Johansson</div>

</body>
</html>
