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

	
$innerHTML = '<tr><th>Сборочная единица</th><th>Входящая</th><th>Кол-во</th><th>user</th><th>datetime</th></tr>';
	
$result = pg_query($db,"select p.*, ce.name as cename, kd.name as kdname from prim p left outer join cennic ce on p.ce=ce.kod left outer join cennic kd on p.kd=kd.kod order by ts desc");	
if ($result) 
	while ($row = pg_fetch_array($result)){
		$cename = str_replace("\\", "\\\\", $row['cename']);
		$cename = str_replace('\'', '\\\'', $cename);
		$kdname = str_replace("\\", "\\\\", $row['kdname']);
		$kdname = str_replace('\'', '\\\'', $kdname);
		if ( $row['kol'] == 0)
			$kol = 'удалена';
		else
			$kol = $row['kol'];
		$innerHTML .= "<tr>"
			."<td>$cename</td>"
			."<td>$kdname</td>"
			."<td>$kol</td>"
			."<td>${row['user']}</td>"
			."<td>${row['ts']}</td></tr>";
	}
//	$field = str_replace("\\", "\\\\", $ar_to_row[6]);
//	$field = str_replace('\'', '\\\'', $field);

$innerHTML = '<table border=1 cellpadding=2 cellspacing=0>' . $innerHTML . '</table>';

echo "div_render_sb.innerHTML='$innerHTML'";

?>