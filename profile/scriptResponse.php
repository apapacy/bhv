<?php 
header('Content-Type: text/javascript');
echo "{$_GET['var']}='". md5(rand())."'";
?>
