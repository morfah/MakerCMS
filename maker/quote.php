<?php
session_start(); // Always first
  
// Checks if admin logged in (session set)
if (!isset($_SESSION['sess_user'])){
  header("Location: index.php?sessiontimeout");
  exit;
}

require_once "../includes/config.php"; // Database and Site settings
//require_once "../includes/validate.php"; // Validation functions
// timezone and charset
$sql = "SELECT timezone,charset FROM `global` WHERE global.id=1";
$query = mysqli_query($conn, $sql);
$fetch = mysqli_fetch_array($query);
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "utf-8";
if ($fetch["timezone"]!="") date_default_timezone_set($fetch["timezone"]);

// Saving.
if (isset($_POST["save"])){
	if (isset ($_POST['random'])) $random = $_POST['random'];
	else $random = '0';
	
	for ($i=1;$i<=52;$i++){
		if (strlen($i)==1) $i = "0".$i;
		if (isset ($_POST['week'.$i])) $week[$i] = htmlentities($_POST['week'.$i],ENT_QUOTES,$charset);
		else $week[$i] = '';
	}
	
	// Update all text fields
	$sql = "UPDATE quotes SET random=".$random." ";
	for ($i=1;$i<=52;$i++){
		if (strlen($i)==1)
			$i = "0".$i;
		$sql .= ", week".$i."='".$week[$i]."'";
	}
	@mysqli_query($conn, $sql) or die("<strong>A fatal MySQL error occurred</strong>.\n<br>\nError: (" . mysqli_connect_errno() . ") " . mysqli_connect_error());
	$what = "ok";
}

$sql = "SELECT * FROM quotes";
$query = mysqli_query($conn, $sql);
$datas = mysqli_fetch_array($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
<title>Quote of the week</title>
<link rel="stylesheet" href="theme/css/stylemaker.css" type="text/css" />
</head>
<body>
<form action="quote.php" method="post">
<table border="0" cellspacing="0" cellpadding="0" style="width:100%;">
	<tr><th colspan="2">Quote of the week!</th></tr>
	<tr><td class="sectionLinks">Week:</td><td class="sectionLinks">Quote:</td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
<?php
$veckonr = date('W');
for ($loop=1; $loop<=52; $loop++){ 
if (strlen($loop)==1)
	$loop = "0".$loop;
?>

	<tr>
		<td class="relatedLinks" style="width:10px;"><?php if ($veckonr == $loop){ echo "<strong>".$loop."</strong>";} else{ echo $loop; } ?></td>
		<td class="relatedLinks"><input name="week<?php echo $loop;?>" type="text" value="<?php echo $datas["week"."$loop"]?>" style="width:100%;" /></td>
	</tr>
<?php
}
?>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2">Use Random quotes, instead of week: <input name="random" type="checkbox" value="1" <?php if ($datas["random"]==1) { ?>checked="checked"<?php } ?>/></td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr align="right"><td colspan="2"><input name="save" id="save" type="submit" value="Save" /></td></tr>
	<tr><td colspan="2" align="right">
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
</body>
</html>
