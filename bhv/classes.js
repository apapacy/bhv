define(["bhv/util"], function(bhv){
alert(bhv)
////////////////////////////////////////////
bhv.classes = {};

bhv.isa = function (toObject, fromObject) {
	for (var p in fromObject)
		if (typeof toObject[p] === "undefined" /*&& p !== "init"*/)
			toObject[p] = fromObject[p];
}

bhv.ISA = function (toObject, fromObject) {
	for (var p in fromObject)
		toObject[p] = fromObject[p];
}

bhv.classes["package"] = function (sNamespace) {
	var aNamespace = sNamespace.split(".");
	var currentNamespace = "";
	var oNamespace = null;
	for (var i = 0; i < aNamespace.length - 1; i++) {
		if (i === 0)
			currentNamespace += aNamespace[i];
		else
			currentNamespace += "." + aNamespace[i];
		if (eval("typeof(" + currentNamespace + ")") === "undefined")
			oNamespace = eval(currentNamespace + "= new Object()");
		else
			oNamespace = eval(currentNamespace);
	}
	return oNamespace;
}

bhv.namespace = function (sNamespace) {
	var oNamespace = null;
	try {
		if (eval("typeof(" + sNamespace + ")") !== "undefined")
			return oNamespace = eval("sNamespace");
	} catch (ex) {}
	var aNamespace = sNamespace.split(".");
	var currentNamespace = "";
	for (var i = 0; i < aNamespace.length; i++) {
		if (i == 0)
			currentNamespace += aNamespace[i];
		else
			currentNamespace += "." + aNamespace[i];
		if (eval("typeof(" + currentNamespace + ")") === "undefined")
			oNamespace = eval(currentNamespace + "= {}");
		else
			oNamespace = eval(currentNamespace);
	}

	return oNamespace;
}


bhv.classes.Class = function () {};
bhv.classes.Class.nativePrototype = bhv.classes.Class.prototype;

bhv.classes.newInstance = function (classConstructor, className) {
	bhv.classes.Class.prototype = classConstructor.prototype;
	var objRef = new bhv.classes.Class();
	objRef.parentList = {};
	objRef.superClass = {};
	objRef.sup = {}
	objRef.parentList["" + className] = true;
	bhv.classes.Class.prototype = bhv.classes.Class.nativePrototype;
	return objRef;
};


bhv.create = function (className) {
	var args = [];
	for (var i = 1; i < arguments.length; i++)
		args[i - 1] = arguments[i];
	if (typeof className === "string") {
		bhv.load(className);
		var classConstructor = eval(className);
		classConstructor.prototype.className = className;
	} else {
		var classConstructor = className;
	}
	classConstructor.prototype.derive = bhv.classes.derive;

	var objRef;
	classConstructor.apply(objRef = bhv.classes.newInstance(classConstructor, className), args);
//	if (typeof objRef.init === 'function')
//		objRef.init.apply(objRef, args);
	return objRef;
};

bhv.classes.derive = function (className, construct, args) {
	if (this.parentList["" + className])
		return this;
	this.parentList["" + className] = true;
	if (!args)
		for (var i = 1; i < arguments.length; i++)
			args[i - 1] = arguments[i];
	if (typeof className === "string") {
		bhv.load(className)
		var classConstructor = eval(className)
		classConstructor.prototype.className = className;
	} else {
		var classConstructor = className;
	}
	bhv.isa(this.constructor.prototype, classConstructor.prototype)
	bhv.isa(this.superClass, classConstructor.prototype)
	if (construct) {
		classConstructor.apply(this, args)
//		if (typeof classConstructor.prototype.init == 'function')
//			classConstructor.prototype.init.apply(this, args);
	}
	return this;
};


bhv.classes.loadedClasses = {};


bhv.load = function (strNameSpace) {
	if (bhv.classes.loadedClasses[strNameSpace])
		return bhv.classes.loadedClasses[strNameSpace];
	var mod = bhv.classes["package"](strNameSpace);

	$.ajax(bhv.getApplicationFolder() + strNameSpace.replace(/\./g, "/") + ".js", {
		async: false,
		dataType: 'script'
	});
	if (typeof Constructor === "function")
		eval(strNameSpace + " = Constructor");
	else if (eval("typeof " + strNameSpace) != "function")
		eval(strNameSpace + " = " + strNameSpace.match(/[^\.]*$/));
	bhv.classes.loadedClasses[strNameSpace] = eval(strNameSpace);

	return bhv.classes.loadedClasses[strNameSpace];
};
/////////////////////////////////////
return bhv;
});
