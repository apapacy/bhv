<?php


header('Content-Type: application/xml; charset="Windows-1251"');

function eh($errorcode, $message, $file, $line){
    global $editor_test_is_ok,
        $editor_test_errorline,
        $editor_test_errormessage;

    if ($editor_test_is_ok){
        $editor_test_errorline = $line;
        $editor_test_errormessage = $message;
    }

    $editor_test_is_ok = false;
    ob_clean();
    header("BHV-errorcode: $errorcode");
    header("BHV-errormessage: $message");
    header("BHV-errorfile: $file");
    header("BHV-errorline: $line");
    echo "<error>Error: #$errorcode - $message in $file line $line</error>";
}


$editor_test_is_ok = true;
$editor_test_errorline = -1;
$editor_test_errormessage = '';

ob_start();

set_error_handler("eh");
error_reporting(E_ALL+E_STRICT);
echo "<?xml version=\"1.0\" encoding=\"Windows-1251\"?>\n\n";

echo '<response>';


$host = $_REQUEST['host'];

if (empty($host))
    $host = 'localhost';

$database = $_REQUEST['database'];
$username = $_REQUEST['username'];
$password = $_REQUEST['password'];

if (strtoupper($host) == 'SQLITE'){
    include('ping_sqlite.php');
    die();
}

elseif (empty($username))
    $mysqli = new mysqli($host);
elseif (empty($password))
    $mysqli = new mysqli($host, $username);
else
    $mysqli = new mysqli($host, $username, $password);

if (! $mysqli){
    echo "<error>Error: no create mysqli</error><nocreate/><noexecute/></response>";
    die();
}

$result = $mysqli->query("set character set cp1251");

$result = $mysqli->query("show databases");

if (! $result){
    echo "<error>Error: no create result</error><nocreate/><noexecute/></response>";
    die();
}


if ($mysqli->errno){
    echo '<errorcode>' . $mysqli->errno . '</errorcode>';
    echo '<error>' . $mysqli->error . '</error>';
    echo "<nocreate/><noexecute/></response>";
    die();
}

$databaselist = array();

echo '<databaselist>';

while ($row = $result->fetch_array()){
    $databaselist[] = strtoupper($row[0]);
    echo "<database>${row[0]}</database>";
}
echo '</databaselist>';

if (array_key_exists('create', $_REQUEST)){
    $mysqli->query("create database if not exists $database character set cp1251");
    if ($mysqli->errno){
        echo '<errorcode>' . $mysqli->errno . '</errorcode>';
        echo '<error>' . $mysqli->error . '</error>';
    } else {
      echo "<execute/><nocreate/></response>";
      @$mysqli->close();
      die();
    }
} 
if (array_key_exists('execute', $_REQUEST)){
    if ($mysqli->select_db($database)){
#        $sql = iconv('UTF-8', 'cp1251',$_REQUEST['execute']);
        $sql = $_REQUEST['execute'];

        $result = $mysqli->multi_query($sql);
        echo "<results>\n";
        do {
            $resultset = $mysqli->store_result();
            if ($mysqli->errno){
                echo '<errorcode>' . $mysqli->errno . '</errorcode>';
                echo '<error>' . $mysqli->error . '</error>';
            } 
            else{
                if ($resultset){
                    $fields = $resultset->fetch_fields();
                    echo "<result>\n<names>";
                    foreach ($fields as $field){
                        echo "<name>$field->name</name>";
                    }
                    echo '</names>';
                    echo '<rows>';
                    while ($row = $resultset->fetch_row()){
                        echo '<row>';
                        foreach ($row as $field){
                            echo "<field>".htmlspecialchars($field, ENT_NOQUOTES)."</field>";
                        } // endfor
                        echo '</row>';
                     } // endwhile
                    echo '</rows></result>';
                    $resultset->close();
                } //if resultset
                else{
                    echo '<result><names><name>Count of processing rows</name></names>';
                    echo '<rows><row><field>#' . $mysqli->affected_rows . '</field></row></rows></result>';
                }// if resulteset
            } //if ermo
        } while ($mysqli->next_result());
        if ($mysqli->errno){
            echo '<errorcode>' . $mysqli->errno . '</errorcode>';
            echo '<error>' . $mysqli->error . '</error>';
        } 
        echo '</results>';

    }
}


if(in_array(strtoupper($database), $databaselist)){
    echo '<nocreate/>';
    if ($mysqli->select_db($database))
        echo '<execute/>';       
}else{
    echo '<create/>';
    echo '<noexecute/>';       
}
$mysqli->close();    
echo '</response>';
?>
