<?php require_once("includes/init.php");?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset?>" />
	<meta name="Author" content="<?php echo $meta_author?>" />
	<meta name="keywords" content="<?php echo $meta_keywords?>" />
	<meta name="description" content="<?php echo $meta_description?>" /> 
	<title><?php echo $title?></title>
	<link rel="stylesheet" href="theme/css/style.css" type="text/css" />
	<link rel="shortcut icon" href="theme/img/affro.ico" type="image/ico" />
	<link rel="icon" href="theme/img/affro.ico" type="image/ico" />
</head>
<body>

<div id="sitehead">
	<a href="<?php echo $url ?>"><img src="theme/img/logo2.png" alt="<?php echo $sitename?>" title="<?php echo $sitename?>" width="344" height="70" id="logo" /></a>
<!--	<div id="tr"><img src="theme/img/topright.jpg" alt="curve" width="50" height="27" /></div>
-->	<form action="?" method="get" title="Search Site" id="searchstyle">
	<p>
		<label for="search">Search site</label>
		<input name="search" id="search" type="text" size="20" maxlength="20"<?php if (isset ($search)){ echo " value=\"$search\"";} ?> />
		<input type="submit" value="Go!" />
	</p>
	</form>
</div>

<div id="quote">
<?php require_once("modules/module_quote.php");?>
</div>

<div id="container">

<div id="leftmenu">
<?php require_once("modules/module_menu.php");?>
</div>

<!--<div id="rightmenu">-->
<!--	<span class="section"><a href="http://nexuiz.affro.net" target="_blank">Our Nexuiz clan</a></span>-->
<!--	<span class="section"><a href="http://www.youtube.com/user/AffroProductions" target="_blank">Our YouTube channel</a></span>-->
<!--	<span class="section irc"><a href="irc://irc.quakenet.org/affro">#affro @ quakenet</a></span>-->
<!--	<span class="section black">Recommended</span>-->
<!--	<div class="subpages">-->
<!--		<a href="http://www.spreadfirefox.com/node&amp;id=42456&amp;t=314"><img border="0" alt="Firefox 3" title="Firefox 3" src="http://sfx-images.mozilla.org/affiliates/Buttons/firefox3/FF3_88x31_b.png"/></a>-->
<!--	</div>-->
<!--	<span class="section black">Valid code</span>-->
<!--	<div class="subpages">-->
<!--		<a href="http://validator.w3.org/check?uri=referer"><img src="theme/img/valid-xhtml10.gif" alt="Valid XHTML 1.0" title="Valid XHTML 1.0" width="80" height="15" /></a>-->
<!--		<a href="http://jigsaw.w3.org/css-validator/check/referer"><img src="theme/img/valid-css.gif" alt="Valid CSS!" title="Valid CSS!" style="margin-top:3px;margin-bottom:3px;" width="80" height="15" /></a>-->
<!--		<a href="http://www.contentquality.com/"><img src="theme/img/valid-sec508.gif" alt="Follows Section 508 guidlines" title="Follows Section 508 guidlines" width="80" height="15" /></a>-->
<!--	</div>-->
<!--</div>-->

<div id="content">
<?php
echo "<!-- Database content, begin -->\n";
if (isset($search)) require_once("modules/module_search.php");
else if (isset($_GET["sitemap"])) require_once("modules/module_sitemap.php");
else if (isset($_GET["wtfomg"])) require_once("modules/module_wtfomg.php");
else if (isset($_GET["news"])) require_once("modules/module_news.php");
else if (isset($_GET["guestbook"])) require_once("modules/module_guestbook.php");
else require_once("modules/module_content.php");
echo "\n<!-- Database content, end -->\n";
?>
</div>
<div id="containerfixer">&nbsp;</div>
</div>

<div id="sitefoot">
<?php
$timeafter = microtime(true); 
$time = $timeafter - $timebefore;
?>
	<!--<img src="theme/img/smalllogo.gif" alt="::" width="23" height="26" />-->
	&nbsp;&copy;&nbsp;2005-<?php echo date("Y");?> <?php echo $sitename?>. <a href="?sitemap=all" title="Shows all pages">Site Map</a><br>
	<?php echo "Server spent ".substr($time,0,5)." seconds loading the page."; ?><br>
	800x600 minimum resolution.
</div>

</body>
</html>
