/**
 * @overview Autocompite commbobox based on Backbone library
 * @module combobox/Combobox.js
 * @licence MIT
 * @copyright Andrey Ovcharenko <An6rey@google.com>
 * @exports Constructor
 */
define( [ /* require Requirejs, jQuery, Backbone( =>Underscore, JSON2 ) */ 'domReady!' ], function( ) {

/** default setting if not redefined */
var defaults = {
  limit: 10,
  page: 0,
  searchValue: '',
  keyName: 'kod', /** SQL column name */
  searchName: 'search', /** SQL column name may be == keyName */
  displayName: 'name' /** SQL column name may be == searchName */
};

var util = new utils( );

var Item = Backbone.Model.extend( {
  idAttribute: 'backbone:combobox:item:id'
} );

var Constructor = Backbone.Collection.extend( {
  model: Item
} );

var Input = Backbone.Model.extend( {
  idAttribute: 'backbone:combobox:input:id',
  keyValue: '',
  searchValue: '',
  displayValue: ''
} ); 


/** member of Constructor */
var fn = {
  initialize: function( settings ) {
    _.extend( this, defaults);
    _.extend( this, settings);
    this.input = new Input( );
    if ( typeof this.init === 'function' ) {
      this.init.apply( this, arguments );
    }
  },
  
  read: function( ) {
    this.fetch( {
      searchValue: this.input.serachValue,
      page: this.page,
      limit: this.limit,
      success: function(m,r,o) {alert(JSON.stringify(r))}
    } );
  }

};

/** attach member to this object (via Constructor.prototype) */
util.ISA( Constructor.prototype, fn );












//* use with Requirejs define( ['combobox/Combobox'], function (cmb) {new cmb({});...} ) */
return Constructor;

/*
 *
 *
 *
 */

 /*
  * @constructor utils
  * @overview provide less functionality 
  */
function utils( ) {
  
  /*
   * @overview copy missing parameters from to Object
   * @param {object} toObject - target object (changed)
   * @param {object} fromObject - prototype object (readonly)
   */
  this.isa = function ( toObject, fromObject ) {
    for ( var p in fromObject ) {
      if ( typeof toObject[p] === "undefined" ) {
        toObject[p] = fromObject[p];
      }
    }
  }
  
  /*
   * @overview copy missing parameters from to Object
   * @param {object} toObject - target object (changed)
   * @param {object} fromObject - prototype object (readonly)
   */
  this.ISA = function ( toObject, fromObject ) {
    for ( var p in fromObject ) {
        toObject[p] = fromObject[p];
      }
    }
  }

  /*
   * @overview create new Array object from parameter (main usage with
   * arguments function inner object)
   * @param {object} obj - prototype object (readonly)
   * @returns new Array
   */
  this.toArray = function( obj ) {
    var arr = [ ];
    for ( var i = 0; i < obj.length; i++ ) {
      arr[i] = obj[i];
    }
    return arr;
  }

  /*
   * @overview search value in Array object
   * @param val - searched value (readonly)
   * @param arrayRef {array} - searched value (readonly)
   * @returns {Boolean}
   */
  this.inArray = function( val, arrayRef ) {
  // requires typeof arrayRef === 'object(array)'
    for ( var i = 0; i < arrayRef.length; i++ ) {
      if ( val === arrayRef[i] ) {
        return true;
      }
    }
    return false;
  }

// end of wrapper function for Requirejs
});