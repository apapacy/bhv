<?php
header("Content-Type: text/html; charset=windows-1251");

ob_start();

function eh($errorcode, $message, $file, $line){
    ob_clean();
    header("BHV-errorcode: $errorcode");
    header("BHV-errormessage: $message");
    header("BHV-errorfile: $file");
    header("BHV-errorline: $line");
    echo "Error: #$errorcode - $message in $file line $line \n";
    die('Файл не сохранен');
}

set_error_handler("eh");

chdir("../");

#$sourcecode = iconv('UTF-8', 'windows-1251', $_GET['sourcecode']);
$sourcecode = $_POST['sourcecode'];
$filename = $_POST['filename'];

if (! file_exists(dirname($filename)))
    mkdir(dirname($filename), 0777, TRUE);

if (file_exists($filename)){
    $i = 0;
    while (file_exists($filename . $i)) 
        $i++;
    copy($filename, $filename . $i);
}

$file_handler = fopen($filename, "w+");
fwrite($file_handler, $sourcecode);
fclose($file_handler);
echo "Файл сохранен";

ob_end_flush();

?>

