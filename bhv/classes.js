define([], function(){
////////////////////////////////////////////
var classes = {};

classes.GUID = '14880588-38C7-4A84-82C3-BC76C167B5A4';

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

classes.EmptyClass = function () {};

classes.EmptyClass.nativePrototype = classes.EmptyClass.prototype;

classes.newInstance = function (classConstructor) {
	classes.EmptyClass.prototype = classConstructor.prototype;
	var objRef = new classes.EmptyClass();
	//objRef[classes.GUID] = [classConstructor];
	classes.EmptyClass.prototype = classes.EmptyClass.nativePrototype;
	return objRef;
};


classes.create = function (classConstructor) {
	var args = [];
	for (var i = 1; i < arguments.length; i++)
		args[i - 1] = arguments[i];
	if (! classConstructor[classes.GUID]) {
		classConstructor.prototype.derive = classes.derive;
		classConstructor.prototype.superClass = {};
	  classConstructor.prototype[classes.GUID] = [classConstructor];
	}
	var objRef = classes.newInstance(classConstructor);
	classConstructor.apply(objRef, args);
	classConstructor[classes.GUID] = true;
	return objRef;
};

classes.derive = function (classConstructor, construct) {
	if (!this.constructor[classes.GUID] && !classes.IN(classConstructor, this.constructor.prototype[classes.GUID])){
		classes.isa(this.constructor.prototype, classConstructor.prototype);
    if (this.constructor.prototype[classes.GUID].length ===1)
      classes.ISA(this.constructor.prototype.superClass, classConstructor.prototype);
    else
      classes.isa(this.constructor.prototype.superClass, classConstructor.prototype);
    this.constructor.prototype[classes.GUID].push(classConstructor);
	}
	if (construct) {
    var args = [];
    for (var i = 2; i < arguments.length; i++)
      args[i - 2] = arguments[i];
		classConstructor.apply(this, args);
  }
	return this;
};

classes.instanceOf = function(objRef, classConstructor) {
	return classes.IN(classConstructor, objRef.constructor.prototype[classes.GUID] );
}
/////////////////////////////////////
return classes;
});
