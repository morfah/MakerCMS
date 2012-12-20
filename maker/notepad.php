<?php
session_start(); // Always first

// Checks if admin logged in (session set)
if (!isset($_SESSION['sess_user'])){
  header("Location: index.php?sessiontimeout");
  exit;
}

require_once "../includes/config.php"; // Database and Site settings

// If saved....
if (isset($_POST["todosave"])){
	if (isset ($_POST["todo"])) {
		$todo = $_POST['todo'];
		$todo = preg_replace('/&(?!.\\w*;)/', '&amp;', $todo); // Convert & to &amp;
	}
	else $todo = '';

	$sql = "UPDATE maker SET todo='".$todo."' WHERE id=".$_SESSION['sess_id'];
	@mysql_query($sql, $conn) or die("error while transfering");

	$what = "saved";
}

$sql = "SELECT maker.todo,global.charset FROM maker,global WHERE global.id=1 AND maker.id=".$_SESSION['sess_id'];
$query = mysql_query($sql, $conn);
$fetch = mysql_fetch_array($query);
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "utf-8";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
<title>TODO List for <?php echo $_SESSION['sess_user'];?></title>
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
		content_css : "maker/theme/css/styletinymce.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "js/template_list.js",
		external_link_list_url : "js/link_list.js",
		external_image_list_url : "js/image_list.js",
		media_external_list_url : "js/media_list.js"
	});
</script>
</head>
<body>
<form action="notepad.php" method="post">
<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
	<tr>
		<th colspan="2">Notepad</th>
	</tr>
	<tr><td class="sectionLinks" colspan="2">For <?php echo $_SESSION['sess_user'];?></td></tr>
	<tr><td class="relatedLinks" colspan="2">
<textarea name="todo" cols="0" rows="0" style="width:100%;height:550px;">
<?php
	// w3c complains otherwise...
	$fetch["todo"] = str_replace('<','&lt;', $fetch["todo"]);
	$fetch["todo"] = str_replace('>','&gt;', $fetch["todo"]);
	echo $fetch["todo"]."\n";
?>
</textarea>
	</td></tr>
	<tr>
		<td><div class="infobox">Admins with access to the database can read your text. So think about your privacy.</div></td>
		<td align="right" class="relatedLinks"><input id="todosave" name="todosave" type="submit" value="Save" /></td>
	</tr>
	<tr><td colspan="2" align="right"><?php if(isset($what)):?><div class="infobox saved">Saved <?php echo date("Y-m-d H:i:s");?></div><?php endif;?></td></tr>
</table>
</form>
</body>
</html>
