<?php
$title = substr($title, (strlen($sitename)+3));
echo "<h3>$title</h3>\n";
if (isset($hl) && (strlen($hl) <= 20) && (strlen($hl) > 0))
	$db["context"] = str_ireplace($hl, "<span class=\"highlight\">$hl</span>", $db["context"]);
echo $db["context"];

if(isset($db["updatedby_date"])) $updated=$db["updatedby_date"];
else $updated=$db["startedby_date"];

echo "\n<p><br><span class=\"updated\">Updated $updated</span></p>";
?>