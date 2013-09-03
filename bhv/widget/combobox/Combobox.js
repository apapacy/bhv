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
  /** Surrogate IDs to prevent item model adding/removing */
  idAttribute: 'backbone:combobox:item:id',
  /** To prevent item model adding/removing fill server response with undefined value */
  undefinedValue: 'backbone:combobox:item:undefined',
  /** Represent input value for search */
  searchField: 'backbone:combobox:searchfield',
  /** Count of item */
  limit: 10,
  /** Current page for pagination */
  page: 0,
  /** SQL column name */
  keyName: 'key',
  /** SQL column name may be == keyName */
  searchName: 'search',
  /** SQL column name may be == searchName */
  displayName: 'value',
  /** delay request to server, ms */
  delay: 1000,
  /** URL for Items fetching */
  url: '',
  /** URL for Item fetching */
  urlRoot: '',
  
  cssItem: {
    itemSelected: 'bbcombobox_item_selected',
    item: 'bbcombobox_item',
  }
};

var util = new utils( );

/**
 * @constructor
 * @overview represent item of list
 */
var Item = Backbone.Model.extend( {

  /**
   * @property {integer} idAttribute - contain [0..limit-1] number,
   * bat not real SQL primary key. It is model of widget, not business logic
   */
  idAttribute: defaults.idAttribute,
  // @todo to init( )
  init: function( settings ) {
    //util.mergeArray( this, [ 'idAttribute' ], defaults, settings );
    return this;
  },

  
  unselect: function( ) {
    this.trigger( 'backbone:combobox:item:unselect' );
  },

  select: function( ) {
    this.trigger( 'backbone:combobox:item:select' );
  }

  
} );

/**
 * @constructor
 * @overview represent set of list items
 */
var Items = Backbone.Collection.extend( {

  model: Item,
  
  /** Count of fetched items */
  actualLength: 0,
  selectedItem: undefined,

  init: function( settings ) {
    util.mergeArray( this, [ 'url' ], defaults, settings );
    return this;
  },
  
  selectNextItem: function( ) {
    if ( this.selectedItem < this.actualLength - 1 ) {
      this.get(this.selectedItem).unselect( );
      this.selectedItem = 1 + this.selectedItem;
      this.get( this.selectedItem ).select( );
    }
  }

} );

/**
 * @constructor
 * @overview represent current state of  widget
 */
var Input = Backbone.Model.extend( {
  
  defaults: {
    active: false,
  },

  init: function( settings ) {
    util.mergeArray( this, [ 'urlRoot', 'keyName', 'searchName', 'displayName' ],
                      defaults, settings );
    this.set( this.searchName, '' );
    this.set( this.displayName, '' );
    this.set( this.keyName, undefined );
    this.set( defaults.searchField, '' );
    this.idAttribute = this.keyName;
    return this;
  }

} );

/**
 * @constructor
 * @overview construct all of need for combobox
 */
function Constructor( settings ) {
  _.extend( this, defaults );
  _.extend( this, settings );
  this.items = ( new Items( ) ).init( settings );
  this.input = ( new Input( ) ).init( settings );
  this.input.items = this.items;
  this.input.on( 'change:' + this.searchField, this.read, this );
  this.inputView = ( new InputView( {model: this.input} ) ).init( settings );
  this.inputView.$el.appendTo( document.body );
  this.itemsView = ( new ItemsView( ) ).init( settings );
  this.itemsView.$el.appendTo( document.body );
  for ( var i = 0; i < this.limit; i++ ) {
    var item = ( new Item( ) ).init( settings );
    item.id = i;
    this.items.add( item );
    var itemView = ( new ItemView( {model: item} ) ).init( settings );
    itemView.$el.appendTo( this.itemsView.$el );
  }
}

_.extend( Constructor.prototype, {

  /** Fetch collection from server with current searchValue */
  read: function( ) {
    this.items.fetch( {
      data: {
        searchValue: this.input.get( defaults.searchField ),
        page: this.page,
        limit: this.limit
      },
      success: function( model, r, o ) {
        if ( model.selectedItem !== undefined) {
          model.get( model.selectedItem ).unselect( );
        }
        if ( model.actualLength > 0) {
          model.selectedItem = 0;
        } else {
          model.selectedItem = undefined;
        }
        if ( model.selectedItem !== undefined) {
          model.get( model.selectedItem ).select( );
        }     
      }
    } );
  },
  
  getValue: function( ) {
    var value = this.input.get( this.keyName );
    if ( value === defaults.undefinedValue ) {
      return undefined;
    } else {
      return value;
    }
  },

  setValue: function( value ) {
    this.input.set( this.keyName, value );
    this.input.fetch( {async:false} );
    alert(JSON.stringify(this.input.attributes))
    // @todo - refresh state of component from server 
  }


} );

var InputView = Backbone.View.extend( {

  tagName: 'input type="text"',

  handleTimeout: null,

  init: function( settings ) {
    util.mergeArray( this, ['delay', 'searchName'], defaults, settings );
    this.setSearchValue = _.bind( function( ) {
        this.model.set( defaults.searchField, this.$el.val( ) );
      },
      this
    );
    return this;
  },

  events: {
    'click': 'onclick',
    'keyup': 'onkeyup',
  },

  onclick: function( ) {
    if ( ! this.model.get('active') ) {
      this.model.set( 'active', true );
      this.$el.val( this.model.get( this.searchName ) );
      this.$el.select( );
    }
  },

  onkeyup: function( e ) {
    if ( this.handleTimeout !== null ) {
      window.clearTimeout( this.handleTimeout );
    }
    if ( e.which > util.key.DELETE || e.witch == util.key.SPACE) {
      this.handleTimeout = window.setTimeout( this.setSearchValue, this.delay );
    } else if ( e.which === util.key.UP ) {
      this.model.items.selectPreviousItem( );
    } else if ( e.which === util.key.DOWN ) {
      this.model.items.selectNextItem( );
    } else if ( e.which === util.key.PAGEUP ) {
      this.model.items.selectPreviousPage( );
    } else if ( e.which === util.key.PAGEDOWN ) {
      this.model.items.selectNextPage;  
    } else if ( e.which === util.key.HOME ) {
      this.model.items.selectFirstItem;
    } else if ( e.which === util.key.END ) {
      this.model.items.selectLastItem;
    }
  }
} );

var ItemsView = Backbone.View.extend( {
  
  tagName: 'div',

  init: function( settings ) {
    //util.mergeArray( this, [ ], defaults, settings );
    return this;
  }

} );

var ItemView = Backbone.View.extend( {

  tagName: 'div',

  init: function( settings ) {
    util.mergeArray( this, [ 'keyName', 'searchName', 'displayName', 'cssItem' ],
                      defaults, settings );
    this.listenTo( this.model, 'change', this.render );
    this.listenTo( this.model, 'backbone:combobox:item:select', this.select );
    this.listenTo( this.model, 'backbone:combobox:item:unselect', this.unselect );
    return this;
  },

  render: function( ) {
    var displayValue = this.model.get( this.displayName );
    if ( displayValue === defaults.undefinedValue ) {
      this.model.collection.actualLength = Math.min( this.model.collection.actualLength, this.model.id );
      this.$el.hide( );
    } else {
      this.model.collection.actualLength = Math.max( this.model.collection.actualLength, this.model.id + 1 );
      this.$el.show( );
    }
    this.$el.text( displayValue );
  },
  
  select: function( ) {
      this.$el.removeClass(this.cssItem.item);
      this.$el.addClass(this.cssItem.itemSelected);    
  },

  unselect: function( ) {
      this.$el.removeClass(this.cssItem.itemSelected);
      this.$el.addClass(this.cssItem.item);    
  }

  
} );

//* use with Requirejs define( ['combobox/Combobox'], function (cmb) {new cmb({});...} ) */
return Constructor;

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

  /*
   * @overview overwrite selected properties from source object
   * @param obj {object} - destination object
   * @param filtr {array} - selected properties
   * @param[, ...] {object} - source object
   */  
  this.mergeArray = function( obj, filter/*, source0, source1 ...*/ ) {
    for ( var i = 2; i < arguments.length; i++ ) {
      for ( var j = 0; j < filter.length; j++ ) {
        if ( typeof arguments[i][filter[j]] !== 'undefined' ) {
          obj[filter[j]] = arguments[i][filter[j]];
        }
      }
    }
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
}

// end of wrapper function for Requirejs
});
