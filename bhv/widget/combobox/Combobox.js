/**
 * @overview Autocompite commbobox based on Backbone library
 * @module combobox/Combobox.js
 * @licence MIT
 * @copyright Andrey Ovcharenko <An6rey@google.com>
 * @exports Constructor
 */
define( [ /* require Requirejs, jQuery, Backbone( =>Underscore, JSON2 ) */ 'domReady!' ], function( ) {

var CONSTANT = {
  /** Surrogate IDs to prevent item model adding/removing */
  idAttribute: 'backbone:combobox:item:id',
  /** To prevent item model adding/removing fill server response with undefined value */
  undefinedValue: 'backbone:combobox:item:undefined',
  /** Represent input value for search */
  searchField: 'backbone:combobox:searchfield',
}


/** default setting if not redefined */
var defaults = {
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
  },
  
  cssItems: {
    listPane: 'bbcombobox_list_pane'
  },

  cssInput: {
    input: 'bbcombobox_input'
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
  idAttribute: CONSTANT.idAttribute,
  // @todo to init( ) why in not work init?
 
  init: function( settings ){
    util.mergeArray( this, [ 'keyName', 'searchName', 'displayName', 'undefinedValue' ], defaults, settings, CONSTANT );
    this.set( this.keyName, this.undefinedValue );
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
  page: 0,

  init: function( settings ) {
    util.mergeArray( this, [ 'url', 'limit' ], defaults, settings, CONSTANT );
    this.actualLength = 0
    this.selectedItem = undefined;
    this.page = undefined;

    return this;
  },
  
  selectNextItem: function( ) {
    if ( this.selectedItem < this.actualLength - 1 ) {
      this.get(this.selectedItem).unselect( );
      this.selectedItem = 1 + this.selectedItem;
      this.get( this.selectedItem ).select( );
    } else if ( this.actualLength === this.length ) {
      this.selectNextPage( );
      //this.trigger( 'backbone:combobox:page:nextpage' );
    }
  },

  selectPreviousItem: function( ) {
    if ( this.selectedItem > 0 ) {
      this.get(this.selectedItem).unselect( );
      this.selectedItem = -1 + this.selectedItem;
      this.get( this.selectedItem ).select( );
    } else {
      this.selectPreviousPage( );
      //this.trigger( 'backbone:combobox:page:previouspage' );
    }
  },
  
  selectNextPage: function( ) {
    if ( this.selectedItem < this.actualLength - 1 ) {
      this.get(this.selectedItem).unselect( );
      this.selectedItem = -1 + this.actualLength;
      this.get( this.selectedItem ).select( );
    } else if ( this.actualLength === this.length ) {
      this.get(this.selectedItem).unselect( );
      this.selectedItem = 0;
      this.trigger( 'backbone:combobox:page:nextpage' );
    }
  },

  selectLastItem: function( ) {
    if ( this.selectedItem < this.actualLength - 1 ) {
      this.get(this.selectedItem).unselect( );
      this.selectedItem = -1 + this.actualLength;
      this.get( this.selectedItem ).select( );
    }
  },
  
  selectPreviousPage: function( ) {
    if ( this.selectedItem > 0 ) {
      this.get(this.selectedItem).unselect( );
      this.selectedItem = 0;
      this.get( this.selectedItem ).select( );
    } else  if ( this.page > 0 ) {
      this.unselectItem();
      this.selectedItem = this.limit - 1;
      this.get(this.selectedItem).select( );
      this.trigger( 'backbone:combobox:page:previouspage' );
    }
  },

  selectFirstItem: function( ) {
    if ( this.selectedItem > 0 ) {
      this.get(this.selectedItem).unselect( );
    }
      this.selectedItem = 0;
      this.get( this.selectedItem ).select( );
  },
  
  unselectItem: function( ) {
    if ( typeof this.get(this.selectedItem) === 'object' ) {
      this.get(this.selectedItem).unselect( );
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
                      defaults, settings, CONSTANT );
    this.set( this.searchName, '' );
    this.set( this.displayName, '' );
    this.set( this.keyName, undefined );
    this.set( CONSTANT.searchField, '' );
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
  _.extend( this, CONSTANT );
  _.extend( this, Backbone.Events );
  this.parentElement = $('#'+this.parent);
  this.items = ( new Items( ) ).init( settings );
  this.listenTo( this.items, 'backbone:combobox:page:nextpage', this.readNextPage);
  this.listenTo( this.items, 'backbone:combobox:page:previouspage', this.readPreviousPage);
  this.input = ( new Input( ) ).init( settings );
  this.input.items = this.items;
  this.input.on( 'change:' + this.searchField, this.readFirstPage, this );
  this.inputView = ( new InputView( {model: this.input} ) ).init( settings );
  this.inputView.$el.appendTo( this.parentElement );
  this.listenTo( this.inputView, 'backbone:combobox:items:show', this.showItems );
  this.listenTo( this.inputView, 'backbone:combobox:items:hide', this.hideItems );
  this.itemsView = ( new ItemsView( ) ).init( settings );
  this.itemsView.$el.appendTo( document.body );
  for ( var i = 0; i < this.limit; i++ ) {
    var item = ( new Item( ) ).init( settings );
    item.set( this.keyName, this.undefinedValue );
    var itemView = ( new ItemView( {model: item} ) ).init( settings );
    item.id = i;
    this.items.add( item );
    itemView.$el.appendTo( this.itemsView.$el );
  }
}

_.extend( Constructor.prototype, {
  
  showItems: function( ) {
    if ( this.POFF20 === true) {
      this.showItems = this.showItemsPOFF20;
      this.showItemsPOFF20( );
      return;
    }
    var conteiner = this.itemsView.$el;
    var input = this.inputView.$el;
    this.itemsView.$el.show( );
 		conteiner.offset( {top: input.offset( ).top} );
    // In some version of FF has not valid value jQuery.offset() when CSS margin > 0
    var deltaTop = input.offset( ).top - conteiner.offset( ).top;
		conteiner.offset( {top:input.height( ) + input.offset( ).top + 2 * deltaTop + 5} );
		conteiner.offset( {left: input.offset( ).left } );
    // In some version of FF has not valid value jQuery.offset() when CSS margin > 0
    var deltaLeft = input.offset( ).left - conteiner.offset( ).left;
		conteiner.offset( {left: input.offset( ).left + 2 * deltaLeft} );
		conteiner.outerWidth( input.width( ) );
  },
  
  /** For plain old FireFox2.0 */
  showItemsPOFF20: function( ) {
    this.itemsView.$el.show( );
    var conteiner = this.itemsView.el;
    var input = this.inputView.el;
		conteiner.style.top = util.top( input ) + "px";
    var deltaTop = util.top( input ) - util.top( conteiner );
		conteiner.style.top = input.offsetHeight + util.top( input ) + 2 * deltaTop + 2 + "px";
		conteiner.style.left = util.left( input ) + "px";
    var deltaLeft = util.left( input ) - util.left( conteiner );
		conteiner.style.left =  util.left( input ) + 2 * deltaLeft  + "px";
		conteiner.style.width = input.clientWidth + "px";
  },

  
  hideItems: function( ) {
    this.itemsView.$el.hide( );
  },

  /** Fetch collection from server with current searchValue */
  read: function( async ) {
    if ( async !== false ) {
      async = true;
    }
    this.items.fetch( {
      async: async,
      data: {
        searchValue: this.input.get( CONSTANT.searchField ),
        page: this.items.page,
        limit: this.limit
      },
      success: function( model, r, o ) {
        if ( model.selectedItem !== undefined) {
          model.get( model.selectedItem ).unselect( );
        }
        if ( model.actualLength > 0) {
          if ( ! model.selectedItem ) {
            model.selectedItem = 0;
          }
        } else {
          model.selectedItem = undefined;
        }
        if ( model.selectedItem !== undefined) {
          model.get( model.selectedItem ).select( );
        }     
      }
    } );
  },

  readFirstPage: function( ) {
    this.items.page = 0;
    this.read( );
  },

  readNextPage: function( ) {
    this.items.page = 1 + this.items.page;
    this.read( false );
    //this.items.at( 0 ).select( );
  },
  
  readPreviousPage: function( ) {
    if ( this.items.page > 0 ) {
      this.items.page = -1 + this.items.page;
      this.read( false );
      //this.items.at( this.limit - 1 ).select( );
    }
  },

  getValue: function( ) {
    var value = this.input.get( this.keyName );
    if ( value === CONSTANT.undefinedValue ) {
      return undefined;
    } else {
      return value;
    }
  },

  setValue: function( value ) {
    this.input.set( this.keyName, value );
    this.input.fetch( {async:false, success:function(m,r,o){},error:function(m,r,o){alert('error')}});
    this.items.each( function( element, index, list){element.set( this.keyName, this.undefinedValue );}, this  );
    this.items.at( 0 ).set( this.input.toJSON( ) );
    this.inputView.$el.val(this.input.get(this.displayName))
    this.items.selectFirstItem();
    //alert(JSON.stringify(this.input.attributes))
    // @todo - refresh state of component from server 
  }


} );

var InputView = Backbone.View.extend( {

  tagName: 'input type="text"',

  handleTimeout: null,

  init: function( settings ) {
    util.mergeArray( this, ['delay', 'searchName', 'cssInput' ], defaults, settings, CONSTANT );
    this.$el.addClass( this.cssInput.input );
    this.setSearchValue = _.bind( function( ) {
        this.model.set( CONSTANT.searchField, this.$el.val( ) );
        //this.model.items.selectFirstItem( );
        this.model.items.unselectItem( );
        this.model.items.selectedItem = 0;
        //
      },
      this
    );
    return this;
  },

  events: {
    'click': 'onclick',
    'keydown': 'onkeydown',
  },

  onclick: function( ) {
    this.trigger('backbone:combobox:items:show');
    if ( ! this.model.get('active') ) {
      this.model.set( 'active', true );
      this.$el.val( this.model.get( this.searchName ) );
      this.$el.select( );
    }
  },

  onkeydown: function( e ) {
    if ( this.handleTimeout !== null ) {
      window.clearTimeout( this.handleTimeout );
    }
    if ( e.which === util.key.ENTER) {
      if ( this.model.get( 'active' ) ) {
        this.model.set( 'active', false);
        this.trigger('backbone:combobox:items:hide');
      }
    }
    
    if ( e.which >= util.key.DELETE || e.witch == util.key.SPACE 
        || e.witch == util.key.BACKSPACE) {
      this.handleTimeout = window.setTimeout( this.setSearchValue, this.delay );
    } else if ( e.which === util.key.UP ) {
      this.model.items.selectPreviousItem( );
    } else if ( e.which === util.key.DOWN ) {
      this.model.items.selectNextItem( );
    } else if ( e.which === util.key.PAGEUP ) {
      this.model.items.selectPreviousPage( );
    } else if ( e.which === util.key.PAGEDOWN ) {
      this.model.items.selectNextPage( );  
    } else if ( e.which === util.key.HOME ) {
      this.model.items.selectFirstItem( );
    } else if ( e.which === util.key.END ) {
      this.model.items.selectLastItem( );
    }
  }
} );

var ItemsView = Backbone.View.extend( {
  
  tagName: 'div',

  init: function( settings ) {
    util.mergeArray( this, [ 'cssItems' ], defaults, settings );
    this.$el.addClass( this.cssItems.listPane );
    return this;
  }

} );

var ItemView = Backbone.View.extend( {

  tagName: 'div',

  init: function( settings ) {
    util.mergeArray( this, [ 'keyName', 'searchName', 'displayName', 'cssItem' ],
                      defaults, settings, CONSTANT );
    this.listenTo( this.model, 'change', this.render );
    this.listenTo( this.model, 'backbone:combobox:item:select', this.select );
    this.listenTo( this.model, 'backbone:combobox:item:unselect', this.unselect );
    this.$el.addClass( this.cssItem.item );
    this.$el.hide();
    return this;
  },

  render: function( ) {
    var displayValue = this.model.get( this.displayName );
    if ( this.model.get(this.keyName) === CONSTANT.undefinedValue ) {
      this.model.collection.actualLength = Math.min( this.model.collection.actualLength, this.model.id );
      this.$el.hide( );
    } else {
      this.model.collection.actualLength = Math.max( this.model.collection.actualLength, this.model.id + 1 );
      //this.$el.removeClass(this.cssItem.item);
      //this.$el.addClass(this.cssItem.item);    
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
  };

  /*
   * @overview copy missing parameters from to Object
   * @param {object} toObject - target object (changed)
   * @param {object} fromObject - prototype object (readonly)
   */
  this.ISA = function ( toObject, fromObject ) {
    for ( var p in fromObject ) {
        toObject[p] = fromObject[p];
    }
  };
  

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
  };

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
  };

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
  };

  this.top = function ( element ) {
    var top = 0;
    try{top = element.offsetTop;
        while(element.offsetParent){
            element = element.offsetParent;
            top += element.offsetTop;
        }
    }catch (ex){}
    return top;
    return jQuery(element).offset().top;
  };

  this.left = function ( element ) {
   var left = 0;
    try{left = element.offsetLeft;
        while(element.offsetParent){
            element = element.offsetParent;
            left += element.offsetLeft;
        }
    }catch (ex){}
    return left;
    return jQuery(element).offset().left;
  };

  
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
