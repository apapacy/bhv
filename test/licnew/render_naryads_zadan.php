<?php

header('Content-type: text/javascript; charset="windows-1251"');

require_once('../../bhv/errorhandler.php');
error_reporting(E_ALL |  E_STRICT);

$cts=array(8.51, 10.03, 11.55, 13.38, 15.20, 8.30, 9.78, 11.27, 13.05);
$host = 'localhost';
$database = 'Ceh16';
$username = 'root';
$password = '26682316';

if (empty($username))
    $db = pg_pconnect("host=$host dbname=$database");
elseif (empty($password))
	$db = pg_pconnect("host=$host dbname=$database user=$username");
else
	$db = pg_pconnect("host=$host dbname=$database user=$username password=$password");

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'spz'){
    $action = 'spz';
    $spz = $_REQUEST['spz'];
    $god = $_REQUEST['god'];
    $mes = $_REQUEST['mes'];
    $result = pg_query(
 "SELECT god, mes, nar, npp, tab, koddet, c.name, l.det, n, nop, zadan, kol, l.nv, l.rc, spz FROM licnew l left outer join cennic c on c.kod=l.koddet WHERE god=$god and mes=$mes and spz=$spz order by nar, npp;\n");
//    $result = pg_query(
//     "SELECT * FROM licnew WHERE god=$god and mes=$mes and spz=$spz order by nar, npp;\n");
} else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'tab'){
    $action = 'tab';
    $tab = $_REQUEST['tab'];
    $god = $_REQUEST['god'];
    $mes = $_REQUEST['mes'];
    $result = pg_query(
     "SELECT god, mes, nar, npp, tab, koddet, c.name, l.det, n, nop, zadan, kol, l.nv, l.rc, spz FROM licnew l left outer join cennic c on c.kod=l.koddet WHERE god=$god and mes=$mes and tab=$tab order by nar, npp;\n");

//    $result = pg_query(
//     "SELECT * FROM licnew WHERE god=$god and mes=$mes and tab=$tab order by nar, npp;\n");
}


echo <<<EOS
selected_row = 0;
id_naryad_pane.innerHTML='<table id=tanle_naryad border="0px" cellpadding="0px" cellspacing="0px">\
<tr><td>№/п.п.</td><td>Год</td><td>Месяц</td><td>Наряд</td><td>Таб.№</td><td>Ф.И.О</td><td>Деталь</td><td>Операция</td>\
<td>Задаено</td><td>Принято</td><td>Мин.</td><td>Коп.</td><td>Заказ</td><td>Н/ч</td><td>Грн.</td><td>Действие</td></tr>
EOS
;//'

$my_counter = 0;
$my_tr = 0;
$my_zp = 0;
$my_tab = 0;
$my_fio = '&nbsp;';
while ($result and $row = pg_fetch_array($result)) {
    $my_counter++;
	echo '<tr>';
    echo "<td>#$my_counter</td>";
    echo "<td>{$row['god']}</td>";
    echo "<td>{$row['mes']}</td>";
    echo "<td>{$row['nar']}</td>";
    $my_tab = $row['tab'];
    echo "<td>{$row['tab']}</td>";
    $fio = pg_query("SELECT * FROM fio WHERE tab=$my_tab and parent = 0\n");
    if ($fio and $rfio = pg_fetch_array($fio))
        $my_fio = $rfio['name'];
    echo "<td>$my_fio</td>";
    echo "<td>{$row['name']} {$row['det']}</td>";
    echo "<td>{$row['n']} {$row['nop']}</td>";
    echo "<td>{$row['zadan']}</td>";
    echo "<td>{$row['kol']}</td>";
    if ($row['nv'] > 0)
        $cur_cts = round($row['rc']/$row['nv']*60/100, 2);
    else
        $cur_cts = 0;
    if (in_array($cur_cts, $cts) || in_array($cur_cts+1, $cts) || in_array($cur_cts-1, $cts))
        $cur_style='';
    else
        $cur_style=' style="background-color: red;" ';   
    echo "<td $cur_style>" . round($row['nv'],2) . "</td>";
    echo "<td $cur_style>" . round($row['rc'],2) . "</td>";
    echo "<td>{$row['spz']}</td>";
    $my_tr += $row['nv']*$row['kol']/60;
    echo "<td>". round($row['nv']*$row['kol']/60,2) ."</td>";
    $my_zp += $row['rc']*$row['kol']/100;
    echo "<td>". round($row['rc']*$row['kol']/100,2) ."</td>";
    echo "<td><div style=\'border: 3px outset; font: arial;font-size: 8pt ;background-color:#dddddd;cursor:pointer\'"
    ."  onclick=\'show_naryad(this,{$row['god']},{$row['mes']},{$row['nar']}, \"$my_tab\" )\'>Показать&nbsp;наряд</div></td>";
  
    echo '</tr>';
}

$my_tr = str_replace('.', ',', round($my_tr,2));
$my_zp = str_replace('.', ',', round($my_zp,2));
echo <<<EOS
<tr><td>\&nbsp;</td><td>$god</td><td>$mes</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>\&nbsp;</td><td>\&nbsp;</td>\
<td>\&nbsp;</td><td>\&nbsp;</td><td>\&nbsp;</td><td>\&nbsp;</td><td>\&nbsp;</td><td><b>$my_tr</b> Н/ч</td><td><b>$my_zp</b> Грн.</td><td>\&nbsp;</td></tr>
EOS
;//'

echo "</table>';\n";
?>

 
