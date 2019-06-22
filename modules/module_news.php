<?php
$sql = 'SELECT news.*, maker.username AS authorname'
	. ' FROM news, maker'
	. ' WHERE news.author = maker.id'
	. ' AND news.deleted!=1'
	. ' ORDER BY news.id DESC';
$query = mysqli_query($conn, $sql);
$rows = mysqli_num_rows($query);

for ($i=0;$i<$rows;$i++){
	$db=mysqli_fetch_array($query);
	$id = $db["id"]; $headline = $db["headline"]; $news = $db["news"]; 
	echo "<!-- News id: $id - Start -->\n";
	echo "<div class=\"newscontainer\">\n";
	echo "<div class=\"newsheader\">$headline</div>\n";
	echo "<div class=\"newsrow\">\n$news\n";
	echo "<span class=\"newsauthor\"><br />Written: ".date("Y-m-d H:i:s", $db["time"]);
	if ($db["time"]!=$db["updatedtime"])
		echo "<br />Updated: ".date("Y-m-d H:i:s", $db["updatedtime"]);
	echo "</span>\n</div>\n";
	echo "</div>\n<p>&nbsp;</p>\n";
	echo "<!-- News id: $id - Stop -->\n\n";
}
?>