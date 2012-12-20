<?php
$sql = "SELECT sid,header FROM sections WHERE visible=1 AND disabled!=1 AND deleted!=1 ORDER BY `order` ASC";
$query = mysql_query($sql, $conn);
$rows = mysql_num_rows($query);

for ($i=0;$i<$rows;$i++){
$fetch=mysql_fetch_array($query);
	$sid = $fetch["sid"]; $sectionname = $fetch["header"];
	echo "\t<!-- Section: $sectionname - Start -->\n";
	echo "\t<span class=\"section\"><a href=\"?sitemap=$sid\">$sectionname</a></span>\n";
	echo "\t<div class=\"subpages\">\n";

	$sql2 = "SELECT id,sid,`order`,header,menuname,headline,url,context FROM content WHERE sid=".$fetch["sid"]." AND visible=1 AND disabled!=1 AND deleted!=1 ORDER BY `order` ASC, `header` ASC";
	$query2 = mysql_query($sql2, $conn);
	$rows2 = mysql_num_rows($query2);

	for ($i2=0;$i2<$rows2;$i2++){
		$fetch2=mysql_fetch_array($query2);
		$sid = $fetch2["sid"]; $pagename = $fetch2["header"]; $menuname = $fetch2["menuname"]; $headline = $fetch2["headline"]; $p = space2html($pagename); $isurl = $fetch2["url"]; $pageurl = $fetch2["context"];

		if (isset($headline) && $headline!=""){
			$menunamemenu = $headline;
			$pageurltitle = "title=\"$headline\"";
			if (isset($menuname) && $menuname!="")
				$menunamemenu = $menuname;
		}
		else{
			$menunamemenu = $pagename;
			$pageurltitle = "";
		}

		if (!$isurl) echo "\t\t<a href=\"?sid=$sid&amp;p=$p\" $pageurltitle>$menunamemenu</a>\n";
		else echo "\t\t<a href=\"$pageurl\" $pageurltitle>$pagename</a>\n";
	}
		echo "\t</div>\n";
		echo "\t<!-- Section: $sectionname - Stop -->\n";
}
?>