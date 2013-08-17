<?php header('Content-type: text/html; charset="windows-1251"');?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
                      "http://www.w3.org/TR/html4/loose.dtd">
<head>
<title>���������� �� ������ (��.��.)</title>
<style type="text/css">
body{
background-color: #bbbccc;
}

td.nomen_left{
border-left: solid 1px;
border-right: solid 1px;
border-top: none;
border-bottom: none;
width:20px;
}

td.nomen_button{
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
	

$result = pg_query($db,' select distinct p.kod, c.name from pdb_nomen p left outer join cennic c on c.kod=p.kod order by c.name');
?>
<div id="id_div_left" style="height:100%;width:300px; border:none; overflow:scroll;float:left">
<?php
if ($result) 
	while ($row = pg_fetch_array($result))
		echo "<a href='javascript:f_show_kod_a(${row['kod']})'>${row['name']}</a><br>";

?>
</div>
<div id="id_div_right" style="height:99%; width:79%; border:none;overflow:hidden">
<div  style="height000:300px; width:96%; border:none;overflow:hidden">
������(��.��.): <span id=combo1 style="width:300px"></span> 
<!--input type=button name="name_button_show_kod" id="id_button_show_kod" value="�������� ������ ��������� �������" onclick="f_show_kod(this);" /-->
<!--input type=checkbox name="name_checkbox_show_kod" id="id_checkbox_show_kod" onclick="f_show_kod(this)"> ����������� ���>
<div style="padding:4px 0px 0px 0px; display:block " id="div_nomen_combo2">
<div id="div_nomen_combo2_inner"-->
<input type=text name="name_input_kod_kod" id="id_input_kod_kod" value="0" disabled/>
<input type=text name="name_input_kod_name" id="id_input_kod_name" value="" disabled/>
<input type=text name="name_input_kod_fullname" id="id_input_kod_fullname" value="" size=50/>
<br>�������� <span id=combo3 style="width:300px"></span>
<input type=button name="add_mat_name" id="id_add_mat_name" value="�������� �����" onclick="f_add_mat(this);" /><input type=text name="name_input_mat_name" id="id_input_mat_name" value=""/>

<br /><span id=combo4 style="width:100px"></span><input type=text name="name_input_kod_matparam" id="id_input_kod_matparam" value="" size=50/>

<br /><input type=button name=name_save_nomen_kod id=id_save_nomen_kod onclick="f_save_nomen_kod(this)" value="��������� ���������">

<br /><span id=combo2 style="width:300px"></span>
<!--input type=button name="name_button_show_kod" id="id_button_show_kod" value="�������� ������ ���������" onclick="f_show_izm(this);" /-->
<br />�����������<input type=text name="name_input_nomen_co_k" id="id_input_nomen_co_k" value="0"/>
������<input type=text name="name_input_nomen_co_part" id="id_input_nomen_co_part" value="0"/>
</div>
<!--/div>
</div-->
<div id="id_div_render_kod" style="height:89%; width:99%; border:none;overflow:scroll;z-index:0"></div>
</div>

<script>
var input_mat_name = bhv.getElementById("id_input_mat_name");
var button_show_kod = bhv.getElementById("id_button_show_kod");
var checkbox_show_kod = bhv.getElementById("id_checkbox_show_kod");
var input_kod_kod = bhv.getElementById("id_input_kod_kod");
var input_kod_name = bhv.getElementById("id_input_kod_name");
var input_kod_fullname = bhv.getElementById("id_input_kod_fullname");
var input_kod_matparam = bhv.getElementById("id_input_kod_matparam");
var input_kod_kol = bhv.getElementById("id_input_kod_kol");
var input_kod_kol = bhv.getElementById("id_input_kod_kol");
var button_show_kod = bhv.getElementById("id_button_show_kod");
var div_render_kod = bhv.getElementById("id_div_render_kod");
//var div_root = bhv.getElementById("id_div_root");
var div_left = bhv.getElementById("id_div_left");
var div_right = bhv.getElementById("id_div_right");

var window_innerHeight = window.innerHeight ? window.innerHeight : document.body.clientHeight
var window_innerWidth = window.innerWidth ? window.innerWidth : document.body.clientWidth
//div_root.style.height=window.innerHeight-10+"px";
div_left.style.height=window_innerHeight-30+"px";
div_right.style.height=window_innerHeight-30+"px";
div_right.style.width=window_innerWidth-300-30+"px";
div_render_kod.style.height=window_innerHeight-30-60+"px";

var input_nomen_co_k = bhv.getElementById("id_input_nomen_co_k");
var input_nomen_co_part = bhv.getElementById("id_input_nomen_co_part");


var combobox1 = new bhv.Combobox("combo1", null, 0, 10,
     "cennic", "kod", "name", "det")
	 
combobox1.afterValueChange = f_show_kod;

var combobox2 = new bhv.Combotree("combo2", null, 0, 10,
     "co", "kod", "name", "name")
var combobox3 = new bhv.Combobox("combo3", null, 0, 10,
     "mat", "name", "name", "name")

var combobox4 = new bhv.Combobox("combo4", null, 0, 10,
     "matvid", "matvid", "matvid", "matvid")


function f_show_kod(self){
bhv.sendScriptRequest("render_nomen.php", "kod="+combobox1.getValue(),  handleRequest, [0,1]);
}
function f_show_kod_a(kod){
combobox1.setValue(kod);
//var combo2= document.getElementById("div_nomen_combo2_inner");
//document.getElementById("div_nomen_combo2").appendChild(combo2);
bhv.sendScriptRequest("render_nomen.php", "kod="+kod,  handleRequest, [0,1]);
}
function handleRequest(a,b){
input_kod_kod.value = bhv.scriptConteiner.kod;
input_kod_name.value = bhv.scriptConteiner.name;
input_kod_fullname.value = bhv.scriptConteiner.fullname;
input_kod_matparam.value = bhv.scriptConteiner.matparam;
var mat = bhv.scriptConteiner.mat;
var matvid = bhv.scriptConteiner.matvid;
combobox3.setValue(mat);
combobox4.setValue(matvid);
}

	 
function f_save_nomen_kod(self){
bhv.sendScriptRequest("nomen_save_kod.php", "kod=" + combobox1.getValue() + "&fullname=" + encodeURIComponent(input_kod_fullname.value)
 + "&mat=" + encodeURIComponent(combobox3.getValue()) + "&matvid=" + encodeURIComponent(combobox4.getValue()) + "&matparam=" + encodeURIComponent(input_kod_matparam.value)
, handleRequest);	
}
	 
function f_add_mat(self){
bhv.sendScriptRequest("nomen_add_mat.php", "name=" + encodeURIComponent(input_mat_name.value.toUpperCase()), f_set_mat);	
}

function f_set_mat(){
combobox3.setValue(""+input_mat_name.value.toUpperCase())
}	 
	 
function f_delete_kd(self){
self.style.borderStyle = "inset";
bhv.sendScriptRequest("nomen_delete_kd.php", "kod="+combobox1.getValue() + "&" + self.parentNode.title ,  handleRequest, [0,1]);	
}

function f_insert_kd(ce, self){
self.style.borderStyle = "inset";
bhv.sendScriptRequest("nomen_insert_kd.php", "kod="+combobox1.getValue() + "&" + "ce=" + ce + "&kd="+combobox2.getValue() + "&kol=" +input_kod_kol.value,  handleRequest, [0,1]);	
}

function f_kol_kd(self){
self.style.borderStyle = "inset";
bhv.sendScriptRequest("nomen_kol_kd.php", "kod="+combobox1.getValue() + "&" + "kol=" +input_kod_kol.value + "&" + self.parentNode.title);	
}

function f_show_izm(self){
bhv.sendScriptRequest("nomen_show_izm.php", "",  handleRequest, [0,1]);	
}

function f_save_nomen_co(kod,type,co){
 bhv.sendScriptRequest("nomen_co_save.php", "kod="+kod+"&type="+type+"&co="+co
      +"&k="+input_nomen_co_k.value+"&part="+input_nomen_co_part.value);	
}

function f_insert_nomen_co(kod,type){
 bhv.sendScriptRequest("nomen_co_insert.php", "kod="+kod+"&type="+type+"&co="+combobox2.getValue()
      +"&k="+input_nomen_co_k.value+"&part="+input_nomen_co_part.value);	
}

function f_delete_nomen_co(kod,type,co){
 bhv.sendScriptRequest("nomen_co_delete.php", "kod="+kod+"&type="+type+"&co="+co);	
}

alert(2222)
</script>

<script>
</script>