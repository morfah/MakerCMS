<?php
function valid($str, $minln, $validlength) {
	$validmask="abcdefghijklmnopqrstuvwxyz0123456789_- ";
	$str=strtolower($str);
	if (strspn($str, $validmask) == strlen($str) && $validlength >= strlen($str) && $minln <= strlen($str))
		return true;
	else
		return false;
}

function emailvalid($str, $validlength) {
	$validmask="abcdefghijklmnopqrstuvwxyz0123456789_-@.";
	$str=strtolower($str);
	if (strspn($str, $validmask) == strlen($str) && $validlength >= strlen($str) && strpos($str, '@',1) && strpos($str, '.',1))
		return true;
	else
		return false;
}

function numbervalid($str, $validlength) {
	$validmask="0123456789";
	if (strspn($str, $validmask) == strlen($str) && strlen($str) <= $validlength && strlen($str) > 0)
		return true;
	else
		return false;
}

function space2html($str) {
	$str = str_replace(' ','+', $str);
	return $str;
}
?>