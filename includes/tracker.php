<?php
	//require("config.php");
	
	$this_url = $_SERVER['HTTP_HOST'];
	if (isset($_SERVER['REQUEST_URI']))
		$this_url .= $_SERVER['REQUEST_URI'];
	$this_url = htmlentities($this_url,ENT_QUOTES);
		
	if (isset($_SERVER["REMOTE_ADDR"]))
		$IP = htmlentities($_SERVER["REMOTE_ADDR"],ENT_QUOTES);
	else $IP = "n/a";
	
	if (isset($_SERVER["HTTP_USER_AGENT"]))
		$browser = htmlentities($_SERVER["HTTP_USER_AGENT"],ENT_QUOTES);
	else $browser = "n/a";
	
	if (isset($_SERVER["HTTP_REFERER"]))
		$referer = htmlentities($_SERVER["HTTP_REFERER"],ENT_QUOTES);
	else $referer = "n/a";
	
	$date_auto = time();
	
	$sql = "INSERT INTO tracker (IP, URL, browser, referer, date_auto) VALUES ('$IP', '$this_url', '$browser', '$referer', '$date_auto')";
	@mysql_query($sql, $conn) or die("<b>A fatal MySQL error occurred</b>.\n<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	unset($this_url, $IP, $browser, $referer, $date_auto,$sql);
?>