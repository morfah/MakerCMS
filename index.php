<?php
/*
================
Copyright 2005-2008 Kristian Johansson
All rights reserved.
================
*/
if (!file_exists("includes/install.php") && file_exists("includes/config.php"))
	include("index.theme.php");

else if (file_exists("includes/install.php") && file_exists("includes/config.php"))
	echo "Please rename or remove install.php. Your installation seems to be complete";

else if (file_exists("includes/install.php") && !file_exists("includes/config.php"))
	header("Location: includes/install.php"); // redirect

else if (!file_exists("includes/install.php") && !file_exists("includes/config.php"))
	echo "Can't find install.php or config.php, which is not good";
?>
