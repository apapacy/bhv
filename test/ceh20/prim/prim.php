<?php header('Content-type: text/html; charset="windows-1251"');?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
                      "http://www.w3.org/TR/html4/loose.dtd">
<head>
<title>Состав сборочных еднниц</title>
<style type="text/css">
body{
background-color: #bbbccc;
}

td.prim_left{
border-left: solid 1px;
border-right: solid 1px;
border-top: none;
border-bottom: none;
width:20px;
}

td.prim_button{
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
	

$result = pg_query($db,' select distinct p.ce, c.name from prim p left outer join cennic c on c.kod=p.ce where p.kol>0 order by c.name');
?>
<div id="id_div_left" style="height:99%;width:20%; border:none; overflow:scroll;float:left">
<?php
if ($result) 
	while ($row = pg_fetch_array($result))
		echo "<a href='javascript:f_show_sb_a(${row['ce']})'>${row['name']}</a><br>";

?>
</div>
<div id="id_div_right" style="height:99%; width:80%; border:none;overflow:hidden">
<div  style="height:60px; width:99%; border:none;overflow:hidden">
Сб. ед.: <span id=combo1></span> 
<input type=button name="name_button_show_sb" id="id_button_show_sb" value="Показать состав сборочной единицы" onclick="f_show_sb(this);" />
<input type=checkbox name="name_checkbox_show_sb" id="id_checkbox_show_sb" onclick="f_show_sb(this)"> Сокр.
<div style="padding:4px 0px 0px 0px ">Входящая: <span id=combo2></span> Кол-во: 
<input type=text name="name_input_sb_kol" id="id_input_sb_kol" value="0" />
<input type=button name="name_button_show_sb" id="id_button_show_sb" value="Изменения" onclick="f_show_izm(this);" />
</div>
</div>
<div id="id_div_render_sb" style="height:89%; width:99%; border:none;overflow:scroll;z-index:0"></div>
</div>

<script>
var button_show_sb = bhv.getElementById("id_button_show_sb");
var checkbox_show_sb = bhv.getElementById("id_checkbox_show_sb");
var input_sb_kol = bhv.getElementById("id_input_sb_kol");
var button_show_sb = bhv.getElementById("id_button_show_sb");
var div_render_sb = bhv.getElementById("id_div_render_sb");
//var div_root = bhv.getElementById("id_div_root");
var div_left = bhv.getElementById("id_div_left");
var div_right = bhv.getElementById("id_div_right");

var window_innerHeight = window.innerHeight ? window.innerHeight : document.body.clientHeight
var window_innerWidth = window.innerWidth ? window.innerWidth : document.body.clientWidth
//div_root.style.height=window.innerHeight-10+"px";
div_left.style.height=window_innerHeight-30+"px";
div_right.style.height=window_innerHeight-30+"px";
div_left.style.width=(300-20)+"px";
div_right.style.width=(window_innerWidth-300-20)+"px";
div_render_sb.style.height=(window_innerHeight-30-60)+"px";
var combobox1 = new bhv.Combobox("combo1", null, 0, 10,
     "cennic", "kod", "name", "name")
var combobox2 = new bhv.Combobox("combo2", null, 0, 10,
     "cennic", "kod", "name", "name")

function f_delete_kd(self){
self.style.borderStyle = "inset";
bhv.sendScriptRequest("prim_delete_kd.php", "sb="+combobox1.getValue() + "&" + self.parentNode.title + "&shot="+(checkbox_show_sb.checked ? 1 : 0) ,  handleRequest, [0,1]);	
}

function f_insert_kd(ce, self){
self.style.borderStyle = "inset";
bhv.sendScriptRequest("prim_insert_kd.php", "sb="+combobox1.getValue() + "&" + "ce=" + ce + "&kd="+combobox2.getValue() + "&kol=" +input_sb_kol.value + "&shot="+(checkbox_show_sb.checked ? 1 : 0),  handleRequest, [0,1]);	
}

function f_kol_kd(self){
self.style.borderStyle = "inset";
bhv.sendScriptRequest("prim_kol_kd.php", "sb="+combobox1.getValue() + "&" + "kol=" +input_sb_kol.value + "&" + self.parentNode.title + "&shot="+(checkbox_show_sb.checked ? 1 : 0),  handleRequest, [0,1]);	
}

function f_show_izm(self){
bhv.sendScriptRequest("prim_show_izm.php", "",  handleRequest, [0,1]);	
}

</script>

<script>
function f_show_sb(self){
bhv.sendScriptRequest("render_prim.php", "sb="+combobox1.getValue() + "&shot="+(checkbox_show_sb.checked ? 1 : 0),  handleRequest, [0,1]);
}
function f_show_sb_a(sb){
combobox1.setValue(sb);
bhv.sendScriptRequest("render_prim.php", "sb="+sb + "&shot="+(checkbox_show_sb.checked ? 1 : 0),  handleRequest, [0,1]);
}
function handleRequest(a,b){
//alert(b)
}
</script>