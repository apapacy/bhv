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

$result = pg_query($db,"select * from co where parent='$kod' and deleted=0");	

if ($result and $row = pg_fetch_array($result) and $row['parent'] == $kod)
	die("alert('Ќевозможно удалить строку верхнего уровн€.');");

$result = pg_query($db,"update co set deleted=1, ts=now(), \"user\"='{$_SERVER["PHP_AUTH_USER"]}' where kod='$kod'");	

$kod = 0;

require('render_co.php');
?>