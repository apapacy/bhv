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
$kol = str_replace(',', '.', $_REQUEST['kol']);
if ($kol == 0)
	die ('alert("Количество не может быть изменено.")');
$result = pg_query($db,"update prim set kol='$kol', ts=now(), \"user\"='{$_SERVER["PHP_AUTH_USER"]}' where ce='$ce' and kd='$kd'");	
require('render_prim.php');
?>