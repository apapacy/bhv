<?php header('Content-type: text/html; charset="windows-1251"');?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
                      "http://www.w3.org/TR/html4/loose.dtd">
<head>
<title>Производственная структура (центры обработки)</title>
<style type="text/css">
body{
background-color: #bbbccc;
}

td.co_left{
border-left: solid 1px;
border-right: solid 1px;
border-top: none;
border-bottom: none;
width:20px;
}

td.co_button{
border:outset;
background-color: #cccccc;
}
</style>
<script id="bhv_util_script" type="text/javascript" src="../../../bhv/util.js"></script>
</head>

<?php
error_reporting(E_ALL+E_STRICT);

require_once('../../../db/setting.php');

if (empty($username))
    $db = pg_pconnect("host=$host dbname=$database");
elseif (empty($password))
	$db = pg_pconnect("host=$host dbname=$database user=$username");
else
	$db = pg_pconnect("host=$host dbname=$database user=$username password=$password");

if (isset($_REQUEST['cennicKod'])){
    $cennic_kod = $_REQUEST['cennicKod'];
}
	

$result = pg_query($db,' select distinct kod, name from co where parent=0 and deleted=0 order by name');
?>
<div id="id_div_left" style="height:100%;width:300px; border:none; overflow:scroll;float:left">
<?php
if ($result) 
	while ($row = pg_fetch_array($result))
		echo "<a href='javascript:f_show_co_a(${row['kod']})'>${row['name']}</a><br>";

?>
</div>
<div id="id_div_right" style="height:99%; width:79%; border:none;overflow:hidden">
<div  style="height:60px; width:99%; border:none;overflow:hidden">
Выбор: <span id=combo1 style="width:300px"></span> 
<input type=button name="name_button_show_co" id="id_button_show_co" value="Показать" onclick="f_show_co(this);" />
<input type=checkbox name="name_checkbox_show_co" id="id_checkbox_show_co" onclick="f_show_co(this)"> Сокр. 
<input type=button name="name_button_show_co" id="id_button_show_co" value="Журнал изм." onclick="f_show_izm(this);" />
<div style="padding:4px 0px 0px 0px ">
<!--Входящая: <span id=combo2></span>-->
Наимен.: <input type=text name="name_input_co_name" id="id_input_co_name" value=" " />
Кол-во: <input type=text name="name_input_co_kol" id="id_input_co_kol" value="0" size="8"/>
Фр.в.: <input type=text name="name_input_co_fef" id="id_input_co_fef" value="0" size="8"/>

</div>
</div>
<div id="id_div_render_co" style="height:89%; width:99%; border:none;overflow:scroll;z-index:0"></div>
</div>

<script>
var button_show_co = bhv.getElementById("id_button_show_co");
var checkbox_show_co = bhv.getElementById("id_checkbox_show_co");
var input_co_name = bhv.getElementById("id_input_co_name");
var input_co_kol = bhv.getElementById("id_input_co_kol");
var input_co_fef = bhv.getElementById("id_input_co_fef");
var button_show_co = bhv.getElementById("id_button_show_co");
var div_render_co = bhv.getElementById("id_div_render_co");
//var div_root = bhv.getElementById("id_div_root");
var div_left = bhv.getElementById("id_div_left");
var div_right = bhv.getElementById("id_div_right");

var window_innerHeight = window.innerHeight ? window.innerHeight : document.body.clientHeight
var window_innerWidth = window.innerWidth ? window.innerWidth : document.body.clientWidth
//div_root.style.height=window.innerHeight-10+"px";
div_left.style.height=(window_innerHeight-30)+"px";
div_right.style.height=(window_innerHeight-30)+"px";
div_left.style.width=300-20+"px";
div_right.style.width=(window_innerWidth-300-20)+"px";
div_render_co.style.height=(window_innerHeight-30-60)+"px";
var combobox1 = new bhv.Combotree("combo1", null, 0, 10,
     "co", "kod", "name", "name")
//var combobox2 = new bhv.Combobox("combo2", null, 0, 10,
//     "cennic", "kod", "name", "det")

var global_co_co = 0;

function f_delete_kod(self){
self.style.borderStyle = "inset";
bhv.sendScriptRequest("co_delete_kod.php", "co="+ global_co_co /*combobox1.getValue()*/ + "&" + self.parentNode.title + "&shot="+(checkbox_show_co.checked ? 1 : 0) ,  handleRequest, [0,1]);	
}

function f_insert_kod(parent, self){
self.style.borderStyle = "inset";
bhv.sendScriptRequest("co_insert_kod.php", "co=" + global_co_co /*combobox1.getValue()*/ + "&" + "parent=" + parent + "&name=" +encodeURIComponent(input_co_name.value) + "&kol=" +input_co_kol.value + "&fef=" +input_co_fef.value + "&shot="+(checkbox_show_co.checked ? 1 : 0),  handleRequest, [0,1]);	
}

function f_kol_kod(self){
self.style.borderStyle = "inset";
bhv.sendScriptRequest("co_kol_kod.php", "co="+ global_co_co /*combobox1.getValue()*/ + "&kol=" +input_co_kol.value + "&fef=" +input_co_fef.value + "&" + self.parentNode.title + "&shot="+(checkbox_show_co.checked ? 1 : 0),  handleRequest, [0,1]);	
}

function f_show_izm(self){
bhv.sendScriptRequest("co_show_izm.php", "",  handleRequest, [0,1]);	
}

</script>

<script>
function f_show_co(self){
global_co_co = combobox1.getValue();
bhv.sendScriptRequest("render_co.php", "co=" + global_co_co /*combobox1.getValue()*/ + "&shot="+(checkbox_show_co.checked ? 1 : 0),  handleRequest, [0,1]);
}
function f_show_co_a(co){
global_co_co = co;
combobox1.setValue(co);
bhv.sendScriptRequest("render_co.php", "co=" + co + "&shot="+(checkbox_show_co.checked ? 1 : 0),  handleRequest, [0,1]);
}
function handleRequest(a,b){
//alert(b)
}
f_show_co_a(1)
</script>