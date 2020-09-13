<?php
function search($searchword, $section, $charset){
	include "config.php";

	if ($section=="all")
		$sql = "SELECT `content`. * , `sections`.`header` AS sidheader"
        . " FROM content, sections"
        . " WHERE content.sid = sections.sid"
        . " AND content.disabled=0"
        . " AND content.deleted=0"
		. " AND content.url=0"
		. " AND sections.disabled=0"
		. " AND sections.deleted=0"
        . " AND content.context LIKE '%$searchword%' ORDER BY content.sid,content.`order` ASC";
	else
		$sql = "SELECT `content`. * , `sections`.`header` AS sidheader"
        . " FROM content, sections"
        . " WHERE sections.sid = $section"
        . " AND content.sid = sections.sid"
        . " AND content.disabled=0"
        . " AND content.deleted=0"
		. " AND content.url=0"
		. " AND sections.disabled=0"
		. " AND sections.deleted=0"
        . " AND content.context LIKE '%$searchword%' ORDER BY content.sid,content.`order` ASC";
	
	//echo "Search sql = $sql<br>";
	//echo "conn = $conn<br>";
?>
<h3>Search results: <a href="?search=<?php echo space2html($searchword); ?>"><?php echo $searchword ?></a></h3>
<br>
<?php
	$query = mysqli_query($conn, $sql) or die(mysqli_connect_error());
	$hits = 0;
	$sid = 0;
	while ($fetch = mysqli_fetch_array($query)){
		$hits++;
		if ($sid <= $fetch["sid"]){
			if ($sid < $fetch["sid"]){
				if ($sid!=0)
					echo "<br></div>";
				echo "\n<div class=\"searchresult\">";
				echo "\n<strong>".$fetch["sidheader"]."</strong><br>\n";
				$sid = $fetch["sid"];
			}
?>
&bull;&nbsp;&nbsp;<a href="?sid=<?php echo($fetch["sid"]); ?>&amp;p=<?php echo(space2html($fetch["header"])); ?>&amp;hl=<?php echo space2html($searchword); ?>"><?php if (isset($fetch["headline"]) && $fetch["headline"]!="") echo($fetch["headline"]); else echo($fetch["header"]);?></a><br>
<?php	
		}			
	}
	if ($hits>0)
		echo "</div>\n<strong><span class=\"green\" style=\"display:block;clear:both;\">Your search gave $hits hits</span></strong><br>\n";
	else
		echo "<br>\n<strong><span class=\"red\" style=\"display:block;clear:both;\">Your search gave $hits hits</span></strong>\n";
}

function sitemap($section){
	include "config.php";
	
	if (numbervalid($section, 4)){
		$sql = "SELECT `content`. * , `sections`.`header` AS sidheader"
        . " FROM content, sections"
        . " WHERE sections.sid = $section"
        . " AND content.sid = sections.sid"
        . " AND content.disabled=0"
        . " AND content.deleted=0"
		. " AND sections.disabled=0"
		. " AND sections.deleted=0"
        . " ORDER BY content.sid,content.order,content.`order` ASC";
        $section_map=true;
	}
	else
		$sql = "SELECT `content`. * , `sections`.`header` AS sidheader"
        . " FROM content, sections"
        . " WHERE content.sid = sections.sid"
        . " AND content.disabled=0"
        . " AND content.deleted=0"
		. " AND sections.disabled=0"
		. " AND sections.deleted=0"
        . " ORDER BY content.sid,content.order,content.`order` ASC";
	
	//echo "Sitemap sql = $sql<br>";
	//echo "conn = $conn<br>";
	
	$query = mysqli_query($conn, $sql) or die("<strong>A fatal MySQL error occurred</strong>.\n<br>\nError: (" . mysqli_connect_errno() . ") " . mysqli_connect_error());
	$hits = mysqli_num_rows($query);
	$sid = 0;
?>
<h3><?php if (!isset($section_map)):?>Site<?php else:?>Section<?php endif;?> Map (<?php echo $hits ?> pages)</h3>
<br>
<?php
	if ($hits==0)
		echo "<div>&nbsp;";
	while($fetch=mysqli_fetch_array($query)){
		if ($sid <= $fetch["sid"]){
			if ($sid < $fetch["sid"]){
				if ($sid!=0)
					echo "<br></div>";
				echo "\n<div class=\"searchresult\">";
				echo "\n<strong>".$fetch["sidheader"]."</strong><br>\n";
				$sid = $fetch["sid"];
			}
$isurl = $fetch["url"];
if (!$isurl){			
?>
&bull;&nbsp;&nbsp;<a href="?sid=<?php echo($fetch["sid"]); ?>&amp;p=<?php echo(space2html($fetch["header"])); ?>"><?php if (isset($fetch["headline"]) && $fetch["headline"]!="") echo($fetch["headline"]); else echo($fetch["header"]);?></a><br>
<?php
}else{
?>
&bull;&nbsp;&nbsp;<a href="<?php echo($fetch["context"]); ?>" <?php if (isset($fetch["headline"]) && $fetch["headline"]!="") echo("title=\"".$fetch["headline"]."\"");?>><?php echo $fetch["header"]; ?></a><br>
<?php
}

		}
	}
	echo "<br></div>\n<span style=\"display:block;clear:both;\">&nbsp;</span>";
}
?>