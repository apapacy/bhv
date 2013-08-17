<?php header('Content-type: text/html; charset="windows-1251"');?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
                      "http://www.w3.org/TR/html4/loose.dtd">
<head>
<title>Весь документ (только чтение)</title>
<style type="text/css">
body{
background-color: #bbbccc;
}

td.co_left{
border-left: solid 1px;
border-right: solid 1px;
border-top: none;
border-bottom: none;
width:20px;
}

td.co_button{
border:outset;
background-color: #cccccc;
}
</style>
<script id="bhv_util_script" type="text/javascript" src="../bhv/util.js"></script>
</head>
<table border=1>
<?php
error_reporting(E_ALL+E_STRICT);

require_once('../db/setting.php');

if (empty($username))
    $db = pg_pconnect("host=$host dbname=$database");
elseif (empty($password))
	$db = pg_pconnect("host=$host dbname=$database user=$username");
else
	$db = pg_pconnect("host=$host dbname=$database user=$username password=$password");

if (isset($_REQUEST['cennicKod'])){
    $cennic_kod = $_REQUEST['cennicKod'];
}
	

$result = pg_query($db,'select d.kod, d.npp, d.kodiz,z.name as izdel_name, d.koddet,c.name as cennic_name, d.nop, d.kol, d.nv, d.rc from doc_det d'
.' left outer join cennic c on d.koddet=c.kod'
.' left outer join izdel z on d.kodiz=z.kod where d.kod=1738 order by d.kod, d.npp');
$i=0;
if ($result) 
	while ($row = pg_fetch_array($result)){
		$i++;$str=ceil($i/20)+1;
		echo "<tr><td>#$i</td><td>#$str</td><td>{$row['kod']}</td><td>{$row['npp']}</td><td>{$row['izdel_name']}</td><td>{$row['cennic_name']}</td><td>{$row['nop']}</td><td>{$row['kol']}</td><td>{$row['nv']}</td><td>{$row['rc']}</td></tr>";
	}	

?>
</table>