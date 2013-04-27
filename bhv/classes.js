if (! bhv)
  var bhv = {};
  
bhv.classes = {};

bhv.isa = function(toObject, fromObject) {
  for (var p in fromObject)
  if (typeof toObject[p] == "undefined")
    toObject[p] = fromObject[p];
}

bhv.ISA = function(toObject, fromObject) {
  for (var p in fromObject)
    toObject[p] = fromObject[p];
}


bhv.classes["package"] = function(sNamespace) {
  var aNamespace = sNamespace.split(".");
  var currentNamespace = "";
  var oNamespace = null;
  for (var i = 0; i < aNamespace.length - 1; i++) {
    if (i == 0)
      currentNamespace += aNamespace[i];
    else
      currentNamespace += "."+aNamespace[i];
        if (eval("typeof("+currentNamespace + ")") == "undefined")
            oNamespace = eval(currentNamespace + "= new Object()");
        else
            oNamespace = eval(currentNamespace);
  }
  return oNamespace;
}

bhv.namespace = function(sNamespace) {
  var aNamespace = sNamespace.split(".");
  var currentNamespace = "";
  var oNamespace = null;
  for (var i = 0; i < aNamespace.length; i++) {
    if (i == 0)
      currentNamespace += aNamespace[i];
    else
      currentNamespace += "."+aNamespace[i];
        if (eval("typeof("+currentNamespace + ")") == "undefined")
            oNamespace = eval(currentNamespace + "= new Object()");
        else
            oNamespace = eval(currentNamespace);
  }
  
  return oNamespace;
}


bhv.classes.Class = function(){}
bhv.classes.Class.nativePrototype = bhv.classes.Class.prototype;

bhv.classes.newInstance = function(classConstructor, className) {
  bhv.classes.Class.prototype = classConstructor.prototype;
  var objRef = new bhv.classes.Class();
  objRef.parentList = {};
  objRef.superClass = {};
  objRef.sup={}
  objRef.parentList[""+className] = true;
  bhv.classes.Class.prototype = bhv.classes.Class.nativePrototype;
  return objRef;
};


bhv.create = function(className) {
  var args = [];
  for (var i = 1; i < arguments.length; i++)
	args[i-1] = arguments[i];
  if (typeof className == "string") {
	bhv.load(className);
	var classConstructor=eval(className);
	classConstructor.prototype.className = className;
  } else
  var classConstructor = className;
  classConstructor.prototype.derive = bhv.classes.derive;

  var objRef;
  classConstructor.apply(objRef = bhv.classes.newInstance(classConstructor, className), args);
  if(objRef.init)
	objRef.init.apply(objRef, args);
  return objRef;
};

bhv.classes.derive=function(className){
  if (this.parentList[""+className])
    return this;
  this.parentList[""+className] = true;
  var args = [];
  for (var i = 1; i < arguments.length; i++)
    args[i-1] = arguments[i];
  if (typeof className == "string") {
    bhv.load(className)
    var classConstructor = eval(className)
    classConstructor.prototype.className = className;
  } else
	var classConstructor = className;
  bhv.isa(this.constructor.prototype, classConstructor.prototype)
  bhv.isa(this.superClass, classConstructor.prototype)
  classConstructor.call(this, args)
  if(classConstructor.prototype.init)
    classConstructor.prototype.init.apply(this, args);
  return this;
};


bhv.classes.loadedClasses = {};

bhv.classes.loader = function (strNameSpace){
    var mod = bhv.classes["package"](strNameSpace);
    eval(this.responseText);
    if ( eval( "typeof " + strNameSpace) != "function") {
      eval(strNameSpace + " = " + strNameSpace.match(/[^\.]*$/));
    }
    bhv.classes.loadedClasses[strNameSpace] = eval(strNameSpace);
};

bhv.load = function(strNameSpace) {
  if (bhv.classes.loadedClasses[strNameSpace])
    return bhv.classes.loadedClasses[strNameSpace];
  try {
    bhv.sendRequest(
        "GET", 
        bhv.getApplicationFolder() + strNameSpace.replace(/\./g,"/") + ".js",
        null, 
        false,
        bhv.classes.loader,
        null,
        [strNameSpace]
)
 /*   bhv.sendScriptRequest(
        bhv.getApplicationFolder() + strNameSpace.replace(/\./g,"/") + ".js",
        null, 
        bhv.classes.loader,
        [strNameSpace]
)*/

;
    }catch (ex) {alert(ex.message)}
    return bhv.namespace(strNameSpace);
};
