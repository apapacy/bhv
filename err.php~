<?php

echo $db = pg_pconnect("host=localhost dbname=test user=Administrator ");
echo $ex = pg_query($db, "select * from products order by name");
$ar = pg_fetch_all($ex);
print_r($ar);

?>