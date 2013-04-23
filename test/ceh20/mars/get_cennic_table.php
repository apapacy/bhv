<?php

//header('Content-type: text/javascript; charset="UTF-8"');
header('Content-type: text/javascript; charset="windows-1251"');
require_once('../../../bhv/errorhandler.php');
error_reporting(E_ALL |  E_STRICT);

//$host = 'localhost';
//$database = 'Ceh16';
//$username = 'root';
//$password = '26682316';

require_once('../../../db/setting.php');

if (empty($username))
    $db = pg_pconnect("host=$host dbname=$database");
elseif (empty($password))
	$db = pg_pconnect("host=$host dbname=$database user=$username");
else
	$db = pg_pconnect("host=$host dbname=$database user=$username password=$password");

//$db = odbc_connect("DSN=PostgreLocal;DATABASE=Ceh16", "", "");

if (isset($_REQUEST['cennicKod'])){
    $cennic_kod = $_REQUEST['cennicKod'];
}
	

$result = pg_query($db,
"select z.kod,z.parent,c.name as cennic_name, c.izs, c.nv, cex, nop, t.name as tipop_name, raz, s , platmin, platkop, stan as nzpcmin, 0 as nzpckop, 0 as nzpzmin, 0 as nzpzkop, zadan, ras" 
. " from dbo.zadan z inner join dbo.cennic c on z.parent=c.kod left join dbo.tipop t on t.kod=z.tipop where z.parent=$cennic_kod order by nop"
);



    $izs="***";
$innerHTML = '<table border="0px" cellpadding="0px" cellspacing="0px">';
while ($result and $row = pg_fetch_array($result)) {
    $izs = $row['izs'];
    $innerHTML .= '<tr id="'. round($row['nop'])  .'"><td class="cennic_name C"><div0 class="cennic_name C">'
    . htmlspecialchars($row['cennic_name']) . '</div0>'
    . '</td><td class="cex C">' . $row['cex']
    . '</td><td class="nop N" onclick="f_edit_row(' . $row['kod'] . ')">' . $row['nop']
    . '</td><td class="tipop_name C"><div0 class="tipop_name C">' 
    . htmlspecialchars($row['tipop_name']) . '</div0>'
    . '</td><td class="raz">' . $row['raz']
    . '</td><td class="s">' . $row['s']
    . '</td><td class="platmin N">' . str_replace('.', ',', $row['zadan'])
    . '</td><td class="platkop N">' . str_replace('.', ',', $row['platmin'])
    . '</td><td class="nzpcmin H printnone">' . $row['nzpcmin']
    . '</td><td class="nzpckop H printnone">' . $row['nzpckop']
    . '</td><td class="nzpzmin H printnone">' . $row['nzpzmin']
    . '</td><td class="nzpzkop H printnone">' . $row['nzpzkop']
    .'</td></tr>';
}
$innerHTML .= '</table>';
echo "id_table_pane.innerHTML = '$innerHTML';\n";
echo "bhv.scriptConteiner.responseJSON = {izs: '$izs'};\n";

?>

 
