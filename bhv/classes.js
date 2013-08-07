define([], function(){
////////////////////////////////////////////
var classes = {};

classes.GUID = '14880588-38C7-4A84-82C3-BC76C167B5A4';

classes.Array = function( obj ) {
  var arr = [];
  for ( var i = 0; i < obj.length; i++ )
    arr[i] = obj[i];
  return arr;
}

classes.newClass = function() {
    var klass = new Function( 'if ( typeof this.init === "function" )\n this.init.apply(this,arguments);' );
    klass.extend = classes.extend;
    klass.include = classes.include;
    return klass;
};

classes.extend = function( obj ) {
  for ( var p in obj )
    this[p] = obj[p];
  if ( typeof obj.extended === 'function' )
    obj.extended.call( this, this );
  return this;
}

classes.include = function( obj ) {
  for ( var p in obj )
    this.prototype[p] = obj[p];
  if ( typeof obj.included === 'function' )
    obj.included.call( this, this );
  return this;
}

classes.isa = function ( toObject, fromObject ) {
  for ( var p in fromObject )
    if ( typeof toObject[p] === "undefined" )
      toObject[p] = fromObject[p];
}

//classes.ISA = function ( toObject, fromObject ) {
//  for (var p in fromObject)
//    toObject[p] = fromObject[p];
//}

classes.IN = function( objRef, arrayRef ) {
  for ( var i = 0; i < arrayRef.length; i++ )
    if ( objRef === arrayRef[i] )
      return true;
  return false;
}

classes.EmptyClass = function () {};

classes.EmptyClass.nativePrototype = classes.EmptyClass.prototype;

classes.newInstance = function ( classConstructor ) {
  classes.EmptyClass.prototype = classConstructor.prototype;
  var objRef = new classes.EmptyClass();
  //objRef[classes.GUID] = [classConstructor];
  classes.EmptyClass.prototype = classes.EmptyClass.nativePrototype;
  return objRef;
};

classes.createnew = function ( classConstructor ) {
  if ( !classConstructor[classes.GUID] ) {
    classConstructor.prototype.construct = classes.construct;
    classConstructor.prototype.superClass = {};
    classConstructor[classes.GUID] = true;
    classConstructor.prototype[classes.GUID] = [];
    classes.ISA.call( classConstructor, classConstructor );
  }
  var objRef = classes.newInstance( classConstructor );
  objRef[classes.GUID] = [classConstructor];
  classConstructor.apply( objRef, classes.Array( arguments ).slice( 1 ) );
  delete objRef[classes.GUID];
  return objRef;
};

classes.ISA = function( classConstructor ) {
  if ( typeof classConstructor.ISA === "object"  && typeof classConstructor.ISA.length === "number" ) {
    for ( var i = 0; i < classConstructor.ISA.length; i++ ) {
      if ( !classes.IN( classConstructor.ISA[i], this.prototype[classes.GUID] ) ) {
        classes.isa( this.prototype, classConstructor.ISA[i].prototype );
        classes.isa( this.prototype.superClass, classConstructor.ISA[i].prototype );
        this.prototype[classes.GUID].push( classConstructor.ISA[i] );
        classes.ISA.call( this, classConstructor.ISA[i] )
      }
    }
  }
}   

classes.construct = function ( classConstructor ) {
  if ( !classes.IN( classConstructor, this[classes.GUID] ) ) {
    this[classes.GUID].push( classConstructor );
    classConstructor.apply( this, classes.Array( arguments ).slice( 1 ) );
  }
  return this;
};

classes.instanceOf = function( objRef, classConstructor ) {
  return classes.IN( classConstructor, objRef.constructor.prototype[classes.GUID] );
}
/////////////////////////////////////
return classes;
});
