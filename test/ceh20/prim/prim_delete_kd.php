<?php
error_reporting(E_ALL+E_STRICT);

require_once('../../../db/setting.php');

if (empty($username))
    $db = pg_pconnect("host=$host dbname=$database");
elseif (empty($password))
	$db = pg_pconnect("host=$host dbname=$database user=$username");
else
	$db = pg_pconnect("host=$host dbname=$database user=$username password=$password");

$ce = $_REQUEST['ce'];
$kd = $_REQUEST['kd'];

$result = pg_query($db,"update prim set kol=0, ts=now(), \"user\"='{$_SERVER["PHP_AUTH_USER"]}' where ce='$ce' and kd='$kd'");	

$kd = 0;

require('render_prim.php');
?>