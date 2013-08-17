<?php 

header('Content-type: text/javascript; charset="widows-1251"');
require_once('../../../bhv/errorhandler.php');
error_reporting(E_ALL |  E_STRICT);

//$host = 'localhost';
//$database = 'Ceh16';
//$username = 'root';
//$password = '26682316';

require_once('../../../db/setting.php');

if (empty($username))
    $db = pg_pconnect("host=$host dbname=$database");
elseif (empty($password))
	$db = pg_pconnect("host=$host dbname=$database user=$username");
else
	$db = pg_pconnect("host=$host dbname=$database user=$username password=$password");

	
$kod=$_REQUEST['kod'];
$parent=$_REQUEST['cennicKod'];
$cex=pg_escape_string(iconv("UTF-8", "windows-1251",$_REQUEST['cex']));
$nop=$_REQUEST['nop'];
$tipop=$_REQUEST['tipop'];
$raz=$_REQUEST['raz'];
$stan=$_REQUEST['oborud'];
$platmin=str_replace(',', '.', $_REQUEST['platmin']);


if ($kod=="new")
	$result = pg_query("insert into zadan (kod,parent,cex,nop,tipop,raz,platmin,stan) values((select max(kod) from zadan)+1,$parent,'$cex',$nop,$tipop,$raz,$platmin,$stan)");
else{
	$result = pg_query("insert into zadan_update select * from zadan where kod=$kod");
	$result = pg_query("update zadan set cex='$cex',nop=$nop,tipop=$tipop,raz=$raz,platmin=$platmin,platkop=0,stan=$stan where kod=$kod");
}	


function render_zadan_field($field){
	$field = str_replace("\\", "\\\\", $field);
	return str_replace('\'', '\\\'', $field);
}

 require('get_cennic_table.php');
 
 echo "bhv\$cennic\$scrollNop0($nop);\n";
?>