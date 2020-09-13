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
	@mysqli_query($conn, $sql) or die("error while transfering");

	$what = "saved";
}

$sql = "SELECT maker.todo,global.charset FROM maker,global WHERE global.id=1 AND maker.id=".$_SESSION['sess_id'];
$query = mysqli_query($conn, $sql);
$fetch = mysqli_fetch_array($query);
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "utf-8";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
<title>TODO List for <?php echo $_SESSION['sess_user'];?></title>
<link rel="stylesheet" href="theme/css/stylemaker.css" type="text/css" />
<script language="javascript" type="text/javascript" src="javascript/tinymce/tinymce.min.js"></script>
<script language="javascript" type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "textareas",
		element_format : 'html',
		theme : "silver",
		plugins : ['autolink', 'lists', 'spellchecker', 'pagebreak', 'table', 'save', 'insertdatetime', 'preview', 'media', 'searchreplace', 'print', 'paste', 'directionality', 'fullscreen', 'noneditable', 'visualchars', 'nonbreaking', 'template', 'code'],

		document_base_url : "<?php echo $url ?>",
		convert_urls : false,
		
		// a html5 tag that randompolygons.com uses
		extended_valid_elements : "aside",

		// Skin options
		skin : "oxide"
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
