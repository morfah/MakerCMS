<?php
	$version = file_get_contents("../maker/.version");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Affro CMS v<?php echo $version?></title>
<link rel="stylesheet" type="text/css" href="../maker/theme/css/stylemaker.css" />
<style type="text/css">
<!--
body {
	background-color: #FFFFFF;
	margin: 70px;
	text-align:center;
}
-->
</style>
</head>

<body>
<?php if (isset($_POST["install"])) {?>
<strong>Installation initiated.</strong><br /><br />

<?php
if (isset($_POST["url"])) $url = $_POST["url"];
if (isset($_POST["sitename"])) $sitename = $_POST["sitename"];
if (isset($_POST["mysqlserver"])) $mysql_server = $_POST["mysqlserver"];
if (isset($_POST["mysqluser"])) $mysql_user = $_POST["mysqluser"];
if (isset($_POST["mysqlpass"])) $mysql_password = $_POST["mysqlpass"];
if (isset($_POST["mysqldatabase"])) $mysql_database = $_POST["mysqldatabase"];
$date = date("Y-m-d H:i:s");

$config =  "<?php\n";
$config .= "// Page settings\n";
$config .= "\$url=\"" . $url . "\";\n";
$config .= "\$sitename=\"" . $sitename . "\";\n";
$config .= "\n";
$config .= "// Mysql settings\n";
$config .= "\$mysql_server = \"" . $mysql_server . "\";\n";
$config .= "\$mysql_user = \"" . $mysql_user . "\";\n";
$config .= "\$mysql_password = \"" . $mysql_password . "\";\n";
$config .= "\$mysql_database = \"" . $mysql_database . "\";\n";
$config .= "\n";
$config .= "\$conn = mysql_connect(\$mysql_server, \$mysql_user, \$mysql_password);\n";
$config .= "mysql_select_db(\$mysql_database, \$conn) or die(\"<br /><span style=\\\"color:red\\\">Could not connect to database and/or table. Please check \\\"includes\\\\config.php\\\"</span><br /><br /><b>A fatal MySQL error occurred</b>.\\n<br />\\nError: (\" . mysql_errno() . \") \" . mysql_error());\n";
$config .= "unset(\$mysql_server,\$mysql_user,\$mysql_password);\n";
$config .= "?>";

$fp = fopen("config.php", "w") or die("<span style=\"color:red;background-color:inherit;\">[FAIL]</span> Could not create config file. Check the permissions on the \"includes\" folder.");
fwrite($fp, $config);
fclose($fp);
?>
<span style="color:green;background-color:inherit;">[OK]</span> Succesfully created config file.<br />
<?php
if (isset($_POST["adminname"])) $adminname = $_POST["adminname"];
if (isset($_POST["adminpassword"])) $adminpassword = $_POST["adminpassword"];
if (isset($_POST["timezone"])) $timezone = $_POST["timezone"];
$table_error_message = "<span style=\"color:red;background-color:inherit;\">[FAIL]</span> Could not add necessary data to database!<br /><br /><b>A fatal MySQL error occurred</b>.\n<br />\n";
	require_once "config.php";
	$sqlErrorText = '';
	$sqlErrorCode = 0;
	$sqlStmt = '';
	$sqlFileToExecute = "emptydb.sql";
	if ($conn !== false){
		// Load and explode the sql file
		$f = fopen($sqlFileToExecute,"r");
		$sqlFile = fread($f,filesize($sqlFileToExecute));
		$sqlArray = explode(';',$sqlFile);

		//Process the sql file by statements
		foreach ($sqlArray as $stmt) {
		if (strlen($stmt)>3){
				$result = mysql_query($stmt);
				if (!$result){
					$sqlErrorCode = mysql_errno();
					$sqlErrorText = mysql_error();
					$sqlStmt      = $stmt;
					break;
				}
			}
		}
	}

	if ($sqlErrorCode == 0){
		echo "<span style=\"color:green;background-color:inherit;\">[OK]</span> Succesfully installed database tables.<br />";
		//Adding necessary data to database.
		//Global site settings
		mysql_query("INSERT INTO `".$mysql_database."`.`global` (`id`, `version`, `site_url`, `site_name`, `meta_author`, `meta_description`, `meta_keywords`, `charset`, `timezone`) VALUES ('1', '".$version."', '".$url."', '".$sitename."', '', '', '', '', '".$timezone."');") or die ($table_error_message . "Error: (" . mysql_errno() . ") " . mysql_error());
		//First section
		mysql_query("INSERT INTO `".$mysql_database."`.`sections` (`sid`, `header`, `startedby`, `startedby_date`, `updatedby`, `updatedby_date`, `order`, `visible`, `disabled`, `deleted`) VALUES ('1', 'A section', '1', '".$date."', NULL, NULL, '1', '1', '0', '0');") or die ($table_error_message . "Error: (" . mysql_errno() . ") " . mysql_error());
		//First page
		mysql_query("INSERT INTO `".$mysql_database."`.`content` (`id`, `sid`, `order`, `header`, `menuname`, `headline`, `context`, `startedby`, `startedby_date`, `updatedby`, `updatedby_date`, `url`, `visible`, `disabled`, `deleted`) VALUES ('1', '1', '1', 'A page', NULL, 'Test page', '<p>First page!</p>', '1', '".$date."', NULL, NULL, '0', '1', '0', '0');") or die ($table_error_message . "Error: (" . mysql_errno() . ") " . mysql_error());
		//404 page
		mysql_query("INSERT INTO `".$mysql_database."`.`content` (`id`, `sid`, `order`, `header`, `menuname`, `headline`, `context`, `startedby`, `startedby_date`, `updatedby`, `updatedby_date`, `url`, `visible`, `disabled`, `deleted`) VALUES ('2', '1', '2', '404', NULL, 'Not Found', '<p>The page might have been removed, renamed or deleted.</p>', '1', '".$date."', NULL, NULL, '0', '0', '1', '0');") or die ($table_error_message . "Error: (" . mysql_errno() . ") " . mysql_error());
		//First user
		mysql_query("INSERT INTO `".$mysql_database."`.`maker` (`id`, `username`, `password`, `last_login`, `todo`) VALUES ('1', '".$adminname."', MD5('".$adminpassword."'), NULL, 'Here you can write your own notes.');") or die ($table_error_message . "Error: (" . mysql_errno() . ") " . mysql_error());
		//Make him/her headadmin
		mysql_query("INSERT INTO `".$mysql_database."`.`permissions` (`uid`, `sid`, `permissions`) VALUES ('1', '0', '1');") or die ($table_error_message . "Error: (" . mysql_errno() . ") " . mysql_error());
		mysql_query("INSERT INTO `".$mysql_database."`.`permissions_extra` (`id`, `name`) VALUES ('1', 'Headadmin');") or die ($table_error_message . "Error: (" . mysql_errno() . ") " . mysql_error());
		//Just add a quote, or else stuff breaks
		mysql_query("INSERT INTO `".$mysql_database."`.`quotes` (`week01`, `week02`, `week03`, `week04`, `week05`, `week06`, `week07`, `week08`, `week09`, `week10`, `week11`, `week12`, `week13`, `week14`, `week15`, `week16`, `week17`, `week18`, `week19`, `week20`, `week21`, `week22`, `week23`, `week24`, `week25`, `week26`, `week27`, `week28`, `week29`, `week30`, `week31`, `week32`, `week33`, `week34`, `week35`, `week36`, `week37`, `week38`, `week39`, `week40`, `week41`, `week42`, `week43`, `week44`, `week45`, `week46`, `week47`, `week48`, `week49`, `week50`, `week51`, `week52`, `random`) VALUES ('test', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0');") or die ($table_error_message . "Error: (" . mysql_errno() . ") " . mysql_error());
		// ALL DONE
		echo "<span style=\"color:green;background-color:inherit;\">[OK]</span> Added necessary data to database.<br />";
		echo "<br /><strong>Done!</strong> Don't forget to delete or rename install.php<br /><br /><a href=\"".$url."\">Visit your new website!</a>";
		} else {
		echo "<span style=\"color:red;background-color:inherit;\">[FAIL]</span> An error occured during installation!<br /><br />";
		echo "Error code: $sqlErrorCode <br/>";
		echo "Error text: $sqlErrorText <br/>";
		echo "Statement:<br/> $sqlStmt <br/>";
	}

} elseif (!file_exists("config.php")) {
?>
	<form action="install.php" method="post">
	<table align="center" border="0" cellspacing="0" cellpadding="0">
	<tr><th colspan="2">Welcome to the installation of Affro CMS</th></tr>
	<tr><td class="sectionLinks" colspan="2">Site settings</td></tr>
	<tr>
		<td class="relatedLinks">Site name:</td>
		<td class="relatedLinks"><input name="sitename" type="text" id="sitename" tabindex="1" style="width:200px" /></td>
	</tr>
	<tr>
		<td class="relatedLinks">Root URL:</td>
		<td class="relatedLinks"><input name="url" type="text" id="url" tabindex="2" style="width:200px" /></td>
	</tr>
	<tr>
		<td class="relatedLinks">Timezone:</td>
		<td class="relatedLinks">
		<select name="timezone">
<?php
		$timezone_identifiers = DateTimeZone::listIdentifiers();
		for($i=0;$i<count($timezone_identifiers);$i++) {
?>
			<option value="<?php echo $timezone_identifiers[$i];?>"<?php if ($timezone_identifiers[$i] == date_default_timezone_get()) echo " selected=\"selected\"";?>><?php echo $timezone_identifiers[$i]; ?></option>
<?php
		}
?>
		</select></td>
	</tr>
	<tr>
		<td class="sectionLinks" colspan="2">Database settings</td>
	</tr>
	<tr>
		<td class="relatedLinks">Mysql server (e.g localhost):</td>
		<td class="relatedLinks"><input name="mysqlserver" type="text" id="mysqlserver" tabindex="3" style="width:200px" /></td>
	</tr>
	<tr>
		<td class="relatedLinks">Mysql database:<br />
			(Must exist and should be empty)</td>
		<td class="relatedLinks"><input name="mysqldatabase" type="text" id="mysqldatabase" tabindex="3" style="width:200px" /></td>
	</tr>
	<tr>
		<td class="relatedLinks">Mysql username: (SELECT, INSERT, DELETE, UPDATE)</td>
		<td class="relatedLinks"><input name="mysqluser" type="text" id="mysqluser" tabindex="4" style="width:200px" /></td>
	</tr>
	<tr>
		<td class="relatedLinks">Mysql password:</td>
		<td class="relatedLinks"><input name="mysqlpass" type="password" id="mysqlpass" tabindex="5" style="width:200px" /></td>
	</tr>

	<tr><td class="sectionLinks" colspan="2">First user</td></tr>

	<tr>
		<td class="relatedLinks">Choose Username:</td>
		<td class="relatedLinks"><input name="adminname" type="text" id="adminname" tabindex="6" style="width:200px" /></td>
	</tr>
	<tr>
		<td class="relatedLinks">And Password:</td>
		<td class="relatedLinks"><input name="adminpassword" type="password" id="adminpassword" tabindex="7" style="width:200px" /></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2" align="right"><input name="install" type="submit" id="install" tabindex="10" value="Install" /></td></tr>
	</table>
	</form>
<?php
}
else{
?>
	<span style="color:red;background-color:inherit;">[Fail]</span> Your site seems to be installed already. Found config.php.
<?php
}
?>
</body>
</html>
