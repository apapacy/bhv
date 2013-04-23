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

if ($ce == $kd || $kol == 0)
	die ('alert("Входящая не может быть добавлена. ")');
	
	
$ar_from = array(array(array($kd, $kd, $kd, 0, false, 1)));
$ar_to = array(array());
$is_end = false;
$max_step = 0;


while (!$is_end){
	$is_end = true;	
	foreach ($ar_from[0] as $ar_from_row){
		$ar_to[0][]= array($kd, $ar_from_row[1], $ar_from_row[2], $ar_from_row[3], true, $ar_from_row[5]);
		if (! $ar_from_row[4]){
			if ($ar_from_row[3] + 1 > $max_step)
				$max_step = $ar_from_row[3] + 1;
			if ($max_step < 100)
				$is_end = false;
			$result = pg_query($db,"select * from prim where kol<>0 and ce='${ar_from_row[2]}'");	
			if ($result) 
				while ($row = pg_fetch_array($result)){
					if ($row['kd'] == $ce)
						die ('alert("Входящая не может быть добавлена (цикличность).")');					
					$ar_to[0][]= array($kd, $ar_from_row[2], $row['kd'], $ar_from_row[3] + 1, false, $ar_from_row[5] * $row['kol']);
				}
		}
	}
	$ar_from = $ar_to;
	$ar_to=array(array());
}

$result = pg_query($db,"select * from prim where ce='$ce' and kd='$kd'");	
if ($result and $row = pg_fetch_array($result)){
		if ($row['kol'] != 0)
			die('alert("Такая входящая уже существует")');
		else
			$result = pg_query($db,"update prim set kol='$kol', ts=now(), \"user\"='{$_SERVER["PHP_AUTH_USER"]}' where ce='$ce' and kd='$kd'");	
}else{
	$result = pg_query($db,"insert into prim (ce,kd,kol,\"user\") values ('$ce', '$kd', '$kol', '{$_SERVER["PHP_AUTH_USER"]}')");	
}
require('render_prim.php');
?>