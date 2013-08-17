<?php
header('Content-type: text/javascript; charset="UTF-8"');
//require_once('../bhv/errorhandler.php');
//error_reporting(E_ALL |  E_STRICT);
//header('Content-type: text/javascript; charset="windows-1251"');

require_once('../db/setting.php');

//$host = 'localhost';
//$database = 'test';
//$username = 'Administrator';
//$password = '';


/*/echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n";
//echo '<response>';*/



if (isset($_REQUEST['table']))
    $table = pg_escape_string($_REQUEST['table']);
if (isset($_REQUEST['keyColumn']))
    $keyColumn = pg_escape_string($_REQUEST['keyColumn']);
if (isset($_REQUEST['displayValueColumn']))
    $displayValueColumn = pg_escape_string($_REQUEST['displayValueColumn']);
if (isset($_REQUEST['searchValueColumn']))    
    $searchValueColumn = pg_escape_string($_REQUEST['searchValueColumn']);
if (isset($_REQUEST['count']))    
    $count = pg_escape_string($_REQUEST['count']);
if (isset($_REQUEST['currentTree']))
	$root = $_REQUEST['currentTree'];
else
	$root = 0;

	
if (isset($_REQUEST['shot']) && $_REQUEST['shot'] == 'yes')
	$whereFilter = "parent = $root and $keyColumn in (select distinct parent from $table where deleted=0)";
else
	$whereFilter = "$keyColumn in (select kod from {$table}_tree where parent=$root)";
	
if (isset($_REQUEST['currentKey']))
    $currentKey = pg_escape_string($_REQUEST['currentKey']);
	
	

if (isset($_REQUEST['currentSearchValue'])){
    $currentSearchValue = iconv("UTF-8", "windows-1251", $_REQUEST['currentSearchValue']); //pg_escape_string(iconv("UTF-8", "windows-1251", $_REQUEST['currentSearchValue']));
	if (substr($currentSearchValue,-1) === ' ')
		$currentSearchValuePrepared = substr($currentSearchValue,0, -1) . '%';
	else
		$currentSearchValuePrepared = implode('%',preg_split('//',$currentSearchValue));
}else{
    $currentSearchValue = '';
	$currentSearchValuePrepared = '%';
}
	

	
//if (isset($_REQUEST['command']) &&  $_REQUEST['command'] == 'previous'){   
//if (isset($_REQUEST['command']) &&  $_REQUEST['command'] == 'previous' && isset($_REQUEST['currentKey'])){

//	$result = pg_query($db,
//		"select $keyColumn as field1, $displayValueColumn as field2," 
//		. " $searchValueColumn as field3 from $table" 
//		. " where ($searchValueColumn) like ('$currentSearchValuePrepared')"
//		. " and $keyColumn <= '$currentKey'"
//		. " order by field3 desc, field1 desc limit $count"
//		. '');
//
//	if (! $result){
//		echo '/*<errorcode>' . 3 . '</errorcode>';
//		echo '<error>' . pg_last_error($db) . '</error></response>*/';
//		die();
//	}
//
//	while ($result and $row = pg_fetch_row($result)){
 //       $currentKey = $row[0];
//	}
//}



////////////////////////////////////////////////////////////////

$result = FALSE;

if (isset($_REQUEST['command']) &&  $_REQUEST['command'] == 'init'){   
	$result = pg_query($db,
		"select $keyColumn as field1, $displayValueColumn as field2," 
		. " $searchValueColumn as field3 from $table" 
		. " where $keyColumn = '$currentKey' and deleted=0 order by field3 asc, field1 limit 1"
		. '');
} elseif (isset($_REQUEST['currentKey'])) {
	$result = pg_query($db,
		"select $keyColumn as field1, $displayValueColumn as field2," 
		. " $searchValueColumn as field3 from $table" 
		. " where $keyColumn = '$currentKey' and deleted=0 order by field3 asc, field1 limit 1"
		. '');
	if ($result and $row = pg_fetch_row($result)) {
		$currentSearchValueByKey = $row[2];
		if (isset($_REQUEST['command']) &&  $_REQUEST['command'] == 'previous') {
			$result = pg_query($db,
				"select $keyColumn as field1, $displayValueColumn as field2," 
				. " $searchValueColumn as field3 from $table" 
				. " where $whereFilter and $searchValueColumn ilike '$currentSearchValuePrepared' and deleted=0"
				. " and ($searchValueColumn = '$currentSearchValueByKey' and $keyColumn <= '$currentKey'"
				. " or $searchValueColumn < '$currentSearchValueByKey')"
				. " order by field3 desc, field1 desc limit $count"
				. '');
			while ($result and $row = pg_fetch_row($result))
				$currentKey = $row[0];
			$result = pg_query($db,
				"select $keyColumn as field1, $displayValueColumn as field2," 
				. " $searchValueColumn as field3 from $table" 
				. " where $keyColumn = '$currentKey'  and deleted=0 order by field3 desc, field1 limit 1"
				. '');
			if ($result and $row = pg_fetch_row($result))
				$currentSearchValueByKey = $row[2];
		}
		$result = pg_query($db,
			"select $keyColumn as field1, $displayValueColumn as field2," 
			. " $searchValueColumn as field3 from $table" 
			. " where $whereFilter and  $searchValueColumn ilike '$currentSearchValuePrepared' and deleted=0"
			. " and ($searchValueColumn = '$currentSearchValueByKey' and $keyColumn >= '$currentKey'"
			. " or $searchValueColumn > '$currentSearchValueByKey')"
			. " order by field3, field1 limit $count"
			. '');
	}
} elseif (isset($_REQUEST['currentSearchValue'])) {
		$result = pg_query($db,
			"select $keyColumn as field1, $displayValueColumn as field2," 
			. " $searchValueColumn as field3 from $table" 
			. " where $whereFilter and  $searchValueColumn ilike '$currentSearchValuePrepared' and deleted=0"
			. " order by field3, field1 limit $count"
			. '');
} else {
	$result = pg_query($db,
			"select $keyColumn as field1, $displayValueColumn as field2," 
			. " $searchValueColumn as field3 from $table  where $whereFilter and  deleted=0" 
			. " order by field3, field1 limit $count");

}
    echo "bhv.scriptConteiner.responseJSON = [[";
    $rowcounter = 0;
    while ($result and $row = pg_fetch_row($result)){
    	$fieldcounter = 0;
    	if ($rowcounter++)
    		echo ',[';//'<row>';
    	else
    		echo '[';//'<row>';
		$field0 = co_full_path($db,$row[0],$root);
		$field1 = co_shot_path($db,$row[0],$root);
		echo "{$row[0]}, '$field0', '$field1'";
        /*foreach ($row as $field){
    		//$field = addslashes($field);
    		$field = str_replace("\\", "\\\\", $field);
    		$field = str_replace('\'', '\\\'', $field);
    		if ($fieldcounter++)
    			echo ",'$field'";//"<field>$field</field>";
    		else
    			echo "'$field'";//"<field>$field</field>";
    	}*/
        echo ']';//'</row>\n';
    }

	
echo '], [';

$is_end = false;
$root = $_REQUEST['currentTree'];
$rowcounter = 0;
while (! $is_end){
	$is_end = true;
	$result = pg_query($db,"select * from co where kod='$root' and deleted=0 order by name");	
	if ($result){ 
		while ($row = pg_fetch_array($result)){
			$root = $row['parent'];
			if ($root !==0){
				$is_end = false;
			}
			echo '[';//'<row>';
			//$fieldto = str_replace("\\", "\\\\", $row['parent']);
			//$fieldto = str_replace('\'', '\\\'', $fieldto);
			$field = str_replace("\\", "\\\\", $row['name']);
			$field = str_replace('\'', '\\\'', $field);
			echo "{$row['kod']}, '$field', '$field'],";
		}
	}
}
echo "[0, '...', '...']]];";

function co_full_path($db, $kod1, $root1){
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

function co_shot_path($db, $kod1, $root1){
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
			if ($kod1 !==0 and $kod1 !== $root1){
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

    
//echo 'alert(bhv.scriptConteiner.responseJSON[1]);';
?>
