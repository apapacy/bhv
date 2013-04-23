<?php header('Content-type: text/html; charset="widows-1251"');?>
<html>
<head>
<script type="text/javascript" src="../../bhv/util.js"></script>
<link rel=stylesheet type="text/css" href="../../combobox/combobox.css">
<script type="text/javascript" src="../../combobox/Combobox.js"></script>

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

<body id='id_body' onkeyup0000000='body_onkeyup(event, this);'>

<div id=edit_pane style="position:absolute;top:100px;left:400px;background-color:#dddddd;padding:20px;display:none;">

<input type=hidden id=edit_god name=edit_god value='' size=5>
<input type=hidden id=edit_mes name=edit_mes value='' size=5>
<input type=hidden id=edit_nar name=edit_nar value='' size=5>
<input type=hidden id=edit_npp name=edit_npp value='' size=5>
<table>
<tr><td>Табельный</td><td><input type=text id=edit_tab name=edit_tab value='' size=5 onkeyup='if (event.keyCode == 13)bhv.selectNextInput(this).select();'></td></tr>
<tr><td>Наименование</td><td><span id=combobox2></span></td></tr>
<script>
var combo2 = new bhv.Combobox("combobox2", undefined, 0, 12,
     "cennic", "kod", "name", "det")
//combo2.afterValueChange = function(){     alert(1)}
</script>
<tr><td>Деталь</td><td><input type=text id=edit_det name=edit_det value='' size=15 onkeyup='if (event.keyCode == 13)bhv.selectNextInput(this).select();' onfocus='this.select()'></td></tr>
<tr><td>Номер операции</td><td><input type=text id=edit_n name=edit_n value='' size=15 onkeyup='if (event.keyCode == 13)bhv.selectNextInput(this).select();'></td></tr>
<tr><td>Примечание</td><td><input type=text id=edit_nop name=edit_nop value='' size=15 onkeyup='if (event.keyCode == 13)bhv.selectNextInput(this).select();'></td></tr>
<tr><td>Количество задано</td><td><input type=text id=edit_zadan name=edit_zadan value='' size=7 onkeyup='if (event.keyCode == 13)bhv.selectNextInput(this).select();'></td></tr>
<tr><td>Количество принято</td><td><input type=text id=edit_kol name=edit_kol value='' size=7 onkeyup='if (event.keyCode == 13)bhv.selectNextInput(this).select();'></td></tr>
<tr><td>Мин.</td><td><input type=text id=edit_min name=edit_min value='' size=10 onkeyup='if (event.keyCode == 13)bhv.selectNextInput(this).select();'></td></tr>
<tr><td>Коп.</td><td><input type=text id=edit_kop name=edit_kop value='' size=10 onkeyup='if (event.keyCode == 13)bhv.selectNextInput(this).select();'></td></tr>
<tr><td>Заказ</td><td><input type=text id=edit_spz name=edit_spz value='' size=10 onkeyup='if (event.keyCode == 13)bhv.selectNextInput(this);'></td></tr>
</table>

<input type=button id=save_record name=save_record value='Сохранить' 
    onclick="f_save_record(this);" >
<input type=button id=cancel_record name=cancel_record value='Отменить' 
    onclick="f_cancel_record(this);" >
</div>


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
<input type=text id=cur_god name=cur_god value='<?php echo $cur_god ?>' disabled size=5>
<input type=text id=cur_mes name=cur_mes value='<?php echo $cur_mes ?>' disabled size=4>
<input type=text id=cur_nar onclick='show_naryad(this,id_cur_god.value,id_cur_mes.value,this.value,0);    id_cur_tab.disabled=false;'>
<input type=checkbox  onchange="enable_god_mes(this.checked);">
<input type=button id=new_naryad name=new_naryad value='Нов.наряд'  onclick="f_new_naryad()">
<input type=button id=show_total name=show_total value="Итог" onclick="f_show_total()">
<input type=button id=export name=export value="Выгруз." onclick="f_export()">
<input type=button id=button_zadan name=button_zadan value="Задано" onclick="f_button_zadan()">
<input type=button id=button_zadan name=button_zadan_min value="Задано+расц" onclick="f_button_zadan_min()">
<input type=button id=button_zadan_307 name=button_zadan_307 value="307" onclick="f_button_zadan_307()">
<input type=button id=button_nar name=button_nar value="Принято" onclick="f_button_nar()">
<input type=button id=button_nar_s name=button_nar_s value="Принято 307" onclick="f_button_nar_s()">
<script>
//////////////////////////////////////////////////////////////
function f_button_zadan(){
cur_mode = "zadan";
id_body.style.backgroundColor="#bbbbcc"
combobox1.style.display=""
id_cur_det.style.display="none";
id_cur_det.value = "";
id_cur_nop.style.display="none";
id_cur_nop.value = "";
id_cur_zadan.style.display="";
id_cur_kol.style.display="none";
id_cur_kol.value = 0;
id_cur_min.style.display="none";
id_cur_min.value = 0;
id_cur_kop.style.display="none";
id_cur_kop.value = 0;
}
///////////////////////////////////////////////////////////////
function f_button_zadan_min(){
cur_mode = "zadan_min";
id_body.style.backgroundColor="#bbbbcc"
combobox1.style.display=""
id_cur_det.style.display="none";
id_cur_det.value = "";
id_cur_nop.style.display="";
id_cur_zadan.style.display="";
id_cur_kol.style.display="none";
id_cur_kol.value = 0;
id_cur_min.style.display="";
id_cur_kop.style.display="";
}
//////////////////////////////////////////////////////////
function f_button_zadan_307(){
cur_mode = "zadan_307";
id_body.style.backgroundColor="#bbbbcc"
combobox1.style.display="none"
id_cur_det.style.display="";
id_cur_nop.style.display="";
id_cur_zadan.style.display="";
id_cur_kol.style.display="none";
id_cur_kol.value = 0;
id_cur_min.style.display="";
id_cur_kop.style.display="";
}
///////////////////////////////////////////////////////////////
function f_button_nar(){
cur_mode = "nar";
id_body.style.backgroundColor="#bbcccc"
combobox1.style.display=""
id_cur_det.style.display="none";
id_cur_det.value = "";
id_cur_nop.style.display="";
id_cur_zadan.style.display="none";
id_cur_zadan.value = 0;
id_cur_kol.style.display="";
id_cur_min.style.display="";
id_cur_kop.style.display="";
}
////////////////////////////////////////////////////////////////
function f_button_nar_s(){
cur_mode = "nar_s";
id_body.style.backgroundColor="#bbcccc"
combobox1.style.display="none"
id_cur_det.style.display="";
id_cur_nop.style.display="";
id_cur_zadan.style.display="none";
id_cur_kol.style.display="";
id_cur_min.style.display="";
id_cur_kop.style.display="";
}
/////////////////////////////////////////////////////////////////
function f_edit_kol(god,mes,nar,npps,kols,sumkols){
var i = 0;
for (i = npps.length - 1; i > -1; i--){
    var inpt = document.getElementById("input_kol"+npps[i]);
    inpt.disabled = false;
    if (sumkols == 0)
       inpt.value = kols[i];
    inpt.select();
    inpt.focus();
}
}
function f_save_kol(god,mes,nar,npps,kols,sumkols){
var i = 0;
var s_npps = "";
var s_kols = "";
for (i=0; i < npps.length; i++){
    var inpt = document.getElementById("input_kol"+npps[i]);
    inpt.disabled = true;
    //if (i==0){ 
    //    s_npps = 'npps[]=' + npps[i];
    //    s_kols = 'kols[]='  + inpt.value;
    //}else {
        s_npps = s_npps + '&npps[]=' + npps[i];
        s_kols = s_kols + '&kols[]='  + inpt.value;
    //}
}
if (!! s_npps)
    bhv.sendScriptRequest('render_naryad_zadan.php','action=kols&god='+god+'&mes='+mes+'&nar='+nar + s_npps + s_kols, bhv.emptyFunction);
}
</script>


<table>
<tr>
<td>Наряд</td><td>Таб.№</td><td>Деталь</td><td>№ опер.</td><td>Прим.</td><td>Кол-во</td><td>Мин.</td><td>Коп.</td><td>Заказ</td><td>Действие</td>
</tr>
<tr>
<td>
<input type=text id=cur_nar name=cur_nar value='' disabled size=4>
</td>
<td>
<input type=text id=cur_tab name=cur_tab value='0' disabled size=5>
</td>
<td><span id=combobox1></span>
<input type=text id=cur_det name=cur_det value='' size=15>
</td>
<script>
var combo1 = new bhv.Combobox("combobox1", undefined, 0, 12,
     "cennic", "kod", "name", "det")
</script>
<td>
<input type=text id=cur_n name=cur_n value='0' size=7 onfocus="this.select();">
</td>
<td>
<input type=text id=cur_nop name=cur_nop value='' size=15>
</td>
<td>
<input type=text id=cur_zadan name=cur_zadan value='0' size=7 >
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
<input type=button id=append_record name=append_record value='Добавить' onclick="f_add_record(this);" disabled >
</td>
</td>
</tr>
</table>
<div id=naryad_pane style="width: 100%; height:550px; border: solid red 2px;overflow:scroll;"></div>
<div id=total_pane style="width: 100%; border: solid red 2px;overflow:auto; display:none"></div>

<script>
function f_export(){
    window.location.href= "export_naryad_zadan.php?god="+id_cur_god.value+"&mes="+id_cur_mes.value;
}

function edit_line(td, god, mes, nar, tab, npp, det, nop, kol, min, kop, spz, koddet, zadan, n){
    edit_pane.style.display="";
    combo2.setValue(koddet)
    id_edit_god.value = god;
    id_edit_mes.value = mes;
    id_edit_nar.value = nar;
    id_edit_tab.value = tab
    id_edit_npp.value = npp;
    id_edit_det.value = det    id_edit_nop.value = nop
    id_edit_kol.value = kol
    id_edit_min.value = min
    id_edit_kop.value = kop
    id_edit_spz.value = spz
    edit_zadan.value = zadan
    edit_n.value = n
    id_edit_tab.focus()
    id_edit_tab.select()
}

function f_save_record(button){
/*    id_edit_tab.style.display = "none";
    id_edit_det.style.display = "none";
    id_edit_nop.style.display = "none";
    id_edit_kol.style.display = "none";
    id_edit_min.style.display = "none";
    id_edit_kop.style.display = "none";
    id_edit_spz.style.display = "none";
    id_save_record.style.display = "none";
    id_cancel_record.style.display = "none";*/

    edit_pane.style.display = "none";
	bhv.sendScriptRequest('render_naryad_zadan.php', 'action=save&'+f_get_edit_params(), f_render_naryad, [id_append_record]);
}

function f_cancel_record(button){
    edit_pane.style.display = "none";
    return;
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
	+ '&koddet=' + combo2.getValue()
	+ '&n=' + edit_n.value
	+ '&zadan=' + edit_zadan.value
}

function show_naryad(button, god, mes, nar,tab){
    id_naryad_pane.innerHTML="Загружается документ...";
    id_cur_god.value=god;
    id_cur_god.scrollIntoView(false);
    id_cur_mes.value=mes;
    id_cur_nar.value=nar;
    id_cur_tab.value=tab;
    id_cur_tab.disabled=true;
    id_append_record.disabled=true;
    bhv.sendScriptRequest('render_naryad_zadan.php','action=render&god='+god+'&mes='+mes+'&nar='+nar, f_render_naryad);
}

function show_spz(div, god, mes, spz){

/*  div.style.borderTopStyle="inset";
    div.style.borderBottomStyle="inset";
    div.style.borderLeftStyle="inset";
    div.style.borderRightStyle="inset";*/
    
    id_naryad_pane.innerHTML="Загружается документ...";
    id_cur_god.value=god;
    id_cur_god.scrollIntoView(false);
    id_cur_mes.value=mes;
    id_cur_tab.disabled=true;
    id_append_record.disabled=true;
    bhv.sendScriptRequest('render_naryads_zadan.php','action=spz&god='+god+'&mes='+mes+'&spz='+spz, bhv.emptyFunction);
}

function show_tab(div, god, mes, tab){
    id_naryad_pane.innerHTML="Загружается документ...";
    id_cur_god.value=god;
    id_cur_god.scrollIntoView(false);
    id_cur_mes.value=mes;
    id_cur_tab.disabled=true;
    id_append_record.disabled=true;
    bhv.sendScriptRequest('render_naryads_zadan.php','action=tab&god='+god+'&mes='+mes+'&tab='+tab, bhv.emptyFunction);
}

function f_show_total(){
    if (id_total_pane.style.display=="block"){
        id_total_pane.style.display="none";
        return;
    }
    id_total_pane.innerHTML = "Идет обработка запроса...";
    id_total_pane.style.display="block";
    bhv.sendScriptRequest('calculate_total_zadan.php', 'god='+id_cur_god.value+'&mes='+id_cur_mes.value,
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
	id_cur_tab.value = "0";
	id_cur_tab.disabled = false;
	id_append_record.disabled = false;
	id_cur_det.value = "";
	id_cur_n.value = 0;
	id_cur_nop.value = "";
	id_cur_zadan.value = "0";
	id_cur_kol.value = "0";
	id_cur_min.value = "0";
	id_cur_kop.value = "0";
	id_cur_spz.value = "0";
	id_cur_tab.select();
}

function f_add_record(button){
    id_naryad_pane.innerHTML="Загружается документ...";
	button.disabled = true;
	id_cur_tab.disabled = true;
	if (Number(id_cur_nar.value) == 0){
		bhv.sendScriptRequest('render_naryad_zadan.php', 'action=' + cur_mode + '&' + f_get_http_params(), f_render_naryad, [button]);
	}else{
		bhv.sendScriptRequest('render_naryad_zadan.php', 'action=' + cur_mode + '&' + f_get_http_params(), f_render_naryad, [button]);
	}
}

function f_get_http_params(){
	return 'nar=' + id_cur_nar.value
	+ '&tab=' + id_cur_tab.value
	+ '&koddet=' + combo1.getValue()
	+ '&det=' + encodeURIComponent(id_cur_det.value)
    + '&n=' + id_cur_n.value
	+ '&nop=' + encodeURIComponent(id_cur_nop.value)
	+ '&zadan=' + id_cur_zadan.value
	+ '&kol=' + id_cur_kol.value
	+ '&min=' + id_cur_min.value
	+ '&kop=' + id_cur_kop.value
	+ '&spz=' + id_cur_spz.value
	+ '&god=' + id_cur_god.value
	+ '&mes=' + id_cur_mes.value
}

function f_render_naryad(button){
	id_append_record.disabled = false;
	if (id_cur_det.style.display != "none")
    	id_cur_det.select();
    else
        combo1.input.select(); //edit()
}

function delete_line(div,god, mes, nar, npp){
    id_naryad_pane.innerHTML="Загружается документ...";
    div.style.borderTopStyle="inset";
    div.style.borderBottomStyle="inset";
    div.style.borderLeftStyle="inset";
    div.style.borderRightStyle="inset";
    bhv.sendScriptRequest('render_naryad_zadan.php','action=delete&god='+god+'&mes='+mes+'&nar='+nar+'&npp='+npp , f_render_naryad);
}


var id_cur_god = document.getElementById("cur_god");
var id_cur_mes = document.getElementById("cur_mes");
var id_naryad_pane = document.getElementById("naryad_pane");
var id_cur_nar = document.getElementById("cur_nar");
var id_cur_tab = document.getElementById("cur_tab");
var id_cur_det = document.getElementById("cur_det");
var id_cur_n = document.getElementById("cur_n");
var id_cur_nop = document.getElementById("cur_nop");
var id_cur_zadan = document.getElementById("cur_zadan");
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
id_cur_tab.onkeyup = function(e){
if (e.keyCode == 13)
    bhv.selectNextInput(id_cur_tab)
    /*if (id_cur_det.style.display == 'none')
       combo1.edit();
	else
	   id_cur_det.select();*/
}
id_cur_det.onkeydown = function(e){
if (e.keyCode == 13)
//    bhv.selectNextInput(id_cur_det).select()
	id_cur_n.select();
}
id_cur_n.onkeydown = function(e){
if (e.keyCode == 13)
//    bhv.selectNextInput(id_cur_n).select()
    if (id_cur_nop.style.display != "none")
        id_cur_nop.select();
    else
        id_cur_zadan.select();
}
id_cur_nop.onkeydown = function(e){
if (e.keyCode == 13)
//    bhv.selectNextInput(id_cur_nop).select()
    if (id_cur_zadan.style.display != "none")
    	id_cur_zadan.select();
    else
    	id_cur_kol.select();
}
id_cur_zadan.onkeydown = function(e){
if (e.keyCode == 13)
//    bhv.selectNextInput(id_cur_zadan).select()
    if (id_cur_min.style.display != "none")
    	id_cur_min.select();
    else
    	id_cur_spz.select();
}
id_cur_kol.onkeydown = function(e){
if (e.keyCode == 13)
//    bhv.selectNextInput(id_cur_kol).select()
    if (id_cur_min.style.display != "none")
    	id_cur_min.select();
    else
    	id_cur_spz.select();
}
id_cur_min.onkeydown = function(e){
if (e.keyCode == 13)
//    bhv.selectNextInput(id_cur_min).select()
	id_cur_kop.select();
}
id_cur_kop.onkeydown = function(e){
if (e.keyCode == 13)
//    bhv.selectNextInput(id_cur_kop).select()
	id_cur_spz.select();
}
id_cur_spz.onkeydown = function(e){
if (e.keyCode == 13)
	id_append_record.focus();
}

var selected_row = 0;

/*function body_onkeyup(event, body){
	if (event.keyCode==38) {// ArrowUp
		selectNextRow(event,body);
	}
	if (event.keyCode==40) {// ArrowDown
		selectNextRow(event,body);
	}
}*/

function selectNextRow(event,body){
	var  table = document.GetElementById('table_naryad')
}

var cur_mode = "zadan";
f_button_zadan();

</script>
</body>
</html>

