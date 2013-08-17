<?php

header('Content-type: text/javascript; charset="windows-1251"');

require_once('../../bhv/errorhandler.php');
error_reporting(E_ALL |  E_STRICT);

$cts=array(8.51, 10.03, 11.55, 13.38, 15.20, 8.30, 9.78, 11.27, 13.05);
$host = 'localhost';
$database = 'Ceh16';
$username = 'root';
$password = '26682316';
$actiongroup = '';

if (empty($username))
    $db = pg_pconnect("host=$host dbname=$database");
elseif (empty($password))
	$db = pg_pconnect("host=$host dbname=$database user=$username");
else
	$db = pg_pconnect("host=$host dbname=$database user=$username password=$password");


if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete'){
    $action = 'delete';
    $god = $_REQUEST['god'];
    $mes = $_REQUEST['mes'];
    $nar = $_REQUEST['nar'];
    $npp = $_REQUEST['npp'];
	$result = pg_query(
	"DELETE FROM licnew where god=$god and mes=$mes and nar=$nar and npp=$npp"
	);

} else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'render'){
    $action = 'render';
    $god = $_REQUEST['god'];
    $mes = $_REQUEST['mes'];
    $nar = $_REQUEST['nar'];
} else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'kols'){
    $action = 'kols';
    $god = $_REQUEST['god'];
    $mes = $_REQUEST['mes'];
    $nar = $_REQUEST['nar'];
    $npps = $_REQUEST['npps'];
    $kols = $_REQUEST['kols'];
    for ($i = 0; $i < sizeof($npps); $i++){
    $result = pg_query(
      "UPDATE licnew SET kol={$kols[$i]} WHERE god=$god AND mes=$mes AND nar=$nar AND npp={$npps[$i]}  ;\n");       
    }


} else if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'zadan' ||
$_REQUEST['action'] == 'zadan_min' || $_REQUEST['action'] == 'zadan_307' || $_REQUEST['action'] == 'nar' ||
$_REQUEST['action'] == 'nar_s' || $_REQUEST['action'] == 'save')){
    $action = $_REQUEST['action'];
    if ($action != 'save')
        $actiongroup = 'add';
    $nar = $_REQUEST['nar'];
    if (isset($_REQUEST['npp']))
        $npp = $_REQUEST['npp'];
    $tab = $_REQUEST['tab'];
    $koddet = $_REQUEST['koddet'];
    $det = pg_escape_string(iconv("UTF-8", "windows-1251",$_REQUEST['det']));
    $n = $_REQUEST['n'];
    $nop = pg_escape_string(iconv("UTF-8", "windows-1251",$_REQUEST['nop']));
    $zadan = $_REQUEST['zadan'];
    $kol = $_REQUEST['kol'];
    $min = str_replace(',', '.', $_REQUEST['min']);
    $kop = str_replace(',', '.', $_REQUEST['kop']);
    $spz = $_REQUEST['spz'];
    $god = $_REQUEST['god'];
    $mes = $_REQUEST['mes'];
}
	
if ($actiongroup == 'add'  && !$nar ){
	$result = pg_query(
	"LOCK TABLE naryadnumber IN ACCESS EXCLUSIVE MODE;\n"
	. "UPDATE naryadnumber SET naryadnumber = naryadnumber + 1 WHERE god=$god and mes=$mes;\n"
	. "SELECT naryadnumber FROM naryadnumber WHERE god=$god and mes=$mes;\n"
	);
	if ($result and $row = pg_fetch_array($result)) {
		$nar = $row['naryadnumber'];
		echo "id_cur_nar.value = $nar;\n";
	}else{
		die('\nalert("Not set naryad number");\n');
	}
}

if ($action == 'zadan') {
    $det = '';
    $result = pg_query(
        "select * from cennic where kod=$koddet");
    if ($result and $row = pg_fetch_array($result)) {
        $det = $row['name'];     
    }
    $kol = 0;
    $koddet0 = $koddet;
    $n0 = $n;
    $min = 0;
    $kop = 0;
    $nop = '';
    $result = pg_query(
        "select * from analoglic where koddet=$koddet and koddetnop=0");
    if ($result and $row = pg_fetch_array($result)) {
        $koddet0 = $row['analog'];     
    }
    
    $result = pg_query(
        "select * from analoglic where koddet=$koddet and koddetnop=$n");
    if ($result and $row = pg_fetch_array($result)) {
        $koddet0 = $row['analog'];     
        $n0 = $row['analognop'];
    }
    if ($koddet0 != 0){
        $result = pg_query(
            "select * from zadan where parent=$koddet0 and nop=$n0");
        if ($result and $row = pg_fetch_array($result)) {
            $min = $row['platmin'];
            $kop = $row['platkop'];
        
            $result = pg_query(
                "select * from tipop where kod={$row['tipop']}");
            if ($result and $row = pg_fetch_array($result)) {
                $nop = $row['name'];
            }
        }
    }
    $result = pg_query(
      "INSERT INTO licnew (god, mes, nar, npp, tab, koddet, det, n, nop, zadan, kol, nv, rc, spz)"
 . " values ($god, $mes, $nar, COALESCE((SELECT MAX(npp + 1) FROM licnew WHERE god=$god and mes=$mes and nar=$nar), 1), $tab, $koddet, '$det', $n, '$nop', $zadan, $kol, $min, $kop, $spz);\n"
    );
}
if ($action == 'zadan_min') {
    $det = '';
    $result = pg_query(
        "select * from cennic where kod=$koddet");
    if ($result and $row = pg_fetch_array($result)) {
        $det = $row['name'];     
    }
    $kol = 0;
    $result = pg_query(
      "INSERT INTO licnew (god, mes, nar, npp, tab, koddet, det, n, nop, zadan, kol, nv, rc, spz)"
 . " values ($god, $mes, $nar, COALESCE((SELECT MAX(npp + 1) FROM licnew WHERE god=$god and mes=$mes and nar=$nar), 1), $tab, $koddet, '$det', $n, '$nop', $zadan, $kol, $min, $kop, $spz);\n"
    );
}

if ($action == 'zadan_307') {
    $koddet = 0;
    $kol = 0;
    $result = pg_query(
      "INSERT INTO licnew (god, mes, nar, npp, tab, koddet, det, n, nop, zadan, kol, nv, rc, spz)"
 . " values ($god, $mes, $nar, COALESCE((SELECT MAX(npp + 1) FROM licnew WHERE god=$god and mes=$mes and nar=$nar), 1), $tab, $koddet, '$det', $n, '$nop', $zadan, $kol, $min, $kop, $spz);\n"
    );
}

if ($action == 'nar') {
    $det = '';
    $result = pg_query(
        "select * from cennic where kod=$koddet");
    if ($result and $row = pg_fetch_array($result)) {
        $det = $row['name'];     
    }
    $zadan = 0;
    $result = pg_query(
      "INSERT INTO licnew (god, mes, nar, npp, tab, koddet, det, n, nop, zadan, kol, nv, rc, spz)"
 . " values ($god, $mes, $nar, COALESCE((SELECT MAX(npp + 1) FROM licnew WHERE god=$god and mes=$mes and nar=$nar), 1), $tab, $koddet, '$det', $n, '$nop', $zadan, $kol, $min, $kop, $spz);\n"
    );
}

if ($action == 'nar_s') {
    $koddet = 0;
    $zaaan = 0;
    $result = pg_query(
      "INSERT INTO licnew (god, mes, nar, npp, tab, koddet, det, n, nop, zadan, kol, nv, rc, spz)"
 . " values ($god, $mes, $nar, COALESCE((SELECT MAX(npp + 1) FROM licnew WHERE god=$god and mes=$mes and nar=$nar), 1), $tab, $koddet, '$det', $n, '$nop', $zadan, $kol, $min, $kop, $spz);\n"
    );
}


if ($action == 'save')
    $result = pg_query(
      "UPDATE licnew SET tab=$tab, koddet=$koddet, det='$det', n=$n, nop='$nop', zadan=$zadan, kol=$kol, nv=$min, rc=$kop, spz=$spz WHERE god=$god AND mes=$mes AND nar=$nar AND npp=$npp  ;\n"
    );

$result = pg_query(
 "SELECT god, mes, nar, npp, tab, koddet, c.name, l.det, n, nop, zadan, kol, l.nv, l.rc, spz FROM licnew l left outer join cennic c on c.kod=l.koddet WHERE god=$god and mes=$mes and nar=$nar order by npp;\n");

echo <<<EOS
selected_row = 0;
id_naryad_pane.innerHTML='<table id=tanle_naryad border="0px" cellpadding="0px" cellspacing="0px">\
<tr><td>№/п.п.</td><td>Год</td><td>Месяц</td><td>Наряд</td><td>Таб.№</td><td>Ф.И.О</td><td>Деталь</td><td>Операция</td>\
<td>Задано</td><td>Принято</td><td>Мин.</td><td>Коп.</td><td>Заказ</td><td>Н/ч</td><td>Грн.</td><td>Действие</td></tr>
EOS
;//'

$my_counter = 0;
$my_tr = 0;
$my_zp = 0;
$my_tab = 0;
$my_fio = '&nbsp;';
$my_npps = "$god,$mes,$nar,[";
$my_kols = '[';
$my_sum_kols = 0;
while ($result and $row = pg_fetch_array($result)) {
    if (! ($my_counter++)){
        $my_npps = $my_npps . "{$row['npp']}";
        $my_kols = $my_kols . "{$row['zadan']}";
    }else {
        $my_npps = $my_npps . ',' . $row['npp'];
        $my_kols = $my_kols . ',' . $row['zadan'];
    }
    $my_sum_kols += $row['kol'];
	echo "<tr>";
	$crow = array();
    foreach ($row as $key => $value)
        $crow[$key] =  str_replace('\'', '\\\'',$value);
    echo "<td  onclick=\'edit_line(this,{$row['god']},{$row['mes']},{$row['nar']},{$row['tab']},{$row['npp']},\"{$crow['det']}\",\"{$crow['nop']}\",\"{$row['kol']}\",\"{$row['nv']}\",\"{$row['rc']}\",\"{$row['spz']}\",\"{$row['koddet']}\",\"{$row['zadan']}\",\"{$row['n']}\" )\'>#$my_counter</td>";
    echo "<td>{$row['god']}</td>";
    echo "<td>{$row['mes']}</td>";
    echo "<td>{$row['nar']}</td>";
    if ($my_tab == 0)
        $my_tab = $row['tab'];
    echo "<td>{$row['tab']}</td>";
    $fio = pg_query("SELECT * FROM fio WHERE tab=$my_tab and parent = 0\n");
    if ($fio and $rfio = pg_fetch_array($fio))
        $my_fio = $rfio['name'];
    if ($my_tab != $row['tab'])
        $my_fio = 'Внимательно проверте табельный номер';
    echo "<td>$my_fio</td>";
    if ($row['name'] == $row['det'])
        echo "<td>{$row['name']}</td>";
    else
        echo "<td>{$row['name']} {$row['det']}</td>";
    echo "<td>{$row['n']} {$row['nop']}</td>";
    echo "<td>{$row['zadan']}</td>";
    echo "<td><input type=text id=\\'input_kol{$row['npp']}\\' value={$row['kol']} size=7 disabled= true onkeyup=\'if (event.keyCode == 13) bhv.selectNextInput(this).select()\'></td>";
    if ($row['nv'] > 0)
        $cur_cts = round($row['rc']/$row['nv']*60/100, 2);
    else
        $cur_cts = 0;
    if (in_array($cur_cts, $cts) || in_array($cur_cts+.01, $cts) || in_array($cur_cts-.01, $cts))
        $cur_style='';
    else
        $cur_style=' style="background-color: red;" ';   
    echo "<td $cur_style>" . round($row['nv'],2) . "</td>";
    echo "<td $cur_style>" . round($row['rc'],2) . "</td>";
    echo "<td>{$row['spz']}</td>";
    $my_tr += round($row['nv']*$row['kol']/60,1);
    echo "<td>". round($row['nv']*$row['kol']/60,1) ."</td>";
    $my_zp += $row['rc']*round($row['kol']/100,2);
    echo "<td>". round($row['rc']*$row['kol']/100,2) ."</td>";
    echo "<td><div style=\'border: 3px outset; font: arial;font-size: 8pt ;background-color:#dddddd;cursor:pointer\'"
    ." onclick=\'return delete_line(this,{$row['god']},{$row['mes']},{$row['nar']},{$row['npp']} )\'>Удалить</div></td>";
  
    echo '</tr>';
}
$my_npps = $my_npps . ']';
$my_kols = $my_kols . ']';
$my_tr = str_replace('.', ',', round($my_tr,2));
$my_zp = str_replace('.', ',', round($my_zp,2));
echo <<<EOS
<tr><td>\&nbsp;</td><td>$god</td><td>$mes</td><td>$nar</td><td>$my_tab</td><td>$my_fio</td><td>\&nbsp;</td><td>\&nbsp;</td>\
<td>\&nbsp;</td><td>\&nbsp;</td><td>\&nbsp;</td><td>\&nbsp;</td><td>\&nbsp;</td><td><b>$my_tr</b> Н/ч</td><td><b>$my_zp</b> Грн.</td><td>\&nbsp;</td></tr></table>
EOS
;

echo "<input type=button id=button_kol_edit value=\"Принято годных\" onclick=\"f_edit_kol($my_npps,$my_kols, $my_sum_kols);\"><input type=button id=button_kol_save value=\"Сохранить принято годных\" onclick=\"f_save_kol($my_npps,$my_kols, $my_sum_kols);\"> ';\n";


/*echo <<<EOS
<tr><td>\&nbsp;</td><td>$god</td><td>$mes</td><td>$nar</td><td>$my_tab</td><td>$my_fio</td><td>\&nbsp;</td><td>\&nbsp;</td>\
<td>\&nbsp;</td><td>\&nbsp;</td><td>\&nbsp;</td><td>\&nbsp;</td><td>\&nbsp;</td><td><b>$my_tr</b> Н/ч</td><td><b>$my_zp</b> Грн.</td><td>\&nbsp;</td></tr>
EOS
;//'*/

?>

 
