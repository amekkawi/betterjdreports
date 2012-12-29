/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

$(function(){
	
	var createCookie = function(name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
	};

	var readCookie = function(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	};

	var eraseCookie = function(name) {
		createCookie(name,"",-1);
	};

	if (document.dblog && document.dblog.length > 0) {
		var container = $('<div>').attr('id', 'DBLog').css({
			//'position': 'absolute',
			//'left': '0',
			//'top': '0'
			'margin-bottom': '10px'
		});
		
		var toggle = $('<div>').text('Show DB Log').css({
			'background-color': '#CCC',
			'border': '1px solid #666',
			'padding': '4px'
		}).disableSelection().appendTo(container);
		
		var inner = $('<div>').css({
			'padding': '6px',
			'background-color': '#FFF'
		}).hide().appendTo(container);
		
		toggle.click(function() {
			if (inner.is(':visible')) {
				inner.hide();
				toggle.text('Show DB Log');
				eraseCookie('dblog');
			}
			else {
				inner.show();
				toggle.text('Hide DB Log');
				createCookie('dblog', 'Y', 1);
			}
		});
		
		var list = $('<ul>').appendTo(inner);
		for (var i = 0; i < document.dblog.length; i++) {
			var item = $('<li>').text('Query ' + (i + 1) + ' (' + document.dblog[i].type + ')').appendTo(list);
			var innerlist = $('<ul>').appendTo(item);
			$('<li>').text(document.dblog[i].sql).appendTo(innerlist);
			$('<li>').text('Time: ' + document.dblog[i].time).appendTo(innerlist);
			if (document.dblog[i].error != null) {
				$('<li>').text('Error: ' + document.dblog[i].error).appendTo(innerlist);
				$('<li>').text('Detail: ' + document.dblog[i].errorinfo).appendTo(innerlist);
			}
			if (document.dblog[i].records != null) {
					$('<li>').text('Records: ' + document.dblog[i].records).appendTo(innerlist);
			}
			if (document.dblog[i].affected != null) {
				$('<li>').text('Affected: ' + document.dblog[i].affected).appendTo(innerlist);
			}
		}
		
		if (readCookie('dblog') == "Y") toggle.click();
		
		container.prependTo('body');
	}
	
});