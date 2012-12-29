/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

$(function(){

var flags = {
	'text': [
		{ value: 'x', label: 'is exactly' },
		{ value: 'c', label: 'contains' },
		{ value: 's', label: 'starts with' },
		{ value: 'e', label: 'ends with' },
	    { value: 'i', label: 'is not' }
	],
	'number': [
	    { value: 'x', label: 'is exactly' },
	    { value: 'g', label: 'is greater than' },
	    { value: 'l', label: 'is less than' },
	    { value: 'i', label: 'is not' }
	],
	'boolean-text': [
   	   	    { value: 'x', label: 'is exactly' },
	   	    { value: 'i', label: 'is not' }
	],
	'select': [
	   	    { value: 'x', label: 'is exactly' },
	   	    { value: 'i', label: 'is not' }
	],
};

var typeOptions = [];
if (document.operations) {
	for (var i = 0; i < document.operations.length; i++) {
		typeOptions.push({ value: i + 1, label: document.operations[i] });
	}
}

searchForm('#LatestSearch', {
	'result': { columnType: 'text', helptext: 'e.g. FAILED' },
	'clientid': { columnType: 'text' },
	'job': { columnType: 'text' },
	'reporttime': { columnType: 'number', helptext: 'YYYY/MM/DD [ hh:mm:ss ]' },
	'errors': { columnType: 'number' }
});

searchForm('#ClientsSearch', {
	'clientid': { columnType: 'text' },
	'totalreports': { columnType: 'number' },
	'tags': { columnType: 'boolean-text' }
});

searchForm('#ReportsSearch', {
	'result': { columnType: 'text', helptext: 'e.g. FAILED' },
	'clientid': { columnType: 'text' },
	'job': { columnType: 'text' },
	'reporttime': { columnType: 'number', helptext: 'YYYY/MM/DD [ hh:mm:ss ]' },
	'duration': { columnType: 'number', helptext: 'in seconds or hh:mm:ss' },
	'uploadsize': { columnType: 'number', helptext: 'in bytes or follow with KB/MB/GB' },
	'changedfiles': { columnType: 'number' },
	'errors': { columnType: 'number' }
});

searchForm('#ItemsSearch', {
	'logorder': { columnType: 'number' },
	'type': {
		columnType: 'select',
		options: typeOptions
	},
	'path': { columnType: 'text' },
	'size2': { columnType: 'number', helptext: 'in bytes or follow with KB/MB/GB' },
	'starttime': { columnType: 'number', helptext: '[ YYYY/MM/DD ] hh:mm:ss' },
	'duration': { columnType: 'number', helptext: 'in seconds or hh:mm:ss' },
	'result': { columnType: 'text', helptext: 'e.g. OK, xSocketTimeout' },
	'detail': { columnType: 'text' }
});

searchForm('#FileSearch', {
	'job': { columnType: 'text' },
	'reporttime': { columnType: 'number', helptext: 'YYYY/MM/DD [ hh:mm:ss ]' },
	'type': {
		columnType: 'select',
		options: typeOptions
	},
	'path': { columnType: 'text' },
	'size2': { columnType: 'number', helptext: 'in bytes or follow with KB/MB/GB' },
	'duration': { columnType: 'number', helptext: 'in seconds or hh:mm:ss' },
	'result': { columnType: 'text', helptext: 'e.g. OK, xSocketTimeout' },
	'detail': { columnType: 'text' }
});

function searchForm(selector, config) {
	$(selector).each(function(){
		var $this = $(this);
		var column = $('select:eq(0)', $this);
		var flag = $('select:eq(1)', $this);
		var text = $('input[type=text]', $this);
		var textParent = text.parent();
		var select = $('<select>').attr('name', 'search[text]');
		var helptext = '';
		
		text.addClass('helptext').focus(function() {
			if (text.hasClass('helptext')) {
				text.val('').removeClass('helptext');
			}
		}).blur(function() {
			if (text.val() == '') {
				text.val(helptext).addClass('helptext');
			}
		});
		
		// Setup the flags list when the column changes, and reset the 'text' box (optionally with helptext).
		column.bind('keyup change', function() {
			// Determine which flags will be used.
			var colConfig = config[column.val()];
			
			// Optionally set helptext.
			helptext = $.isDefined(colConfig.helptext) ? colConfig.helptext : '';
			
			// Reset the text box and optionally set helptext.
			//text.val('').blur();
			
			// Add the flags for this column.
			flag.empty();
			if (colConfig.columnType) {
				// Add the flags for this type.
				for (var i = 0; i < flags[colConfig.columnType].length; i++) {
					$('<option>').attr('value', flags[colConfig.columnType][i].value).text(flags[colConfig.columnType][i].label).appendTo(flag);
				}
				
				// Select 'is exactly'
				flag.val('x');
				
				if (colConfig.columnType == 'select') {
					text.detach();
					select.empty().appendTo(textParent);
					
					for (var i = 0; i < colConfig.options.length; i++) {
						select.append($('<option>').attr('value', colConfig.options[i].value).text(colConfig.options[i].label));
					}
					
					select[0].selectedIndex = 0;
				}
				else {
					select.detach();
					
					// Reset the text box and optionally set helptext.
					text.appendTo(textParent).val('').blur();
				}
			}
		});
		
		// Init the form.
		column[0].selectedIndex = 0;
		column.change();
		
		// Set a default value to the search boxes.
		// Used primarily when a criteria is removed.
		if ($.isDefined(document.initialSearchValues)) {
			column.val(document.initialSearchValues.column);
			column.change();
			flag.val(document.initialSearchValues.flag);
			text.focus().val(document.initialSearchValues.text);
		}
	});
}

});