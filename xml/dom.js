if (typeof bhv != "object")
  var bhv = {};
if (typeof bhv.dom != "object")
  bhv.dom = {};
 
  
bhv.ISA = function(to, from){
  for (var p in from)
    to[p] = from[p];
  return to;  
}

bhv.isa = function(to, from){
  for (var p in from)
    if (typeof to[p] == "undefined")
      to[p] = from[p];
    return to;
}

bhv.IN = function(element, collection) {
  for (var p in collection)
    if (element === collection[p])
      return true;
  return false;
}

bhv.proto = new Object();

bhv.dom.object2xml = function(object, name, path, pathNames) {
  if (typeof path == "undefined") {
    path = [];
    pathNames = [];
  }
  path.push(object);
  pathNames.push(name);
  var xmlString = "<" + name + ">";
  if (typeof object == "object")
    for (var p in object)try{
      if (typeof object[p] != "function" && (! bhv.proto[p] || p == "value"))
        if (bhv.IN(object[p], path))
          xmlString += bhv.dom.object2xml("<![JSON[" + pathNames.join(".") + "." + p + "]]>", p, path, pathNames);
        else
          xmlString += bhv.dom.object2xml(object[p], p, path, pathNames);
    }catch(ex){}
  else if (typeof object != "function")
    xmlString += (""+object).replace(/&/g, "&amp;").replace(/</g, "&lt;");
  xmlString += "</" + name + ">\n";
  path.pop();
  pathNames.pop();
  return xmlString;
}

bhv.dom.form2xml = function(form) {
  if (typeof form == "string") {
    var name = form;
    form = document.forms[name] || document.getElementByID(name);
  } else
    var name = form.name || form.id;
  var xmlString = "<" + name + ">";

  for (var i = 0; i < form.elements.length; i++){
    if (form.elements[i].tagName == "INPUT") {
      if (form.elements[i].type != "radio" && form.elements[i].type != "checkbox" || form.elements[i].checked){
        xmlString += "<" + (form.elements[i].name || form.elements[i].id || form.elements[i].type) + ">"; 
        xmlString += form.elements[i].value.replace(/&/g, "&amp;").replace(/</g, "&lt;");
        xmlString += "</" + (form.elements[i].name || form.elements[i].id || form.elements[i].type) + ">\n";  
      }
    } else if (form.elements[i].tagName == "SELECT") {
      for (var k = 0; k < form.elements[i].options.length; k++){
        if (form.elements[i].options[k].selected){
          xmlString += "<" + (form.elements[i].name || form.elements[i].id || "select") + ">"; 
          xmlString += form.elements[i].options[k].value.replace(/&/g, "&amp;").replace(/</g, "&lt;");
          xmlString += "</" + (form.elements[i].name || form.elements[i].id || "select") + ">\n"; 
        }
      }
    }
  }
  xmlString += "</" + name + ">";

 alert(xmlString)

  return xmlString;
}



bhv.dom.ELEMENT_NODE       = 1;
bhv.dom.ATTRIBUTE_NODE     = 2;
bhv.dom.TEXT_NODE          = 3;
bhv.dom.CDATA_SECTION_NODE = 4;
bhv.dom.ENTITY_REFERENCE_NODE = 5;
bhv.dom.ENTITY_NODE        = 6;
bhv.dom.PROCESSING_INSTRUCTION_NODE = 7;
bhv.dom.COMMENT_NODE       = 8;
bhv.dom.DOCUMENT_NODE      = 9;
bhv.dom.DOCUMENT_TYPE_NODE = 10;
bhv.dom.DOCUMENT_FRAGMENT_NODE = 11;
bhv.dom.NOTATION_NODE      = 12;

bhv.dom.Document = function(name, encoding){
  this.documentElement = this.createElement(name);
  this.documentElement.parentNode = this;
  this.encoding = encoding || "windows-1251";
};

bhv.dom.Document.prototype = {
nodeType: bhv.dom.DOCUMENT_NODE
,/////////////////////////////////
createElement: function(tagName){
  return new bhv.dom.Element(tagName);
}
,/////////////////////////////////
createTextNode: function(text){
  return new bhv.dom.Text(text)
}
,/////////////////////////////////////
toString: function(){
  return "<?xml version=\"1.0\" encoding=\""+this.encoding+"\"?>\n\n"+
     this.documentElement.toString();
}
,/////////////////////////////////////
getElementsByTagName: function(tagName){
  return this.documentElement.getElementsByTagName(tagName);
}
}

bhv.dom.Attr = function(name, value){
  this.name = name;
  this.value = value;
}



bhv.dom.Element = function(tagName){
  this.tagName = tagName;
  this.attributes = [];
  this.childNodes = [];
};

bhv.dom.Element.prototype = {
nodeType: bhv.dom.ELEMENT_NODE
,/////////////////////////////////////////////
getAttrribute: function(name){
  for (var i = 0; i < this.attributes.length; i++)
    if (this.attributes[i].name == name)
      return this.attributes[i].value;
  return undefined;
}
,////////////////////////////////////////////
setAttribute: function(name, value){
  for (var i = 0; i < this.attributes.length; i++)
    if (this.attributes[i].name == name){
      this.attributes[i].value;
      return;
    }
  this.attributes.push(new bhv.dom.Attr(name, value));
}
,///////////////////////////////////////////
removeAttribute: function(name){
  for (var i = 0; i < this.attributes.length; i++)
    if (this.attributes[i].name == name)
      this.attributes.splice(i, 1);
}
,//////////////////////////////////////////
getElementsByTagName: function(tagName, nodes){
  if (typeof nodes == "undefined")
    nodes = [];
  for (var i = 0; i < this.childNodes.length; i++){
    if (this.childNodes[i].tagName == tagName)
      nodes.push(this.childNodes[i])
    if (typeof this.childNodes[i].getElementsByTagName == "function")
      this.childNodes[i].getElementsByTagName(tagName, nodes);
  }
  return nodes;
}
,/////////////////////////////////////////
__getChildNodeIndex: function(node){
  var nodeIndex = -1;
  for (var i = 0; i < this.childNodes.length; i++)
    if (this.childNodes[i] == node)
      nodeIndex = i;
  return nodeIndex;
}
,//////////////////////////////////////////
insertBefore: function(newChild, beforeChild){
  var nodeIndex = this.__getChildNodeIndex(beforeChild)
  if (nodeIndex > -1)
    this.childNodes.splice(nodeIndex, 0, newChild);
  else
     throw new Error("Не найден узел для вставки");
  var oldParent = newChild.parentNode;
  newChild.parentNode = this;
  if (oldParent && oldParent.removeChild)
     oldParent.removeChild(newChild);
}
,//////////////////////////////////////////
replaceChild: function(newChild, replacedChild){
  var nodeIndex = this.__getChildNodeIndex(replacedChild)
  if (nodeIndex > -1)
     this.childNodes[nodeIndex] = newChild;
   else
     throw new Error("Не найден узел для замены");
   replacedChild.parentNode = undefined;
   var oldParent = newChild.parentNode;
   newChild.parentNode = this;
   if (oldParent && oldParent.removeChild)
     oldParent.removeChild(newChild);
}
,//////////////////////////////////////////
removeChild: function(removedChild){
  var nodeIndex = this.__getChildNodeIndex(removedChild)
  if (nodeIndex > -1)
     this.childNodes.splice(nodeIndex, 1);
   else
     throw new Error("Не найден узел для удаления");
  removedChild.parentNode = undefined; 
}
,//////////////////////////////////////////
appendChild: function(newChild){
   this.childNodes.push(newChild);
   var oldParent = newChild.parentNode;
   newChild.parentNode = this;
   if (oldParent && oldParent.removeChild)
     oldParent.removeChild(newChild);

}
,//////////////////////////////////////
toString: function(xmlString){
   if (typeof xmlString != "string")
     xmlString = "";
   xmlString += "<"+this.tagName;
   for (var i = 0; i < this.attributes.length; i++)
      xmlString += " " + this.attributes[i].name +"=" + "\"" 
       + this.attributes[i].value.replace(/&/g, "&amp;").replace(/</g, "&lt;") + "\"";
   if (this.childNodes.length == 0)   
      xmlString += "/>";
    else {
        xmlString += ">";
        for(i = 0; i < this.childNodes.length; i++)
        xmlString = this.childNodes[i].toString(xmlString);
        xmlString += "</"+this.tagName+">";      
    }
return xmlString;
}
}


bhv.dom.Text = function(data){
  this.data = data;
}

bhv.dom.Text.prototype.name = "#text"

bhv.dom.Text.prototype.toString = function(xmlString){
  if (typeof xmlString != "string")
    return this.data.replace(/&/g, "&amp;").replace(/</g, "&lt;");
  else return xmlString + this.data.replace(/&/g, "&amp;").replace(/</g, "&lt;");
}
