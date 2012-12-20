<?php
$sql = "SELECT `content` . *"
	. " FROM content , sections"
	. " WHERE content . sid = sections . sid"
	. " AND content . deleted = 0"
	. " AND content . url = 0"
	. " AND content . disabled = 0"
	. " AND sections . deleted = 0"
	. " ORDER BY content.sid,content.id ASC";
$query = mysql_query($sql, $conn);
$rows = mysql_num_rows($query);

for ($i=0;$i<$rows;$i++){
	$fetch=mysql_fetch_array($query);
	$sid = $fetch["sid"]; $id = $fetch["id"]; $headline = $fetch["headline"]; $pagename = $fetch["header"];
	if (isset($headline) && $headline!="") $title = $headline;
  	else $title = $pagename;
	echo "<!-- Sid: $sid Id: $id - Start -->\n";
	echo "<h3>$title</h3>\n".$fetch["context"];

	if(isset($fetch["updatedby_date"])) $updated=$fetch["updatedby_date"];
	else $updated=$fetch["startedby_date"];

	echo "\n<p><br /><span class=\"updated\">Updated $updated</span></p>\n";
	echo "<!-- Sid: $sid Id: $id - Stop -->\n\n";
}
?>