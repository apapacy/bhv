if (!Function.prototype.apply){
    Function.prototype.apply = function(thisObj, args){
        var a = [];
        for (var i = 0; i < args.length; i++){
            a[i] = "args[" + i + "]";
        }
        thisObj.__apply__ = this;
        a="thisObj.__apply__(" + a.join(",") +")";
        var r = eval(a);
        delete thisObj.__apply__;
        return r;
    }
}


if (!Function.prototype.call){
    Function.prototype.call = function(thisObj){
        var args = []; //copy all arguments but the first
        for (var i = 1; i < arguments.length; i++){
            args[i-1] = arguments[i];
        }
        return this.apply(thisObj, args);
    }
} 
//////////////////////////////////////////////////////////////////////////////////////////////////
/* ***************************
** Most of this code was kindly 
** provided to me by
** Andrew Clover (and at doxdesk dot com)
** http://and.doxdesk.com/ 
** in response to my plea in my blog at 
** http://worldtimzone.com/blog/date/2002/09/24
** It was unclear whether he created it.
*/
function utf8(wide) {
  var c, s;
  var enc = "";
  var i = 0;
  wide = ""+wide;
  while(i<wide.length) {
    c= wide.charCodeAt(i++);
    // handle UTF-16 surrogates
    if (c>=0xDC00 && c<0xE000) continue;
    if (c>=0xD800 && c<0xDC00) {
      if (i>=wide.length) continue;
      s= wide.charCodeAt(i++);
      if (s<0xDC00 || c>=0xDE00) continue;
      c= ((c-0xD800)<<10)+(s-0xDC00)+0x10000;
    }
    // output value
    if (c<0x80) enc += String.fromCharCode(c);
    else if (c<0x800) enc += String.fromCharCode(0xC0+(c>>6),0x80+(c&0x3F));
    else if (c<0x10000) enc += String.fromCharCode(0xE0+(c>>12),0x80+(c>>6&0x3F),0x80+(c&0x3F));
    else enc += String.fromCharCode(0xF0+(c>>18),0x80+(c>>12&0x3F),0x80+(c>>6&0x3F),0x80+(c&0x3F));
  }
  return enc;
}

var hexchars = "0123456789ABCDEF";

function toHex(n){
  return hexchars.charAt(n>>4) + hexchars.charAt(n & 0xF);
}

var okURIchars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-";

if (typeof encodeURIComponent != "function")
	var encodeURIComponent = function(s){
	s = utf8(s);
	var c;
	var enc = "";
	for (var i = 0; i < s.length; i++) {
		if (okURIchars.indexOf(s.charAt(i)) == -1)
			enc += "%" + toHex(s.charCodeAt(i));
		else
			enc += s.charAt(i);
	}
	return enc;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////
String.prototype.isEmpty = function(){
	if (this.replace(/[\r|\n]/g,"").search(/\S+/) < 0)
		return true;
	else
		return false;
}

bhv.addEventListener = function(element, strEvent, callback){
      if (element.addEventListener) 
        element.addEventListener(strEvent, callback, false);
      else
        element.attachEvent("on" + strEvent, callback);
}

bhv.IE5 = true;

bhv.util.getXMLHTTPRequest = bhv.util.getXMLHttpRequest = function(){
  var xmlReq;
  if (window.XMLHttpRequest){
    xmlReq = new window.XMLHttpRequest();
	bhv.IE5 = false;
  }else
    try {
		xmlReq = new ActiveXObject("Msxml2.XMLHTTP");
		bhv.IE5 = false;
    }catch(e) {
		try {
			xmlReq = new ActiveXObject("Microsoft.XMLHTTP");
			bhv.IE5 = false;
		}catch (e) {
        xmlReq = null;
      }
    }
  return xmlReq;
}

bhv.util.nullFunction = bhv.util.emptyFunction = function(){};

bhv.util.defaultError = function(){
  if (typeof this.responseText != "undefined")
    alert("������:\n" + this.responseText);
  else
    alert("������: XMLHttpRequest");
}

bhv.util.registreCallbackFunction = function(xmlHttpRequest, callback, onerror, callbackArgsArray){
    return function(){
        if(xmlHttpRequest.readyState == 4){
			bhv.scriptConteiner = {};
            if(! xmlHttpRequest.status || xmlHttpRequest.status >= 200 && xmlHttpRequest.status < 300 
                   || xmlHttpRequest.status == 304 /*|| xmlHttpRequest.status == 404*/)
                callback.apply(xmlHttpRequest, callbackArgsArray);
            else
                if (typeof onerror == "function")
                    onerror.apply(xmlHttpRequest, callbackArgsArray);
                else
                    throw new Error("������ �������� XMLHttpRequest")
			bhv.scriptConteiner = {};
            xmlHttpRequest.onreadystatechange = bhv.util.nullFunction;        
        };
    }
}

if (bhv.IE4)////////////////////////////////////////////////////////////////////////////
bhv.sendScriptRequest = function(url, httpParams, callback, callbackArgsArray, onerror){
  bhv.scriptConteiner = {};
  var scriptID = "scriptID" + Math.round(Math.random()*1000000)
  if (httpParams)
    httpParams = "?rand=" + encodeURIComponent(Math.round(Math.random()*10000000000000,0)) + "&" + httpParams;
  else
    httpParams = "?rand=" + encodeURIComponent(Math.round(Math.random()*10000000000000,0));
  document.write("<script id=" + scriptID + " src='"+ url + httpParams +"'></script>")
  document.close();
  var currentScript = window[scriptID];
  currentScript.onload = bhv.util.scriptCallback(currentScript, callback, callbackArgsArray, onerror);
  currentScript.onreadystatechange = bhv.util.scriptCallback(currentScript, callback, callbackArgsArray);
  currentScript.bhv_readyState = false;
}
else////////////////////////////////////////////////////////////////////////////////////
bhv.sendScriptRequest = function(url, httpParams, callback, callbackArgsArray, onerror){
  bhv.scriptConteiner = {};
  var currentScript = document.createElement("script");// bhv.sendScriptRequest.free.pop();
  currentScript.onload = bhv.util.scriptCallback(currentScript, callback, callbackArgsArray, onerror);
  currentScript.onreadystatechange = bhv.util.scriptCallback(currentScript, callback, callbackArgsArray);
  if (httpParams)
    httpParams = "?rand=" + encodeURIComponent(Math.round(Math.random()*10000000000000,0)) + "&" + httpParams;
  else
    httpParams = "?rand=" + encodeURIComponent(Math.round(Math.random()*10000000000000,0));
  currentScript.bhv_readyState = false;
  currentScript.type = "text/javascript";
  currentScript.src = url + httpParams;
  document.getElementsByTagName("head")[0].appendChild(currentScript);
  document.close();
}

if (bhv.IE5 || bhv.IE4)////////////////////////////////////////////////////////////////////////////////////////////////////
bhv.sendRequest = function(httpMethod, url, httpParams, async, callback, onerror, callbackArgsArray, contentType, headers){
	bhv.sendScriptRequest(url, httpParams, callback, callbackArgsArray, onerror);
};
else///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
bhv.sendRequest = function(httpMethod, url, httpParams, async, callback, onerror, callbackArgsArray, contentType, headers){
  if (! onerror)
    onerror = bhv.util.defaultError;
  if (! callbackArgsArray)
    callbackArgsArray = [];
  if (! contentType)
    contentType = "application/x-www-form-urlencoded";
  if (! headers)
    headers = {};

  var xmlHttpRequest = bhv.util.getXMLHttpRequest();

  if (async)
    xmlHttpRequest.onreadystatechange = bhv.util.registreCallbackFunction(xmlHttpRequest, callback, onerror, callbackArgsArray);

  try{
    if (httpMethod.toLowerCase() == "get"){
        if (! httpParams)
            httpParams = "antiCache=" + Math.random();
        else
            httpParams = "antiCache=" + Math.random() + "&"+ httpParams;
        xmlHttpRequest.open("get", url + "?" + httpParams, async);
        xmlHttpRequest.setRequestHeader("Content-Type", contentType);
        for (var header in headers)
            xmlHttpRequest.setRequestHeader(header, headers[header]);
        xmlHttpRequest.send(null);
    }
    else{
        xmlHttpRequest.open("post", url, async);
        xmlHttpRequest.setRequestHeader("Content-Type", contentType);
        for (var header in headers)
            xmlHttpRequest.setRequestHeader(header, headers[header]);
        xmlHttpRequest.send(httpParams);
    }
  }catch(e){
    xmlHttpRequest.onreadystatechange = bhv.util.emptyFunction;
    if (typeof onerror == "function")
        onerror.apply(xmlHttpRequest, callbackArgsArray);
    else
        throw new Error("������ XMLHttpRequest")
  }
    
  if (! async)
    if (! xmlHttpRequest.status || xmlHttpRequest.status >= 200 && xmlHttpRequest.status < 300 || xmlHttpRequest.status == 304){
		bhv.scriptConteiner = {};
        callback.apply(xmlHttpRequest, callbackArgsArray);
		bhv.scriptConteiner = {};
    }else
        if (typeof onerror == "function")
            onerror.apply(xmlHttpRequest, callbackArgsArray);
        else
            throw new Error("������ XMLHttpRequest")
}
//------------------------------------------------------------
bhv.util.scriptCallback = function(currentScript, callback, callbackArgsArray, onerror){
  return function(){
    if (currentScript.bhv_readyState)
        return;
    if (!currentScript.readyState || currentScript.readyState == "loaded" || currentScript.readyState == "complete"){
        currentScript.bhv_readyState = true;
		try{
		  callback.apply(currentScript, callbackArgsArray)
		}catch(e){
			if (onerror) onerror.apply(currentScript, callbackArgsArray);
		}
		bhv.scriptConteiner = {};
        currentScript.parentNode.removeChild(currentScript);
    }
  }
}
//------------------------------------------------------------
bhv.sendIframeRequest = function(url, httpParams, callback, callbackArgsArray, onerror){
  bhv.scriptConteiner = {};
  var currentIframe = document.createElement("iframe");// bhv.sendScriptRequest.free.pop();
  currentIframe.style.display = "none";
  currentIframe.onload = bhv.util.iframeCallback(currentIframe, callback, callbackArgsArray, onerror);
  bhv.addEventListener(currentIframe, 'load', bhv.util.iframeCallback(currentIframe, callback, callbackArgsArray, onerror))
  //currentScript.onreadystatechange = bhv.util.scriptCallback(currentScript, callback, callbackArgsArray);
  if (httpParams)
    httpParams = "?rand=" + encodeURIComponent(Math.round(Math.random()*10000000000000)) + "&" + httpParams;
  else
    httpParams = "?rand=" + encodeURIComponent(Math.round(Math.random()*10000000000000));
  currentIframe.bhv_readyState = false;
  //currentScript.type = "text/javascript";
  currentIframe.src = url + httpParams;
  document.body.appendChild(currentIframe);
  currentIframe.location = url + httpParams;
  //document.close();
}
//------------------------------------------------------------
bhv.util.iframeCallback = function(currentIframe, callback, callbackArgsArray, onerror){
  return function(){
  	var doc = extractIFrameBody(currentIframe);
  	alert(doc.getElementsByTagName('pre')[0].innerHTML);
	eval(doc.getElementsByTagName('pre')[0].innerHTML);
	//eval(doc.firstChild.data);
	//eval(doc.innerText);
    if (currentIframe.bhv_readyState)
        return;
    if (!currentIframe.readyState || currentIframe.readyState == "loaded" || currentIframe.readyState == "complete"){
        currentIframe.bhv_readyState = true;
		try{
		  callback.apply(currentIframe, callbackArgsArray)
		}catch(e){
			if (onerror) onerror.apply(currentIframe, callbackArgsArray);
		}
		bhv.scriptConteiner = {};
        //currentIframe.parentNode.removeChild(currentIframe);
    }
  }
}
////////////////////////////////////////////////////////////////
function extractIFrameBody(iFrameEl) {
  var doc = null;
  if (iFrameEl.contentDocument)// For NS6
    doc = iFrameEl.contentDocument; 
  else if (iFrameEl.contentWindow)// For IE5.5 and IE6
    doc = iFrameEl.contentWindow.document;
  else if (iFrameEl.document)// For IE5
    doc = iFrameEl.document;
  else{
    alert("Error: could not find sumiFrame document");
    return null;
  }
  return doc.body;
}
//------------------------------------------------------------
bhv.getElementData = function(parent, child){
if (! child)
    child = parent;
if (typeof child == "string")
    child = parent.getElementsByTagName(child)[0];
return child.firstChild.data;
// if undefined child - throw new Error()
}
//------------------------------------------------------------
bhv.key={};

bhv.key.BACKSPACE = 8;
bhv.key.TAB = 9;
bhv.key.ENTER = 13;
bhv.key.SHIFT = 16;
bhv.key.CTRL = 17;
bhv.key.ALT = 18;
bhv.key.PAUSE = 19;
bhv.key.CAPSLOOK = 18;
bhv.key.ESC = 27;

bhv.key.SPACE = 32;

bhv.key.PAGEUP	= 33;
bhv.key.PAGEDOWN = 34;
bhv.key.END = 35;
bhv.key.HOME = 36;

bhv.key.LEFT = 37;
bhv.key.UP = 38;
bhv.key.RIGHT = 39;
bhv.key.DOWN = 40;

bhv.key.PRINTSCREEN = 44;
bhv.key.INSERT = 45;
bhv.key.DELETE = 46;

bhv.key.F1 = 112;
bhv.key.F2 = 113;
bhv.key.F3 = 114;
bhv.key.F4 = 115;
bhv.key.F5 = 116;
bhv.key.F6 = 117;
bhv.key.F7 = 118;
bhv.key.F8 = 119;
bhv.key.F9 = 120;
bhv.key.F10 = 121;
bhv.key.F11 = 122;
bhv.key.F12 = 123;
//--------------------------------------------------------------------
bhv.isVisible = function(elem){
if (typeof elem == "string")
    elem = document.getElementByID(elem);
if (typeof elem != "object")
    return false;
if (elem.type == "hidden")
    return false;
var isNone = false
var isVisible = false
var isHidden = false
do{
    if (elem.style){
        isNone = elem.style.display == "none";
        if (! isHidden)
            isHidden = elem.style.visibility == "hidden";
        if (! isHidden && ! isVisible)
            isVisible = elem.style.visibility == "visible";
    }
    elem = elem.parentNode;
} while (! isNone && elem);
return ! isNone && (! isHidden || isVisible);
}
//--------------------------------------------------------------------
bhv.selectPreviousInput = function(elem){
if (elem)
	elem.blur();
else
	return;
var allInput = document.getElementsByTagName("input");
var isNext = false;
if (allInput && allInput.length > 0)
	for (var i = allInput.length - 1; i >= 0; i--)
	try {
		if (isNext && bhv.isVisible(allInput[i]) && !allInput[i].disabled)
		{
			allInput[i].focus();
			return true;
		}			
		if (! isNext && allInput[i] == elem)
			isNext = true;
	}catch (ex){}

elem.focus();
}
//--------------------------------------------------------------------
bhv.selectNextInput = function(elem){

if (elem)
	elem//.blur();
else
	return true;
var allInput = document.getElementsByTagName("input");
var isNext = false;
if (allInput && allInput.length > 0)
	for (var i = 0 ; i < allInput.length; i++)
	try {
		if (isNext && bhv.isVisible(allInput[i]) && !allInput[i].disabled){
			allInput[i].focus();
			return true;
		}
		if (! isNext && allInput[i] == elem)
			isNext = true;
	}catch (ex){}
elem.focus();
return true;
}
//---------------------------------------------------------------------------------
bhv.commandQueue={}
bhv.commandId = 0;
//----------------------------------------------------------------
bhv.callCommand=function(name, id){
    if (bhv.commandQueue[name] && bhv.commandQueue[name][id])
       var currentCommand = bhv.commandQueue[name][id];
    else
        return;
    delete bhv.commandQueue[name][id];
    currentCommand.command.apply(currentCommand.context, currentCommand.args);
    delete currentCommand.command;
    delete currentCommand.context;
    delete currentCommand.args;   
}
//------------------------------------------------------------------
bhv.setCommand=function(command, context, args, timeout, name){
var id = "id"+ (++bhv.commandId%1000);
if (! timeout && (timeout !== 0))
    timeout = 1000;
if (! name)
    name = "default";
else if (bhv.commandQueue[name])
    delete bhv.commandQueue[name];    
if (! bhv.commandQueue[name])
    bhv.commandQueue[name] = {};
bhv.commandQueue[name][id] = {};
bhv.commandQueue[name][id]["command"] = command;
bhv.commandQueue[name][id]["context"] = context;
bhv.commandQueue[name][id]["args"] = args;
setTimeout("bhv.callCommand('" + name+ "', '" + id + "')", timeout);
}
//------------------------------------------------------------------
bhv.unsetCommand=function(name){
bhv.commandQueue[name] = null;    
delete bhv.commandQueue[name];    
}
//---------------------------------------------------------------------------------
bhv.compareString = function(string0, string1){
if (typeof string0 != "string")
    return -1;
if (typeof string1 != "string")
    return -1;
string0 = string0.toUpperCase();
string1 = string1.toUpperCase();
var length = Math.max(string0.length, string1.length);
for (var i = 1; i <= length; i++)
    if (string0.substr(0,i) != string1.substr(0,i))
        return i - 1;
return length;
}

/*bhv.APPLICATION_FOLDER = null;

bhv.getApplicationFolder = function(){
  if (bhv.APPLICATION_FODER)
    return bhv.APPLICATION_FOLDER;

  var scripts = document.getElementsByTagName("SCRIPT");
  var indexOfRoot = -1;
  for (var i = 0; i < scripts.length; i++) {
    indexOfRoot = String(scripts[i].src).replace(/\\/g,'/').lastIndexOf('bhv/util.js');
    if (indexOfRoot >= 0){
        bhv.APPLICATION_FOLDER = new String(scripts[i].src).substring(0, indexOfRoot)
        return bhv.APPLICATION_FOLDER;
    }
  }
}*/


bhv.getAbsolutePath = function(path, relative) {

  path = path.replace(/\\/g, "/");
  if (path.substring(0, 1) == "/")
    return path;

  var current = document.location.pathname;
  current = current.replace(/\\/g, "/");
  current = current.substring(0, current.lastIndexOf("/") + 1);

  if (relative) {
    relative = relative.replace(/\\/g, "/");
    relative = relative.substring(0, relative.lastIndexOf("/") + 1);
    if (relative.substring(0, 1) == "/")
      current = relative;
    else 
      current = current + relative;
    }

  return current + path;
}

bhv.relocateSRC = function(htmlText, relative) {
  var newText = htmlText.replace(/(<[^>]*\s(src|href)\s*=\s*(\"|\'))(.*)(\3[^>]*>)/gi,"$1"+bhv.getAbsolutePath("$4",relative)+"$5");
  newText = newText.replace(/(<[^>]*\s(src|href)\s*=\s*)([^\s\"\'>]+)([^>]*>)/gi,"$1"+bhv.getAbsolutePath("$3",relative)+"$4");
  return newText;
}

bhv.top = function(element){
  var top = 0;
  try{
    top = element.offsetTop;
    while(element.offsetParent){
      element = element.offsetParent;
      top += element.offsetTop
    }
  } catch (ex){}

return top;
}

bhv.left = function(element){
  var left = 0;
  try{
    left = element.offsetLeft;
    while(element.offsetParent){
      element = element.offsetParent;
      left += element.offsetLeft
    }
  } catch (ex){}

return left;
}




document.write('<div id="bhv_contentPane" style="position:absolute;top:0;left:0;margin:0;padding:0;border:0;z-index:0"><span style="display:none"><br></span></div>');
bhv.contentPane = function(){ return document.getElementById("bhv_contentPane");}

