<?php header('Content-type: text/javascript; charset="windows-1251"');?>
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
echo "bhv.scriptConteiner={};\n";
echo "bhv.scriptConteiner.kod=$kod;\n";

$result = pg_query($db,"select * from pdb_nomen where kod='$kod'");	

if ($result && $row = pg_fetch_array($result)){
		$name = render_nomen_field($row['name']);
		echo "bhv.scriptConteiner['new']=false;\n";
		echo "bhv.scriptConteiner.name='$name';\n";
		$fullname = render_nomen_field($row['fullname']);
		echo "bhv.scriptConteiner.fullname='$fullname';\n";
		$mat = render_nomen_field($row['mat']);
		echo "bhv.scriptConteiner.mat='$mat';\n";
		$matvid = render_nomen_field($row['matvid']);
		echo "bhv.scriptConteiner.matvid='$matvid';\n";
		$matparam = render_nomen_field($row['matparam']);
		echo "bhv.scriptConteiner.matparam='$matparam';\n";
}else{ 
	$result = pg_query($db,"select * from cennic where kod='$kod'");	
	if ($result && $row = pg_fetch_array($result)){
		$name = render_nomen_field($row['name']);
		echo "bhv.scriptConteiner['new']=true;\n";
		echo "bhv.scriptConteiner.name='$name';\n";
		echo "bhv.scriptConteiner.fullname='';\n";
		echo "bhv.scriptConteiner.mat='';\n";
		echo "bhv.scriptConteiner.matvid='';\n";
		echo "bhv.scriptConteiner.matparam='';\n";
	}
}
	
echo "div_render_kod.innerHTML='";	

for ($i=1; $i<4; $i++){
if ($i==1) $nomen_co_title = '���������';
else if ($i==2) $nomen_co_title = '������������';
else if ($i==3) $nomen_co_title = '����������';

$result = pg_query($db,"select nc.kod, nc.type, nc.co, c.name, nc.k, nc.part  from pdb_nomen_co nc left outer join co c on nc.co=c.kod where nc.kod='$kod' and nc.type=$i order by nc.type, nc.k desc, c.name asc");			

echo  "<table  border=1 cellpadding=2 cellspacing=0><tr><th>$nomen_co_title</th><th>�����������</th><th>������ ������</th><th colspan>��������</th><td onclick=\'f_insert_nomen_co($kod, $i)\'class=\'nomen_button\' >��������</td></tr>";
if ($result) 
	while ($row = pg_fetch_array($result)){
		echo '<tr><td>' . co_full_path($db, $row['co']) . "</td><td>{$row['k']}</td><td>{$row['part']}</td><td onclick=\'f_save_nomen_co({$row['kod']},{$row['type']},{$row['co']}) \'class=\'nomen_button\' >��������</td><td  onclick=\'f_delete_nomen_co({$row['kod']},{$row['type']},{$row['co']})\' class=\'nomen_button\'>�������</td></tr>";
}
echo '</table><br />';

}

echo "';";

//echo 'document.getElementById("div_nomen_after_post").appendChild(document.getElementById("div_nomen_combo2_inner"));';


function render_nomen_field($field){
	$field = str_replace("\\", "\\\\", $field);
	return str_replace('\'', '\\\'', $field);
}

function co_full_path($db, $kod1/*, $root1*/){
$is_end = false;
//$root0 = $_REQUEST['currentTree'];
//$rowcounter = 0;
$str = '';
while (! $is_end){
	$is_end = true;
	$result = pg_query($db,"select * from co where kod='$kod1' and deleted=0 order by name");	
	if ($result){ 
		while ($row = pg_fetch_array($result)){
			$kod1 = $row['parent'];
			if ($kod1 !==0){
				$is_end = false;
			}
			$field = str_replace("\\", "\\\\", $row['name']);
			$field = str_replace('\'', '\\\'', $field);
			$str = '/' . $field . $str;
		}
	}
}
return $str;
}

?>