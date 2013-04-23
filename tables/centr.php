<?php


header('Content-type: text/javascript; charset="UTF-8"?');
require_once('../bhv/errorhandler.php');
error_reporting(E_ALL | E_STRICT);
require_once('../db/setting.php');

$result = pg_query($db, 'select * from centr where "parent" = 0 order by name');
while ($result and $row = pg_fetch_array($result)) {
	$cur_god = $row['god'];
	$cur_mes = $row['mes'];
}





?>