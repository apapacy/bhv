<?php
function mb_str_split( $string ) {
    # Split at all position not after the start: ^
    # and not before the end: $
    return preg_split('/(?<!^)(?!$)/u', $string );
} 

header('Content-type: text/javascript; charset="UTF-8"');
//require_once('../bhv/errorhandler.php');
//error_reporting(E_ALL |  E_STRICT);
//header('Content-type: text/javascript; charset="windows-1251"');

require_once('../db/setting.php');

//$host = 'localhost';
//$database = 'test';
//$username = 'Administrator';
//$password = '';


/*echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n";
echo '<response>';*/

if (!$db){
    echo '/*<errorcode>' . 1 . '</errorcode>';
    echo '<error>' . pg_last_error($db) . '</error></response>*/';
    die();
}
//pg_client_encoding($db, 'UTF8');
//mb_regex_encoding('UTF8');
if (isset($_REQUEST['table']))
    $table = pg_escape_string($_REQUEST['table']);
if (isset($_REQUEST['keyColumn']))
    $keyColumn = pg_escape_string($_REQUEST['keyColumn']);
if (isset($_REQUEST['displayValueColumn']))
    $displayValueColumn = pg_escape_string($_REQUEST['displayValueColumn']);
if (isset($_REQUEST['searchValueColumn']))    
    $searchValueColumn = pg_escape_string($_REQUEST['searchValueColumn']);
if (isset($_REQUEST['addonce']))    
    $addonce = ' ,' . pg_escape_string($_REQUEST['addonce']);
else 
    $addonce = '';

    
if (isset($_REQUEST['count']))    
    $count = pg_escape_string($_REQUEST['count']);

	
if (isset($_REQUEST['currentKey']))
    $currentKey = pg_escape_string($_REQUEST['currentKey']);
	
//mb_regex_encoding('UTF-8');	

if (isset($_REQUEST['currentSearchValue'])){
    $currentSearchValue = $_REQUEST['currentSearchValue'];
//iconv("UTF-8", "windows-1251", $_REQUEST['currentSearchValue']); //pg_escape_string(iconv("UTF-8", "windows-1251", $_REQUEST['currentSearchValue']));
	if (substr($currentSearchValue,-1) === ' ')
		$currentSearchValuePrepared = substr($currentSearchValue,0, -1) . '%';
	elseif (isset($_REQUEST['exactly']))
		$currentSearchValuePrepared = $currentSearchValue . '%';		
	else
	    $currentSearchValuePrepared =  implode('%', preg_split('//u',$currentSearchValue));
} else {
	$currentSearchValue = '';
	$currentSearchValuePrepared = '%';
}

$currentSearchValuePrepared = pg_escape_string($currentSearchValuePrepared);

	
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

if (isset($_REQUEST['filter'])){
  $filter = ' and ' . $_REQUEST['filter'] . ' ';
  $wherefilter = ' where ' . $_REQUEST['filter'] . ' ';
}else{
  $wherefilter = $filter = ' ';
}

if (isset($_REQUEST['command']) &&  $_REQUEST['command'] == 'init'){   
	$result = pg_query($db,
		"select $keyColumn as field1, $displayValueColumn as field2," 
		. " $searchValueColumn as field3 $addonce from $table" 
		///////////////////////. " where $keyColumn = '$currentKey'$filter order by field3 desc, field1 limit 1"
		. " where $keyColumn = '$currentKey' order by field3 desc, field1 limit 1"
		. '');
} elseif (isset($_REQUEST['currentKey'])) {
	$result = pg_query($db,
		"select $keyColumn as field1, $displayValueColumn as field2," 
		. " $searchValueColumn as field3 $addonce from $table" 
		////////////////////////////. " where $keyColumn = '$currentKey' $filter order by field3 desc, field1 limit 1"
		. " where $keyColumn = '$currentKey' order by field3 desc, field1 limit 1"
		. '');
	if ($result and $row = pg_fetch_row($result)) {
		$currentSearchValueByKey = $row[2];
		if (isset($_REQUEST['command']) &&  $_REQUEST['command'] == 'previous') {
			$result = pg_query($db,
				"select $keyColumn as field1, $displayValueColumn as field2," 
				. " $searchValueColumn as field3 $addonce from $table" 
				. " where $searchValueColumn ilike '$currentSearchValuePrepared'"
				. " and ($searchValueColumn = '$currentSearchValueByKey' and $keyColumn <= '$currentKey'"
				. " or $searchValueColumn < '$currentSearchValueByKey') $filter"
				. " order by field3 desc, field1 desc limit $count"
				. '');
			while ($result and $row = pg_fetch_row($result))
				$currentKey = $row[0];
			$result = pg_query($db,
				"select $keyColumn as field1, $displayValueColumn as field2," 
				. " $searchValueColumn as field3 $addonce from $table" 
				////////////////. " where $keyColumn = '$currentKey' $filter order by field3 desc, field1 limit 1"
				. " where $keyColumn = '$currentKey' order by field3 desc, field1 limit 1"
				. '');
			if ($result and $row = pg_fetch_row($result))
				$currentSearchValueByKey = $row[2];
		}
		$result = pg_query($db,
			"select $keyColumn as field1, $displayValueColumn as field2," 
			. " $searchValueColumn as field3 $addonce from $table" 
			. " where $searchValueColumn ilike '$currentSearchValuePrepared'"
			. " and ($searchValueColumn = '$currentSearchValueByKey' and $keyColumn >= '$currentKey'"
			. " or $searchValueColumn > '$currentSearchValueByKey') $filter"
			. " order by field3, field1 limit $count"
			. '');
	}
} elseif (isset($_REQUEST['currentSearchValue'])) {
		$result = pg_query($db,
			"select $keyColumn as field1, $displayValueColumn as field2," 
			. " $searchValueColumn as field3 $addonce from $table" 
			. " where $searchValueColumn ilike '$currentSearchValuePrepared' $filter"
			. " order by field3, field1 limit $count"
			. '');
} else {////////////////////////////////////////////////////////////////////////What is it $filter withiyn
	$result = pg_query($db,
			"select $keyColumn as field1, $displayValueColumn as field2," 
			. " $searchValueColumn as field3 $addonce from $table $wherefilter " 
			. " order by field3, field1 limit $count");

}
    echo "/*bhv.scriptConteiner.responseJSON = */[";
    $rowcounter = 0;
    while ($result and $row = pg_fetch_row($result)){
    	$fieldcounter = 0;
    	if ($rowcounter++)
    		echo ',[';//'<row>';
    	else
    		echo '[';//'<row>';
        foreach ($row as $field){
    		//$field = addslashes($field);
    		$field = str_replace("\\", "\\\\", $field);
    		$field = str_replace('\'', '\\\'', $field);
    		if ($fieldcounter++)
    			echo ",'$field'";//"<field>$field</field>";
    		else
    			echo "'$field'";//"<field>$field</field>";
    	}
        echo ']';//'</row>\n';
    }
    
    echo ']/*;*/'//'</response>';
?>
