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
	@mysqli_query($conn, $sql) or die("<strong>A fatal MySQL error occurred</strong>.\n<br>\nError: (" . mysqli_connect_errno() . ") " . mysqli_connect_error());
	unset($this_url, $IP, $browser, $referer, $date_auto,$sql);
?>