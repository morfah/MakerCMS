<?php
if ((strlen($search) <= 20) && (strlen($search) > 0)) {
	// Include search functions
	include "includes/search_and_sitemap.php";
	search($search,"all",$charset);
}
else
	echo "<h3>Error your search was not valid.</h3><br>Search criterea: 1-20 characters.";
?>