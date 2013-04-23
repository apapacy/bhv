<?php 

header('Content-type: application/javascript; charset="windows-1251"');
require_once('../bhv/errorhandler.php');
error_reporting(E_ALL |  E_STRICT);

//$host = 'localhost';
//$database = 'Ceh16';
//$username = 'root';
//$password = '26682316';

require_once('../db/setting.php');

if (empty($username))
    $db = pg_pconnect("host=$host dbname=$database");
elseif (empty($password))
	$db = pg_pconnect("host=$host dbname=$database user=$username");
else
	$db = pg_pconnect("host=$host dbname=$database user=$username password=$password");



if (isset($_REQUEST['kod']))
    $kod = $_REQUEST['kod'];
else
	die("alert('Ошибка!);");



$result = pg_query("select * from zadan where kod=$kod");
if ($result and $row = pg_fetch_array($result)) {
	echo "bhv.scriptConteiner['cex'] = '" . render_zadan_field($row['cex']) ."';"
	. "bhv.scriptConteiner['nop'] = {$row['nop']};"
	. "bhv.scriptConteiner['tipop'] = {$row['tipop']};"
	. "bhv.scriptConteiner['raz'] = {$row['raz']};"
	. "bhv.scriptConteiner['platmin'] = {$row['platmin']};"
	. "bhv.scriptConteiner['kod'] = {$row['kod']};"
	. "bhv.scriptConteiner['parent'] = {$row['parent']};";

}else
	die( "alert('Ошибка!');");

/*
CREATE TABLE zadan
(
  kod numeric(10,0) NOT NULL,
  parent numeric(10,0),
  cex character varying(7),
  nop numeric(6,1),
  tipop numeric(5,0),
  raz numeric(1,0),
  zadan numeric(10,3),
  stan numeric(5,0),
  prim text,
  nvr numeric(10,3),
  zpl numeric(10,3),
  nzpcmin numeric(10,3),
  nzpckop numeric(10,3),
  nzpzmin numeric(10,3),
  nzpzkop numeric(10,3),
  otzmin numeric(10,3),
  otzkop numeric(10,3),
  ras numeric(10,3),
  tab text,
  platmin numeric(10,3),
  platkop numeric(10,3),
  platmin090517 numeric(10,3),
  platkop090517 numeric(10,3),
  tab090517 character varying,
  CONSTRAINT "Zanat_PK_KOD" PRIMARY KEY (kod),
  CONSTRAINT zadan_cen FOREIGN KEY (parent)
      REFERENCES cennic (kod) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT
)
*/
function render_zadan_field($field){
	$field = str_replace("\\", "\\\\", $field);
	return str_replace('\'', '\\\'', $field);
}


?>