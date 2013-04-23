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

$kod = $_REQUEST['kod'];
$type = $_REQUEST['type'];
$co = $_REQUEST['co'];
$k = str_replace(',', '.',$_REQUEST['k']);
$part = str_replace(',', '.',$_REQUEST['part']);
//$fullname = pg_escape_string(iconv("UTF-8", "windows-1251",$_REQUEST['fullname']));
//$mat = pg_escape_string(iconv("UTF-8", "windows-1251",$_REQUEST['mat']));
//$kol = str_replace(',', '.', $_REQUEST['kol']);

$result = pg_query($db,"insert into pdb_nomen_co (kod, type, co, k, part) values ($kod, $type, $co, $k, $part)");	

require('render_nomen.php');
?>