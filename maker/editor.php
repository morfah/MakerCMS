<?php
session_start(); // Always first

// Checks if admin logged in (session set)
if (!isset($_SESSION['sess_user'])){
  header("Location: index.php?sessiontimeout");
  exit;
}

require_once "../includes/config.php"; // Database connection
require_once "classes/autoload.php"; // autoload classes

// Headadmin, timezone and charset
$sql = "SELECT T1.id, T1.name, T3.charset, T3.timezone, (SELECT COUNT(*) FROM permissions AS T2 WHERE T2.sid = 0 AND T2.permissions = T1.id AND T2.uid=".$_SESSION["sess_id"].") AS headadmin
		FROM permissions_extra AS T1, global AS T3 WHERE T1.id = 1 AND T3.id = 1";
$query = mysql_query($sql, $conn);
$fetch = mysql_fetch_array($query);
$headadmin = $fetch["headadmin"];
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "utf-8";
if ($fetch["timezone"]!="") date_default_timezone_set($fetch["timezone"]);

if (isset($_POST["save"]) or isset($_POST["delete"]))
		$content = new content;

// Saving.
if (isset($_POST["save"])){
	require_once "../includes/validate.php";
	$date = date("Y-m-d H:i:s");

	// Start alot of checks (to silence the "undefined notice message" in PHP)
	if (isset ($_GET["id"])) $id = $_GET["id"];
	else $id = "";

	if (isset ($_GET["sid"])) $sid = $_GET["sid"];
	else $sid = "";

	if (isset ($_POST["header"])) {
		$header = htmlentities($_POST["header"],ENT_QUOTES,$charset);
		// We do not want & or &amp; in header, because PHP handles & in urls as a param.
		$header = str_replace("&amp;","", $header);
		if ($_POST["header"]=="") $header = "noname $date";
		//else if ($_POST["header"]);
	}
	else $header = "noname $date";

	if (isset ($_POST["header_before"])) $header_before = $_POST["header_before"];
	else $header_before = $header;

	if (isset ($_POST["menuname"])) $menuname = htmlentities($_POST["menuname"],ENT_QUOTES,$charset);
	else $menuname = "";

	if (isset ($_POST["headline"])) $headline = htmlentities($_POST["headline"],ENT_QUOTES,$charset);
	else $headline = "";

	if (isset ($_POST["vis"])) $vis = $_POST["vis"];
	else $vis = "0";
	if (isset ($_POST["dis"])) $dis = $_POST["dis"];
	else $dis = "0";

	if (isset ($_POST["context"])) {
		$context = preg_replace("/&(?!.\\w*;)/", "&amp;", $_POST["context"]); // Evil &
		$context = str_replace("'","&#39;", $context); // Evil '
	}
	else $context = "";

	if (isset ($_POST["movepage"])) $movepage = $_POST["movepage"];
	else $movepage = $sid;

	// New Page
	if ($id == "") {
		$redirect_suffix = "";
		//make sure pagename is unique
		while (!$content->UniquePageName($header, $sid)){
			$header .= "_";
			$redirect_suffix = "&was_not_unique=1";
		}
		$content->NewPage($sid, $vis, $dis, $context, $date, $header, $menuname, $headline, 0);
		header("Location: editor.php?sid=$sid&id=$content->LatestPageId&refresh=yes$redirect_suffix"); // Open this page in Editor
	}
	// Updating Existing Page
	else {
		$redirect_suffix = "";
		//make sure pagename is unique when moving to a new section
		if ($movepage != $sid)
			while (!$content->UniquePageName($header, $movepage)){
				$header .= "_";
				$redirect_suffix = "&was_not_unique=1";
			}
		//and do the same if we renamed it
		else if ($header != $header_before)
			while (!$content->UniquePageName($header, $sid)){
				$header .= "_";
				$PageNameWasNotUnique = true;
			}

		$content->UpdatePage($id, $sid, $movepage, $vis, $dis, $context, $date, $header, $menuname, $headline);
		//$what needs to be set for the content_editor frame to reload
		$what = "Updated";

		// This re-orders the pages on the section we moved a page from
		if ($movepage != $sid){
			$content->SortPages($sid);
			header("Location: editor.php?sid=$movepage&id=$id&refresh=yes$redirect_suffix"); // Open this page in Editor
		}
	}
}
// Moving to trashcan.
if (isset ($_POST["delete"])) {
	if (isset ($_GET["sid"])) $sid = $_GET["sid"];
	else $sid = "";
	if (isset ($_GET["id"])) $id = $_GET["id"];
	else $id = "";
	$content->DeletePage($id,$sid);
	// Re-Order current alive pages. This fixes wholes in order.
	$content->SortPages($sid);
	header("Location: trashcan.php?fromeditor=yes");
}

// Need the page params so we can return to this page after saving.
$param = "?sid=".$_GET["sid"];
// ID is set. We are therefore editing a page.
if (isset($_GET["id"])){
	// Need the page params so we can return to this page after saving.
	$param .= "&amp;id=".$_GET["id"];
	$edit=true;
	// Started by
	$sql = "SELECT maker.username AS startedby_name, content.startedby AS startedby FROM maker, content WHERE maker.id = startedby AND content.id = ".$_GET["id"];
	$query = mysql_query($sql, $conn);
	$datas2 = mysql_fetch_array($query);
	if (!isset($datas2["startedby_name"])) $datas2["startedby_name"] = "&lt;unknown&gt;";

	// Updated by
	$sql = "SELECT maker.username AS updatedby_name, content.startedby AS updatedby FROM maker, content WHERE maker.id = updatedby AND content.id = ".$_GET["id"];
	$query = mysql_query($sql, $conn);
	$datas3 = mysql_fetch_array($query);
	if (!isset($datas3["updatedby_name"])) $datas3["updatedby_name"] = "&lt;unknown&gt;";

	// Fetching the page, if the admin has permission to it.
    $sql = "SELECT content.*
    FROM content, permissions
    WHERE (content.sid = ".$_GET["sid"]." AND content.id = ".$_GET["id"]." AND content.url = 0)
    AND ((
              permissions.uid = ".$_SESSION['sess_id']." AND permissions.sid = 0 AND permissions.permissions = 1
    		) OR (
              permissions.uid = ".$_SESSION['sess_id']." AND permissions.sid = content.sid AND permissions.permissions = 1
        ))
        GROUP BY content.id";
	$query = mysql_query($sql, $conn);
	$datas = mysql_fetch_array($query);

}

// New page.
else{
	$edit=false;

	// Checking if this admin can create a page on this section.
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
}
$title = "Page editor"; // Title

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
<title><?php echo $title;?></title>
<link rel="stylesheet" href="theme/css/stylemaker.css" type="text/css" />
<script language="javascript" type="text/javascript" src="javascript/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		document_base_url : "<?php echo $url ?>",
		convert_urls : false,

		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		
		// a html5 tag that randompolygons.com uses
		extended_valid_elements : "aside",

		// Skin options
		skin : "o2k7",
		skin_variant : "silver",

		// Example content CSS (should be your site CSS)
		content_css : "theme/css/style.css,maker/theme/css/styletinymce.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "js/template_list.js",
		external_link_list_url : "js/link_list.js",
		external_image_list_url : "js/image_list.js",
		media_external_list_url : "js/media_list.js"
	});
</script>
<script type="text/javascript" src="javascript/overlib/Mini/overlib_mini.js"><!-- overLIB (c) Erik Bosrup --></script>
<script type="text/javascript">
function refresh_ce(){
	setTimeout('parent.content_editor.location = "content_editor.php"',100);
}
</script>
</head>
<body>
<?php if (isset($what) || isset($_GET["refresh"])){?>
<script type="text/javascript">refresh_ce();</script>
<?php }?>
<?php if (mysql_num_rows($query) == 0 || !isset($_GET["sid"])){?>
<div class="infobox error">This page does not exist, or you don't have permission to edit it.</div>
<?php } else {?>

<script type="text/javascript">
	var ol_hpos = LEFT;
	var ol_width = "350";
	var ol_fgcolor = "#ffffcc";
	var ol_bgcolor = "#666666";
</script>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<form action="editor.php<?php echo $param?>" method="post">
<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
	<tr><th colspan="6"><?php echo $title; ?></th></tr>
	<tr><td class="sectionLinks" colspan="6"><?php if ($edit){?>SID: <?php echo $_GET["sid"]; ?> ID: <?php echo $_GET["id"]; } else {?>New page for SID: <?php echo $_GET["sid"]; }?></td></tr>
	<tr>
		<td class="relatedLinks"><span<?php if(isset($_GET["was_not_unique"]) || isset($PageNameWasNotUnique)){?> class="NameWasNotUnique"<?php }?>>Page Name:</span></td>
		<td class="relatedLinks">
			<input name="header" type="text" <?php if ($edit) {?>value="<?php echo $datas["header"];?>"<?php }?> maxlength="50" size="20" onmouseover="return overlib('You &lt;b&gt;must&lt;/b&gt; type a Page Name.&lt;br /&gt;This is the name which will be used to identify the page.');" onmouseout="return nd();" style="width:100%;" />
			<?php if ($edit) {?><input name="header_before" type="hidden" value="<?php echo $datas["header"];?>" /><?php }?>
		</td>
		<td class="relatedLinks">Menu Name (optional):</td>
		<td class="relatedLinks">
			<input name="menuname" type="text" <?php if ($edit) {?>value="<?php echo $datas["menuname"];?>"<?php }?> maxlength="50" size="20" onmouseover="return overlib('This is the name which will be shown in menus.&lt;br /&gt;If Menu Name is not set Page Name will be used instead.&lt;br /&gt;&lt;br /&gt;&lt;em&gt;If Menu Name is not set but Headline is; Headline will be used as Menu Name.&lt;/em&gt;');" onmouseout="return nd();" style="width:100%;" />
		</td>
		<td class="relatedLinks" align="right">
		<span onmouseover="return overlib('&lt;span class=yes&gt;&lt;b&gt;Visible&lt;/b&gt;&lt;/span&gt; means that this page &lt;b&gt;will&lt;/b&gt; show up in menu for end-users.');" onmouseout="return nd();">
			<label for="visyes"><span class="yes">Visible</span></label>
			<input name="vis" id="visyes" type="radio" value="1" <?php if ($edit && $datas["visible"]==1) {?>checked="checked"<?php } else if (!$edit) {?>checked="checked"<?php }?> />
		</span>
		</td>
		<td class="relatedLinks" align="left">
		<span onmouseover="return overlib('&lt;span class=no&gt;&lt;b&gt;Hidden&lt;/b&gt;&lt;/span&gt; means that this page will &lt;b&gt;not&lt;/b&gt; show up in menu for end-users.');" onmouseout="return nd();">
			<label for="visno"><span class="no">Hidden</span></label>
			<input name="vis" id="visno" type="radio" value="0" <?php if ($edit && $datas["visible"]!=1) {?>checked="checked"<?php }?> />
		</span>
		</td>
	</tr>
	<tr>
		<td class="relatedLinks">Headline (optional):</td>
		<td class="relatedLinks" colspan="3">
			<input name="headline" type="text" value="<?php if ($edit) echo $datas["headline"];?>" maxlength="100" size="60" onmouseover="return overlib('This is the text which will be shown as headlines.&lt;br /&gt;If Headline is not set Page Name will be used instead.&lt;br /&gt;&lt;br /&gt;&lt;em&gt;If Menu Name is not set but Headline is; Headline will be used as Menu Name.&lt;/em&gt;');" onmouseout="return nd();" style="width:100%;" />
		</td>
		<td class="relatedLinks" align="right">
		<span onmouseover="return overlib('&lt;span class=yes&gt;&lt;b&gt;Enabled&lt;/b&gt;&lt;/span&gt; means that this page can be accessed on the site and is not hidden from sitemap and search.');" onmouseout="return nd();">
			<label for="disno"><span class="yes">Enabled</span></label>
			<input name="dis" id="disno" type="radio" value="0" <?php if ($edit && $datas["disabled"]!=1) {?>checked="checked"<?php } else if (!$edit) {?>checked="checked"<?php }?> />
		</span>
		</td>
		<td class="relatedLinks" align="left">
		<span onmouseover="return overlib('&lt;span class=no&gt;&lt;b&gt;Disabled&lt;/b&gt;&lt;/span&gt; means that this page &lt;b&gt;can\'t&lt;/b&gt; be accessed on the site and is hidden from sitemap and search.');" onmouseout="return nd();">
			<label for="disyes"><span class="no">Disabled</span></label>
			<input name="dis" id="disyes" type="radio" value="1" <?php if ($edit && $datas["disabled"]==1) {?>checked="checked"<?php }?> />
		</span>
		</td>
	</tr>
	<tr><td class="relatedLinks" colspan="6">
<textarea name="context" rows="0" cols="0" style="width:100%;height:450px;">
<?php
if ($edit) {
	// w3c complains otherwise... and bad things may happen.
	$datas["context"] = str_replace('<','&lt;', $datas["context"]);
	$datas["context"] = str_replace('>','&gt;', $datas["context"]);
	echo $datas["context"]."\n";
}
?>
</textarea>
	</td></tr>

<?php if ($edit) {?>
	<tr><td colspan="6">&nbsp;</td></tr>
	<tr><td colspan="6">Originally written by <b><?php echo $datas2["startedby_name"]; ?></b>&nbsp;(<?php echo $datas["startedby_date"]; ?>)</td></tr>
<?php if ($datas["updatedby"]!="") {?>
	<tr><td colspan="6">Last updated by <b><?php echo $datas3["updatedby_name"]; ?></b>&nbsp;(<?php echo $datas["updatedby_date"]; ?>)</td></tr>
<?php } if ($datas["deleted"]==1) {?>
	<tr><td colspan="6">&nbsp;</td></tr>
	<tr><td colspan="6"><span class="no"><b>Moved to <a href="trashcan.php">Trashcan</a></b></span></td></tr>
<?php }}?>
	<tr>
<?php
if ($edit){
?>
	  <td colspan="6" align="right">Move to section:
	    <select name="movepage">
<?php
	$sql2 = "SELECT sections.*
	FROM sections, permissions
	WHERE sections.deleted != 1
	AND ((
			permissions.uid = ".$_SESSION['sess_id']." AND permissions.sid = 0 AND permissions.permissions = 1
			) OR (
			permissions.uid = ".$_SESSION['sess_id']." AND permissions.sid = sections.sid AND permissions.permissions = 1
		))
		GROUP BY sections.sid ORDER BY `order`";
	$query2 = mysql_query($sql2, $conn);
	$rows2 = mysql_num_rows($query2);
	for ($i2=0;$i2<$rows2;$i2++){
		$fetch2=mysql_fetch_array($query2);
?>
			<option value="<?php echo $fetch2["sid"] ?>" <?php if ($fetch2["sid"] == $_GET["sid"]) echo "selected=\"selected\"";?>><?php echo $fetch2["header"]?></option>
<?php
	}
?>
    </select></td></tr>
<?php
}
?>
    <tr><td colspan="6">&nbsp;</td></tr>
	<tr><td colspan="6" align="right"><input name="save" type="submit" value="Save" /><?php if ($edit && $headadmin && $datas["deleted"]!=1) {?>&nbsp;&nbsp;&nbsp;<input name="delete" type="submit" value="Delete" /><?php }?></td></tr>
	<tr><td colspan="6" align="right">
		<?php if (isset($_POST["save"]) || isset($_POST["delete"])):?><br /><div class="infobox saved">Saved <?php echo date("Y-m-d H:i:s");?></div><?php endif;?>
	</td></tr>
</table>
</form>
<?php
}
?>
</body>
</html>