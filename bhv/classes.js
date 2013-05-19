define([], function(bhv){
////////////////////////////////////////////
var classes = {};

classes.secret = 'dkO30fjvkJwe)alsKjdpeO,ri39rG&*dkFkgms=dfeoiru';

classes.isa = function (toObject, fromObject) {
	for (var p in fromObject)
		if (typeof toObject[p] === "undefined")
			toObject[p] = fromObject[p];
}

classes.ISA = function (toObject, fromObject) {
	for (var p in fromObject)
		toObject[p] = fromObject[p];
}

classes.IN = function(objRef, arrayRef){
	for (var i = 0; i < arrayRef.length; i++)
		if (objRef === arrayRef[i])
			return true;
	return false;
}

classes.Class = function () {};

classes.Class.nativePrototype = classes.Class.prototype;

classes.newInstance = function (classConstructor) {
	classes.Class.prototype = classConstructor.prototype;
	var objRef = new classes.Class();
	objRef[classes.secret] = [classConstructor];
	classes.Class.prototype = classes.Class.nativePrototype;
	return objRef;
};


classes.create = function (classConstructor) {
	var args = [];
	for (var i = 1; i < arguments.length; i++)
		args[i - 1] = arguments[i];
	if (! classConstructor.prototype[classes.secret]) {
		classConstructor.prototype.derive = classes.derive;
		classConstructor.prototype.superClass = {};
	}
	var objRef = classes.newInstance(classConstructor);
	classConstructor.apply(objRef, args);
	classConstructor.prototype[classes.secret] = true;
	return objRef;
};

classes.derive = function (classConstructor, construct) {
	if (classes.IN(classConstructor, this[classes.secret]))
		return this;
	var args = [];
	this[classes.secret].push(classConstructor);
	for (var i = 2; i < arguments.length; i++)
		args[i - 2] = arguments[i];
	if (! this.constructor.prototype[classes.secret]) {
		classes.isa(this.constructor.prototype, classConstructor.prototype);
		classes.isa(this.constructor.prototype.superClass, classConstructor.prototype);
	}
	if (construct)
		classConstructor.apply(this, args);
	return this;
};
/////////////////////////////////////
return classes;
});
