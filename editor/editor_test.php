<?php
header("Content-Type: text/html; charset=UTF-8");

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
    echo "Error: #$errorcode - $message in $file line $line";
}


$editor_test_is_ok = true;
$editor_test_errorline = -1;
$editor_test_errormessage = '';

ob_start();

set_error_handler("eh");
error_reporting(E_ALL+E_STRICT);

$filename = $_REQUEST['filename'];
$tmp_filename = "edit_debug.php";
#$sourcecode = iconv('UTF-8', 'windows-1251', $_REQUEST['sourcecode']);
$sourcecode = $_REQUEST['sourcecode'];


$file_handler = fopen($tmp_filename, "w+");
fwrite($file_handler, $sourcecode);
fclose($file_handler);

$real_tmp_path = realpath($tmp_filename);
chdir("../");
chdir(dirname($filename));

$editor_test_is_ok = true;

ini_set("display_errors", "TRUE");
include($real_tmp_path);
ini_set("display_errors", "FALSE");

if (! $editor_test_is_ok){
    ob_clean();
    $highlighted_string = highlight_string($sourcecode, true);
    $chrpos = 0;
    $chrpos0 = 0;
    for ($i = 0; $i < $editor_test_errorline-1; $i++){
        $chrpos = stripos($highlighted_string, '<br', $chrpos + 1);
        $chrpos0 = stripos($highlighted_string, '>', $chrpos + 1) + 1;
    }
    $chrpos1 = stripos($highlighted_string, '<br', $chrpos + 1);

    $highlighted_string = '<div style="font-size:10pt;border:groove #bbbbbb 2px;background:#ccccdd">' 
                                     . "<div style='font-size:12pt;background:peru;color:white;padding:4;border:outset #bbbbbb 2px;padding:2px'>Листинг скрипта $filename</div>" 
                                     . substr($highlighted_string,0,$chrpos)
                                     . '<div style="border-color:red;color:red;border-style:solid;border-width:4;padding:8">'
                                     . "Error: $editor_test_errormessage</div>"
                                     . substr($highlighted_string, $chrpos0, $chrpos1 - $chrpos0)
                                     . substr($highlighted_string, $chrpos1) . '</div>';
    echo($highlighted_string);
}

ob_end_flush();

?>

