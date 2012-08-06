<?php
include("../etc/config.php");
$lines = $_GET["lines"] ? $_GET["lines"] : 10; 
$follow = $_GET["follow"] ? "-f" : ""; 
$filter = $_GET["filter"] ? '| grep '.$_GET["filter"] : ""; 
$handle = popen("tail $follow -n $lines $error_log_mokodesk $filter 2>&1", 'r');
while(!feof($handle)) {
    $buffer = fgets($handle);
	$buffer = preg_replace("/(\s\d+:\d+:\d+\s)/iU", "<font color=\"#FF0000\">$1</font>", $buffer);
	$buffer = preg_replace("/(error)/iU", "<font color=\"#FF0000\">$1</font>", $buffer);
	$buffer = preg_replace("/(\[\w+\])/iU", "<font color=\"#FF0000\">$1</font>", $buffer);
	$buffer = preg_replace("/(\[[^\]]+\])/iU", "<font color=\"#CCCBBB\">$1</font>", $buffer);
	$buffer = preg_replace("/([^\/].php\(\d+\))/iU", "<font color=\"#DD0055\">$1</font>", $buffer);
	$buffer = preg_replace("/([^\/]+ on line \d+?)/iU", "<font color=\"#DD0055\">$1</font>", $buffer);
    $output_string = "$buffer<br>\n$output_string";
    ob_flush();
    flush();
}
print $output_string;
pclose($handle);
?>