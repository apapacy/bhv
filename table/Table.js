////////////////////////////////////////////////////
bhv.Table = function(element, definition, script){
this.init(element, definition, script);
};
////////////////////////////////////////////////////
bhv.Table.prototype = {
////////////////////////////////////////////////////
init: function(element, definition, script){
var the = this;
this.currentOffset = 0;
this.currentRow = -1;
if (typeof script == "string")
  this.script = script;
else
  this.script = bhv.getApplicationFolder() + "table/table_query.php";
this.definition = definition;
if (typeof element == "string")
  this.element = document.getElementById(element);
else
  this.element = element;
bhv.sendRequest("get", bhv.getApplicationFolder()+this.definition, null, false, 
  function(){the.xmlDefinition = this.responseXML;});
try{
  this.count = parseInt(bhv.getElementData(this.xmlDefinition, "count"));
}catch(e){}
this.columns = [];
var xmlColumns = this.xmlDefinition.getElementsByTagName("column");
for (var i = 0; i < xmlColumns.length; i++){
  var xmlColumn = xmlColumns[i];
  var column = {};
  column.header = bhv.getElementData(xmlColumn, "header");
  column.value = bhv.getElementData(xmlColumn, "value").allTrim();
  try{
    column.displayValue = bhv.getElementData(xmlColumn, "displayValue").allTrim();
  }catch(e){
    column.displayValue = bhv.getElementData(xmlColumn, "value").allTrim();
  }
  try{
    column.width = bhv.getElementData(xmlColumn, "width").allTrim();
  }catch(e){
    column.width = false;
  }
  this.columns[i] = column;
  try{
    this.columns[i].editor = bhv.getElementData(xmlColumn, "editor");
  }catch(e){
    this.columns[i].editor = "new bhv.Table.TableDefaultEditor(table_editor)";
  }
  try{
    this.columns[i].editor.hide();
  }catch(e){}
}
this.createHtmlTable(this);
for (var i = 0; i < xmlColumns.length; i++){
  var table_editor = document.createElement("span");
  table_editor.style.position = "absolute";
  this.columns[i].editorComponent = eval(this.columns[i].editor);
  bhv.contentPane().appendChild(table_editor);
  this.columns[i].editorComponent.hide();
  if (this.columns[i].editorComponent.element && this.columns[i].editorComponent.element.parentNode !==bhv.contentPane())
    bhv.contentPane().appendChild(this.columns[i].editorComponent.element);
}
this.getDataFromServer();
}// end bhv.Table
,//////////////////////////////////////////////////////////////////////////////
getDataFromServer: function(page){
if (!page)
  page = 1;
var queryString = "page=" + page + "&definition=" + '../' + this.definition;
var the = this;
bhv.sendScriptRequest(this.script, queryString, this.handleRequest, [the]);
}
,//////////////////////////////////////////////////////////////////////////////
handleRequest: function(table){
table.countAll = bhv.scriptConteiner.countAll;
table.currentOffset = bhv.scriptConteiner.currentOffset;
table.data = bhv.scriptConteiner.responseJSON;
var count = Math.ceil(table.countAll / (table.count - 1));
var pages = table.htmlFooter.childNodes;
if (!table.currentPage)
  table.currentPage = 1
//var aPages = bhv$table$pages(table.currentPage,count)
//for (var i = pages.length; i < aPages.length; i++){
for (var i = pages.length; i < count; i++){
  var span = document.createElement("SPAN");
  span.innerHTML="&nbsp;";
  span.className = "page";
  table.htmlFooter.appendChild(span)
  span.onmousedown = function(){
    table.cancelCurrentRow();
    table.currentPage = parseInt(this.innerHTML);    
    table.getDataFromServer(table.currentPage);
  };
}
/*for (var i=0;i<pages.length;i++){
  pages[i].innerHTML= aPages[i]
  if (aPages[i] == table.currentPage)
    pages[i].className = "selectedPage";
  else
    pages[i].className = "page";
}*/
for (var i = 0; i < count; i++){
  pages[i].innerHTML = " " + (i + 1);
  if (i + 1 == table.currentPage)
    pages[i].className = "selectedPage";
  else
    pages[i].className = "page";
}
table.currentRow = -1;
table.displayHtmlTable(table);
}
,//////////////////////////////////////////////////////////////////////////////
createHtmlTable: function(table){
table.htmlTable = document.createElement("DIV");
table.htmlTable.className = "bhv_table";
var row = table.htmlHead = document.createElement("DIV");
table.htmlHead.className = "bhv_table_head";
//var row = document.createElement("DIV");
var field = document.createElement("DIV");
field.className = "bhv_table_column_head";
field.appendChild(document.createTextNode("#"))
row.appendChild(field);
field.style.width = '50px';
for (var i = 0; i < table.columns.length; i++){
  var field = document.createElement("DIV");
	field.className = "bhv_table_column_head";
  field.innerHTML = table.columns[i].header;
	field.className = "bhv_table_column_head";
  if (table.columns[i].width)
    field.style.width = table.columns[i].width;
  row.appendChild(field);
}
field = document.createElement("DIV");
field.innerHTML = "Действия";
field.className = "bhv_table_column_head";  
row.appendChild(field);
//table.htmlHead.appendChild(row);
table.htmlTable.appendChild(table.htmlHead);
table.htmlBody = document.createElement("DIV");
table.htmlBody.className = "bhv_table_body";
for (var r = 0; r < table.count; r++){
  row = document.createElement("DIV");
  row.className = "bhv_table_row";
  row.title = r;
  field = document.createElement("DIV");
  field.className = "bhv_table_ceil";
  field.appendChild(document.createTextNode("#"+(r + table.currentOffset + 1)))
  field.style.width = '50px';
  row.appendChild(field);
  for (var i = 0; i < table.columns.length; i++) {
    field = document.createElement("DIV");
  	field.className = "bhv_table_ceil";
    if (table.columns[i].width)
      field.style.width = table.columns[i].width;
    field.onclick = function(){
      table.editRow(table, this);
	  	return true;
    };
    row.appendChild(field);
  }
  var td = document.createElement("DIV");
  td.className = "bhv_table_ceil";
  field = document.createElement("SPAN");
  field.className = "silverButton";
  field.innerHTML="save"
  field.onclick = function(){
    var the=this;
    this.className="pressedSilverButton"
    table.saveCurrentRow()
    window.setTimeout(function(){the.className="silverButton";},1000);
  };
  td.appendChild(field);
  field = document.createElement("span");
  field.innerHTML="cancel"
  field.className = "silverButton";
  field.onclick = function(){
    var the=this;
    this.className="pressedSilverButton"
    table.cancelCurrentRow(this);
    window.setTimeout(function(){the.className="silverButton";},1000);
  };
  td.appendChild(field);
  if (r != table.count - 1){
    field = document.createElement("span");
    field.title=r;
    field.innerHTML="insert"
    field.className = "silverButton";
    field.onclick = function(){
      var the=this;
      this.className="pressedSilverButton"
      table.insertCurrentRow(this);
      window.setTimeout(function(){the.className="silverButton";},1000);
    };
    td.appendChild(field);
  }
  row.appendChild(td);
  table.htmlBody.appendChild(row);
}
table.htmlFooter = document.createElement("DIV")
row = document.createElement("DIV");
field = document.createElement("SPAN");
field.colSpan=table.columns.length + 2;
row.appendChild(field)
table.htmlFooter.appendChild(row)
table.htmlTable.appendChild(table.htmlBody);
table.htmlTable.appendChild(table.htmlFooter);
table.element.appendChild(table.htmlTable);
table.htmlFooter = field;
table.htmlFooter.style.lineHeight ="100px";
}
,//////////////////////////////////////////////////////////////////////////////
getHtmlCeil: function(row, column){
var body = this.htmlBody;
var rows = body.childNodes;
return rows[row].childNodes[column+1];
}
,//////////////////////////////////////////////////////////////////////////////
getCeilIndex0: function(ceil){
var body = this.htmlBody;
var rows = body.childNodes;
for (var row = 0; row < rows.length; row++){
  var columns = rows[row].childNodes;
  for (var column = 0; column < columns.length; column++)
    if (columns[column] === ceil)
        return [row, column-1];
}
}
,//////////////////////////////////////////////////////////////////////////////
getCeilIndex: function(ceil){
var rows_row = ceil.parentNode;
row = rows_row.title;
var columns = rows_row.childNodes;
for (var column = 0; column < columns.length; column++)
  if (columns[column] === ceil)
    return [row, column-1];
}
,//////////////////////////////////////////////////////////////////////////////
getCeilEditor: function(index, i, ceil){
var editor = this.columns[i].editorComponent;
//if (editor.setValue)
editor.setValue(this.data[index][this.columns[i].value]);
if (editor.input){
  editor.input.style.position = "absolute"
  editor.input.style.left = bhv.left(ceil, true)+"px";
  editor.input.style.top = bhv.top(ceil, true)+"px";
  editor.input.style.width = ceil.offsetWidth+"px";
  editor.show();
}
return editor;
}
,//////////////////////////////////////////////////////////////////////////////
editRow: function(table, ceil){
var index = ceil.parentNode.title;
this.cancelCurrentRow(ceil);
this.currentRow = index;
this.whereClause = this.data[index]["where_clause"];
var htmlRow = ceil.parentNode;
var htmlCeils = htmlRow.childNodes;
var isFocused = false;
for (var i = 0; i < this.columns.length; i++){
  try{
    var editor = this.getCeilEditor(index, i, htmlCeils[i+1]);
	if (!isFocused && typeof editor.input == "object" && typeof editor.input.focus == "function"){
		editor.input.focus(); isFocused = true;
	}
  }catch(e){alert("error"+ i)}
}
this.inEdit = true;
}
,//////////////////////////////////////////////////////////////////////////////
insertCurrentRow: function(ceil){
this.cancelCurrentRow();

var index = this.insertedIndex = ceil.title;
this.currentRow = index;
this.whereClause = this.data[index]["where_clause"];
var htmlRow = ceil.parentNode.parentNode;
var htmlCeils = htmlRow.childNodes;
this.whereClause = this.data[index]["where_clause"];
var isFocused = false;
for (var i = 0; i < this.columns.length; i++){
  try{
    var editor = this.getCeilEditor(index, i, htmlCeils[i+1]);
	if (!isFocused && typeof editor.input == "object" && typeof editor.input.focus == "function")
	  editor.input.focus(); isFocused = true;
  }catch(e){alert("error"+ i)}
}
this.htmlHead.style.height=50-htmlRow.clientHeight+"px";
htmlRow.style.marginBottom = htmlRow.clientHeight+"px";
this.inInsert = true;
}
,//////////////////////////////////////////////////////////////////////////////
saveCurrentRow: function(){
if (!this.inEdit && !this.inInsert)
  return;
if (this.inEdit) 
  var action = "update";
else 
  var action = "insert"
this.cancelCurrentRow();
var params = "command=" + action + "&where_clause=" + encodeURIComponent(this.whereClause) + "&definition=" + '../' + this.definition;
for (var i = 0; i < this.columns.length; i++)
  params += "&" + this.columns[i].value + "=" + encodeURIComponent(this.columns[i].editorComponent.getValue());
var the = this;
bhv.sendScriptRequest(this.script, params, this.handleRequestRow, [the, action]);
}
,//////////////////////////////////////////////////////////////////////////////
cancelCurrentRow: function(){
this.inEdit = false;
this.inInsert = false;
for (var i = 0; i < this.columns.length; i++)
	this.columns[i].editorComponent.hide();
var rows = this.htmlBody.childNodes;
this.htmlHead.style.height = 50 + "px";
for (var i = 0; i < rows.length; i++)
    rows[i].style.marginBottom = "0px";
}
,//////////////////////////////////////////////////////////////////////////////
handleRequestRow: function(table, command){
if (command == "insert"){
  table.currentRow++; 
  for (var i = Math.min(table.data.length, table.count - 1); i > table.currentRow; i--){
    table.data[i] = table.data[i-1];
    table.displayHtmlTableRow(table, i);
  }
}
table.data[table.currentRow] = bhv.scriptConteiner.responseJSON[0];// (eval (this.responseText))[0];
for (var i = 0; i < table.columns.length; i++)
  table.columns[i].editorComponent.hide();
table.displayHtmlTableRow(table, table.currentRow);
for (var i = table.count - 1; i >= 0;i--){
  if (i == table.currentRow)
    table.htmlBody.childNodes[i].className="bhv_table_current_row";
  else
	table.htmlBody.childNodes[i].className="bhv_table_row";
}
}
,//////////////////////////////////////////////////////////////////////////////
displayHtmlTable: function(table){
for (var r = 0; r < +table.count; r++){
  if (r == table.currentRow)
    table.htmlBody.childNodes[r].className="bhv_table_current_row";
  else
	table.htmlBody.childNodes[r].className="bhv_table_row";
  if (table.data[r]) {
    table.getHtmlCeil(r, -1).innerHTML = "#" + (r + table.currentOffset + 1);
    for (var i = 0; i < table.columns.length; i++)
      table.getHtmlCeil(r, i).innerHTML=table.data[r][table.columns[i].displayValue]
  }else{
    table.getHtmlCeil(r, -1).innerHTML = "#" + (r + table.currentOffset + 1);
    for (var i = 0; i < table.columns.length; i++) 
      table.getHtmlCeil(r, i).innerHTML = "&nbsp;"
  }
}
}
,//////////////////////////////////////////////////////////////////////////////
displayHtmlTableRow: function(table, i){
  if (typeof i == 'undefined')
    var currentRow = table.currentRow;
  else
    var currentRow = i;
  for (var i = 0; i < table.columns.length; i++) 
    table.getHtmlCeil(currentRow, i).innerHTML = table.data[currentRow][table.columns[i].displayValue];
}
/////////////////////////////////////////////////////////////////////////////
}
/////////////////////////////////////////////////////////////////////////
bhv.Table.TableData = function(json){
this.data = eval(json);
}
/////////////////////////////////////////////////////////////////////////
bhv.Table.TableData.prototype = {
}
////////////////////////////////////////////////////////////////////
bhv.Table.TableDefaultEditor = function(element, type){
var the = this;
this.element = element;
this.input = document.createElement("INPUT");
this.input.onkeydown = function(event0){
  event0 = arguments[0] || window.event;
  if (event0.keyCode == bhv.key.ENTER){
    bhv.selectNextInput(this);
    return false;
  }
}
/*this.input.onblur = function(event0){
  event0 = arguments[0] || window.event;
  this.select(false);
}*/
this.input.onfocus = function(event0){
  event0 = arguments[0] || window.event;
  this.select();
}
/*this.input.onclick = function(event0){
    event0 = arguments[0] || window.event;
    the.edit();
}*/
this.input.type = "TEXT";
this.element.appendChild(this.input);
}
bhv.Table.TableDefaultEditor.prototype = {
/////////////////////////////////////////////////////////////////////////////
setValue: function(value){
this.input.value = value;
}
,/////////////////////////////////////////////////////////////////////////////
getValue: function(){
return String(this.input.value).replace(',', '.');
}
,////////////////////////////////////////////////////////////////////////////
show: function(){
this.element.style.display = "block";
}
,////////////////////////////////////////////////////////////////////////////
hide: function(){
this.element.style.display = "none";
}
,////////////////////////////////////////////////////////////////////////////
edit: function(){
if (this.input.setSelectionRange){
  this.input.focus();
  //this.input.setSelectionRange(0, this.input.value.length);
  //this.input.select();
}else if (this.input.createTextRange){
  this.input.focus();
  //var range = input.createTextRange();
  //range.collapse(true);
  //range.moveEnd('character', selectionEnd);
  //range.moveStart('character', selectionStart);
  //range.select();
  //var textRange = this.input.createTextRange();
  //textRange.moveStart('character', 1);
  //textRange.select();
  //this.input.focus();
}else {
  this.input.focus();
  //this.input.select();
}
}
/////////////////////////////////////////////////////////////////////////////
}
///////////////////////////////////////////////////////////////////////////////
function bhv$table$pages(page, count){
var pages=[];
pages.push(page)
var factor = 1;
  while (pages[0]>1 || pages[pages.length-1]<count){
    for(var i= -1; pages[0] !=1 && i > -5; i--) { 
       var current = page + i * factor;
       if (current < 1)
         current = 1;
       pages.unshift(current)
       if (current == 1)
         break;
    }  
    for(var i= 1; pages[pages.length-1]!=count && i < 5; i++) { 
       var current = page + i * factor;
       if (current > count)
         current = count;
       pages.push(current)
       if (current == count)
         break;
    }  
    factor *=10;
    page = Math.ceil(page/factor)*factor
  }
  return pages;
}
////////////////////////////////////////////////////////////////////////////////////
bhv.Table.VirtualEditor = function(){}
///////////////////////////////////////////////////////////////
bhv.Table.VirtualEditor.prototype = {
/////////////////////////////////////////////////////////////////////////////
setValue: function(value){
this.value = "" + value;
}
,/////////////////////////////////////////////////////////////////////////////
getValue: function(){
return this.value;
}
,////////////////////////////////////////////////////////////////////////////
show: function(){
}
,////////////////////////////////////////////////////////////////////////////
hide: function() {
}
,////////////////////////////////////////////////////////////////////////////
edit: function() {
}
//////////////////////////////////////////////////////////////////////////////////
}
