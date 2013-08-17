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




if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete'){
    $action = 'delete';
    $god = $_REQUEST['god'];
    $mes = $_REQUEST['mes'];
    $nar = $_REQUEST['nar'];
    $npp = $_REQUEST['npp'];
	$result = pg_query(
	"DELETE FROM licnew where god=$god and mes=$mes and nar=$nar and npp=$npp"
	);

} else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'add'){
    $action = 'add';
    $nar = $_REQUEST['nar'];
    $tab = $_REQUEST['tab'];
    $det = pg_escape_string(iconv("UTF-8", "windows-1251",$_REQUEST['det']));
    $nop = pg_escape_string(iconv("UTF-8", "windows-1251",$_REQUEST['nop']));
    $kol = $_REQUEST['kol'];
    $min = str_replace(',', '.', $_REQUEST['min']);
    $kop = str_replace(',', '.', $_REQUEST['kop']);
    $spz = $_REQUEST['spz'];
    $god = $_REQUEST['god'];
    $mes = $_REQUEST['mes'];
}

	
if (! $nar && $action == 'add' ){
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

if ($action == 'add')
    $result = pg_query(
      "INSERT INTO licnew (god, mes, nar, npp, tab, det, nop, kol, nv, rc, spz) values ($god, $mes, $nar, COALESCE((SELECT MAX(npp + 1) FROM licnew WHERE god=$god and mes=$mes and nar=$nar), 1), $tab, '$det', '$nop', $kol, $min, $kop, $spz);\n"
    );

$result = pg_query(
 "SELECT * FROM licnew WHERE god=$god and mes=$mes and nar=$nar order by npp;\n"
);

echo <<<EOS
selected_row = 0;
id_naryad_pane.innerHTML='<table id=tanle_naryad border="0px" cellpadding="0px" cellspacing="0px">\
<tr><td>�/�.�.</td><td>���</td><td>�����</td><td>�����</td><td>���.�</td><td>�.�.�</td><td>������</td><td>��������</td>\
<td>����������</td><td>���.</td><td>���.</td><td>�����</td><td>�/�</td><td>���.</td><td>��������</td></tr>
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
    /*foreach ($row as $key => $value){
            echo "<td>" . str_replace('\'', '\\\'',$value) . "</td>";
    }*/
    echo "<td>#$my_counter</td>";
    echo "<td>{$row['god']}</td>";
    echo "<td>{$row['mes']}</td>";
    echo "<td>{$row['nar']}</td>";
    if ($my_tab == 0)
        $my_tab = $row['tab'];
    echo "<td>{$row['tab']}</td>";
    if ($my_tab != $row['tab'])
        $my_fio = '*************';
    $fio = pg_query("SELECT * FROM fio WHERE tab=$my_tab and parent = 0\n");
    if ($fio and $rfio = pg_fetch_array($fio))
        $my_fio = $rfio['name'];
    echo "<td>$my_fio</td>";
    echo "<td>{$row['det']}</td>";
    echo "<td>{$row['nop']}</td>";
    echo "<td>{$row['kol']}</td>";
    echo "<td>" . round($row['nv'],2) . "</td>";
    echo "<td>" . round($row['rc'],2) . "</td>";
    echo "<td>{$row['spz']}</td>";
    $my_tr += $row['nv']*$row['kol']/60;
    echo "<td>". round($row['nv']*$row['kol']/60,2) ."</td>";
    $my_zp += $row['rc']*$row['kol']/100;
    echo "<td>". round($row['rc']*$row['kol']/100,2) ."</td>";
    echo "<td><div style=\'border: 3px outset; font: arial;font-size: 8pt ;background-color:#dddddd;cursor:pointer\'"
    ." onclick=\'delete_line(this,{$row['god']},{$row['mes']},{$row['nar']},{$row['npp']} )\'>�������</div></td>";
  
    echo '</tr>';
}

$my_tr = str_replace('.', ',', round($my_tr,2));
$my_zp = str_replace('.', ',', round($my_zp,2));
echo <<<EOS
<tr><td>\&nbsp;</td><td>$god</td><td>$mes</td><td>$nar</td><td>$my_tab</td><td>$my_fio</td><td>\&nbsp;</td><td>\&nbsp;</td>\
<td>\&nbsp;</td><td>\&nbsp;</td><td>\&nbsp;</td><td>\&nbsp;</td><td><b>$my_tr</b> �/�</td><td><b>$my_zp</b> ���.</td><td>\&nbsp;</td></tr>
EOS
;//'



echo "</table>';\n";
?>

 
