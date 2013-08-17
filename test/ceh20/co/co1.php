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

<div id="id_div_left" style="height:100%;width:300px; border:none; overflow:scroll;float:left">
</div>
<div id="id_div_right" style="height:99%; width:79%; border:none;overflow:hidden">
<div  style="height:60px; width:99%; border:none;overflow:hidden">
Выбор: <span id=combo1 style="width:300px"></span> 
Входящая: <span id=combo2></span>
</div>
</div>
<div id="id_div_render_co" style="height:89%; width:99%; border:none;overflow:scroll;z-index:0"></div>
</div>

<script>
var div_render_co = bhv.getElementById("id_div_render_co");

var div_left = bhv.getElementById("id_div_left");
var div_right = bhv.getElementById("id_div_right");
var window_innerHeight = window.innerHeight ? window.innerHeight : document.body.clientHeight
var window_innerWidth = window.innerWidth ? window.innerWidth : document.body.clientWidth
div_left.style.height=(window_innerHeight-30)+"px";
div_right.style.height=(window_innerHeight-30)+"px";
div_left.style.width=300-20+"px";
div_right.style.width=(window_innerWidth-300-20)+"px";
div_render_co.style.height=(window_innerHeight-30-60)+"px";
var combobox1 = new bhv.Combotree("combo1", null, 0, 10,
     "co", "kod", "name", "name")
var combobox2 = new bhv.Combotree("combo1", null, 0, 10,
     "co", "kod", "name", "name")

</script>