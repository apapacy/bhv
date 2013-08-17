<?php header('Content-type: text/html; charset="windows-1251"');?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
                      "http://www.w3.org/TR/html4/loose.dtd"> 

<html>
<head>
<style>
body {background-color: #FFFFFF; font-size: 10px; font-name: Arial;}
table {padding: 0px; margin: 0px; border: 1px solid; table-layout0:fixed; white-space0: nowrap;}
td {padding: 2px; margin: 0px; border: 1px solid #000000; color: #000000;}
</style>
</head>



<?php
require_once('../bhv/errorhandler.php');
error_reporting(E_ALL |  E_STRICT);

$host = 'localhost';
$database = 'Ceh16';
$username = 'root';
$password = '26682316';

//if (empty($username))
//    $db = pg_pconnect("host=$host dbname=$database");
//elseif (empty($password))
//	$db = pg_pconnect("host=$host dbname=$database user=$username");
//else
//	$db = pg_pconnect("host=$host dbname=$database user=$username password=$password");

$db = odbc_connect("DSN=PostgreLocal;DATABASE=Ceh16", "", "");

if (isset($_REQUEST['hidden_kod'])){
    $cennic_kod = $_REQUEST['hidden_kod'];
}
	
$cennic = odbc_exec($db,"select * from cennic where kod=$cennic_kod limit 1");
if ($cennic and $detal = odbc_fetch_array($cennic) ){
echo "<h1>{$detal['name']}</h1>";
echo "<h3>({$detal['det']})</h3>";
}else{
    die("Not found #$cennic_kod");
}


$result = odbc_exec($db,
"select c.name as cennic_name, c.izs, c.nv, cex, nop, t.name as tipop_name, raz, s , platmin, platkop, nzpcmin, nzpckop, nzpzmin, nzpzkop" 
. " from dbo.zadan z inner join dbo.cennic c on z.parent=c.kod left join dbo.tipop t on t.kod=z.tipop where z.parent=$cennic_kod order by nop"
);
?>

<table border="0px" cellpadding="0px" cellspacing="0px">
<tr><td>Наименование</td><td>Цех</td><td>№ оп.</td><td>Операция</td><td>р</td><td>с</td><td>мин.</td><td>коп.</td></tr>


<?php    
$sum_nv = 0;
$sum_rc = 0;
while ($result and $row = odbc_fetch_array($result)) {
    $sum_nv += $row['platmin'];
    $sum_rc += $row['platkop'];
    echo '<tr><td>'
    . htmlspecialchars($row['cennic_name'])
    . '</td><td>' . $row['cex']
    . '</td><td>' . $row['nop']
    . '</td><td>'    . htmlspecialchars($row['tipop_name'])
    . '</td><td>' . $row['raz']
    . '</td><td>' . $row['s']
    . '</td><td>' . str_replace('.', ',', $row['platmin'])
    . '</td><td>' . str_replace('.', ',', $row['platkop'])
    .'</td></tr>';
}
echo "<tr><td colspan=6>Итого пооперационные нормы</td><td>$sum_nv</td><td>$sum_rc</td></tr>";
echo "<tr><td colspan=6>Утверждено ООТЗ</td><td>{$detal['nv']}</td><td>{$detal['rc']}</td></tr>";
?>
</table>
 
