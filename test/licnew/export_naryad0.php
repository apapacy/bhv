<?php

header('Content-type: text/html; charset="windows-1251"');

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



    $god = $_REQUEST['god'];
    $mes = $_REQUEST['mes'];

$result = pg_query(
 "SELECT * FROM licnew WHERE god=$god and mes=$mes order by nar, npp;\n");

echo <<<EOS
<table id=tanle_naryad border="0px" cellpadding="0px" cellspacing="0px">
<tr><td>�/�.�.</td><td>���</td><td>�����</td><td>�����</td><td>���.�</td><td>�.�.�</td><td>������</td><td>��������</td>
<td>����������</td><td>���.</td><td>���.</td><td>�����</td><td>�/�</td><td>���.</td></tr>
EOS
;//'

$my_counter = 0;
$my_tr = 0;
$my_zp = 0;
$my_tab = 0;
$my_fio = '&nbsp;';
while ($result and $row = pg_fetch_array($result)) {
    $my_counter++;
	echo "<tr>";
    /*foreach ($row as $key => $value){
            echo "<td>" . str_replace('\'', '\\\'',$value) . "</td>";
    }*/
    echo "<td  onclick=\'edit_line(this,{$row['god']},{$row['mes']},{$row['nar']},{$row['npp']} )\'>#$my_counter</td>";
    echo "<td>{$row['god']}</td>";
    echo "<td>{$row['mes']}</td>";
    echo "<td>{$row['nar']}</td>";
    if ($my_tab != $row['tab']){
        $my_tab = $row['tab'];
        $fio = pg_query("SELECT * FROM fio WHERE tab=$my_tab and parent = 0\n");
        if ($fio and $rfio = pg_fetch_array($fio))
            $my_fio = $rfio['name'];
        else
            $my_fio = '***************';
    }
    echo "<td>{$row['tab']}</td>";

    //if ($my_tab != $row['tab'])
    //    $my_fio = '����������� �������� ��������� �����';
    echo "<td>$my_fio</td>";
    echo "<td>{$row['det']}</td>";
    echo "<td>{$row['nop']}</td>";
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
 
    echo '</tr>';
}

$my_tr = str_replace('.', ',', round($my_tr,2));
$my_zp = str_replace('.', ',', round($my_zp,2));
echo <<<EOS
<tr><td>&nbsp;</td><td>$god</td><td>$mes</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td><b>$my_tr</b> �/�</td><td><b>$my_zp</b> ���.</td><td>&nbsp;</td></tr>
EOS
;//'

echo "</table>;\n";
?>

 
