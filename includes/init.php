<?php
$timebefore = microtime(true);

require_once "includes/config.php";
require_once "includes/validate.php";
require_once "includes/tracker.php";

// global settings
$sql = "SELECT * FROM `global` WHERE `global`.`id` = 1";
$query = mysql_query($sql, $conn);
$fetch = mysql_fetch_array($query);
if ($fetch["timezone"]!="") date_default_timezone_set($fetch["timezone"]);
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "UTF-8";
$meta_author = $fetch["meta_author"];
$meta_keywords = $fetch["meta_keywords"];
$meta_description = $fetch["meta_description"];
$title = $sitename;
unset($sql);

if(empty($_GET['sid']))
	$_GET['sid'] = 1; // Default section
if(empty($_GET['id']))
	$_GET['id'] = 1; // Default page
if (!empty($_GET['p']))
	$page = htmlentities($_GET['p'],ENT_QUOTES,$charset);

if (isset($_GET["news"]))
	$title .= " - News";
else if (isset($_GET["search"])){
	$search = htmlentities(stripslashes(strip_tags($_GET["search"])),ENT_QUOTES,$charset);
	$title .= " - Searching for " . $search;
}
else if (isset($_GET["sitemap"]))
	$title = "Site Map";
else{
	if (numbervalid($_GET['sid'], 4) && isset($page)){
		$sql = "SELECT `content` . * "
			. " FROM content , sections "
			. " WHERE content . sid = sections . sid "
			. " AND content . sid = " . $_GET['sid']
			. " AND content . header = '$page'"
			. " AND content . url = 0"
			. " AND content . deleted = 0"
			. " AND content . disabled = 0"
			. " AND sections . disabled = 0"
			. " AND sections . deleted = 0"
			. " LIMIT 0,1";
	}
	elseif (numbervalid($_GET['sid'], 4) && numbervalid($_GET['id'], 4)){
		$sql = "SELECT `content` . * "
			. " FROM content , sections "
			. " WHERE content . sid = sections . sid "
			. " AND content . sid = " . $_GET['sid']
			. " AND content . id = " . $_GET['id']
			. " AND content . url = 0"
			. " AND content . deleted = 0"
			. " AND content . disabled = 0"
			. " AND sections . disabled = 0"
			. " AND sections . deleted = 0"
			. " LIMIT 0,1";
	}
	//echo $sql;
	$query = mysql_query($sql, $conn);
	
	// NOT FOUND? If so, show our 404 page
	if (@mysql_num_rows($query)==0){
		$sql = "SELECT * FROM content WHERE sid=1 AND header='404'";
		header("HTTP/1.1 404 Not Found");
		$query = mysql_query($sql, $conn);
	}
	
	$db = @mysql_fetch_array($query) or die("<span style=\"color:red;\">Could not fetch from database.<br/>".mysql_error()."</span>");
	if (isset($db["headline"]) && $db["headline"]!="") $title .= " - ".$db["headline"];
  	else $title .= " - ".$db["header"]; 
  	
	if (isset($_GET["hl"])) {
		$hl = htmlentities(stripslashes(strip_tags($_GET["hl"])),ENT_QUOTES,$charset);
		$title .= " - Highlighting ".$hl;
	}
}
?>