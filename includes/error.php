<?php
function errorpage($errorcode, $scriptfile, $extra){
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Error</title>
<style type="text/css">
body,td,th {
	font-family: Courier New, Courier, monospace;
	font-size: 12px;
	color: #000000;
}
body {
	background-color: #FFF;
	/*background: #FFF url("../theme/img/errorbg.png") repeat-x;*/
	margin: 40px auto;
}
.errorbox{border:#CC3333 5px solid;width:800px;margin: 40px auto;}
.header{color:#FFFFFF; background-color:#CC3333; font-size:16px; font-weight:bold; padding-left:5px; padding-bottom:5px;}
.explaination{color:inherit;background-color:#FFF;padding:5px; border-bottom:#000000 1px dashed;}
.solution{color:inherit;background-color:#FFF;padding:5px;}

</style>
</head>

<body>
<?php if ($errorcode == 1001){ ?>
<div class="errorbox">
<div class="header">Error: Missing file. Inside: <?php echo $scriptfile ?>.</div>
<div class="explaination">Could not find &quot;<?php echo $extra ?>&quot;</div>
<div class="solution">
	Troubleshooting:
	<ul>
		<li>Have you installed Maker CMS correctly?</li>
	</ul>
</div>
</div>
<?php } else if ($errorcode == 1002){ ?>
<div class="errorbox">
<div class="header">Error code: 1002. Inside: <?php echo $scriptfile ?>.</div>
<div class="explaination">exp</div>
<div class="solution">
	Troubleshooting:
		<ul>
		<li>thing?</li>
	</ul>
</div>
</div>
<?php } else { ?>
<div class="errorbox">
<div class="header">Unexpected Error Inside: <?php echo $scriptfile ?>.</div>
<div class="explaination">Unexpected Error</div>
<div class="solution">
	Troubleshooting:
		<ul>
		<li>Contanct xxx</li>
	</ul>
</div>
</div>
<?php }?>
</body>
</html>
<?php }?>
