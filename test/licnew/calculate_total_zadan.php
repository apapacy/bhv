<?php

header('Content-type: text/javascript; charset="windows-1251"');

require_once('../../bhv/errorhandler.php');
error_reporting(E_ALL |  E_STRICT);
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




$god = $_REQUEST['god'];
$mes = $_REQUEST['mes'];

//************************************************************************************************	
$result = pg_query(
	"SELECT god, mes, spz, sum(round(nv*kol/60,1)) as tr, sum(round(rc*kol/100,2)) as zp"
	. " FROM licnew  WHERE god=$god and mes=$mes GROUP BY god, mes, spz ORDER by spz;\n"
	);
echo <<<EOS
id_total_pane.innerHTML='<table id=tanle_naryad border="0px" cellpadding="0px" cellspacing="0px">\
<tr><td>Год</td><td>Месяц</td><td>Заказ</td<td>Н/ч</td><td>Грн.</td><td>Действие</td></tr>
EOS
;//'
$my_tr = 0;
$my_zp = 0;

while ($result and $row = pg_fetch_array($result)) {
	echo '<tr>';
    echo "<td>{$row['god']}</td>";
    echo "<td>{$row['mes']}</td>";
    echo "<td>{$row['spz']}</td>";
    $my_tr += $row['tr'];
    echo "<td>". $row['tr'] ."</td>";
    $my_zp += $row['zp'];
    echo "<td>". $row['zp'] ."</td>";
    echo "<td><div style=\'border: 3px outset; font: arial;font-size: 8pt ;background-color:#dddddd;cursor:pointer\'"
    ." onclick=\'show_spz(this,{$row['god']},{$row['mes']},{$row['spz']} )\'>Показать</div></td>";
  
    echo '</tr>';
}

$my_tr = str_replace('.', ',', round($my_tr,3));
$my_zp = str_replace('.', ',', round($my_zp,2));
echo <<<EOS
<tr><td>$god</td><td>$mes</td><td>Итого</td><td><b>$my_tr</b> Н/ч</td><td><b>$my_zp</b> Грн.</td><td>\&nbsp;</td></tr>
EOS
;//'
echo "</table>'\n+";

//******************************************************************************************************************



//************************************************************************************************	
$result = pg_query(
	"SELECT god, mes, tab, sum(round(nv*kol/60,1)) as tr, sum(round(rc*kol/100,2)) as zp"
	. " FROM licnew  WHERE god=$god and mes=$mes GROUP BY tab, god, mes ORDER by tab;\n"
	);
echo <<<EOS
'<table id=tanle_naryad border="0px" cellpadding="0px" cellspacing="0px">\
<tr><td>№/п.п.</td><td>Год</td><td>Месяц</td><td>Таб.№</td><td>Ф.И.О</td><td>Н/ч</td><td>Грн.</td><td>Действие</td></tr>
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
    $my_tab = $row['tab'];
    $fio = pg_query("SELECT * FROM fio WHERE tab=$my_tab and parent = 0\n");
    if ($fio and $rfio = pg_fetch_array($fio))
        $my_fio = $rfio['name'];
    else
        $my_fio = 'Табельного нет в списке рабочих';
    echo "<td>$my_tab</td>";
    echo "<td>$my_fio</td>";
    $my_tr += $row['tr'];
    echo "<td>". $row['tr'] ."</td>";
    $my_zp += $row['zp'];
    echo "<td>". $row['zp'] ."</td>";
    echo "<td><div style=\'border: 3px outset; font: arial;font-size: 8pt ;background-color:#dddddd;cursor:pointer\'"
    ." onclick=\'show_tab(this,{$row['god']},{$row['mes']},{$row['tab']} )\'>Показать</div></td>";
  
    echo '</tr>';
}

$my_tr = str_replace('.', ',', round($my_tr,2));
$my_zp = str_replace('.', ',', round($my_zp,2));
echo <<<EOS
<tr><td>\&nbsp;</td><td>$god</td><td>$mes</td><td>Итого</td><td>\&nbsp;</td><td><b>$my_tr</b> Н/ч</td><td><b>$my_zp</b> Грн.</td><td>\&nbsp;</td></tr>
EOS
;//'
echo "</table>'\n+";
//******************************************************************************************************************





//************************************************************************************************	
$result = pg_query(
	"SELECT god, mes, nar, max(tab) as tab, min(tab) as tab0, sum(round(nv*kol/60,1)) as tr, sum(round(rc*kol/100,2)) as zp"
	. " FROM licnew  WHERE god=$god and mes=$mes GROUP BY nar, god, mes ORDER by nar;\n"
	);
echo <<<EOS
'<table id=tanle_naryad border="0px" cellpadding="0px" cellspacing="0px">\
<tr><td>№/п.п.</td><td>Год</td><td>Месяц</td><td>Наряд</td><td>Таб.№</td><td>Ф.И.О</td><td>Н/ч</td><td>Грн.</td><td>Действие</td></tr>
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
    if ($row['tab'] == $row['tab0']){
        $my_tab = $row['tab'];
        //$fio = pg_query("SELECT * FROM fio WHERE tab=$my_tab and parent = 0\n");
        //if ($fio and $rfio = pg_fetch_array($fio))
        //    $my_fio = $rfio['name'];
        //else
        //    $my_fio = 'Табельного нет в списке рабочих';
    }else{
        $my_tab = "Ошибка";
        //$my_fio = '*************';
    }    
    echo "<td>$my_tab</td>";
    echo "<td>$my_fio</td>";
    $my_tr += $row['tr'];
    echo "<td>". $row['tr'] ."</td>";
    $my_zp += $row['zp'];
    echo "<td>". $row['zp'] ."</td>";
    echo "<td><div style=\'border: 3px outset; font: arial;font-size: 8pt ;background-color:#dddddd;cursor:pointer\'"
    ." onclick=\'show_naryad(this,{$row['god']},{$row['mes']},{$row['nar']}, \"$my_tab\" )\'>Показать</div></td>";
  
    echo '</tr>';
}

$my_tr = str_replace('.', ',', round($my_tr,2));
$my_zp = str_replace('.', ',', round($my_zp,2));
echo <<<EOS
<tr><td>\&nbsp;</td><td>$god</td><td>$mes</td><td>Итого</td><td>\&nbsp;</td><td>\&nbsp;</td><td><b>$my_tr</b> Н/ч</td><td><b>$my_zp</b> Грн.</td><td>\&nbsp;</td></tr>
EOS
;//'
echo "</table>";
//******************************************************************************************************************
//left outer join cennic c on c.kod=l.koddet
$result = pg_query(
	"SELECT l.god, l.mes, l.koddet, c.name, sum(round(l.nv*l.kol/60,1)) as tr, sum(round(l.rc*l.kol/100,2)) as zp"
	. " FROM licnew l left outer join cennic c on c.kod=l.koddet WHERE god=$god and mes=$mes GROUP BY koddet, god, mes , name order by name ;\n"
	);
echo <<<EOS
<table id=tanle_naryad border="0px" cellpadding="0px" cellspacing="0px">\
<tr><td>№/п.п.</td><td>Год</td><td>Месяц</td><td>Наряд</td><td>Таб.№</td><td>Ф.И.О</td><td>Н/ч</td><td>Грн.</td><td>Действие</td></tr>
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
    echo "<td>{$row['name']}</td>";
    $my_tr += $row['tr'];
    echo "<td>". $row['tr'] ."</td>";
    $my_zp += $row['zp'];
    echo "<td>". $row['zp'] ."</td>";
    echo "<td><div style=\'border: 3px outset; font: arial;font-size: 8pt ;background-color:#dddddd;cursor:pointer\'"
    ." onclick=\'show_name(this,{$row['god']},{$row['mes']},{$row['name']} )\'>Показать</div></td>";
  
    echo '</tr>';
}

$my_tr = str_replace('.', ',', round($my_tr,2));
$my_zp = str_replace('.', ',', round($my_zp,2));
echo <<<EOS
<tr><td>\&nbsp;</td><td>$god</td><td>$mes</td><td>Итого</td><td>\&nbsp;</td><td>\&nbsp;</td><td><b>$my_tr</b> Н/ч</td><td><b>$my_zp</b> Грн.</td><td>\&nbsp;</td></tr>
EOS
;//'
echo "</table>';\n";
//******************************************************************************************************************


?>

 
