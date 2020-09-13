<?php
session_start(); // Always first
  
// Checks if admin logged in (session set)
if (!isset($_SESSION['sess_user'])){
  header("Location: index.php?sessiontimeout");
  exit;
}

require_once "../includes/config.php"; // Database and Site settings
// charset
$sql = "SELECT global.charset FROM global WHERE global.id=1";
$query = mysqli_query($conn, $sql);
$fetch = mysqli_fetch_array($query);
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "utf-8";

if (isset($_POST["submit_clear"])){
	$sql = "TRUNCATE TABLE `tracker`;";
	//echo $sql;
	mysqli_query($conn, $sql) or die(mysqli_connect_error());	
}
if (isset($_POST["submit_ips"]))
	$_GET["showips"] = $_POST["select_ips"];
if (isset($_POST["submit_urls"]))
	$_GET["showurls"] = $_POST["select_urls"];
if (isset($_POST["submit_referers"]))
	$_GET["showreferers"] = $_POST["select_referers"];

if (!isset($_GET["showips"]))
	if (isset($_COOKIE["showips"]))
		$_GET["showips"] = $_COOKIE["showips"];
	else
		$_GET["showips"] = 10;
if (!isset($_GET["showurls"]))
	if (isset($_COOKIE["showurls"]))
		$_GET["showurls"] = $_COOKIE["showurls"];
	else
		$_GET["showurls"] = 10;
if (!isset($_GET["showreferers"]))
	if (isset($_COOKIE["showreferers"]))
		$_GET["showreferers"] = $_COOKIE["showreferers"];
	else
		$_GET["showreferers"] = 10;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
<title>Statistics</title>
<link rel="stylesheet" href="theme/css/stylemaker.css" type="text/css" />
<script src="javascript/cookies.js" language="javascript" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
<!--
createCookie('showips',<?php echo $_GET["showips"];?>,7);
createCookie('showurls',<?php echo $_GET["showurls"];?>,7);
createCookie('showreferers',<?php echo $_GET["showreferers"]?>,7);
//-->
</script>
</head>
<body>
	<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
		<tr><th colspan="2">Site statistics</th></tr>
		<tr><td class="relatedLinks"><form method="post" action="stats.php"><input type="submit" value="Refresh" /></form></td><td class="relatedLinks"><form method="post" action="" name="clear" onsubmit="javascript:return confirm('Do you really want to remove all the stats?');"><input type="submit" value="Clear all visits" name="submit_clear" /></form></td></tr>

		
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td class="sectionLinks">Visited URLs:</td><td class="sectionLinks">Hits:</td></tr>
<?php
	$sql = "SELECT URL , count(*) FROM tracker GROUP BY URL ORDER BY `count(*)` DESC LIMIT 0,".$_GET["showurls"];
	$result = mysqli_query($conn, $sql);

	while ($row = mysqli_fetch_assoc($result)) {
		$visited_url = $row["URL"];
		$views = $row["count(*)"];
?>
		<tr><td class="relatedLinks"><a href="http://<?php echo $visited_url ?>" target="_blank">http://<?php echo $visited_url ?></a></td>
		<td class="relatedLinks"><?php echo $views ?></td></tr>
<?php
	}

//$exclude = str_ireplace("/","",str_ireplace("http://","",$url));
$exclude = $url;
?>
		<tr><td class="relatedLinks" colspan="2">
		<form method="post" action="">
			Show: 
			<select name="select_urls">
				<option value ="10"<?php if($_GET["showurls"]==10) echo " selected=\"selected\""?>>Top10</option>
				<option value ="20"<?php if($_GET["showurls"]==20) echo " selected=\"selected\""?>>Top20</option>
				<option value ="999999"<?php if($_GET["showurls"]==999999) echo " selected=\"selected\""?>>All</option>
			</select>
			<input type="submit" value="Go" name="submit_urls" />
		</form>
		</td></tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td class="sectionLinks">Referer: <span style="font-weight:normal;">(excluding clicks from root url)</span></td><td class="sectionLinks">Clicks:</td></tr>
<?php
	//$sql = "SELECT referer , count(*) FROM tracker WHERE referer NOT LIKE '%$exclude%' GROUP BY referer ORDER BY `count(*)` DESC LIMIT 0,".$_GET["showreferers"];
	$sql = "SELECT referer , count(*) FROM tracker WHERE referer != '$exclude' GROUP BY referer ORDER BY `count(*)` DESC LIMIT 0,".$_GET["showreferers"];
	$result = mysqli_query($conn, $sql);

	while ($row = mysqli_fetch_assoc($result)) {
		$referer = $row["referer"];
		$views = $row["count(*)"];
?>
		<tr><td class="relatedLinks"><?php if ($referer == "n/a"): echo "(Direct Access / no known referrer)"; else: ?><a href="<?php echo $referer?>"><?php echo $referer?></a><?php endif;?></td>
		<td class="relatedLinks"><?php echo $views ?></td></tr>
<?php
	}
?>
		<tr><td class="relatedLinks" colspan="2">
		<form method="post" action="">
			Show: 
			<select name="select_referers">
				<option value ="10"<?php if($_GET["showreferers"]==10) echo " selected=\"selected\""?>>Top10</option>
				<option value ="20"<?php if($_GET["showreferers"]==20) echo " selected=\"selected\""?>>Top20</option>
				<option value ="999999"<?php if($_GET["showreferers"]==999999) echo " selected=\"selected\""?>>All</option>
			</select>
			<input type="submit" value="Go" name="submit_referers" />
		</form>
		</td></tr>
<?php
	$sql = "SELECT IP FROM tracker GROUP BY IP";
	$result = mysqli_query($conn, $sql);
	$views = mysqli_num_rows($result);
?>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td class="sectionLinks">Visitors IP adresses: <span style="font-weight:normal;">(<?php echo $views ?> unique IPs found)</span></td><td class="sectionLinks">Hits:</td></tr>
<?php
	$sql = "SELECT IP, count(*) FROM tracker GROUP BY IP ORDER BY `count(*)` DESC LIMIT 0,".$_GET["showips"];
	$result = mysqli_query($conn, $sql);

	while ($row = mysqli_fetch_assoc($result)) {
		$IP = $row["IP"];
		$views = $row["count(*)"];
?>
		<tr><td class="relatedLinks"><a href="stats_ip.php?ip=<?php echo $IP ?>"><?php echo $IP ?></a></td><td class="relatedLinks"><?php echo $views ?></td></tr>
<?php
	}
?>
		<tr><td class="relatedLinks" colspan="2">
		<form method="post" action="">
			Show: 
			<select name="select_ips">
				<option value ="10"<?php if($_GET["showips"]==10) echo " selected=\"selected\""?>>Top10</option>
				<option value ="20"<?php if($_GET["showips"]==20) echo " selected=\"selected\""?>>Top20</option>
				<option value ="999999"<?php if($_GET["showips"]==999999) echo " selected=\"selected\""?>>All</option>
			</select>
			<input type="submit" value="Go" name="submit_ips" />
		</form>
		</td></tr>
	</table>
</body>
</html>