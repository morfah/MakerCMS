<?php
session_start(); // Always first

// Checks if admin logged in (session set)
if (!isset($_SESSION['sess_user'])){
  header("Location: index.php?sessiontimeout");
  exit;
}

require_once "../includes/config.php"; // Database and Site settings
require_once "../includes/validate.php"; // Validation functions
// timezone and charset
$sql = "SELECT timezone,charset FROM `global` WHERE global.id=1";
$query = mysqli_query($conn, $sql);
$fetch = mysqli_fetch_array($query);
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "utf-8";
if ($fetch["timezone"]!="") date_default_timezone_set($fetch["timezone"]);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
<title>News</title>
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
</head>
<body>
<?php
if (isset($_POST["submit"])){
	if (!isset($_POST["headline"]) || $_POST["headline"]=="")
		$_POST["headline"] = "Fixme: No headline";
	if (!isset($_POST["news"]) || $_POST["news"]=="")
		$_POST["news"] = "Fixme: No news";
	if (isset($_GET["edit"])){
		if (!isset($_POST["deleted"]))
			$_POST["deleted"]=0;
		$sql = "UPDATE news SET headline='".htmlentities($_POST["headline"],ENT_QUOTES,$charset)."', news='".$_POST["news"]."', updatedtime=".time().", deleted=".$_POST["deleted"]." WHERE id=".$_GET["edit"];
	}
	else{
		$sql = "INSERT INTO news (headline,news,author,time,updatedtime) VALUES ('".htmlentities($_POST["headline"],ENT_QUOTES,$charset)."','".$_POST["news"]."',".$_SESSION['sess_id'].",".time().",".time().")";
	}
	@mysqli_query($conn, $sql) or die("error while transfering");
}
elseif(isset($_POST["submit_hide"])){
	$sql = "UPDATE news SET deleted=1 WHERE id=".$_GET["hide"];
	@mysqli_query($conn, $sql) or die("error while transfering");
}
elseif(isset($_POST["submit_show"])){
	$sql = "UPDATE news SET deleted=0 WHERE id=".$_GET["show"];
	@mysqli_query($conn, $sql) or die("error while transfering");
}
elseif(isset($_POST["submit_delete"])){
	$sql = "DELETE FROM news WHERE id=".$_GET["delete"];
	@mysqli_query($conn, $sql) or die("error while transfering");
}
if (isset($_GET["edit"])){
	$edit=true;
	$sql = "SELECT * FROM news WHERE id=".$_GET["edit"];
	$query = mysqli_query($conn, $sql);
	$db = mysqli_fetch_array($query);
}
else
	$edit=false;
?>
<form action="news.php<?php if($edit){?>?edit=<?php echo $_GET["edit"]; }?>" method="post">
<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
	<tr><th colspan="2">News</th></tr>
	<tr><td class="sectionLinks" colspan="2"><?php if ($edit){?>Update news<?php }else{?>Post news<?php }?></td></tr>
	<tr>
		<td class="relatedLinks">Headline:</td>
		<td class="relatedLinks">
			<input type="text" maxlength="100" size="81" name="headline"<?php if ($edit) echo " value=\"".$db["headline"]."\"";?> style="width:100%;" />
		</td>
	</tr>
	<tr><td class="relatedLinks" colspan="2">
<textarea name="news" rows="0" cols="0" style="width:100%;height:300px;">
<?php
if ($edit) {
	// w3c complains otherwise...
	$db["news"] = str_replace('<','&lt;', $db["news"]);
	$db["news"] = str_replace('>','&gt;', $db["news"]);
	echo $db["news"]."\n";
}
?>
</textarea>
	</td></tr>
	<tr><td class="relatedLinks" align="right" colspan="2"><?php if ($edit){?>Hide<input name="deleted" type="checkbox" value="1" <?php if ($db["deleted"]==1){?> checked="checked"<?php }?> /><?php }?>&nbsp;&nbsp;&nbsp;<input type="submit" <?php if ($edit){?>value="Update news"<?php }else{?>value="Post news"<?php }?> name="submit" /></td></tr>
</table>
</form>
<p>&nbsp;</p>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
<tr>
	<td class="sectionLinks" colspan="2">Headline</td>
	<td class="sectionLinks">Author</td>
	<td class="sectionLinks" colspan="4">Updated</td>
</tr>
<?php
$sql = 'SELECT news.*, maker.username AS authorname'
. ' FROM news, maker'
. ' WHERE news.author = maker.id'
. ' ORDER BY news.id DESC';
$query = mysqli_query($conn, $sql);
$rows = mysqli_num_rows($query);
for ($i=0;$i<$rows;$i++){
$db=mysqli_fetch_array($query);
if ($edit && $db["id"]==$_GET["edit"]){
	$style = "color:#00F;background-color:#ffffcc;";
	if ($db["deleted"]==1) $style = "color:#aaf;background-color:#ffffcc;";
}
else if ($db["deleted"]==1) $style = "color:#faa;background-color:#eee;";
else $style = "";
?>
<tr style="<?php echo $style?>">
	<td class="relatedLinks"><strong><?php echo $db["headline"]?></strong></td>
	<td class="relatedLinks">(id: <?php echo $db["id"]?>)</td>
	<td class="relatedLinks"><?php echo $db["authorname"]?></td>
	<td class="relatedLinks"><?php echo date("Y-m-d H:i:s", $db["updatedtime"])?></td>
	<td class="relatedLinks"><?php if ($edit && $db["id"]==$_GET["edit"]):?><form method="post" action="news.php"><input type="submit" name="submit_cancel" value="Cancel edit" style="width:100%;" /></form><?php else:?><form method="post" action="news.php?edit=<?php echo $db["id"]?>"><input type="submit" name="submit_edit" value="Edit" style="width:100%;" /></form><?php endif;?></td>
	<td class="relatedLinks"><?php if($db["deleted"]==1):?><form method="post" action="news.php?show=<?php echo $db["id"]?>"><input type="submit" name="submit_show" value="Show" style="width:100%;" /></form><?php else:?><form method="post" action="news.php?hide=<?php echo $db["id"]?>"><input type="submit" name="submit_hide" value="Hide" style="width:100%;" /></form><?php endif;?></td>
	<td class="relatedLinks"><form method="post" action="news.php?delete=<?php echo $db["id"]?>" onsubmit="javascript:return confirm('Do you really want to remove this news item forever?');"><input type="submit" name="submit_delete" value="Delete" style="width:100%;" /></form></td>
</tr>
<?php }?>
</table>
</body>
</html>
