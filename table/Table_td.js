////////////////////////////////////////////////////
bhv.Table = function(element, definition, script){
  this.init(element, definition, script)
};

bhv.Table.prototype = {
////////////////////////////////////////////////////
init: function(element, definition, script){
if (! bhv.contentPane)
  bhv.contentPane=bhv_contentPane;
var the = this;
this.currentOffset = 0;
this.script = script;
this.script = bhv.getApplicationFolder()+"table/table_query.php";
this.definition = definition;
if (typeof element == "string")
    this.element = document.getElementById(element);
else
    this.element = element;
bhv.sendRequest("get", bhv.getApplicationFolder()+this.definition, null, false, 
    function(){the.xmlDefinition = this.responseXML;})
try{
    this.count = parseInt(bhv.getElementData(this.xmlDefinition, "count"));
}catch(e){}

this.columns = [];

var xmlColumns = this.xmlDefinition.getElementsByTagName("column");

for (var i = 0; i < xmlColumns.length; i++){
    var xmlColumn = xmlColumns[i];
    var column = {};
    column.header = bhv.getElementData(xmlColumn, "header");
    column.value = bhv.getElementData(xmlColumn, "value").replace(/^\s+|\s+$/g,"");

    try{
        column.displayValue = bhv.getElementData(xmlColumn, "displayValue").replace(/^\s+|\s+$/g,"");
    }catch(e){
        column.displayValue = bhv.getElementData(xmlColumn, "value").replace(/^\s+|\s+$/g,"");
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
    bhv.contentPane.appendChild(table_editor);
    this.columns[i].editorComponent.hide();
    if (this.columns[i].editorComponent.element.parentNode !==bhv.contentPane)
        bhv.contentPane.appendChild(this.columns[i].editorComponent.element);
}

this.getDataFromServer();

}// end bhv.Table
,//////////////////////////////////////////////////////////////////////////////
getDataFromServer: function(page){
if (! page)
  page = 1;
var queryString = "page=" + page + 
    "&definition="+bhv.getApplicationFolder() + this.definition
var the = this;
bhv.sendRequest("get", this.script, queryString, true, 
        this.handleRequest, function(){alert(this.responseText)}, [the]);
}
,//////////////////////////////////////////////////////////////////////////////
handleRequest: function(table){
table.data = eval (this.responseText);
var count=Math.ceil(table.countAll/(table.count-1));
var pages = table.htmlFooter.childNodes;
if (!table.currentPage)
  table.currentPage = 1
var aPages = bhv$table$pages(table.currentPage,count)
for (var i=pages.length;i<aPages.length;i++){
  var span = document.createElement("SPAN");
  span.innerHTML=i+" "
  span.className = "page"
  table.htmlFooter.appendChild(span)
  span.onmousedown = function(){
    table.cancelCurrentRow();
    table.currentPage = parseInt(this.innerHTML);    
    table.getDataFromServer(table.currentPage);
  }
}
for (var i=0;i<aPages.length;i++){
  pages[i].innerHTML= aPages[i]
  if (aPages[i] == table.currentPage)
    pages[i].className = "selectedPage";
  else
    pages[i].className = "page";
}
table.displayHtmlTable(table);
}
,//////////////////////////////////////////////////////////////////////////////
createHtmlTable: function(table){

table.htmlTable = document.createElement("DIV");
table.htmlHead = document.createElement("DIV");
var row = document.createElement("DIV");
var field = document.createElement("SPAN");

field.appendChild(document.createTextNode("#"))
row.appendChild(field);

for (var i = 0; i < table.columns.length; i++) {
    var field = document.createElement("SPAN");
    field.innerHTML = table.columns[i].header   
    row.appendChild(field);
}

field = document.createElement("SPAN");
field.innerHTML = "Действия"   
row.appendChild(field);

table.htmlHead.appendChild(row);
table.htmlTable.appendChild(table.htmlHead);

table.htmlBody = document.createElement("DIV");

for (var r = 0; r <+ table.count; r++){
  row = document.createElement("DIV");
  field = document.createElement("SPAN");
  field.appendChild(document.createTextNode("#"+(r + table.currentOffset + 1)))
  row.appendChild(field);
  for (var i = 0; i < table.columns.length; i++) {
    field = document.createElement("SPAN");
    field.onmousedown = function(){
        table.editRow(table, this);
    }
    row.appendChild(field);
  }

  var td = document.createElement("SPAN");
  field = document.createElement("SPAN");
  field.className = "silverButton";
  field.innerHTML="save"
  field.onclick = function(){
    var the=this;
    this.className="pressedSilverButton"
    table.saveCurrentRow()
    window.setTimeout(function(){the.className="silverButton";},1000);
  }
  td.appendChild(field);

  field = document.createElement("span");
  field.innerHTML="cancel"
  field.className = "silverButton";
  field.onclick = function(){
    var the=this;
    this.className="pressedSilverButton"
    table.cancelCurrentRow();
    window.setTimeout(function(){the.className="silverButton";},1000);
  }
  td.appendChild(field);
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
table.htmlFooter = field
table.htmlFooter.style.lineHeight ="100px"

}
,//////////////////////////////////////////////////////////////////////////////
createNativeHtmlTable: function(table){

table.htmlTable = document.createElement("TABLE");
table.htmlHead = document.createElement("THEAD");
var row = document.createElement("TR");
var field = document.createElement("TH");

field.appendChild(document.createTextNode("#"))
row.appendChild(field);

for (var i = 0; i < table.columns.length; i++) {
    var field = document.createElement("TH");
    field.innerHTML = table.columns[i].header   
    row.appendChild(field);
}

field = document.createElement("TH");
field.innerHTML = "Действия"   
row.appendChild(field);

table.htmlHead.appendChild(row);
table.htmlTable.appendChild(table.htmlHead);

table.htmlBody = document.createElement("TBODY");

for (var r = 0; r <+ table.count; r++){
  row = document.createElement("TR");
  field = document.createElement("TD");
  field.appendChild(document.createTextNode("#"+(r + table.currentOffset + 1)))
  row.appendChild(field);
  for (var i = 0; i < table.columns.length; i++) {
    field = document.createElement("TD");
    field.onmousedown = function(){
        table.editRow(table, this);
    }
    row.appendChild(field);
  }

  var td = document.createElement("TD");
  field = document.createElement("SPAN");
  field.className = "silverButton";
  field.innerHTML="save"
  field.onclick = function(){
    var the=this;
    this.className="pressedSilverButton"
    table.saveCurrentRow()
    window.setTimeout(function(){the.className="silverButton";},1000);
  }
  td.appendChild(field);

  field = document.createElement("span");
  field.innerHTML="cancel"
  field.className = "silverButton";
  field.onclick = function(){
    var the=this;
    this.className="pressedSilverButton"
    table.cancelCurrentRow();
    window.setTimeout(function(){the.className="silverButton";},1000);
  }
  td.appendChild(field);
  row.appendChild(td);
  table.htmlBody.appendChild(row);
}

table.htmlFooter = document.createElement("TFOOT")
row = document.createElement("TR");
field = document.createElement("TD");
field.colSpan=table.columns.length + 2;
row.appendChild(field)
table.htmlFooter.appendChild(row)
table.htmlTable.appendChild(table.htmlBody);
table.htmlTable.appendChild(table.htmlFooter);
table.element.appendChild(table.htmlTable);
table.htmlFooter = field
table.htmlFooter.style.lineHeight ="100px"

}
,//////////////////////////////////////////////////////////////////////////////
getHtmlCeil: function(row, column){

var body = this.htmlBody;
var rows = body.getElementsByTagName("TR");
return rows[row].getElementsByTagName("TD")[column+1];

}
,//////////////////////////////////////////////////////////////////////////////
getCeilIndex: function(ceil){

var body = this.htmlBody;
var rows = body.getElementsByTagName("TR");
for (var row = 0; row < rows.length; row++){
  var columns = rows[row].getElementsByTagName("TD");
  for (var column = 0; column < columns.length; column++){
    if (columns[column]=== ceil){
        return [row, column-1];
    }
  }
}

}
,//////////////////////////////////////////////////////////////////////////////
getCeilEditor: function(ceil){
var index = this.getCeilIndex(ceil);
var editor = this.columns[index[1]].editorComponent;
editor.tableField = this.columns[index[1]].displayValue;
if (editor.setValue)
    editor.setValue(this.data[index[0]][this.columns[index[1]].value]);
editor.input.style.position = "absolute"
editor.input.style.left = bhv.left(ceil,true)+"px";
editor.input.style.top = bhv.top(ceil,true)+"px";
editor.input.style.width = ceil.offsetWidth+"px";
editor.show();//element.style.display = "block";

return editor;
}
,//////////////////////////////////////////////////////////////////////////////
editRow: function(table, ceil){
this.inEdit = true;
var index = this.getCeilIndex(ceil)[0];
var column = this.getCeilIndex(ceil)[1];
this.currentRow = index;
this.currentValues = {};
this.currentEditors = [];
this.whereClause = this.data[index]["where_clause"];
var htmlRow = ceil.parentNode;
var htmlCeils = htmlRow.getElementsByTagName("TD");
for (var i = 0; i < htmlCeils.length; i++)
  if (htmlCeils[i] !== ceil)
    try{
      var editor = table.getCeilEditor(htmlCeils[i]);
      this.currentEditors[this.currentEditors.length] = editor;
      this.currentValues[editor.tableField] = editor.getValue()
    } catch(e){}
var editor = table.getCeilEditor(ceil)
editor.input.focus();
if (editor.input.onclick)
    editor.input.onclick();
this.currentEditors[this.currentEditors.length] = editor;
}
,//////////////////////////////////////////////////////////////////////////////
saveCurrentRow: function(){
if (!this.inEdit)
	return;
var params = "command=update&where_clause=" + encodeURIComponent(this.whereClause)+
            "&definition="+bhv.getApplicationFolder() + this.definition;
for (var i = 0; i < this.currentEditors.length; i++)
params += "&" + this.columns[i].value + "="
        + encodeURIComponent(this.columns[i].editorComponent.getValue())

var the = this;
bhv.sendRequest("get", this.script, params, false, 
        this.handleRequestRow, function(){alert(this.responseText)}, [the]);
for (var i = 0; i < this.currentEditors.length; i++)
  this.currentEditors[i].hide();//element.style.display = "none"

}
,//////////////////////////////////////////////////////////////////////////////
cancelCurrentRow: function(){
this.inEdit = false;
if (this.currentEditors)
  for (var i = 0; i < this.currentEditors.length; i++)
    this.currentEditors[i].hide();//element.style.display = "none"
}
,//////////////////////////////////////////////////////////////////////////////
handleRequestRow: function(table){
  table.data[table.currentRow] = (eval (this.responseText))[0];
  table.displayHtmlTableRow(table);
}
,//////////////////////////////////////////////////////////////////////////////
displayHtmlTable: function(table){
for (var r = 0; r <+ table.count; r++){
  if (table.data[r]) {
    table.getHtmlCeil(r, -1).innerHTML="#" + (r + table.currentOffset + 1)
    for (var i = 0; i < table.columns.length; i++) 
      table.getHtmlCeil(r, i).innerHTML=table.data[r][table.columns[i].displayValue]
  }else {
    table.getHtmlCeil(r, -1).innerHTML="&nbsp;"
    for (var i = 0; i < table.columns.length; i++) 
      table.getHtmlCeil(r, i).innerHTML="&nbsp;"
  }
  
}
}
,//////////////////////////////////////////////////////////////////////////////
displayHtmlTableRow: function(table){
  for (var i = 0; i < table.columns.length; i++) 
    table.getHtmlCeil(table.currentRow, i).innerHTML=table.data[table.currentRow][table.columns[i].displayValue];
  
}
/////////////////////////////////////////////////////////////////////////////
}

bhv.Table.TableData = function(json){
this.data = eval(json);
}

bhv.Table.TableData.prototype = {
/////////////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////
}

bhv.Table.TableDefaultEditor = function(element){
    this.element = element;
    this.input = document.createElement("INPUT");
    //this.input.onblur = function(){element.style.display = "none";}
    this.input.onkeyup = function(event0){
        event0 = arguments[0] || window.event;
        if (event0.keyCode == bhv.key.ENTER){
            bhv.selectNextInput(this);
            return false;
        }
    }


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
    return this.input.value;
}
,////////////////////////////////////////////////////////////////////////////
show: function() {
    this.element.style.display = "block";
}
,////////////////////////////////////////////////////////////////////////////
hide: function() {
    this.element.style.display = "none";
}
/////////////////////////////////////////////////////////////////////////////
}

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
    page = Math.round(page/factor)*factor
  }
  return pages;
}