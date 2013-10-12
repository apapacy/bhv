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
    item: 'bbcombobox_item'
  },

  cssItems: {
    listPane: 'bbcombobox_list_pane'
  },
  
  cssInput: {
    input: 'bbcombobox_input'
  },

  cssNextPage: {
    pane: 'bbcombobox_next_page_view',
    pressed: 'bbcombobox_next_page_view_pressed'
  },

  cssPreviousPage: {
    pane: 'bbcombobox_previous_page_view',
    pressed: 'bbcombobox_previous_page_view_pressed'
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
    util.mergeArray( this, [ 'keyName', 'searchName', 'displayName', 'undefinedValue' ],
                      defaults, settings, CONSTANT );
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
    var url = this.url;
    this.url = function( ) { return url + '?bust=' + Math.random( ); };
    this.actualLength = 0
    this.selectedItem = undefined;
    this.page = undefined;

    return this;
  },

  selectNextItem: function( ) {
    if ( this.selectedItem < this.actualLength - 1 ) {
      this.selectItem( 1 + this.selectedItem );
    } else if ( this.actualLength === this.length ) {
      this.selectNextPage( );
    }
  },

  selectPreviousItem: function( ) {
    if ( this.selectedItem > 0 ) {
      this.selectItem( -1 + this.selectedItem );
    } else {
      this.selectPreviousPage( );
    }
  },

  selectNextPage: function( ) {
    if ( this.selectedItem < this.actualLength - 1 ) {
      this.selectItem( -1 + this.actualLength );
    } else if ( this.actualLength === this.length ) {
      this.unselectItem( );
      this.selectedItem = 0;
      this.trigger( 'backbone:combobox:page:nextpage' );
    }
  },

  selectLastItem: function( ) {
    if ( this.selectedItem < this.actualLength - 1 ) {
      this.selectItem( -1 + this.actualLength );
    }
  },

  selectPreviousPage: function( ) {
    if ( this.selectedItem > 0 ) {
      this.selectItem( 0 );
    } else  if ( this.page > 0 ) {
      this.unselectItem();
      this.selectedItem = this.limit - 1;
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
  },

  selectItem: function( ind ) {
    this.unselectItem( );
    if ( typeof ind !== 'undefined' ) {
      this.selectedItem = ind;
    }
    if ( typeof this.get(this.selectedItem) === 'object' ) {
      this.get(this.selectedItem).select( );
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
    util.mergeArray( this, [ 'url', 'urlRoot', 'keyName', 'searchName', 'displayName' ],
                      defaults, settings, CONSTANT );
    var url = this.url;
    delete this.url;
    if ( ! this.urlRoot ) {            
      this.urlRoot = function( ){ return url + '?id=' + this.id + '&bust='+Math.random( ); };
    }
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

  if ( settings['keyName'] &&  ! settings['searchName'] && ! settings['displayName'] ) {
      settings['searchName'] = settings['displayName'] = settings['keyName'];
  }
  if ( ! settings['keyName'] && settings['searchName'] && ! settings['displayName'] ) {
    settings['displayName'] = settings['keyName'] = settings['searchName'];
  }
  if ( settings['keyName'] &&  ! settings['searchName'] && settings['displayName'] ) {
      settings['keyName'] = settings['searchName'] = settings['displayName'];
  }  
  if ( ! settings['keyName'] ) {
    settings['keyName'] = settings['displayName'];
  } 
  if ( ! settings['searchName'] ) {
    settings['searchName'] = settings['displayName'];
  } 
  if ( ! settings['displayName'] ) {
    settings['displayName'] = settings['searchName'];
  }
 
  _.extend( this, defaults );
  _.extend( this, settings );
  _.extend( this, CONSTANT );
  _.extend( this, Backbone.Events );
   
  this.parentElement = $('#'+this.parent);
  if ( this.store ) {
    this.storeElement = $('#'+this.store);
  }
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
  this.listenTo( this.inputView, 'backbone:combobox:items:accept', this.acceptValue );
  this.itemsView = ( new ItemsView( ) ).init( settings );
  this.itemsView.$el.appendTo( document.body );
  this.previousPageView = (new PreviousPageView( )).init( settings );
  this.previousPageView.render( );
  this.previousPageView.$el.appendTo( this.itemsView.$el );
  this.listenTo( this.previousPageView, 'backbone:combobox:page:previouspage', this.readPreviousPage);
  for ( var i = 0; i < this.limit; i++ ) {
    var item = ( new Item( ) ).init( settings );
    item.set( this.keyName, this.undefinedValue );
    item.id = i;
    var itemView = ( new ItemView( {model: item} ) ).init( settings );
    this.listenTo( itemView, 'backbone:combobox:items:accept', this.acceptValue );
    this.items.add( item );
    itemView.$el.appendTo( this.itemsView.$el );
  }
  this.nextPageView = ( new NextPageView( ) ).init(  settings );
  this.nextPageView.render( );
  this.nextPageView.$el.appendTo( this.itemsView.$el );
  this.nextPageView.listenTo( itemView, 'backbone:combobox:item:show', this.nextPageView.show ); 
  this.nextPageView.listenTo( itemView, 'backbone:combobox:item:hide', this.nextPageView.hide ); 
  this.listenTo( this.nextPageView, 'backbone:combobox:page:nextpage', this.readNextPage);
  //this.nextPageView.items = this.items;
}

_.extend( Constructor.prototype, {

  showItems: function( ) {
    this.inputView.$el.focus( );
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
		conteiner.outerWidth( input.outerWidth( ) );
  },

  /** For plain old FireFox2.0 */
  showItemsPOFF20: function( ) {
    this.inputView.$el.focus( );
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

  acceptValue: function( ) {
    this.inputView.$el.focus( );
    if ( this.items.selectedItem < this.items.actualLength && this.items.selectedItem >= 0 ) {
      this.input.set( this.keyName, this.items.at( this.items.selectedItem ).get( this.keyName ) );
      this.input.set( this.searchName, this.items.at( this.items.selectedItem ).get( this.searchName ) );
      this.input.set( this.displayName, this.items.at( this.items.selectedItem ).get( this.displayName ) );
      if ( this.storeElement ) {
        this.storeElement.val( this.getValue( ) );
      }
      this.inputView.$el.val(  this.items.at( this.items.selectedItem ).get( this.displayName ) );
      util.delay( 500 );
      this.hideItems( );
      this.input.set( 'active', false );
    }
  },

  hideItems: function( ) {
    this.itemsView.$el.hide( );
    this.inputView.el.focus( );
    //this.inputView.el.select( );
  },

  /** Fetch collection from server with current searchValue */
  read: function( async ) {
    this.inputView.$el.focus( );
    if ( this.items.page === 0) {
      this.previousPageView.hide( );
    } else {
      this.previousPageView.show( );
    }
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
      success: function( model, resp, opt ) {
        model.selectItem( );
      }
    } );
  },

  readFirstPage: function( ) {
    this.inputView.$el.focus( );
    this.items.page = 0;
    this.read( );
  },

  readNextPage: function( ) {
    this.inputView.$el.focus( );
    this.items.selectItem( 0 );
    this.items.page = 1 + this.items.page;
    this.read( false );
  },

  readPreviousPage: function( ) {
    this.inputView.$el.focus( );
    if ( this.items.page > 0 ) {
      this.items.selectItem( this.limit - 1 );
      this.items.page = -1 + this.items.page;
      this.read( false );
    }
  },

  getValue: function( ) {
    //this.inputView.$el.focus( );
    var value = this.input.get( this.keyName );
    if ( value === CONSTANT.undefinedValue ) {
      return undefined;
    } else {
      return value;
    }
  },

  setValue: function( value ) {
    this.storeElement.val( value );
    this.inputView.$el.focus( );
    this.input.set( this.keyName, value );
    this.input.fetch( {async:false, success:function(m,r,o){},error:function(m,r,o){alert('error')}});
    this.items.each( function( element, index, list){element.set( this.keyName, this.undefinedValue );}, this  );
    this.items.at( 0 ).set( this.input.toJSON( ) );
    this.inputView.$el.val(this.input.get(this.displayName))
    this.items.selectFirstItem();
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
        this.model.items.unselectItem( );
        this.model.items.selectItem( 0 );
        this.model.set( 'active', true );
        this.trigger('backbone:combobox:items:show');
      },
      this
    );

    this.restoreValue = _.bind( function( ) {
      this.$el.val( this.model.get( this.model.displayName ) );
      },
      this
    );

    this.$el.val( this.model.get( this.model.displayName ) );

    return this;
  },

  events: {
    'click': 'onclick',
    'keydown': 'onkeydown',
    'keyup': 'onkeyup',
    'blur': 'onblur'
  },
  
  onblur:  function( e ) {
    if ( this.model.get('active') ) {
      //this.$el.focus( );
      // this.model.set( 'active', false );
      //this.$el.val( this.model.get( this.model.displayName ) );
      //this.trigger('backbone:combobox:items:hide');
    }
  },
  
  onclick: function( ) {
    if ( ! this.model.get('active') ) {
      this.model.set( 'active', true );
      this.$el.val( this.model.get( this.searchName ) );
      this.$el.select( );
      this.trigger('backbone:combobox:items:show');
    }
  },
  
  onkeyup: function( e ) {

    if ( e.which === util.key.ESC) {
      return false;
    }
    
  },

  onkeydown: function( e ) {
    if ( this.handleTimeout !== null ) {
      window.clearTimeout( this.handleTimeout );
    }
    if ( e.which === util.key.ENTER || e.which === util.key.TAB) {
      if ( this.model.get( 'active' ) ) {
        this.model.set( 'active', false);
        this.trigger('backbone:combobox:items:accept');
        return false;
      } else {
        util.selectNextInput( this.el);
        return false;
      }
    }

    if ( e.which === util.key.LEFT || e.which === util.key.UP) {
      if ( ! this.model.get( 'active' ) ) {
        util.selectPreviousInput( this.el);
        return false;
      }
    }

    
    if ( e.which === util.key.ESC) {
      if ( this.model.get('active') ) {
        this.model.set( 'active', false );
        //this.$el.val( this.model.get( this.model.displayName ) );
        window.setTimeout(this.restoreValue,0);
        this.trigger('backbone:combobox:items:hide');
        return false;
      }
    }

    if ( e.which === util.key.UP ) {
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
    } else {
      this.handleTimeout = window.setTimeout( this.setSearchValue, this.delay );
    }
    
    if ( ! this.model.get('active') /*&& e.which >= util.key.DELETE*/ ) {
      this.model.set( 'active', true );
      //this.$el.val( this.model.get( this.model.searchName ) );
      this.$el.val( this.model.get( '' ) );
      //this.$el.select( );
      this.trigger('backbone:combobox:items:show');
      //return true;
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
      this.trigger( 'backbone:combobox:item:hide' );
    } else {
      this.model.collection.actualLength = Math.max( this.model.collection.actualLength, this.model.id + 1 );
      this.$el.show( );
      this.trigger( 'backbone:combobox:item:show' );
    }
    this.$el.text( displayValue );
  },

  events: {
    'click': 'onclick'
  },

  select: function( ) {
      this.$el.removeClass(this.cssItem.item);
      this.$el.addClass(this.cssItem.itemSelected);
  },

  unselect: function( ) {
      this.$el.removeClass(this.cssItem.itemSelected);
      this.$el.addClass(this.cssItem.item);
  },

  onclick: function( e ) {
    this.select( );
    this.model.collection.selectItem( this.model.id );
    this.trigger( 'backbone:combobox:items:accept' );
  }

} );

var NextPageView = Backbone.View.extend( {

  tagName: 'div',

  init: function( settings ) {
    util.mergeArray( this, [ 'cssNextPage' ],
                      defaults, settings, CONSTANT );
    this.$el.addClass( this.cssNextPage.pane );
    this.unpress = _.bind( function( ) {
      //this.previousPageView.$el.removeClass( this.previousPageView.cssPreviousPage.pressed );
      //this.previousPageView.$el.addClass( this.previousPageView.cssPreviousPage.pane );
      this.$el.removeClass( this.cssNextPage.pressed );
      this.$el.addClass( this.cssNextPage.pane );
    }, this );

    return this;
  },
 
  render: function( ) {
    this.$el.html( '<center>▼</center>' );
    this.$el.hide( );
  },
  
  show: function( ) {
    this.$el.show( );
  },
  
  hide: function( ) {
    this.$el.hide( );
  },
 
 events: {
    'click': 'onclick'
  },
  
  onclick: function( ) {
    this.$el.removeClass( this.cssNextPage.pane );
    this.$el.addClass( this.cssNextPage.pressed );
    window.setTimeout( this.unpress, 300);
    this.trigger( 'backbone:combobox:page:nextpage' );
  }

  } );

var PreviousPageView = Backbone.View.extend( {

  tagName: 'div',
  
  init: function( settings ) {
    util.mergeArray( this, [ 'cssPreviousPage' ],
                      defaults, settings, CONSTANT );
    this.$el.addClass( this.cssPreviousPage.pane );
    this.unpress = _.bind( function( ) {
      //this.previousPageView.$el.removeClass( this.previousPageView.cssPreviousPage.pressed );
      //this.previousPageView.$el.addClass( this.previousPageView.cssPreviousPage.pane );
      this.$el.removeClass( this.cssPreviousPage.pressed );
      this.$el.addClass( this.cssPreviousPage.pane );
    }, this );
    return this;
  },

  render: function( ) {
    this.$el.html( '<center>▲</center>' );
    this.$el.hide( );
  },
  
  show: function( ) {
    this.$el.show( );
  },
  
  hide: function( ) {
    this.$el.hide( );
  },
  
  events: {
    'click': 'onclick'
  },
 
  events: {
    'click': 'onclick'
  },
  
  onclick: function( ) {
    this.$el.removeClass( this.cssPreviousPage.pane );
    this.$el.addClass( this.cssPreviousPage.pressed );
    window.setTimeout( this.unpress, 300 );
    this.trigger( 'backbone:combobox:page:previouspage' );
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
  
  this.selectNextInput = function ( input ) {
    var inputs = $( 'input:visible' );
    for ( var i = 0; i < inputs.length - 1; i++ ){
      if ( inputs[i] === input ) {
        inputs[i+1].focus( );
        return
      }
    }
  } 

  this.selectPreviousInput = function ( input ) {
    var inputs = $( 'input:visible' );
    for ( var i = 1; i < inputs.length; i++ ){
      if ( inputs[i] === input ) {
        inputs[i-1].focus( );
        return
      }
    }
  } 

  this.delay = function ( delay ) {
    for (var i = new Date().getTime(); new Date().getTime() - i < delay; ){
      Math.random();
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
