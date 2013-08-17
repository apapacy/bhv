<?php header('Content-type: text/html; charset="windows-1251"');?>
<?php
error_reporting(E_ALL+E_STRICT);
require_once('../../../db/setting.php');

if (empty($username))
    $db = pg_pconnect("host=$host dbname=$database");
elseif (empty($password))
	$db = pg_pconnect("host=$host dbname=$database user=$username");
else
	$db = pg_pconnect("host=$host dbname=$database user=$username password=$password");

	
	
	
//$name = pg_escape_string(iconv("UTF-8", "windows-1251", $_REQUEST['name']));
$name = pg_escape_string($_REQUEST['name']);
	
$result = @pg_query($db,"insert into mat (name) values ('$name')");	

?>