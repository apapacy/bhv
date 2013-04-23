<?php
error_reporting(E_ALL+E_STRICT);

require_once('../../../db/setting.php');

if (empty($username))
    $db = pg_pconnect("host=$host dbname=$database");
elseif (empty($password))
	$db = pg_pconnect("host=$host dbname=$database user=$username");
else
	$db = pg_pconnect("host=$host dbname=$database user=$username password=$password");

$parent = $_REQUEST['parent'];
$kod = $_REQUEST['kod'];
$kol = str_replace(',', '.', $_REQUEST['kol']);
$fef = str_replace(',', '.', $_REQUEST['fef']);

$result = pg_query($db,"update co set kol='$kol', fef='$fef', ts=now(), \"user\"='{$_SERVER["PHP_AUTH_USER"]}' where kod='$kod'");	
require('render_co.php');
?>