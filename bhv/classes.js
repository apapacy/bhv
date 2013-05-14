define(["bhv/util"], function(bhv){
////////////////////////////////////////////
bhv.classes = {};

bhv.classes.secret = 'dkO30fjvkJwe)alskjdpeori39rg&*dkfkgmsdfeoiru';

bhv.isa = function (toObject, fromObject) {
	for (var p in fromObject)
		if (typeof toObject[p] === "undefined" /*&& p !== "init"*/)
			toObject[p] = fromObject[p];
}

bhv.ISA = function (toObject, fromObject) {
	for (var p in fromObject)
		toObject[p] = fromObject[p];
}

bhv.IN = function(objRef, arrayRef){
	for (var i = 0; i < arrayRef.length; i++)
		if (objRef === arrayRef[i])
			return true;
	return false;
}

bhv.classes.Class = function () {};

bhv.classes.Class.nativePrototype = bhv.classes.Class.prototype;

bhv.classes.newInstance = function (classConstructor) {
	bhv.classes.Class.prototype = classConstructor.prototype;
	var objRef = new bhv.classes.Class();
	objRef.parentList = [];
	objRef.parentList.push(classConstructor);
	bhv.classes.Class.prototype = bhv.classes.Class.nativePrototype;
	return objRef;
};


bhv.create = function (classConstructor) {
	var args = [];
	for (var i = 1; i < arguments.length; i++)
		args[i - 1] = arguments[i];
	if (! classConstructor.prototype[bhv.classes.secret]) {
		classConstructor.prototype.derive = bhv.classes.derive;
		classConstructor.prototype.superClass = {};
	}
	var objRef = bhv.classes.newInstance(classConstructor);
	classConstructor.apply(objRef, args);
	classConstructor.prototype[bhv.classes.secret] = true;
	return objRef;
};

bhv.classes.derive = function (classConstructor, construct) {
	var args = [];
	if (bhv.IN(classConstructor, this.parentList))
		return this;
	this.parentList.push(classConstructor);
	for (var i = 2; i < arguments.length; i++)
		args[i - 2] = arguments[i];
	if (! this.constructor.prototype[bhv.classes.secret]) {
		bhv.isa(this.constructor.prototype, classConstructor.prototype);
		bhv.isa(this.constructor.prototype.superClass, classConstructor.prototype);
	}
	if (construct)
		classConstructor.apply(this, args);
	return this;
};


bhv.classes.loadedClasses = {};


/////////////////////////////////////
return bhv;
});
