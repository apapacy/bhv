<?php 	
$host = 'localhost';
	$database = 'proba';
	$username = 'root';
	$password = '26682316'; 

if (empty($username))
    $db = pg_pconnect("host=$host dbname=$database");
elseif (empty($password))
	$db = pg_pconnect("host=$host dbname=$database user=$username");
else
	$db = pg_pconnect("host=$host dbname=$database user=$username password=$password");


?>
