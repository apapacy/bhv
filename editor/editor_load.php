<?php
header("Content-Type: text/html; charset=UTF-8");
ob_start();

function eh($errorcode,$message,$file,$line){
    ob_clean();
    header("BHV-errorcode: $errorcode");
    header("BHV-errormessage: $message");
    header("BHV-errorfile: $file");
    header("BHV-errorline: $line");
    echo "Error: #$errorcode - $message in $file line $line";
}

set_error_handler("eh");

chdir('../');
$filename=$_GET['filename'];
readfile($filename);

ob_end_flush();
?>
