<?php
session_start(); // Always first
  
// Checks if admin logged in (session set)
if (!isset($_SESSION['sess_user'])){
  header("Location: index.php?sessiontimeout");
  exit;
}

require_once "../includes/config.php"; // Database and Site settings
// timezone and charset
$sql = "SELECT timezone,charset FROM `global` WHERE global.id=1";
$query = mysql_query($sql, $conn);
$fetch = mysql_fetch_array($query);
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "utf-8";
if ($fetch["timezone"]!="") date_default_timezone_set($fetch["timezone"]);

$cur_IP = $_GET['ip'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
<title>Statistics for ip: <?php echo $cur_IP ?></title>
<link rel="stylesheet" href="theme/css/stylemaker.css" type="text/css" />
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
	<tr><th colspan="4">Pages visited by <?php echo $cur_IP ?> (<?php echo gethostbyaddr($cur_IP)?>)</th></tr>
	<tr><td colspan="2" class="relatedLinks" align="left"><form method="post" action="stats_ip.php?ip=<?php echo $cur_IP ?>"><input type="submit" value="Refresh" /></form></td><td colspan="2" class="relatedLinks" align="right"><form method="post" action="stats.php"><input type="submit" value="Back to Stats" /></form></td></tr>
	<tr><td colspan="4">&nbsp;</td></tr>
	<tr><td class="sectionLinks">Page:</td><td class="sectionLinks">Browser:</td><td class="sectionLinks">Referer:</td><td class="sectionLinks">Date and Time <span style="font-weight:normal;">(Timezone: <?php echo date_default_timezone_get();?>):</span></td></tr>

<?php
	$sql = "SELECT * FROM tracker WHERE IP = '$cur_IP' ORDER BY date_auto DESC";
	$result = mysql_query($sql, $conn);
	for ($i = 0; $i < mysql_num_rows($result); $i++){
		$url = mysql_result($result, $i, "URL");
		$date_auto = mysql_result($result, $i, "date_auto");
		$date = date("Y-m-d H:i:s", $date_auto);
		$browser = mysql_result($result, $i, "browser");
		$referer = mysql_result($result, $i, "referer");
?>
		<tr><td class="relatedLinks"><a href="http://<?php echo $url ?>" target="_blank">http://<?php echo $url ?></a></td>
		<td class="relatedLinks"><?php echo $browser ?></td><td class="relatedLinks"><?php if ($referer != "n/a"){?><a href="<?php echo $referer ?>" target="_blank"><?php echo $referer ?></a><?php }else{?>n/a<?php }?></td><td class="relatedLinks"><?php echo $date ?></td></tr>
<?php
	}
?>
</table>
</body>
</html>