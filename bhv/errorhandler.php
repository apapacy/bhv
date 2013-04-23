<?php
function errorhandler($errorcode, $message, $file, $line){
    global $errorhandler_is_ok,
        $errorhandler_errorline,
        $errorhandler_errormessage;
    if ($errorhandler_is_ok){
        $errorhandler_errorline = $line;
        $errorhandler_errormessage = $message;
    }
    $errorhandler_is_ok = false;
    #ob_clean();
    header("BHV-errorcode: $errorcode");
    header("BHV-errormessage: $message");
    header("BHV-errorfile: $file");
    header("BHV-errorline: $line");
    echo "Error: #$errorcode - $message in $file line $line";
}
$errorhandler_is_ok = true;
$errorhandler_errorline = -1;
$errorhandlererrormessage = '';
set_error_handler("errorhandler");

?>