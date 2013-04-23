<?php
header('Content-type: text/javascript; charset="windows-1251"');

require_once('../bhv/errorhandler.php');
error_reporting(E_ALL |  E_STRICT);

require_once('../db/setting.php');

//$host = 'localhost';
//$database = 'test';
//$username = 'Administrator';
//$password = '';


/*/echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n";
//echo '<response>';*/

if (empty($username))
    $db = pg_pconnect("host=$host dbname=$database");
elseif (empty($password))
	$db = pg_pconnect("host=$host dbname=$database user=$username");
else
	$db = pg_pconnect("host=$host dbname=$database user=$username password=$password");



if (empty($username))
    $db = pg_pconnect("host=$host dbname=$database");
elseif (empty($password))
	$db = pg_pconnect("host=$host dbname=$database user=$username");
else
	$db = pg_pconnect("host=$host dbname=$database user=$username password=$password");

if (!$db){
    echo '<errorcode>' . 1 . '</errorcode>';
    echo '<error>' . pg_last_error($db) . '</error></response>';
    die();
}

//$strxml = "";//file_get_contents($_REQUEST["definition"]);
$table_definition = new DOMDocument();
$table_definition->load($_REQUEST["definition"]);
//$table_definition->loadXML($strxml);
$table_sql = $table_definition->getElementsByTagName('sql')->item(0)->firstChild->data;
$table_update = $table_definition->getElementsByTagName('update')->item(0)->firstChild->data;
$table_insert = $table_definition->getElementsByTagName('insert')->item(0)->firstChild->data;
$table_count = $table_definition->getElementsByTagName('count')->item(0)->firstChild->data;
$table_order = $table_definition->getElementsByTagName('order')->item(0)->firstChild->data;
$table_filter = $table_definition->getElementsByTagName('filter')->item(0)->firstChild->data;
$table_where_clause = $table_definition->getElementsByTagName('whereClause')->item(0)->firstChild->data;
$table_offset = 0;



if (isset($_REQUEST['page'])){
    $table_offset = ($_REQUEST['page'] - 1) * ($table_count - 1);
}

foreach ($_REQUEST as $key => $value)
  $$key = pg_escape_string($db,$value);

if (isset($_REQUEST["command"])  && ('update' == $_REQUEST["command"])){
	eval("\$table_update = \"$table_update\";");

	//echo   "/* $table_update  where $where_clause */";
	$result = pg_query($db, "$table_update where $where_clause");
	//echo "/*jr*/";
  
	//eval("\$table_where_clause = \"$table_where_clause\";");
	//echo "/*$table_sql where $table_where_clause*/";
	$result = pg_query($db,"$table_sql where $where_clause");
	//echo "/*jr*/";
}
else if (isset($_REQUEST["command"])  && ('insert' == $_REQUEST["command"])){
  
	//$table_update = $table_definition->getElementsByTagName('update')->item(0)->firstChild->data;
	//eval("\$table_update = \"$table_update\";");

	//echo   "/* $table_update  where $where_clause */";
	//$result = pg_query($db, "$table_update where $where_clause");
	//echo "/*jr*/";
  
	eval("\$table_where_clause0 = \"$table_where_clause\";");
	//echo "/*$table_sql where $table_where_clause*/";
	$result = pg_query($db,"$table_sql where $table_where_clause0 $table_order limit 2");
	//echo "/*jr*/";
  if ($result && $row = pg_fetch_array($result)){
    $befor_npp=$row['npp'];
    if ($row = pg_fetch_array($result))
      $current_npp = $befor_npp + ($row['npp'] - $befor_npp)/3.;
    else
      $current_npp = round($befor_npp + 3*3*3*3*3);
  }else
      $current_npp = 1;
  $npp = $current_npp;
	eval("\$table_insert = \"$table_insert\";");
	$result = pg_query($db,"$table_insert");
	eval("\$table_where_clause0 = \"$table_where_clause\";");
	$result = pg_query($db,"$table_sql where $table_where_clause0 $table_order limit 1");
}else{
	echo "bhv.scriptConteiner.currentOffset = $table_offset;";
	$result = pg_query($db, preg_replace( "/[\s\S]* from /i",
    "select count(*) as count_all from ",
    "$table_sql" . " where $table_filter "));

	$row = pg_fetch_array($result);
	echo "bhv.scriptConteiner.countAll = {$row['count_all']}; \n";

	$result = pg_query($db,"$table_sql where $table_filter $table_order limit $table_count offset $table_offset");

}



#echo json_encode($mysqli->store_result());

echo "bhv.scriptConteiner.responseJSON = ([";
$my_counter = 0;
#$resultset = $mysqli->store_result();
while ($result and $row = pg_fetch_array($result)) {
    if ($my_counter == 0){
        $my_counter++;
        echo '{';
    } else {
        echo ', {';
    }
    $i=0;
    foreach ($row as $key => $value){
        if ($i++ == 0){
            echo "$key : '" . str_replace('\'', '\\\'',$value) . "'";
        } else {
            echo ", $key : '" . str_replace('\'', '\\\'',$value) . "'";
        }
    }
    echo '}';
    #echo json_encode($row). ',';
}

echo "]);";
#echo '</response>';





?>

 