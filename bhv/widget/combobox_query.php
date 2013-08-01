<?php
function mb_str_split( $string ) {
    # Split at all position not after the start: ^
    # and not before the end: $
    return preg_split('/(?<!^)(?!$)/u', $string );
} 
header('Content-type: text/javascript; charset="windows-1251"');
require_once('../../db/setting.php');
if (isset($_REQUEST['table']))
    $table = substr($db->quote($_REQUEST['table']), 1, -1) ;
if (isset($_REQUEST['keyColumn']))
    $keyColumn = '"' . substr($db->quote($_REQUEST['keyColumn']), 1, -1) . '"';
if (isset($_REQUEST['displayValueColumn']))
    $displayValueColumn = '"' . substr($db->quote($_REQUEST['displayValueColumn']), 1, -1) . '"';
if (isset($_REQUEST['searchValueColumn']))    
    $searchValueColumn = '"' . substr($db->quote($_REQUEST['searchValueColumn']), 1, -1) . '"';
if (isset($_REQUEST['addonce']))    
    $addonce = ' ,' . substr($db->quote($_REQUEST['addonce']), 1, -1);
else 
    $addonce = '';

    
if (isset($_REQUEST['count']))    
    $count = substr($db->quote($_REQUEST['count']), 1, -1);

	
if (isset($_REQUEST['currentKey']))
    $currentKey = $db->quote($_REQUEST['currentKey']);
	
//mb_regex_encoding('UTF-8');	

if (isset($_REQUEST['currentSearchValue'])){
    $currentSearchValue = $_REQUEST['currentSearchValue'];
$currentSearchValue = iconv("UTF-8", "windows-1251", $_REQUEST['currentSearchValue']);
//$db->quote(iconv("UTF-8", "windows-1251", $_REQUEST['currentSearchValue']));
	if (substr($currentSearchValue,-1) === ' ')
		$currentSearchValuePrepared = substr($currentSearchValue,0, -1) . '%';
	elseif (isset($_REQUEST['exactly']))
		$currentSearchValuePrepared = $currentSearchValue . '%';		
	else
	    $currentSearchValuePrepared =  implode('%', preg_split('//',$currentSearchValue));
	    //$currentSearchValuePrepared =  implode('%', preg_split('//u',$currentSearchValue));
} else {
	$currentSearchValue = '';
	$currentSearchValuePrepared = '%';
}

$currentSearchValuePrepared = $db->quote($currentSearchValuePrepared);


////////////////////////////////////////////////////////////////

$result = FALSE;

if (isset($_REQUEST['filter'])){
  $filter = ' and ' . $_REQUEST['filter'] . ' ';
  $wherefilter = ' where ' . $_REQUEST['filter'] . ' ';
}else{
  $wherefilter = $filter = ' ';
}

if (isset($_REQUEST['command']) &&  $_REQUEST['command'] == 'init'){   
	$result = $db->query(
		"select $keyColumn as field1, $displayValueColumn as field2," 
		. " $searchValueColumn as field3 $addonce from $table" 
		///////////////////////. " where $keyColumn = '$currentKey'$filter order by field3 desc, field1 limit 1"
		. " where $keyColumn = $currentKey order by field3 desc, field1 limit 1"
		. '');
} elseif (isset($_REQUEST['currentKey'])) {
	$result = $db->query(
		"select $keyColumn as field1, $displayValueColumn as field2," 
		. " $searchValueColumn as field3 $addonce from $table" 
		////////////////////////////. " where $keyColumn = '$currentKey' $filter order by field3 desc, field1 limit 1"
		. " where $keyColumn = $currentKey order by field3 desc, field1 limit 1"
		. '');
	if ($result and $row = $result->fetch(PDO::FETCH_NUM)) {
		$currentSearchValueByKey = $db->quote($row[2]);
		if (isset($_REQUEST['command']) &&  $_REQUEST['command'] == 'previous') {
			$result = $db->query(
				"select $keyColumn as field1, $displayValueColumn as field2," 
				. " $searchValueColumn as field3 $addonce from $table" 
				. " where $searchValueColumn ilike $currentSearchValuePrepared"
				. " and ($searchValueColumn = $currentSearchValueByKey and $keyColumn <= $currentKey"
				. " or $searchValueColumn < $currentSearchValueByKey) $filter"
				. " order by field3 desc, field1 desc limit $count"
				. '');
			while ($result and $row = $result->fetch(PDO::FETCH_NUM))
				$currentKey = $db->quote($row[0]);
			$result = $db->query(
				"select $keyColumn as field1, $displayValueColumn as field2," 
				. " $searchValueColumn as field3 $addonce from $table" 
				////////////////. " where $keyColumn = '$currentKey' $filter order by field3 desc, field1 limit 1"
				. " where $keyColumn = $currentKey order by field3 desc, field1 limit 1"
				. '');
			if ($result and $row = $result->fetch(PDO::FETCH_NUM))
				$currentSearchValueByKey = $db->quote($row[2]);
		}
		$result = $db->query(
			"select $keyColumn as field1, $displayValueColumn as field2," 
			. " $searchValueColumn as field3 $addonce from $table" 
			. " where $searchValueColumn ilike $currentSearchValuePrepared"
			. " and ($searchValueColumn = $currentSearchValueByKey and $keyColumn >= $currentKey"
			. " or $searchValueColumn > $currentSearchValueByKey) $filter"
			. " order by field3, field1 limit $count"
			. '');
	}
} elseif (isset($_REQUEST['currentSearchValue'])) {
		$result = $db->query(
			"select $keyColumn as field1, $displayValueColumn as field2," 
			. " $searchValueColumn as field3 $addonce from $table" 
			. " where $searchValueColumn ilike $currentSearchValuePrepared $filter"
			. " order by field3, field1 limit $count"
			. '');
} else {////////////////////////////////////////////////////////////////////////What is it $filter withiyn
	$result = $db->query(
			"select $keyColumn as field1, $displayValueColumn as field2," 
			. " $searchValueColumn as field3 $addonce from $table $wherefilter " 
			. " order by field3, field1 limit $count");

}
    echo "/*select $keyColumn as field1, $displayValueColumn as field2," 
			. " $searchValueColumn as field3 $addonce from $table" 
			. " where $searchValueColumn ilike $currentSearchValuePrepared $filter"
			. " order by field3, field1 limit $count bhv.scriptConteiner.responseJSON = */[";
    $rowcounter = 0;
    while ($result and $row = $result->fetch(PDO::FETCH_NUM)){
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
