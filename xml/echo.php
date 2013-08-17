<?php
header("Content-Type: application/xml");
echo iconv("UTF-8", "Windows-1251",$HTTP_RAW_POST_DATA);
#echo $HTTP_RAW_POST_DATA;
?>