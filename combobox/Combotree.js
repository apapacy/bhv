bhv.Combotree = function(element, valueElement, initialValue, count,
    table, keyColumn, displayValueColumn, searchValueColumn){
  this.init(element, valueElement, initialValue, count,
    table, keyColumn, displayValueColumn, searchValueColumn);
};

bhv.Combotree.prototype ={
constructor: bhv.Combotree
,//-------------------------------------------------------
init: function(element, valueElement, initialValue, count,
                 table, keyColumn, displayValueColumn, searchValueColumn){
// ����������� the ����������� � ����������
var the = this;
// ��� �������� ����������� ������ � ���������� ���������� �������� the
this.destroy = function(){
  the.destroy = null;
  the = null;
};

this.enabled = false;
this.count = count;
this.selectTree = false;


this.data = new bhv.Combotree.CombotreeData(this.count);

if (typeof element == "string")
	if (typeof document.getElementById != 'undefined')
		this.element = document.getElementById(element);
	else
		this.element = document.all(element);
else
    this.element = element;

if (bhv.IE4){
	this.element.insertAdjacentHTML("afterBegin","<input type=text>")
	this.input = this.element.all.tags('INPUT')[0]
}else{
	this.input = document.createElement("input");
	this.element.appendChild(this.input);
	this.input.type = "text";
	this.input.style.width = this.element.style.width ;
}

// ��� �������-������������ ������� �������� �������-������ ������� Combotree,
// ������� ��������� ��������� ���������� the
this.input.onkeyup = function() {
    var event0 = arguments[0] || window.event;
    the.onkeyup(event0);
};
this.input.onkeydown = function() {
    var event0 = arguments[0] || window.event;
    the.onkeydown(event0);
};
this.input.onclick = function() {
    var event0 = arguments[0] || window.event;
    the.onclick(event0);
};
this.input.onblur = function() {
	
    var event0 = arguments[0] || window.event;
	//alert(event0.keyCode)
    if (the.enabled && !the.selectTree) {
        the.enabled = false;
	    the.assignValue();
        setTimeout(function(){if (the.selectTree) return; the.hideComboTree();the.treeconteiner.style.visibility = "hidden";}, 3000);
        event0.cancelBubble = true;
        event0.returnValue = false;
        this.focus();
        return false;
   }
};

if (! valueElement)
    this.valueElement = {};
else if (typeof valueElement == "string")
		if (typeof document.getElementById != 'undefined')
			this.valueElement = document.getElementById(valueElement);
		else
			this.valueElement = document.all(valueElement);
	else
		this.valueElement = valueElement;

if (bhv.IE4){
var optionID="optionID" + Math.round(Math.random()*1000000)
var optionHTML = "<DIV class=textDropDown id=" + optionID + ">"
for (var i = 0; i < this.count; i++)
    optionHTML = optionHTML + "<DIV class=otherItem></DIV>";
	optionHTML = optionHTML + "</DIV>"
	bhv.contentPane().insertAdjacentHTML("afterBegin", optionHTML)
	this.conteiner = window[optionID]
	this.conteiner.onmousedown = function() {
		the.assignValue();
		the.hideComboTree();
		the.treeconteiner.style.visibility = "hidden";
	}
}else{
this.conteiner = document.createElement("DIV");
this.conteiner.className = "textDropDown"
this.conteiner.onmousedown = function() {
if (!the.selectTree){
	the.treeconteiner.style.visibility = "hidden";
    the.assignValue();
    the.hideComboTree();

}else{
	the.selectTree = false;
	the.enabled = false;
	the.data.currentTree=the.data.getCurrentKey();
	the.getValueFromServerSync("currentSearchValue=" + encodeURIComponent('') /*+ "&shot=yes"*/, null, null, true);
	the.input.value = "";
	the.input.select();
	//the.showComboTree();
}
}
for (var i = 0; i < this.count; i++)
    this.conteiner.appendChild(document.createElement("DIV")) ;
for (var i = 0; i < this.count; i++) {
    this.conteiner.childNodes[i].className = "otherItem";
    this.conteiner.childNodes[i].onmouseover = function() {the.selectOption(this);};
}
bhv.contentPane().appendChild(this.conteiner)
}

this.treeconteiner = document.createElement("DIV");
this.treeconteiner.className = "textDropDown"
this.treeconteiner.onmousedown = function() {
    //the.assignTreeValue();
    //the.hideComboTree();
}
for (var i = 0; i < this.count; i++)
    this.treeconteiner.appendChild(document.createElement("DIV")) ;
for (var i = 0; i < this.count; i++) {
    this.treeconteiner.childNodes[i].className = "otherItem";
	this.treeconteiner.childNodes[i].onmouseover = function() {the.selectOption(this);};
	void function loc(){
		var inneri = i;
		the.treeconteiner.childNodes[i].onclick = function() {the.data.currentTree=the.data.tree[inneri][0];
																this.getElementsByTagName("input")[0].checked=true;};
		the.treeconteiner.childNodes[i].ondblclick = function() {the.data.currentTree=the.data.tree[inneri][0];
																this.getElementsByTagName("input")[0].checked=true;
																the.showChild(inneri);};
	}();
	this.treeconteiner.childNodes[i].innerHTML="&nbsp;";
}
bhv.contentPane().appendChild(this.treeconteiner)

this.table = table;
this.keyColumn = keyColumn;
this.displayValueColumn = displayValueColumn;
this.searchValueColumn = searchValueColumn;
this.requestedKey = null;
this.requestedSearchValue = null;

if (typeof initialValue != "undefined"  && initialValue != null) {
    this.valueElement.value = initialValue;
    this.requestedKey = initialValue;
}

if (typeof this.valueElement.value != "undefined") {
    this.getValueFromServerSync("currentKey=" + encodeURIComponent(this.valueElement.value), "init");
}

}
,///////////////////////////////////////////////////////////////////////////////
SERVER_SCRIPT: bhv.getApplicationFolder()+"combobox/combotree_query.php"
,///////////////////////////////////////////////////////////////////////////////
getServerScript: function() {
    return this.SERVER_SCRIPT;
}
,///////////////////////////////////////////////////////////////////////////////
getHttpParams: function(additions, command) {
var params = "";
params = "table=" + this.table
    + "&keyColumn=" + this.keyColumn
    + "&displayValueColumn=" + this.displayValueColumn
    + "&searchValueColumn=" + this.searchValueColumn    + "&count=" + this.count
	+ "&currentTree=" + this.data.currentTree

if (additions)
    params += "&"+additions;

if (command)
    params += "&command=" + command;
    return params;

}
,//////////////////////////////////////////////////////////////////////////////
getValueFromServer: function(additions, command, selected, timeout) {

bhv.unsetCommand("bhv_combotree_" + this.element.id);

var the = this;
var timeout = 0;

//if ((typeof  additions == "string") &&  additions.indexOf("currentSearchValue") >= 0)
    timeout = 700;

//bhv.setCommand(bhv.sendRequest, bhv,
//    ["get", this.getServerScript(), this.getHttpParams(additions, command), true,
//    this.handleRequest, function(){alert(this.responseText)}, [the, selected]], timeout, "bhv_combotree_" + this.element.id);
bhv.setCommand(bhv.sendScriptRequest, bhv,
    [this.getServerScript(), this.getHttpParams(additions, command), 
    this.handleRequest, [the, selected]], timeout, "bhv_combotree_" + this.element.id);
}
,//////////////////////////////////////////////////////////////////////////////
getValueFromServerSync: function(additions, command, selected, expand) {

bhv.unsetCommand("bhv_combotree_" + this.element.id);

var the = this;

//bhv.sendRequest("get", this.getServerScript(), this.getHttpParams(additions, command), false,
//    this.handleRequest, null, [the, selected]);
bhv.sendScriptRequest(this.getServerScript(), this.getHttpParams(additions, command),  this.handleRequest, [the, selected, expand, command]);


}
,//////////////////////////////////////////////////////////////////////////////
handleRequest: function(combotree, selected, expand, command) {
combotree.data.parseXML(/*this.responseXML*/);
if (combotree.enabled || expand) {
	combotree.enabled = true;
    combotree.element.value = combotree.data.getCurrentKey();
    //combotree.input.value = combotree.data.getCurrentSearchValue();
    combotree.showComboTree(selected);
    var matchedChar = bhv.compareString(String(combotree.input.value).toLowerCase(), String(combotree.data.getCurrentSearchValue()).toLowerCase());
}else {
//  combotree.input.value = combotree.data.getCurrentSearchValue();
    combotree.input.value = combotree.data.getCurrentDisplayValue();
    if (command == "init" && typeof combotree.afterValueChange == 'function')
      combotree.afterValueChange();
//  combotree.element.value = combotree.data.getCurrentSearchValue();
}
}
,//////////////////////////////////////////////////////////////////////////////
assignValue: function(selected) {
  this.input.value = this.data.getCurrentDisplayValue();
  this.valueElement.value = this.data.getCurrentKey();
  if (typeof this.afterValueChange == "function")	this.afterValueChange(this);
}
,//////////////////////////////////////////////////////////////////////////////
hideComboTree: function(selected) {
    this.enabled = false;
    this.conteiner.style.visibility = "hidden";
	//this.treeconteiner.style.visibility = "hidden";
    this.input.focus();
//    this.input.readonly = true;
}
,//////////////////////////////////////////////////////////////////////////////
showComboTree: function(selected) {
if (!this.enabled)
    return;
//this.input.readonly = false;
this.conteiner.style.visibility = "visible";
this.conteiner.style.top = this.input.offsetHeight + bhv.top(this.input)+"px"//this.input.offsetTop;
this.conteiner.style.left = bhv.left(this.input)+"px"//this.input.offsetLeft;
this.conteiner.style.width = this.input.clientWidth + "px";//offsetWidth-delta+"px";

if (selected == "last")
    this.data.currentIndex = this.data.currentCount - 1;
else if (selected == "first")
    this.data.currentIndex = 0;
else if (selected)
    this.data.currentIndex = this.data.getKeyIndex(selected);

for (var i = 0; i < this.count; i++) {
    this.conteiner.childNodes[i].innerHTML =(this.selectTree ? "<input type=radio>" : "")+this.data.getSearchValue(i);
    if (i == this.data.currentIndex)
        this.conteiner.childNodes[i].className = "selectedItem"
    else if (i < this.data.currentCount)
        this.conteiner.childNodes[i].className = "otherItem"
    else
        this.conteiner.childNodes[i].className = "hiddenItem"
}
this.treeconteiner.style.visibility = "visible";
this.treeconteiner.style.top = (this.conteiner.offsetHeight + bhv.top(this.conteiner)+ 2) + "px"; //this.input.offsetTop;
this.treeconteiner.style.left = /*this.input.clientWidth + */bhv.left(this.input) + "px"; //this.input.offsetLeft;

for (var i = 0; i < this.count; i++){
	if (this.data.tree.length > i){
		if (this.data.tree[i][0] == this.data.currentTree)
			this.treeconteiner.childNodes[i].innerHTML = "<input type=radio id=id_radio_tree name=name_radio_tree onclick='' checked>" + this.data.tree[i][2];
		else
			this.treeconteiner.childNodes[i].innerHTML = "<input type=radio id=id_radio_tree name=name_radio_tree onclick=''>" + this.data.tree[i][2];
        this.treeconteiner.childNodes[i].className = "otherItem"
	}else {
	    this.treeconteiner.childNodes[i].innerHTML = "&nbsp;";
        this.treeconteiner.childNodes[i].className = "hiddenItem"
	}
}
this.treeconteiner.style.width = this.input.clientWidth + "px";//offsetWidth-delta+"px";
}
,//////////////////////////////////////////////////////////////////////////////
showChild: function(inneri){
this.conteiner.style.visibility = "visible";
this.conteiner.style.top = this.input.offsetHeight + bhv.top(this.input)+"px"//this.input.offsetTop;
this.conteiner.style.left = bhv.left(this.input)+"px"//this.input.offsetLeft;
this.conteiner.style.width = this.input.clientWidth + "px";//offsetWidth-delta+"px";
this.selectTree = true;
//this.enabled = true;
this.getValueFromServerSync("currentSearchValue=" + encodeURIComponent('') + "&shot=yes", null, null, true);
this.input.value="";
this.input.select();

return

if (selected == "last")
    this.data.currentIndex = this.data.currentCount - 1;
else if (selected == "first")
    this.data.currentIndex = 0;
else if (selected)
    this.data.currentIndex = this.data.getKeyIndex(selected);

for (var i = 0; i < this.count; i++) {
    this.conteiner.childNodes[i].innerHTML ="<input type=radio>"+this.data.getDisplayValue(i);

    if (i == this.data.currentIndex)
        this.conteiner.childNodes[i].className = "selectedItem"
    else if (i < this.data.currentCount)
        this.conteiner.childNodes[i].className = "otherItem"
    else
        this.conteiner.childNodes[i].className = "hiddenItem"
}

this.treeconteiner.style.visibility = "visible";
this.treeconteiner.style.top = this.conteiner.offsetHeight + bhv.top(this.conteiner)+"px"; //this.input.offsetTop;
this.treeconteiner.style.left = this.input.clientWidth + bhv.left(this.input)+100+"px"; //this.input.offsetLeft;

for (var i = 0; i < this.count; i++) {
	if (this.data.tree.length > i){
		if (this.data.tree[i][0] == this.data.currentTree)
			this.treeconteiner.childNodes[i].innerHTML = "<input type=radio id=id_radio_tree name=name_radio_tree onclick='' checked>" + this.data.tree[i][2];
		else
			this.treeconteiner.childNodes[i].innerHTML = "<input type=radio id=id_radio_tree name=name_radio_tree onclick=''>" + this.data.tree[i][2];
        this.treeconteiner.childNodes[i].className = "otherItem"
	}else {
	    this.treeconteiner.childNodes[i].innerHTML = "&nbsp;";
        this.treeconteiner.childNodes[i].className = "hiddenItem"
	}
}
this.treeconteiner.style.width = this.input.clientWidth + "px";//offsetWidth-delta+"px";
}
,//////////////////////////////////////////////////////////////////////////////
selectOption: function(selectedOption) {

for (var i = 0; i < this.count; i++) {
    if (this.conteiner.childNodes[i].className == "selectedItem")
        this.conteiner.childNodes[i].className = "otherItem"
    if (this.conteiner.childNodes[i] == selectedOption) {
        this.data.currentIndex = i;
        selectedOption.className = "selectedItem";
    }
}
}
,//////////////////////////////////////////////////////////////////////////////
onkeydown: function(event0) {

event0.returnValue = true;
event0.cancelBubble = true;

if (event0.keyCode == bhv.key.ESC) {
    //if (this.enabled) {
    this.enabled = false;
	this.input.select();
    this.getValueFromServer("currentKey=" + encodeURIComponent(this.valueElement.value), "init");
    this.hideComboTree();
	this.treeconteiner.style.visibility = "hidden";
    //}
    return true;
}

if (event0.keyCode == bhv.key.TAB) {
    if (this.enabled) {
        this.assignValue();
        this.hideComboTree();
		this.treeconteiner.style.visibility = "hidden";
        this.enabled = false;
	} 
	return false;
}

if (event0.keyCode == bhv.key.ENTER) {

	if (this.selectTree){
		this.selectTree = false;
		this.enabled = true;
		this.data.currentTree=this.data.getCurrentKey();
		this.getValueFromServerSync("currentSearchValue=" + encodeURIComponent('')/* + "&shot=yes"*/, null, null, true);
		this.input.value = "";
		this.input.select();
	}else if (this.enabled) {
        this.assignValue();
        this.hideComboTree();
		this.treeconteiner.style.visibility = "hidden";
        this.enabled = false;
	} else {
        bhv.selectNextInput(this.input);
        return true;
    }
    return true;
}

if (event0.keyCode == bhv.key.RIGHT) {

    if (! this.enabled) {
    //    this.assignValue();
    //    this.hideComboTree();
    //    this.enabled = false;
	//} else {
        bhv.selectNextInput(this.input);
        return true;
    }
    return true;
}


if (event0.keyCode == bhv.key.LEFT) {

    if (! this.enabled) {
    //    this.assignValue();
    //    this.hideComboTree();
    //    this.enabled = false;
	//} else {
        bhv.selectPreviousInput(this.input);
        return true;
    }
    return true;
}


if (! this.enabled) {
    this.enabled = true;
    this.showComboTree();
    this.input.value = this.data.getCurrentSearchValue();//String.fromCharCode(event0.charCode || event0.keyCode);//this.data.getCurrentSearchValue();
    this.input.select();
	this.input.focus();
	return true;
}
	
	
if (event0.keyCode == bhv.key.PAGEDOWN) {
    if (this.data.currentIndex < this.data.currentCount - 1)
        this.data.currentIndex = this.data.currentCount - 1
    else
        this.getValueFromServer("currentKey=" + this.data.getCurrentKey() + "&currentSearchValue=" + encodeURIComponent(this.input.value));

} else if (event0.keyCode == bhv.key.PAGEUP) {
    if (this.data.currentIndex > 0)
        this.data.currentIndex = 0
    else
        this.getValueFromServer("currentKey=" + this.data.getCurrentKey() + "&currentSearchValue=" + encodeURIComponent(this.input.value), "previous", "first");

} else if (event0.keyCode == bhv.key.DOWN) {
    if (this.data.currentIndex < this.data.currentCount - 1)
        this.data.currentIndex ++;
    else
        this.getValueFromServer("currentKey=" + this.data.getCurrentKey() + "&currentSearchValue=" + encodeURIComponent(this.input.value));

} else if (event0.keyCode == bhv.key.UP) {
    if (this.data.currentIndex > 0)
        this.data.currentIndex --;
    else
        this.getValueFromServer("currentKey=" + this.data.getCurrentKey() + "&currentSearchValue=" + encodeURIComponent(this.input.value), "previous", this.data.getCurrentKey());
    } else {
        if (!this.enabled)
            combotree.input.value = combotree.data.getCurrentSearchValue();
 //       else
			//if (!String(this.input.value).isEmpty())
				//this.getValueFromServer("currentSearchValue=" + encodeURIComponent(this.input.value));
    }

this.showComboTree();
return true;
}
,//////////////////////////////////////////////////////////////////////////////
onclick: function(event0) {
    this.enabled = true;
    this.showComboTree();
    this.input.value = this.data.getCurrentSearchValue();
    this.input.select();
	this.input.focus();
}
,//////////////////////////////////////////////////////////////////////////////
onkeyup: function(event0) {

event0.returnValue = true;
event0.cancelBubble = true;
//alert(event0.keyCode);

if (event0.keyCode == bhv.key.ESC 
	|| event0.keyCode == bhv.key.TAB 
	|| event0.keyCode == bhv.key.ENTER || event0.keyCode == bhv.key.RIGHT
	|| event0.keyCode == bhv.key.LEFT
	|| event0.keyCode == bhv.key.PAGEDOWN
	|| event0.keyCode == bhv.key.PAGEUP
    || event0.keyCode == bhv.key.DOWN
    || event0.keyCode == bhv.key.UP)
	
	return true;

else if (!this.enabled)
    combotree.input.value = combotree.data.getCurrentSearchValue();
else if (!String(this.input.value).isEmpty())
	this.getValueFromServer("currentSearchValue=" + encodeURIComponent(this.input.value));

this.showComboTree();
return true;
}
,//////////////////////////////////////////////////////////////////////////////
onclick: function(event0) {
    this.enabled = true;
    this.showComboTree();
    this.input.value = this.data.getCurrentSearchValue();
    this.input.select();
	this.input.focus();
}
,////////////////////////////////////////////////////////////////////////////
setValue: function(value) {
    //this.enabled = true;
    this.valueElement.value = value;
    this.requestedKey = value;
    this.getValueFromServerSync("currentKey=" + encodeURIComponent(value), 'init', null);
}
,////////////////////////////////////////////////////////////////////////////
getValue: function() {
    return this.valueElement.value ;
}
,////////////////////////////////////////////////////////////////////////////
show: function() {
    this.element.style.display = "block";
}
,////////////////////////////////////////////////////////////////////////////
hide: function() {
    this.element.style.display = "none";
}
,////////////////////////////////////////////////////////////////////////////
edit: function() {
    this.enabled = true;
    this.onclick.call(this);
}
/////////////////////////////////////////////////////////////////////////////
}// end prototype


/////////////////////////////////////////////////////////////////////////////
bhv.Combotree.CombotreeData = function (count) {
    this.count = count;
    this.currentCount = -1;
    this.currentIndex = -1;
    this.data = [];
    for (var i = 0; i < count; i++)
        this.data[i] = [];
	this.tree = [[0,'...','...']];
	this.currentTree = 0;
};
/////////////////////////////////////////////////////////////////////////////
bhv.Combotree.CombotreeData.prototype = {
/////////////////////////////////////////////////////////////////////////////
parseXML: function(xmlDocument) {
var rows = bhv.scriptConteiner.responseJSON[0];
if (rows && rows.length && (rows.length > 0)) {
    this.currentIndex = 0;
    this.currentCount = rows.length;
    for (var i = 0; i < rows.length; i++)
        for (var j = 0; j < 3; j++)
            if (rows[i][j])
                this.data[i][j] = rows[i][j];
            else
                this.data[i][j] = "<i>Empty</i>";
}else {
    this.currentIndex = -1;
    this.currentCount = 0;
}
	this.tree = bhv.scriptConteiner.responseJSON[1];
//	alert(bhv.scriptConteiner.responseJSON[1])
}
,////////////////////////////////////////////////////////////////////////////
parseXML0: function(xmlDocument) {
var rows = xmlDocument.getElementsByTagName("row");
if (rows && rows.length && (rows.length > 0)) {
    this.currentIndex = 0;
    this.currentCount = rows.length;
    for (var i = 0; i < rows.length; i++)
        for (var j = 0; j < 3; j++)
            if (rows[i].childNodes[j].firstChild)
                this.data[i][j] = rows[i].childNodes[j].firstChild.data;
            else
                this.data[i][j] = "<i>Empty</i>";
}else {
    this.currentIndex = -1;
    this.currentCount = 0;
}
}
,////////////////////////////////////////////////////////////////////////////
getDisplayValue: function(rowIndex) {
if (rowIndex >= this.currentCount)
    return false;
return this.data[rowIndex][1];
}
,////////////////////////////////////////////////////////////////////////////
getSearchValue: function(rowIndex) {
if (rowIndex >= this.currentCount)
    return false;
return this.data[rowIndex][2];
}
,////////////////////////////////////////////////////////////////////////////
getKeyValue: function(rowIndex) {
if (rowIndex >= this.currentCount)
    return false;
return this.data[rowIndex][0];
}
,////////////////////////////////////////////////////////////////////////////
getKeyIndex: function(rowKey) {
for (var i = 0; i < this.currentCount; i++)
    if (this.getKeyValue(i) == rowKey)
        return i;
return -1;
}
,////////////////////////////////////////////////////////////////////////////
getCurrentDisplayValue: function() {
if (this.currentIndex < 0)  
  return '<i>Empty</i>';
return this.data[this.currentIndex][1];
}
,///////////////////////////////////////////////////////////////////////////
getCurrentKey: function() {
if (this.currentIndex < 0)
  return '<i>Empty</i>';
return this.data[this.currentIndex][0];
}
,///////////////////////////////////////////////////////////////////////////
getCurrentSearchValue: function() {
if (this.currentIndex < 0)
  return '<i>Empty</i>';
return this.data[this.currentIndex][2];
}
////////////////////////////////////////////////////////////////////////////
}// end prototype






