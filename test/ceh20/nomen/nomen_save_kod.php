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

$kod = $_REQUEST['kod'];
$fullname = pg_escape_string(iconv("UTF-8", "windows-1251",$_REQUEST['fullname']));
$mat = pg_escape_string(iconv("UTF-8", "windows-1251",$_REQUEST['mat']));
$matvid = pg_escape_string(iconv("UTF-8", "windows-1251",$_REQUEST['matvid']));
$matparam = pg_escape_string(iconv("UTF-8", "windows-1251",$_REQUEST['matparam']));
//$kol = str_replace(',', '.', $_REQUEST['kol']);

$result = pg_query($db,"select * from pdb_nomen where kod=$kod");	
if ($result && $row = pg_fetch_array($result)){
	$result = pg_query($db,"update pdb_nomen set fullname='$fullname', mat='$mat', matvid='$matvid', matparam='$matparam' where kod=$kod");	
}else{
	$result = pg_query($db,"select * from cennic where kod=$kod");	
	if ($result && $row = pg_fetch_array($result))
		$name = $row['name'];
	else
		$name = '';
	$result = pg_query($db,"insert into pdb_nomen (kod,name,fullname,mat,matvid,matparam) values ($kod,'$name','$fullname','$mat','$matvid','$matparam')");	
}
if ($result)
  echo 'alert("������ ���������");';
require('render_nomen.php');
?>