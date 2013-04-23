<?php header('Content-type: text/html; charset="windows-1251"');?>
<?php
error_reporting(E_ALL+E_STRICT);
require_once('../../../db/setting.php');

if (empty($username))
    $db = pg_pconnect("host=$host dbname=$database");
elseif (empty($password))
	$db = pg_pconnect("host=$host dbname=$database user=$username");
else
	$db = pg_pconnect("host=$host dbname=$database user=$username password=$password");

$sb = $_REQUEST['sb'];
if (!isset($ce))
	$ce = $sb;
if (!isset($kd))
	$kd = $sb;
// 0-root  1-ce  2-kd  3-st  4-end  5-kol 6-name 7-kol-

$root = 'Код сборочной единицы ' . $sb;
$result = pg_query($db,"select name from cennic where kod='$sb'");	
if ($result) 
	if ($row = pg_fetch_array($result))
		$root = $row['name'];

$ar_from = array(array(array($sb, $sb, $sb, 0, false, 1, $root, 1)));
$ar_to = array(array());
$is_end = false;
$max_step = 0;

$shot = $_REQUEST['shot'];

while (!$is_end){
	$is_end = true;	
	foreach ($ar_from[0] as $ar_from_row){
		$ar_to[0][]= array($sb, $ar_from_row[1], $ar_from_row[2], $ar_from_row[3], true, $ar_from_row[5], $ar_from_row[6], $ar_from_row[7]);
		if (! $ar_from_row[4]){
			if ($ar_from_row[3] + 1 > $max_step)
				$max_step = $ar_from_row[3] + 1;
			if ($max_step < 100)
				$is_end = false;
			$result = pg_query($db,"select p.*, c.name from prim p left outer join cennic c on p.kd=c.kod where kol<>0 and ce='${ar_from_row[2]}' order by c.name");	
			if ($result) 
				while ($row = pg_fetch_array($result)){
					$ar_to[0][]= array($sb, $ar_from_row[2], $row['kd'], $ar_from_row[3] + 1, false, $ar_from_row[5] * $row['kol'], $row['name'], $row['kol'] + 0);
				}
		}
	}
	$ar_from = $ar_to;
	$ar_to=array(array());
	if ($shot){
		$is_end = true;
		$max_step = 2;
	}
}

$innerHTML = '';
$set_current = false;
foreach ($ar_from[0] as $ar_to_row){
	if (! $set_current && ($ar_to_row[1] == $ce &&  $ar_to_row[2] == $kd || $kd == 0 && $ar_to_row[2] == $ce)){
		$set_current = true;
		$innerHTML .= "<tr id=\'prim_current_row\' title=\'ce=${ar_to_row[1]}&kd=${ar_to_row[2]}\' style=\'background-color:red\'>";
	}else 
		$innerHTML .= "<tr title=\'ce=${ar_to_row[1]}&kd=${ar_to_row[2]}\'>";
	for ($i=1; $i<=$ar_to_row[3]; $i++)
		$innerHTML .= "<td class=\'prim_left\'>&nbsp</td>";
	$col_span = $max_step-$ar_to_row[3];
	$field = str_replace("\\", "\\\\", $ar_to_row[6]);
	$field = str_replace('\'', '\\\'', $field);
	$innerHTML .="<td colspan=\'$col_span\'><a href=\'javascript:f_show_sb_a(${ar_to_row[2]})\'>$field</a></td><td>${ar_to_row[7]}</td><td>${ar_to_row[5]}</td>"
		. ( $ar_to_row[3] !=0 ? "<td onclick=\'f_delete_kd(this)\' class=\'prim_button\'>Удалить</td>" :  '<td>&nbsp;</td>')
		. ( $shot==0 || $ar_to_row[3]==0 ? "<td onclick=\'f_insert_kd(${ar_to_row[2]}, this)\' class=\'prim_button\'>Добавить</td>" : '<td>&nbsp;</td>')
		. ( $ar_to_row[3] !=0 ? "<td onclick=\'f_kol_kd(this)\' class=\'prim_button\'>Изм.кол.</td></tr>" : '<td>&nbsp;</td></tr>');
}

$col_span = $max_step + 0;
$result = pg_query($db,"select p.*, c.name from prim p left outer join cennic c on p.ce=c.kod where kol<>0 and kd='$sb' order by c.name");	
	if ($result){ 
		$fieldto = str_replace("\\", "\\\\", $ar_from[0][0][6]);
		$fieldto = str_replace('\'', '\\\'', $fieldto);
		while ($row = pg_fetch_array($result)){
			$innerHTML .= "<tr>";
			$field = str_replace("\\", "\\\\", $row['name']);
			$field = str_replace('\'', '\\\'', $field);
			$innerHTML .= "<td colspan=\'{$col_span}\'><u>$fieldto</u></td><td>${row['kol']} шт.</td><td colspan=4>входит в <a href=\'javascript:f_show_sb_a(${row['ce']})\'>$field</a></td></tr>";
		}
	}

				
$innerHTML = '<table border=1 cellpadding=2 cellspacing=0>' . $innerHTML . '</table>';

echo "div_render_sb.innerHTML='$innerHTML';";
echo "document.getElementById('prim_current_row').scrollIntoView()";

?>