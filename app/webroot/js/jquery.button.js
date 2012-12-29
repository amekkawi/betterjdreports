/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

(function($) {
	
	$.widget("ui.button", {
		
		options: {
			enabled: true,
			pressed: null,
			icon: null,
			'float': 'left',
			hover: true,
			color: null,
			dark: false,
			focusable: true,
			allowActive: true,
			cursor: null
		},
		
		_create: function() {
			var self = this, elem = this.element;
			elem.addClass(this.widgetBaseClass).disableSelection();
			
			// Wrap the contents
			if (elem.html() == '') elem.append('<div></div');
			else elem.wrapInner('<div></div>');
			
			// Set the wrapper to '_inner'
			this._inner = elem.children().eq(0).addClass(this.widgetBaseClass + '-inner');
			
			// Configure the button.
			this.enabled(this.options.enabled);
			this['float'](this.options['float']);
			this.focusable(this.options.focusable);
			this.color(this.options.color);
			this.dark(this.options.dark);
			this.hover(this.options.hover);
			this.icon(this.options.icon);
			this.cursor(this.options.cursor);
			this.allowActive(this.options.allowActive);
			
			// Set up the click handler.
			elem.click(function(e){
				self._trigger('pressed', e);
			});
		},
		
		color: function(color) {
			if ($.isString(color)) {
				if (this.options.color) this.element.removeClass(this.widgetBaseClass + '-' + this.options.color);
				this.element.addClass(this.widgetBaseClass + '-' + color);
				this.options.color = color;
			}
			else {
				return this.options.color;
			}
		},
		
		text: function(text) {
			if ($.isString(text)) {
				this._inner.text(text);
			}
			else {
				return this._inner.text();
			}
		},
		
		pressed: function(fn, e) {
			if ($.isFunction(fn)) {
				this.element.bind(this.widgetEventPrefix + 'pressed', fn);
			}
			else {
				this._trigger('pressed', e);
			}
		},
		
		enabled: function(val) {
			if ($.isBoolean(val)) {
				if (val) this.element.removeClass(this.widgetBaseClass + '-disabled');
				else this.element.addClass(this.widgetBaseClass + '-disabled');
				this.options.enabled = val;
			}
			else {
				return this.options.enabled;
			}
		},
		
		icon: function(url) {
			if ($.isString(url)) {
				if (url == '') {
					this.element.removeClass(this.widgetBaseClass + '-icon');
					this._inner.css('background-image', '');
					this.options.icon = null;
				}
				else {
					this.element.addClass(this.widgetBaseClass + '-icon');
					this._inner.css('background-image', url);
					this.options.icon = url;
				}
			}
			else {
				return this.options.icon;
			}
		},
		
		dark: function(val) {
			if ($.isBoolean(val)) {
				if (val) this.element.addClass(this.widgetBaseClass + '-dark');
				else this.element.removeClass(this.widgetBaseClass + '-dark');
				this.options.dark = !!val;
			}
			else {
				return this.options.dark;
			}
		},
		
		hover: function(val) {
			if ($.isBoolean(val)) {
				if (val) this.element.addClass(this.widgetBaseClass + '-hover');
				else this.element.removeClass(this.widgetBaseClass + '-hover');
				this.options.hover = !!val;
			}
			else {
				return this.options.hover;
			}
		},
		
		allowActive: function(val) {
			if ($.isBoolean(val)) {
				if (val) this.element.removeClass(this.widgetBaseClass + '-disableactive');
				else this.element.addClass(this.widgetBaseClass + '-disableactive');
				this.options.allowActive = !!val;
			}
			else {
				return this.options.allowActive;
			}
		},
		
		focusable: function(val) {
			var self = this, elem = this.element;
			if ($.isBoolean(val)) {
				if (val) {
					elem
						.attr('tabindex', '0')
						.addClass(this.widgetBaseClass + '-focusable')
						.bind('keydown.' + this.widgetEventPrefix, function(e){
							if (e.keyCode == 13 || e.keyCode == 32) {
								elem.addClass(self.widgetBaseClass + '-active');
								e.preventDefault();
							}
						})
						.bind('keyup.' + this.widgetName, function(e) {
							if (e.keyCode == 13 || e.keyCode == 32) {
								elem.removeClass(self.widgetBaseClass + '-active');
								self._trigger('pressed', e);
							}
						});
				}
				else {
					elem
						.removeAttr('tabindex')
						.removeClass(this.widgetBaseClass + '-focusable')
						.unbind('keydown.' + this.widgetName)
						.unbind('keyup.' + this.widgetName);
				}
				
				this.options.focusable = !!val;
			}
			else {
				return this.options.focusable;
			}
		},
		
		'float': function(val) {
			if ($.isString(val)) {
				this.element.css('float', this.options['float']);
				this.options['float'] = val;
			}
			else {
				return this.options['float'];
			}
		},
		
		cursor: function(val) {
			if ($.isString(val)) {
				this.element.css('cursor', val);
				this.options.cursor = val;
			}
			else {
				return this.options.cursor;
			}
		}
	});
	
	//$.extend($.ui.button, );
	
})(jQuery);
