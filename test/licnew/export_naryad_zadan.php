<?php

//header('Content-type: application/xxx; charset="windows-1251"');
$god = $_REQUEST['god'];
$mes = $_REQUEST['mes'];
$date = date("Y-m-d-H:i:s");
header("Content-type: application/force_download; charset='windows-1251';\n");
header("Content-Disposition: attachment; filename=lic-$god-$mes-($date).csv");

require_once('../../bhv/errorhandler.php');
error_reporting(E_ALL |  E_STRICT);

$cts=array(8.29, 9.77, 11.25, 13.02, 14.80, 8.09, 9.54, 10.98, 12.72);
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




$result = pg_query(
"SELECT god, mes, nar, npp, tab, koddet, c.name, l.det, n, nop, zadan, kol, l.nv, l.rc, spz FROM licnew l left outer join cennic c on c.kod=l.koddet WHERE god=$god and mes=$mes order by nar, npp;\n"
 /*"SELECT * FROM licnew WHERE god=$god and mes=$mes order by nar, npp;\n"*/);

echo <<<EOS
"№/п.п.","Год","Месяц","Наряд","Таб.№","Ф.И.О","Наименование","Деталь","Номер операции","Операция","Количество","Мин","Коп","Заказ","Н/ч","Грн"\n
EOS
;//'

$my_counter = 0;
$my_tr = 0;
$my_zp = 0;
$my_tab = 0;
$my_fio = '&nbsp;';
while ($result and $row = pg_fetch_array($result)) {
    $my_counter++;
    echo "\"$my_counter\",";
    echo "{$row['god']},";
    echo "{$row['mes']},";
    echo "{$row['nar']},";
    if ($my_tab != $row['tab']){
        $my_tab = $row['tab'];
        $fio = pg_query("SELECT * FROM fio WHERE tab=$my_tab and parent = 0\n");
        if ($fio and $rfio = pg_fetch_array($fio))
            $my_fio = $rfio['name'];
        else
            $my_fio = '***************';
    }
    echo "{$row['tab']},";
    echo "\"'" . str_replace('"', '\'', $my_fio) . "\",";
    echo "\"'" . str_replace('"', '\'', $row['name']) . "\",";
    echo "\"'" . str_replace('"', '\'', $row['det']) . "\",";
    echo "\"" . str_replace('"', '\'', $row['n']) . "\",";
    echo "\"'" . str_replace('"', '\'', $row['nop']) . "\",";
    echo "{$row['kol']},";
    echo "\"" . str_replace('.', ',', $row['nv']) . "\",";
    echo "\"" . str_replace('.', ',', $row['rc']) . "\",";
    echo "{$row['spz']},";
    $my_tr += $row['nv']*$row['kol']/60;
    $my_zp += $row['rc']*$row['kol']/100;

    echo "\n";
}

$my_tr = str_replace('.', ',', round($my_tr,1));
$my_zp = str_replace('.', ',', round($my_zp,2));
echo <<<EOS
"$my_tr","$my_zp"
EOS
;//'
echo "\n";
?>

 
