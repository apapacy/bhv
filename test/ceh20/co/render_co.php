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

if (isset($_REQUEST['co']))
	$co = $_REQUEST['co'];
else
	$co= 0;
if (!isset($parent))
	$parent = $co;
if (!isset($kod))
	$kod = $co;
// 0-root  1-parent  2-kod  3-st  4-end  5-kol 6-name 7-kol 8-fef

$result = pg_query($db,"select * from co where kod='$co' and deleted=0");	
if ($result) 
	if ($row = pg_fetch_array($result))
		$ar_from = array(array(array($co, $co, $co, 0, false, $row['kol']+0, $row['name'], $row['kol']+0, $row['fef']+0)));
	else
		$ar_from = array(array());


$ar_to = array(array());
$is_end = false;
$max_step = 0;

$shot = $_REQUEST['shot'];

while (!$is_end){
	$is_end = true;	
	foreach ($ar_from[0] as $ar_from_row){
		$ar_to[0][]= array($co, $ar_from_row[1], $ar_from_row[2], $ar_from_row[3], true, $ar_from_row[5], $ar_from_row[6], $ar_from_row[7], $ar_from_row[8]);
		if (! $ar_from_row[4]){
			if ($ar_from_row[3] + 1 > $max_step)
				$max_step = $ar_from_row[3] + 1;
			if ($max_step < 100)
				$is_end = false;
			$result = pg_query($db,"select * from co where parent='${ar_from_row[2]}' and deleted=0 order by name");	
			if ($result) 
				while ($row = pg_fetch_array($result)){
					$ar_to[0][]= array($co, $ar_from_row[2], $row['kod'], $ar_from_row[3] + 1, false, $ar_from_row[5] * $row['kol'], $row['name'], $row['kol'] + 0,$row['fef'] + 0);
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

$col_span = $max_step + 3;

$is_end = false;
$root = $co;
while (! $is_end){
	$is_end = true;
	$result = pg_query($db,"select k.parent, p.name as parentname,k.kod,k.name as kodname, k.kol from co k inner join co p on p.kod=k.parent where k.kod='$root' and k.deleted=0 order by k.name");	
	if ($result){ 
		while ($row = pg_fetch_array($result)){
			$fieldto = str_replace("\\", "\\\\", $row['parent']);
			$fieldto = str_replace('\'', '\\\'', $fieldto);
			$is_end = false;
			$root = $row['parent'];
			$field = str_replace("\\", "\\\\", $row['parentname']);
			$field = str_replace('\'', '\\\'', $field);
			$innerHTML = "<tr><td colspan=$col_span><a href=\'javascript:f_show_co_a(${row['parent']})\'>$field</a></td><td onclick=\'f_insert_kod(${row['parent']}, this)\' class=\'co_button\'>Добавить</td></tr>" . $innerHTML;
		}
	}
}

$innerHTML = "<tr><td colspan=$col_span><a href=\'javascript:f_show_co_a(0)\'>...</a></td><td onclick=\'f_insert_kod(0, this)\' class=\'co_button\'>Добавить</td></tr>" . $innerHTML;

$set_current = false;

foreach ($ar_from[0] as $ar_to_row){
	if (! $set_current && ($ar_to_row[1] == $parent &&  $ar_to_row[2] == $kod || $kod == 0 && $ar_to_row[2] == $parent)){
		$set_current = true;
		$innerHTML .= "<tr id=\'co_current_row\' title=\'parent=${ar_to_row[1]}&kod=${ar_to_row[2]}\' style=\'background-color:red\'>";
	}else 
		$innerHTML .= "<tr title=\'parent=${ar_to_row[1]}&kod=${ar_to_row[2]}\'>";
	for ($i=1; $i<=$ar_to_row[3]; $i++)
		$innerHTML .= "<td class=\'co_left\'>&nbsp</td>";
	$col_span = $max_step-$ar_to_row[3];
	$field = str_replace("\\", "\\\\", $ar_to_row[6]);
	$field = str_replace('\'', '\\\'', $field);
	$innerHTML .="<td colspan=\'$col_span\'><a href=\'javascript:f_show_co_a(${ar_to_row[2]})\'>$field</a></td><td>${ar_to_row[7]}</td><td>${ar_to_row[8]}</td>"
		. ( $ar_to_row[3] !=-1 ? "<td onclick=\'f_delete_kod(this)\' class=\'co_button\'>Удал.</td>" :  '<td>&nbsp;</td>')
		. ( $shot==0 || $ar_to_row[3]==0 ? "<td onclick=\'f_insert_kod(${ar_to_row[2]}, this)\' class=\'co_button\'>Добавить</td>" : '<td>&nbsp;</td>')
		. ( $ar_to_row[3] !=-1 ? "<td onclick=\'f_kol_kod(this)\' class=\'co_button\'>Изм.кол.</td></tr>" : '<td>&nbsp;</td></tr>');
}


				
$innerHTML = '<table border=1 cellpadding=2 cellspacing=0>' . $innerHTML . '</table>';

echo "div_render_co.innerHTML='$innerHTML';\n";
$innerHTML = '';
$result = pg_query($db,' select distinct kod, name from co where parent=0 and deleted=0 order by name');
if ($result) 
	while ($row = pg_fetch_array($result))
		$innerHTML .= "<a href=\'javascript:f_show_co_a(${row['kod']})\'>${row['name']}</a><br>";
echo "div_left.innerHTML = '<a href=\"../mars/mars.html\">Пооперационные маршруты</a><br><a href=\"../nomen/nomen.php\">Сведения о детали</a><br>$innerHTML';\n";
echo "document.getElementById('co_current_row').scrollIntoView();\n";









?>