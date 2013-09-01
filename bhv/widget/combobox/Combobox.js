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
  idAttribute: 'backbone:combobox:item:id',
  limit: 10,
  page: 0,
  searchValue: '',
  keyName: 'kod', /** SQL column name */
  searchName: 'search', /** SQL column name may be == keyName */
  displayName: 'name', /** SQL column name may be == searchName */
  delay: 5000 /** delay request to server, ms */
};

var util = new utils( );

var Item = Backbone.Model.extend ( {
  /**
   * @property {integer} idAttribute - contain [0..limit-1] number,
   * bat not real SQL primary key. It is model of widget, not business logic 
   */
  //defaults: defaults,
  idAttribute: defaults.idAttribute
} );

var Items = Backbone.Collection.extend( {

  model: Item,
    
  initialize: function( settings ) {
    _.extend( this, defaults );
    _.extend( this, settings );
    /** user defined initialization of instance */
    if ( typeof this.init === 'function' ) {
      this.init.apply( this, arguments );
    }
  },

  read: function( ) {
    this.fetch( 
      {
        data: 
          {
            searchValue: this.input.get( 'searchValue' ),
            page: this.page,
            limit: this.limit
          },
        success: function(m,r,o) {m.at(0).set('wwwwwwwwwwwwwwwwwwwwww', "5555555555555555")}
      } 
    );
  }
    
} );

var Input = Backbone.Model.extend ( {
  keyValue: '',
  searchValue: '',
  displayValue: '',
  active: false,
  defaults:defaults
} );

/**
 * @constructor {Backbone.Collection}
 * @overview contains model Item and some other model
 * @todo or better Constructor( ) { this.items = new Items( ); } ?
 */
  
function Constructor( settings ) {
  _.extend( this, defaults );
  _.extend( this, settings );
  this.items = new Items( );
  this.items.url = settings.url;
  this.input = new Input( settings );
  this.input.on('change:searchValue', this.read, this);
  this.inputView = new InputView( settings );
  this.inputView.model = this.input;
  this.inputView.$el.appendTo( document.body );
  this.itemsView = new ItemsView( settings );
  this.itemsView.$el.appendTo( document.body );
  for ( var i = 0; i < this.limit; i++ ) {
    var item = new Item( { 'backbone:combobox:item:id': String(i) } );
    //item.on('change',function(){})
    this.items.add( item );
    var itemView = new ItemView( _.extend( { model: item }, settings) );
    itemView.$el.appendTo( this.itemsView.$el );
  }  
}

_.extend( Constructor.prototype, {
  
  read: function( ) {
    this.items.fetch( {
      data:{
        searchValue: this.input.get( 'searchValue' ),
        page: this.page,
        limit: this.limit
      },
      success: function(m,r,o) {alert(JSON.stringify(r))}
    } );
  }
} );


var InputView = Backbone.View.extend( {
  defaults: defaults,
    
  tagName: 'input type="text"',
    
  handleTimeout: null,
    
  initialize: function( settings ) {
    _.extend( this, defaults);
    _.extend( this, settings);
    this.setSearchValue = _.bind(
      function( ) {
        this.model.set( 'searchValue', this.$el.val( ) );
      },
      this
    );
    if ( typeof this.init === 'function' ) {
     this.init.apply( this, arguments );
   }
  },
    
  events: {
    'click': 'onclick',
    'keyup': 'onkeyup',
  },

  onclick: function( ) {
    if ( ! this.model.get('active') ) {
      this.model.set( 'active', true );
      this.$el.val( this.model.get( 'searchValue' ) );
      this.$el.select();
    }
  },
  
  onkeyup: function( ) {
    if ( this.handleTimeout !== null ) {
      window.clearTimeout( this.handleTimeout );
    }
    this.handleTimeout = window.setTimeout( this.setSearchValue, this.delay );
  }
} );

var ItemsView = Backbone.View.extend( {
  tagName: 'div',
    
  initialize: function( settings ) {
    _.extend( this, defaults);
    _.extend( this, settings);
  }
    
} );

var ItemView = Backbone.View.extend( {

  tagName: 'div',
    
  initialize: function( settings ) {
    _.extend( this, defaults);
    _.extend( this, settings);
    this.listenTo( this.model, 'change', this.render );
  },

  render: function( ) {
    this.$el.text( this.model.get( this.displayName ) );
  }

} );


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
  
  this.key = {};

  this.key.BACKSPACE = 8;
  this.key.TAB = 9;
  this.key.ENTER = 13;
  this.key.SHIFT = 16;
  this.key.CTRL = 17;
  this.key.ALT = 18;
  this.key.PAUSE = 19;
  this.key.CAPSLOOK = 18;
  this.key.ESC = 27;

  this.key.SPACE = 32;

  this.key.PAGEUP = 33;
  this.key.PAGEDOWN = 34;
  this.key.END = 35;
  this.key.HOME = 36;

  this.key.LEFT = 37;
  this.key.UP = 38;
  this.key.RIGHT = 39;
  this.key.DOWN = 40;

  this.key.PRINTSCREEN = 44;
  this.key.INSERT = 45;
  this.key.DELETE = 46;

  this.key.F1 = 112;
  this.key.F2 = 113;
  this.key.F3 = 114;
  this.key.F4 = 115;
  this.key.F5 = 116;
  this.key.F6 = 117;
  this.key.F7 = 118;
  this.key.F8 = 119;
  this.key.F9 = 120;
  this.key.F10 = 121;
  this.key.F11 = 122;
  this.key.F12 = 123;


// end of wrapper function for Requirejs
});