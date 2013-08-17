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

	
$innerHTML = '<tr><th>Цех/участок и т.д.</th><th>Участок/рабочее место и т.д.</th><th>Кол-во</th><th>Фр.в.</th><th>Удаление</th><th>user</th><th>datetime</th></tr>';
	
$result = pg_query($db,"select p.name as pname, k.name as kname, k.kol, k.fef, k.deleted, k.user, k.ts from co k left outer join co p on k.parent=p.kod order by ts desc");	
if ($result) 
	while ($row = pg_fetch_array($result)){
		$pname = str_replace("\\", "\\\\", $row['pname']);
		$pname = str_replace('\'', '\\\'', $pname);
		$kname = str_replace("\\", "\\\\", $row['kname']);
		$kname = str_replace('\'', '\\\'', $kname);
		if ( $row['deleted'] == 1)
			$deleted = 'удалена';
		else
			$deleted = '&nbsp;';
		$innerHTML .= "<tr>"
			."<td>$pname</td>"
			."<td>$kname</td>"
			."<td>${row['kol']}</td>"
			."<td>${row['fef']}</td>"
			."<td>$deleted</td>"
			."<td>${row['user']}</td>"
			."<td>${row['ts']}</td></tr>";
	}
//	$field = str_replace("\\", "\\\\", $ar_to_row[6]);
//	$field = str_replace('\'', '\\\'', $field);

$innerHTML = '<table border=1 cellpadding=2 cellspacing=0>' . $innerHTML . '</table>';

echo "div_render_co.innerHTML='$innerHTML'";

?>