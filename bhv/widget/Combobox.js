define(['bhv/util', 'bhv/classes'], function(bhv, classes){
/////////////////////////////////////////////
var styleSheet = jQuery('<link href="'+bhv.getApplicationFolder()+'css/widget/combobox.css" rel="stylesheet" type="text/css" />'
	).appendTo("head");
styleSheet.attr({
	href : bhv.getApplicationFolder() + "css/widget/combobox.css",
	rel  : "stylesheet",
	type : "text/css"
});

var _bhv = {};
_bhv.Combobox = function (element, valueElement, initialValue, count,
	table, keyColumn, displayValueColumn, searchValueColumn, exactly, filter,
	addonce) {
	this.init(element, valueElement, initialValue, count,
		table, keyColumn, displayValueColumn, searchValueColumn, exactly, filter,
		addonce);
};

_bhv.Combobox.prototype = {
	constructor: _bhv.Combobox, 
	init: function (element, valueElement, initialValue, count,
		table, keyColumn, displayValueColumn, searchValueColumn, exactly, filter,
		addonce) {
		// Переменнаая the исползуется в замыканиях
		var the = this;

		// Для удаления циклических ссылок в замыканиях достаточно обнулить the
		this.destroy = function () {
			the.destroy = null;
			the = null;
		};

		this.exactly = !! exactly;
		this.filter = filter;
		this.addonce = addonce;
		this.enabled = false;
		this.count = count;
		this.data = new _bhv.Combobox.ComboboxData(this.count);

		if (typeof element === "string")
			this.element = document.getElementById(element);
		else
			this.element = element;

		this.input = document.createElement("input");
		this.element.appendChild(this.input);
		this.input.type = "text";
		this.input.style.width = this.element.offsetWidth + "px";

		// Для фукнций-обработчиков событий вызываем функции-методы объекта Combobox,
		// которые исползуют замыкание переменной the
		//this.input.onkeyup = function () {
		//	var event0 = arguments[0] || window.event;
		//	the.onkeyup(event0);
		//};
		jQuery(this.input).keyup(
			function(event) {
				the.onkeyup(event);
			}
		);
		jQuery(this.input).keydown(
			function(event){
				if (event.keyCode === 27)
					the.onkeyup(event);
			}
		);
		jQuery(this.input).mouseup(
			function (event) {
				the.onclick(event);
			}
		);
		jQuery(this.input).blur(
			function(event) {
				if (the.enabled) {
					the.enabled = false;
					//the.assignValue();
					window.setTimeout(function(){the.hideComboBox()}, 200);
					this.focus();
				}
				var saveText = this.value;
				this.value = '';
				this.value = saveText;

			}
		);
		jQuery(this.input).focus(
			function (event) {
				this.select();
			}
		);

		if (!valueElement)
			this.valueElement = {};
		else if (typeof valueElement == "string")
			this.valueElement = document.getElementById(valueElement);
		else
			this.valueElement = valueElement;

		this.conteiner = document.createElement("DIV");
		this.conteiner.className = "textDropDown"

		jQuery(this.conteiner).mouseup(function(event) {
			the.assignValue();
			window.setTimeout(function(){the.hideComboBox();},200);
			the.input.focus();
			the.input.select();
			//jQuert(the.input).attr('selected', '');
		});

		for (var i = 0; i < this.count; i++)
			this.conteiner.appendChild(document.createElement("DIV"));
		jQuery("div", this.conteiner).attr("class", "otherItem");
		jQuery("div", this.conteiner).mouseover(function(){the.selectOption(this);});
		//bhv.contentPane().appendChild(this.conteiner);
		jQuery(this.conteiner).appendTo(jQuery('body'));

		this.table = table;
		this.keyColumn = keyColumn;
		this.displayValueColumn = displayValueColumn;
		this.searchValueColumn = searchValueColumn;
		this.requestedKey = null;
		this.requestedSearchValue = null;

		if (typeof initialValue !== "undefined" && initialValue !== null) {
			this.valueElement.value = initialValue;
			this.requestedKey = initialValue;
		}

		if (typeof this.valueElement.value !== "undefined") {
			this.getValueFromServerSync("currentKey=" + encodeURIComponent(this.valueElement
				.value), "init");
		}

	}, 
	SERVER_SCRIPT: bhv.getApplicationFolder() + "bhv/widget/combobox_query.php", 
	getServerScript: function () {
		return this.SERVER_SCRIPT;
	}, 
	getHttpParams: function (additions, command) {
		var params = "";
		params = "table=" + this.table + "&keyColumn=" + this.keyColumn +
			"&displayValueColumn=" + this.displayValueColumn + "&searchValueColumn=" +
			this.searchValueColumn + "&count=" + this.count + (this.exactly ?
			"&exactly=1" : "") + (this.filter ? "&filter=" + encodeURIComponent(this.filter) :
			"") + (this.addonce ? "&addonce=" + encodeURIComponent(this.addonce) : "")

		if (additions)
			params += "&" + additions;

		if (command)
			params += "&command=" + command;
		return params;

	},
	getValueFromServer: function (additions, command, selected, timeout) {
		bhv.setCommand(this.getValueFromServer$, this, [additions, command, selected, true],
			700, "bhv_combobox_" + this.element.id);
		return;
	},
	getValueFromServerSync: function (additions, command, selected) {
		bhv.unsetCommand("bhv_combobox_" + this.element.id); //clear command queue by name
		this.getValueFromServer$(additions, command, selected, false);
		return;
	},
	getValueFromServer$: function (additions, command, selected, async) {
		var settings = {
			context: {
				selected: selected,
				timeout: 5*60*1000,
				async: async
			},
			data: this.getHttpParams(additions, command),
			dataType: 'text',
			success: this.handleRequest$
		};
		settings.context.combobox = this;
		jQuery.ajax(this.getServerScript(), settings)
	},
	handleRequest$: function (data, textStatus, jqXHR) {
		var combobox = this.combobox,
			selected = this.selected;
		this.combobox = null;
		combobox.data.parseJSON(data);
		if (combobox.enabled) {
			combobox.element.value = combobox.data.getCurrentKey();
			combobox.showComboBox(selected);
			var matchedChar = bhv.matchedChar(String(combobox.input.value).toLowerCase(),
				String(combobox.data.getCurrentSearchValue()).toLowerCase());
		} else {
			combobox.input.value = combobox.data.getCurrentDisplayValue();
		}
	},
	assignValue: function (selected) {
		this.input.value = this.data.getCurrentDisplayValue();
		this.valueElement.value = this.data.getCurrentKey();
		if (typeof this.afterValueChange === "function")
			this.afterValueChange(this);
	},
	hideComboBox: function (selected) {
		this.enabled = false;
		if (this.conteiner.style.visibility === "hidden")
			return;
		this.conteiner.style.visibility = "hidden";
	},
	showComboBox: function (selected) {
		if (!this.enabled)
			return;
		this.conteiner.style.visibility = "visible";
		this.conteiner.style.top = this.input.offsetHeight + bhv.top(this.input) + "px";
		this.conteiner.style.left = bhv.left(this.input) + "px";
		this.conteiner.style.width = this.input.clientWidth + "px";

		if (selected == "last")
			this.data.currentIndex = this.data.currentCount - 1;
		else if (selected == "first")
			this.data.currentIndex = 0;
		else if (selected)
			this.data.currentIndex = this.data.getKeyIndex(selected);

		for (var i = 0; i < this.count; i++) {
			this.conteiner.childNodes[i].innerHTML = this.data.getDisplayValue(i);

			if (i == this.data.currentIndex)
				this.conteiner.childNodes[i].className = "selectedItem"
			else if (i < this.data.currentCount)
				this.conteiner.childNodes[i].className = "otherItem"
			else
				this.conteiner.childNodes[i].className = "hiddenItem"
		}
	},
	selectOption: function (selectedOption) {
		var the = this;
		jQuery("div.selectedItem", this.conteiner).attr("class", "otherItem");
		jQuery("div", this.conteiner).each(
			function(i) {
				if (selectedOption === this) {
					the.data.currentIndex = i;
					selectedOption.className = "selectedItem";
					return false;
				}
			}
		);
	},
	onkey: function (event0) {

		if (event0.keyCode == bhv.key.ESC) {
			this.enabled = false;
			this.input.select();
			this.getValueFromServerSync("currentKey=" + encodeURIComponent(this.valueElement
				.value), "init");
			this.hideComboBox();
			return true;
		}

		if (event0.keyCode == bhv.key.TAB) {
			if (this.enabled) {
				this.assignValue();
				this.hideComboBox();
				this.enabled = false;
			}
			return true;
		}

		if (event0.keyCode == bhv.key.ENTER) {

			if (this.enabled) {
				this.assignValue();
				this.hideComboBox();
				this.enabled = false;
				this.input.focus();
				this.input.select();
			} else {
				bhv.selectNextInput(this.input);
				return true;
			}
			return true;
		}

		if (event0.keyCode == bhv.key.RIGHT) {
			if (!this.enabled) {
				bhv.selectNextInput(this.input);
				return true;
			}
			return true;
		}


		if (event0.keyCode == bhv.key.LEFT) {

			if (!this.enabled) {
				bhv.selectPreviousInput(this.input);
				return true;
			}
			return true;
		}


		if (!this.enabled) {
			this.enabled = true;
			this.showComboBox();
			this.input.focus();
			return true;
		}


		if (event0.keyCode == bhv.key.PAGEDOWN) {
			if (this.data.currentIndex < this.data.currentCount - 1)
				this.data.currentIndex = this.data.currentCount - 1
			else
				this.getValueFromServer("currentKey=" + this.data.getCurrentKey() +
					"&currentSearchValue=" + encodeURIComponent(this.input.value));

		} else if (event0.keyCode == bhv.key.PAGEUP) {
			if (this.data.currentIndex > 0)
				this.data.currentIndex = 0
			else
				this.getValueFromServer("currentKey=" + this.data.getCurrentKey() +
					"&currentSearchValue=" + encodeURIComponent(this.input.value), "previous",
					"first");

		} else if (event0.keyCode == bhv.key.DOWN) {
			if (this.data.currentIndex < this.data.currentCount - 1)
				this.data.currentIndex++;
			else
				this.getValueFromServer("currentKey=" + this.data.getCurrentKey() +
					"&currentSearchValue=" + encodeURIComponent(this.input.value));

		} else if (event0.keyCode == bhv.key.UP) {
			if (this.data.currentIndex > 0)
				this.data.currentIndex--;
			else
				this.getValueFromServer("currentKey=" + this.data.getCurrentKey() +
					"&currentSearchValue=" + encodeURIComponent(this.input.value), "previous",
					this.data.getCurrentKey());
		} else {
			if (!this.enabled)
				combobox.input.value = combobox.data.getCurrentSearchValue();
		}

		this.showComboBox();
		return true;
	},
	onclick: function (event0) {
		bhv.clearSelection();
		this.enabled = true;
		this.showComboBox();
		this.input.value = this.data.getCurrentSearchValue();
		//this.input.select();
		this.input.focus();
	},
	onkeyup: function (event0) {

		if (event0.keyCode == bhv.key.ESC || event0.keyCode == bhv.key.TAB || event0.keyCode ==
			bhv.key.ENTER || event0.keyCode == bhv.key.RIGHT || event0.keyCode == bhv.key
			.LEFT || event0.keyCode == bhv.key.PAGEDOWN || event0.keyCode == bhv.key.PAGEUP ||
			event0.keyCode == bhv.key.DOWN || event0.keyCode == bhv.key.UP) {
			return this.onkey(event0) && false;
		} else if (!this.enabled) {
			this.onkey(event0);
			this.getValueFromServer("currentSearchValue=" + encodeURIComponent(this.input
				.value));
		} else if (!String(this.input.value).isEmpty()) {
			this.onkey(event0)
			this.getValueFromServer("currentSearchValue=" + encodeURIComponent(this.input
				.value));
		}

		this.showComboBox();
		return true;
	},
	onclick: function () {
		bhv.clearSelection();
		this.enabled = true;
		this.showComboBox();
		this.input.value = this.data.getCurrentSearchValue();
		this.input.select();
		this.input.focus();
	}, 
	setValue: function (value) {
		this.valueElement.value = value;
		this.requestedKey = value;
		this.getValueFromServerSync("currentKey=" + encodeURIComponent(value), 'init',
			null, 1);
	}, 
	getValue: function () {
		return this.valueElement.value;
	}, 
	show: function () {
		this.element.style.display = "block";
	}, 
	hide: function () {
		this.element.style.display = "none";
	}, 
	edit: function () {
		this.enabled = true;
		this.onclick.call(this);
	}
}//end prototype


_bhv.Combobox.ComboboxData = function (count) {
	this.count = count;
	this.currentCount = -1;
	this.currentIndex = -1;
	this.data = [];
	for (var i = 0; i < count; i++)
		this.data[i] = [];
};

_bhv.Combobox.ComboboxData.prototype = {
	parseJSON: function (json) {
		var rows = eval("(" + json + ")");
		if (rows && rows.length && (rows.length > 0)) {
			this.currentIndex = 0;
			this.currentCount = rows.length;
			for (var i = 0; i < rows.length; i++)
				for (var j = 0; j < rows[i].length; j++)
					if (rows[i][j])
						this.data[i][j] = rows[i][j];
					else
						this.data[i][j] = "";
		} else {
			this.currentIndex = -1;
		}
	}, 
	parseXML: function (xmlDocument) {
		var rows = bhv.scriptConteiner.responseJSON;
		if (rows && rows.length && (rows.length > 0)) {
			this.currentIndex = 0;
			this.currentCount = rows.length;
			for (var i = 0; i < rows.length; i++)
				for (var j = 0; j < rows[i].length; j++)
					if (rows[i][j])
						this.data[i][j] = rows[i][j];
					else
						this.data[i][j] = "";
		} else {
			this.currentIndex = -1;
		}
	}, 
	parseXML0: function (xmlDocument) {
		var rows = xmlDocument.getElementsByTagName("row");
		if (rows && rows.length && (rows.length > 0)) {
			this.currentIndex = 0;
			this.currentCount = rows.length;
			for (var i = 0; i < rows.length; i++)
				for (var j = 0; j < 3; j++)
					if (rows[i].childNodes[j].firstChild)
						this.data[i][j] = rows[i].childNodes[j].firstChild.data;
					else
						this.data[i][j] = "";
		} else {
			this.currentIndex = -1;
			this.currentCount = 0;
		}
	}, 
	getDisplayValue: function (rowIndex) {
		if (rowIndex >= this.currentCount)
			return false;
		return this.data[rowIndex][1];
	}, 
	getSearchValue: function (rowIndex) {
		if (rowIndex >= this.currentCount)
			return false;
		return this.data[rowIndex][2];
	}, 
	getKeyValue: function (rowIndex) {
		if (rowIndex >= this.currentCount)
			return false;
		return this.data[rowIndex][0];
	}, 
	getKeyIndex: function (rowKey) {
		for (var i = 0; i < this.currentCount; i++)
			if (this.getKeyValue(i) == rowKey)
				return i;
		return -1;
	},
	getCurrentDisplayValue: function () {
		if (this.currentIndex < 0) return "";
		return this.data[this.currentIndex][1];
	},
	getCurrentKey: function () {
		return this.data[this.currentIndex][0];
	},
	getCurrentSearchValue: function () {
		if (this.currentIndex < 0) return "";
		return this.data[this.currentIndex][2];
	},
	getCurrentAddonceValue: function (i) {
		if (this.currentIndex < 0) return "";
    		return this.data[this.currentIndex][2 + i];
	}

	
} // end prototype

function Constructor(element, valueElement, initialValue, count, table, keyColumn, displayValueColumn, searchValueColumn, exactly, filter, addonce) {
	this.init(element, valueElement, initialValue, count, table, keyColumn, displayValueColumn, searchValueColumn, exactly, filter,	addonce);
}

	
classes.ISA(Constructor.prototype, _bhv.Combobox.prototype)
/////////////////////////////////////////////////////////////////////////////
return Constructor;
});

