10<?php
error_reporting(E_ALL+E_STRICT);

require_once('../../db/setting.php');

if (empty($username))
  $db = pg_pconnect("host=$host dbname=$database");
elseif (empty($password))
	$db = pg_pconnect("host=$host dbname=$database user=$username");
else
	$db = pg_pconnect("host=$host dbname=$database user=$username password=$password");

$current_zip = 1;  

$pov = array(); 
$result = pg_query($db,"select * from rc_pov where zip='$current_zip'");
if ($result) while ($row = pg_fetch_array($result, NULL, PGSQL_ASSOC)){
  $www = array();
  foreach ($row as $key => $value)
    $www[$key] = $value;
  $pov[] = $www;
}
print_r($pov);
  
?>12