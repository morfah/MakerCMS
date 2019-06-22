<?php
$sql = "SELECT * FROM quotes";
$query = mysqli_query($conn, $sql);
$fetch = mysqli_fetch_array($query);
$week = date('W');
$maxtries = 1;
if ($fetch["random"]==1)
	$week = rand(1, 52);

while (!isset($fetch["week".$week]) || $fetch["week".$week]==""){
	$week = rand(1, 52);
	// Avoids endless loop
	if ($maxtries > 20) break;
	$maxtries++;
}

if ($maxtries < 20)
	echo $fetch["week".$week]."\n";
else
	echo "&nbsp;";
// End, Quote of the week
?>