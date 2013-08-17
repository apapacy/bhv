<?php header('Content-type: text/html; charset="widows-1251"');?>
<html>
<head>
<script type="text/javascript" src="../../bhv/util.js"></script>
<style type=text/css>
body {background-color: #bbcccc; font: arial;font-size: 10pt ;}
div {padding: 0px; margin: 0px; border: 0px none; font: arial;font-size: 10pt ;}
table {padding: 0px; margin: 0px; border: 0px none; table-layout0:fixed; white-space0: nowrap;}
div.table_head {float: left; padding: 2px; margin: 2px 2px 2px 2px ; border: 2px; height:35px; background-color: #ffffcc}
td {padding: 1px 5px 1px 5px; margin: 0px; border: 1px solid #ffffcc;  color: blue;font: arial;font-size: 10pt ;}
tr.selected {background-color: #eecccc;}
input {color: #000000}
</style>
</head>

<body onkeyup='body_onkeyup(event, this);'>

<input type=hidden id=edit_god name=edit_god value='' size=5 style="position:absolute; display:none;">
<input type=hidden id=edit_mes name=edit_mes value='' size=5 style="position:absolute; display:none;">
<input type=hidden id=edit_nar name=edit_nar value='' size=5 style="position:absolute; display:none;">
<input type=hidden id=edit_npp name=edit_npp value='' size=5 style="position:absolute; display:none;">

<input type=text id=edit_tab name=edit_tab value='' size=5 style="position:absolute; display:none;">
<input type=text id=edit_det name=edit_det value='' size=15 style="position:absolute; display:none;">
<input type=text id=edit_nop name=edit_nop value='' size=15 style="position:absolute; display:none;">
<input type=text id=edit_kol name=edit_kol value='' size=7  style="position:absolute; display:none;">
<input type=text id=edit_min name=edit_min value='' size=10 style="position:absolute; display:none;">
<input type=text id=edit_kop name=edit_kop value='' size=10 style="position:absolute; display:none;">
<input type=text id=edit_spz name=edit_spz value='' size=10 style="position:absolute; display:none;">
<input type=button id=save_record name=save_record value='���������' 
    onclick="f_save_record(this);" style="position:absolute; display:none;">
<input type=button id=cancel_record name=cancel_record value='��������' 
    onclick="f_cancel_record(this);" style="position:absolute; display:none;">

<?php
$host = 'localhost';
$database = 'Ceh16';
$username = 'root';
$password = '26682316';

if (empty($username))
    $db = pg_pconnect("host=$host dbname=$database");
elseif (empty($password))
	$db = pg_pconnect("host=$host dbname=$database user=$username");
else
	$db = pg_pconnect("host=$host dbname=$database user=$username password=$password");

if (isset($_REQUEST['cennicKod'])){
    $cennic_kod = $_REQUEST['cennicKod'];
}
	

$result = pg_query("select * from c_mes order by god desc, mes desc limit 1");
if ($result and $row = pg_fetch_array($result)) {
	$cur_god = $row['god'];
	$cur_mes = $row['mes'];
}
?>
<input type=text id=cur_god name=cur_god value='<?php echo $cur_god ?>' disabled>
<input type=text id=cur_mes name=cur_mes value='<?php echo $cur_mes ?>' disabled>
<input type=checkbox  onchange="enable_god_mes(this.checked);">
<input type=button id=new_naryad name=new_naryad value='����� �����'  onclick="f_new_naryad()">
<input type=button id=show_total name=show_total value="�����" onclick="f_show_total()">
<input type=button id=export name=export value="���������" onclick="f_export()">


<div id=naryad_pane style="width: 100%; height:450px; border: solid red 2px;overflow:scroll;"></div>
<table>
<tr>
<td>�����</td><td>���.�</td><td>������</td><td>��������</td><td>���-��</td><td>���.</td><td>���.</td><td>�����</td><td>��������</td>
</tr>
<tr>
<td>
<input type=text id=cur_nar name=cur_nar value='' disabled size=4>
</td>
<td>
<input type=text id=cur_tab name=cur_tab value='' disabled size=5>
</td>
<td>
<input type=text id=cur_det name=cur_det value='' size=15>
</td>
<td>
<input type=text id=cur_nop name=cur_nop value='' size=15>
</td>
<td>
<input type=text id=cur_kol name=cur_kol value='0' size=7 >
</td>
<td>
<input type=text id=cur_min name=cur_min value='0' size=10>
</td>
<td>
<input type=text id=cur_kop name=cur_kop value='0' size=10>
</td>
<td>
<input type=text id=cur_spz name=cur_spz value='0' size=10>
</td>
<td>
<input type=button id=append_record name=append_record value='��������' onclick="f_add_record(this);" disabled >
</td>
</td>
</tr>
</table>

<div id=total_pane style="width: 100%; border: solid red 2px;overflow:auto; display:none"></div>

<script>
function f_export(){
    window.location.href= "export_naryad.php?god="+id_cur_god.value+"&mes="+id_cur_mes.value;
}

function edit_line(td, god, mes, nar, npp){

    id_edit_god.value = god;
    id_edit_mes.value = mes;
    id_edit_nar.value = nar;
    id_edit_npp.value = npp;

    var ceils = td.parentNode.getElementsByTagName("td");

    id_edit_tab.style.left = bhv.left(ceils[4],true)+"px";
    id_edit_tab.style.top = bhv.top(ceils[4],true)+"px";
    id_edit_tab.style.width = ceils[4].offsetWidth+"px";
    id_edit_tab.value = ceils[4].innerHTML;

    id_edit_det.style.left = bhv.left(ceils[6],true)+"px";
    id_edit_det.style.top = bhv.top(ceils[6],true)+"px";
    id_edit_det.style.width = ceils[6].offsetWidth+"px";
    id_edit_det.value = ceils[6].innerHTML;

    id_edit_nop.style.left = bhv.left(ceils[7],true)+"px";
    id_edit_nop.style.top = bhv.top(ceils[7],true)+"px";
    id_edit_nop.style.width = ceils[7].offsetWidth+"px";
    id_edit_nop.value = ceils[7].innerHTML;

    id_edit_kol.style.left = bhv.left(ceils[8],true)+"px";
    id_edit_kol.style.top = bhv.top(ceils[8],true)+"px";
    id_edit_kol.style.width = ceils[8].offsetWidth+"px";
    id_edit_kol.value = ceils[8].innerHTML;

    id_edit_min.style.left = bhv.left(ceils[9],true)+"px";
    id_edit_min.style.top = bhv.top(ceils[9],true)+"px";
    id_edit_min.style.width = ceils[9].offsetWidth+"px";
    id_edit_min.value = ceils[9].innerHTML;

    id_edit_kop.style.left = bhv.left(ceils[10],true)+"px";
    id_edit_kop.style.top = bhv.top(ceils[10],true)+"px";
    id_edit_kop.style.width = ceils[10].offsetWidth+"px";
    id_edit_kop.value = ceils[10].innerHTML;


    id_edit_spz.style.left = bhv.left(ceils[11],true)+"px";
    id_edit_spz.style.top = bhv.top(ceils[11],true)+"px";
    id_edit_spz.style.width = ceils[11].offsetWidth+"px";
    id_edit_spz.value = ceils[11].innerHTML;

    id_save_record.style.left = bhv.left(ceils[12],true)+"px";
    id_save_record.style.top = bhv.top(ceils[12],true)+"px";

    id_save_record.style.display = "";
    id_cancel_record.style.left = bhv.left(ceils[12],true)+"px";
    id_cancel_record.style.top = bhv.top(ceils[12],true)+id_save_record.offsetHeight+"px";
    id_cancel_record.style.width = id_save_record.offsetWidth+"px";

    id_edit_tab.style.display = "";
    id_edit_det.style.display = "";
    id_edit_nop.style.display = "";
    id_edit_kol.style.display = "";
    id_edit_min.style.display = "";
    id_edit_kop.style.display = "";
    id_edit_spz.style.display = "";
    id_cancel_record.style.display = "";
}

function f_save_record(button){
    id_edit_tab.style.display = "none";
    id_edit_det.style.display = "none";
    id_edit_nop.style.display = "none";
    id_edit_kol.style.display = "none";
    id_edit_min.style.display = "none";
    id_edit_kop.style.display = "none";
    id_edit_spz.style.display = "none";
    id_save_record.style.display = "none";
    id_cancel_record.style.display = "none";
	bhv.sendScriptRequest('render_naryad.php', 'action=save&'+f_get_edit_params(), f_render_naryad, [id_append_record]);
}

function f_cancel_record(button){
    id_edit_tab.style.display = "none";
    id_edit_det.style.display = "none";
    id_edit_nop.style.display = "none";
    id_edit_kol.style.display = "none";
    id_edit_min.style.display = "none";
    id_edit_kop.style.display = "none";
    id_edit_spz.style.display = "none";
    id_save_record.style.display = "none";
    id_cancel_record.style.display = "none";

}


function f_get_edit_params(){
	return 'nar=' + id_edit_nar.value
	+ '&tab=' + id_edit_tab.value
	+ '&det=' + encodeURIComponent(id_edit_det.value)
	+ '&nop=' + encodeURIComponent(id_edit_nop.value)
	+ '&kol=' + id_edit_kol.value
	+ '&min=' + id_edit_min.value
	+ '&kop=' + id_edit_kop.value
	+ '&spz=' + id_edit_spz.value
	+ '&god=' + id_edit_god.value
	+ '&mes=' + id_edit_mes.value
	+ '&nar=' + id_edit_nar.value
	+ '&npp=' + id_edit_npp.value
}

function show_naryad(button, god, mes, nar,tab){
    id_naryad_pane.innerHTML="����������� ��������...";
    id_cur_god.value=god;
    id_cur_god.scrollIntoView(false);
    id_cur_mes.value=mes;
    id_cur_nar.value=nar;
    id_cur_tab.value=tab;
    id_cur_tab.disabled=true;
    id_append_record.disabled=true;
    bhv.sendScriptRequest('render_naryad.php','action=render&god='+god+'&mes='+mes+'&nar='+nar, f_render_naryad);
}

function show_spz(div, god, mes, spz){

/*  div.style.borderTopStyle="inset";
    div.style.borderBottomStyle="inset";
    div.style.borderLeftStyle="inset";
    div.style.borderRightStyle="inset";*/
    
    id_naryad_pane.innerHTML="����������� ��������...";
    id_cur_god.value=god;
    id_cur_god.scrollIntoView(false);
    id_cur_mes.value=mes;
    id_cur_tab.disabled=true;
    id_append_record.disabled=true;
    bhv.sendScriptRequest('render_naryads.php','action=spz&god='+god+'&mes='+mes+'&spz='+spz, bhv.emptyFunction);
}

function show_tab(div, god, mes, tab){
    id_naryad_pane.innerHTML="����������� ��������...";
    id_cur_god.value=god;
    id_cur_god.scrollIntoView(false);
    id_cur_mes.value=mes;
    id_cur_tab.disabled=true;
    id_append_record.disabled=true;
    bhv.sendScriptRequest('render_naryads.php','action=tab&god='+god+'&mes='+mes+'&tab='+tab, bhv.emptyFunction);
}

function f_show_total(){
    if (id_total_pane.style.display=="block"){
        id_total_pane.style.display="none";
        return;
    }
    id_total_pane.innerHTML = "���� ��������� �������...";
    id_total_pane.style.display="block";
    bhv.sendScriptRequest('calculate_total.php', 'god='+id_cur_god.value+'&mes='+id_cur_mes.value,
     f_render_total, []);
}

function f_render_total(){
}

function enable_god_mes(enable){
	if (String(enable) == "false"){
		document.getElementById("cur_god").disabled=true;
		document.getElementById("cur_mes").disabled=true;
	}else{
		document.getElementById("cur_god").disabled=false;
		document.getElementById("cur_mes").disabled=false;
	}
}

function f_new_naryad(){
	id_naryad_pane.innerHTML = "";
	id_cur_nar.value = 0;
	id_cur_tab.value = "";
	id_cur_tab.disabled = false;
	id_append_record.disabled = false;
	id_cur_det.value = "";
	id_cur_nop.value = "";
	id_cur_kol.value = "";
	id_cur_min.value = "";
	id_cur_kop.value = "";
	id_cur_spz.value = "";
	id_cur_tab.select();
}

function f_add_record(button){
    id_naryad_pane.innerHTML="����������� ��������...";
	button.disabled = true;
	id_cur_tab.disabled = true;
	if (Number(id_cur_nar.value) == 0){
		bhv.sendScriptRequest('render_naryad.php', 'action=add&'+f_get_http_params(), f_render_naryad, [button]);
	}else{
		bhv.sendScriptRequest('render_naryad.php', 'action=add&'+f_get_http_params(), f_render_naryad, [button]);
	}
}

function f_get_http_params(){
	return 'nar=' + id_cur_nar.value
	+ '&tab=' + id_cur_tab.value
	+ '&det=' + encodeURIComponent(id_cur_det.value)
	+ '&nop=' + encodeURIComponent(id_cur_nop.value)
	+ '&kol=' + id_cur_kol.value
	+ '&min=' + id_cur_min.value
	+ '&kop=' + id_cur_kop.value
	+ '&spz=' + id_cur_spz.value
	+ '&god=' + id_cur_god.value
	+ '&mes=' + id_cur_mes.value
}

function f_render_naryad(button){
	id_append_record.disabled = false;
	id_cur_det.select()
}

function delete_line(div,god, mes, nar, npp){
    id_naryad_pane.innerHTML="����������� ��������...";
    div.style.borderTopStyle="inset";
    div.style.borderBottomStyle="inset";
    div.style.borderLeftStyle="inset";
    div.style.borderRightStyle="inset";
    bhv.sendScriptRequest('render_naryad.php','action=delete&god='+god+'&mes='+mes+'&nar='+nar+'&npp='+npp , f_render_naryad);
}


var id_cur_god = document.getElementById("cur_god");
var id_cur_mes = document.getElementById("cur_mes");
var id_naryad_pane = document.getElementById("naryad_pane");
var id_cur_nar = document.getElementById("cur_nar");
var id_cur_tab = document.getElementById("cur_tab");
var id_cur_det = document.getElementById("cur_det");
var id_cur_nop = document.getElementById("cur_nop");
var id_cur_kol = document.getElementById("cur_kol");
var id_cur_min= document.getElementById("cur_min");
var id_cur_kop = document.getElementById("cur_kop");
var id_cur_spz = document.getElementById("cur_spz");
var id_total_pane = document.getElementById("total_pane");
var id_append_record = document.getElementById("append_record");

var id_edit_god = document.getElementById("edit_god");
var id_edit_mes = document.getElementById("edit_mes");
var id_edit_nar = document.getElementById("edit_nar");
var id_edit_npp = document.getElementById("edit_npp");
var id_edit_tab = document.getElementById("edit_tab");
var id_edit_det = document.getElementById("edit_det");
var id_edit_nop = document.getElementById("edit_nop");
var id_edit_kol = document.getElementById("edit_kol");
var id_edit_min = document.getElementById("edit_min");
var id_edit_kop = document.getElementById("edit_kop");
var id_edit_spz = document.getElementById("edit_spz");
var id_save_record = document.getElementById("save_record");
var id_cancel_record = document.getElementById("cancel_record");
id_cur_nar = document.getElementById("cur_nar");
id_cur_tab.onkeydown = function(e){
if (e.keyCode == 13)
	id_cur_det.select();
}
id_cur_det.onkeydown = function(e){
if (e.keyCode == 13)
	id_cur_nop.select();
}
id_cur_nop.onkeydown = function(e){
if (e.keyCode == 13)
	id_cur_kol.select();
}
id_cur_kol.onkeydown = function(e){
if (e.keyCode == 13)
	id_cur_min.select();
}
id_cur_min.onkeydown = function(e){
if (e.keyCode == 13)
	id_cur_kop.select();
}
id_cur_kop.onkeydown = function(e){
if (e.keyCode == 13)
	id_cur_spz.select();
}
id_cur_spz.onkeydown = function(e){
if (e.keyCode == 13)
	id_append_record.focus();

}

var selected_row = 0;

function body_onkeyup(event, body){
	if (event.keyCode==38) {// ArrowUp
		selectNextRow(event,body);
	}
	if (event.keyCode==40) {// ArrowDown
		selectNextRow(event,body);
	}
}

function selectNextRow(event,body){
	var  table = document.GetElementById('table_naryad')
}
</script>
</body>
</html>

