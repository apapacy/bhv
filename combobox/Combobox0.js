////////////////////////////////////////////////////////////////////////////////
bhv.Combobox = function(element, valueElement, initialValue, count,
    table, keyColumn, displayValueColumn, searchValueColumn){
  this.init(element, valueElement, initialValue, count,
    table, keyColumn, displayValueColumn, searchValueColumn);
};
////////////////////////////////////////////////////////////////////////////////
bhv.Combobox.prototype = {
constructor: bhv.Combobox
,///////////////////////////////////////////////////////////////////////////////
init: function(element, valueElement, initialValue, count,
                 table, keyColumn, displayValueColumn, searchValueColumn){
// Переменнаая the исползуется в замыканиях
var the = this;
// Для удаления циклических ссылок в замыканиях достаточно обнулить the
this.destroy = function(){
  the.destroy = null;
  the = null;
};
this.enabled = false;
this.count = count;
this.data = new bhv.Combobox.ComboboxData(this.count);

if (typeof element == "string")
    this.element = document.getElementById(element);
else
    this.element = element;

this.input = document.createElement("INPUT");
this.input.type = "text";
this.input.style.width = this.element.style.width ;
// Для фукнций-обработчиков событий вызываем функции-методы объекта Combobox,
// которые исползуют замыкание переменной the
this.input.onkeyup = function() {
    var event0 = arguments[0] || window.event;
    the.onkeyup(event0);
};
this.input.onclick = function() {
    var event0 = arguments[0] || window.event;
    the.onclick(event0);
};
this.input.onblur = function() {
    var event0 = arguments[0] || window.event;
    if (the.enabled) {
        the.enabled = false;
	    the.assignValue();
        setTimeout(function(){the.hideComboBox()}, 100);
        event0.cancelBubble = true;
        event0.returnValue = false;
        this.focus();
        return false;
   }
};

this.element.appendChild(this.input);

if (!valueElement)
    this.valueElement = {};
else if (typeof valueElement == "string")
		this.valueElement = document.getElementById(valueElement);
	else
		this.valueElement = valueElement;


this.conteiner = document.createElement("DIV");
this.conteiner.className = "textDropDown"
this.conteiner.onmousedown = function() {
    the.assignValue();
    the.hideComboBox();
}

for (var i = 0; i < this.count; i++)
    this.conteiner.appendChild(document.createElement("DIV")) ;

for (var i = 0; i < this.count; i++) {
    this.conteiner.childNodes[i].className = "otherItem";
    this.conteiner.childNodes[i].onmouseover = function() {the.selectOption(this);};
}

this.element.appendChild(this.conteiner)

this.table = table;
this.keyColumn = keyColumn;
this.displayValueColumn = displayValueColumn;
this.searchValueColumn = searchValueColumn;
this.requestedKey = null;
this.requestedSearchValue = null;

if (typeof initialValue != "undefined" && initialValue != null) {
    this.valueElement.value = initialValue;
    this.requestedKey = initialValue;
}

if (typeof this.valueElement.value != "undefined") {
    this.getValueFromServer("currentKey=" + encodeURIComponent(this.valueElement.value));
}

}
,///////////////////////////////////////////////////////////////////////////////
SERVER_SCRIPT: bhv.getApplicationFolder()+"combobox/combobox_query.php"
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

if (additions)
    params += "&"+additions;

if (command)
    params += "&command=" + command;
    return params;

}
,//////////////////////////////////////////////////////////////////////////////
getValueFromServer: function(additions, command, selected, timeout) {

bhv.unsetCommand("bhv_combobox_" + this.element.id);

var the = this;
var timeout = 0;

//if ((typeof  additions == "string") &&  additions.indexOf("currentSearchValue") >= 0)
    timeout = 700;

//bhv.setCommand(bhv.sendRequest, bhv,
//    ["get", this.getServerScript(), this.getHttpParams(additions, command), true,
//    this.handleRequest, function(){alert(this.responseText)}, [the, selected]], timeout, "bhv_combobox_" + this.element.id);

bhv.setCommand(bhv.sendScriptRequest, bhv,
    [this.getServerScript(), this.getHttpParams(additions, command), 
    this.handleRequest, [the, selected]], timeout, "bhv_combobox_" + this.element.id);


}
,//////////////////////////////////////////////////////////////////////////////
getValueFromServerSync: function(additions, command, selected) {

bhv.unsetCommand("bhv_combobox_" + this.element.id);

var the = this;

//bhv.sendRequest("get", this.getServerScript(), this.getHttpParams(additions, command), false,
//    this.handleRequest, null, [the, selected]);

bhv.sendScriptRequest(this.getServerScript(), this.getHttpParams(additions, command),  this.handleRequest, [the, selected]);


}
,//////////////////////////////////////////////////////////////////////////////
handleRequest: function(combobox, selected) {
//alert(this.responseText)
//eval(this.responseText);
combobox.data.parseXML(/*this.responseXML*/);
if (combobox.enabled) {
    combobox.element.value = combobox.data.getCurrentKey();
    //combobox.input.value = combobox.data.getCurrentSearchValue();
    combobox.showComboBox(selected);
    var matchedChar = bhv.compareString(String(combobox.input.value).toLowerCase(), String(combobox.data.getCurrentSearchValue()).toLowerCase());
	if (matchedChar < String(combobox.input.value).length)
		if (combobox.input.createTextRange) {
			var textRange = combobox.input.createTextRange();
			textRange.moveStart('character', matchedChar);
			textRange.select();
		}else
			combobox.input.setSelectionRange(matchedChar, combobox.input.value.length);

}else {
//  combobox.input.value = combobox.data.getCurrentSearchValue();
    combobox.input.value = combobox.data.getCurrentDisplayValue();
//  combobox.element.value = combobox.data.getCurrentSearchValue();

}
}
,//////////////////////////////////////////////////////////////////////////////
assignValue: function(selected) {
  this.input.value = this.data.getCurrentDisplayValue();
  this.valueElement.value = this.data.getCurrentKey();
  if (typeof this.afterValueChange == "function")
	this.afterValueChange(this);
}
,//////////////////////////////////////////////////////////////////////////////
hideComboBox: function(selected) {

    this.enabled = false;
    this.conteiner.style.visibility = "hidden";
    this.input.focus();
//    this.input.readonly = true;
}
,//////////////////////////////////////////////////////////////////////////////
showComboBox: function(selected) {
if (! this.enabled)
    return;

//this.input.readonly = false;
this.conteiner.style.visibility = "visible";
this.conteiner.style.top = this.input.offsetHeight + bhv.top(this.input)+"px"//this.input.offsetTop;
this.conteiner.style.left = bhv.left(this.input)+"px"//this.input.offsetLeft;
//this.conteiner.style.width = this.input.clientOffset //xWidth(this.input);
//var delta=this.input.offsetWidth;
//this.conteiner.style.width = this.input.offsetWidth;
//delta = (this.conteiner.offsetWidth-this.conteiner.clientWidth)/2;
//  if (delta !=0)
this.conteiner.style.width = this.input.clientWidth + "px";//offsetWidth-delta+"px";

if (selected == "last")
    this.data.currentIndex = this.data.currentCount - 1;
else if (selected == "first")
    this.data.currentIndex = 0;
else if (selected)
    this.data.currentIndex = this.data.getKeyIndex(selected);

for (var i = 0; i < this.count; i++) {
    this.conteiner.childNodes[i].innerHTML =this.data.getDisplayValue(i);

    if (i == this.data.currentIndex)
        this.conteiner.childNodes[i].className = "selectedItem"
    else if (i < this.data.currentCount)
        this.conteiner.childNodes[i].className = "otherItem"
    else
        this.conteiner.childNodes[i].className = "hiddenItem"
}
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
onkeyup: function(event0) {

event0.returnValue = false;
event0.cancelBubble = true;
if (event0.keyCode == bhv.key.ESC) {
    //if (this.enabled) {
    this.enabled = false;
    this.getValueFromServer("currentKey=" + encodeURIComponent(this.valueElement.value));
    this.hideComboBox();
    //}
    return false;
}

if (event0.keyCode == bhv.key.TAB) {
    if (this.enabled) {
        this.assignValue();
        this.hideComboBox();
        this.enabled = false;
	} 
}

if (event0.keyCode == bhv.key.ENTER || event0.keyCode == bhv.key.RIGHT) {

    if (this.enabled) {
        this.assignValue();
        this.hideComboBox();
        this.enabled = false;
	} else {
        bhv.selectNextInput(this.input);
        return false;
    }
    return false;
}

if (event0.keyCode == bhv.key.LEFT) {

    if (this.enabled) {
        this.assignValue();
        this.hideComboBox();
        this.enabled = false;
	} else {
        bhv.selectPreviousInput(this.input);
        return false;
    }
    return false;
}


if (!this.enabled) {
    this.enabled = true;
    this.showComboBox();
    this.input.value = this.data.getCurrentSearchValue();//String.fromCharCode(event0.charCode || event0.keyCode);//this.data.getCurrentSearchValue();
    this.input.select();
	this.input.focus();
	return false;
}
	
	
if (event0.keyCode == bhv.key.PAGEDOWN) {
    if (this.data.currentIndex < this.data.currentCount - 1)
        this.data.currentIndex = this.data.currentCount - 1
    else
        this.getValueFromServer("currentKey=" + this.data.getCurrentKey());

} else if (event0.keyCode == bhv.key.PAGEUP) {
    if (this.data.currentIndex > 0)
        this.data.currentIndex = 0
    else
        this.getValueFromServer("currentKey=" + this.data.getCurrentKey(), "previous", "first");

} else if (event0.keyCode == bhv.key.DOWN) {
    if (this.data.currentIndex < this.data.currentCount - 1)
        this.data.currentIndex ++;
    else
        this.getValueFromServer("currentKey=" + this.data.getCurrentKey());

} else if (event0.keyCode == bhv.key.UP) {
    if (this.data.currentIndex > 0)
        this.data.currentIndex --;
    else
        this.getValueFromServer("currentKey=" + this.data.getCurrentKey(), "previous", this.data.getCurrentKey());
    } else {
        if (!this.enabled)
            combobox.input.value = combobox.data.getCurrentSearchValue();
        else
			if (!String(this.input.value).isEmpty())
				this.getValueFromServer("currentSearchValue=" + encodeURIComponent(this.input.value));
    }

this.showComboBox();
return false;
}
,//////////////////////////////////////////////////////////////////////////////
onclick: function(event0) {
    this.enabled = true;
    this.showComboBox();
    this.input.value = this.data.getCurrentSearchValue();
    this.input.select();
	this.input.focus();
}
,////////////////////////////////////////////////////////////////////////////
setValue: function(value) {
    //this.enabled = true;
    this.valueElement.value = value;
    this.requestedKey = value;
    this.getValueFromServerSync("currentKey=" + encodeURIComponent(value), null, null, 1);
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
bhv.Combobox.ComboboxData = function (count) {
    this.count = count;
    this.currentCount = -1;
    this.currentIndex = -1;
    this.data = [];
    for (var i = 0; i < count; i++)
        this.data[i] = [];
};
/////////////////////////////////////////////////////////////////////////////
bhv.Combobox.ComboboxData.prototype = {
/////////////////////////////////////////////////////////////////////////////
parseXML: function(xmlDocument) {
var rows = bhv.scriptConteiner.responseJSON;
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
return this.data[this.currentIndex][1];
}
,///////////////////////////////////////////////////////////////////////////
getCurrentKey: function() {
return this.data[this.currentIndex][0];
}
,///////////////////////////////////////////////////////////////////////////
getCurrentSearchValue: function() {
return this.data[this.currentIndex][2];
}
////////////////////////////////////////////////////////////////////////////
}// end prototype

