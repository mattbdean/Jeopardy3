/*global $*/
/*jslint browser: true, white:true, devel:true, plusplus:true, vars:true */

var icons = {};
icons.collapse = 'res/img/collapse.png';
icons.expand = 'res/img/expand.png';

$(function() {
	"use strict";

	$('#game-data input[type="text"], #game-meta input[type="text"]').keydown(function() {
		var previousVal = $(this).val();

		$(this).keyup(function() {
			$(this).unbind('keyup');

			// Capture the value before the keyevent
			var val = $(this).val();
			// Make sure the value has changed to prevent giving the input an error
			// class when the button pressed was something like control or tab
			if (val !== previousVal) {
				if (val.length === 0) {
					// Now empty/invalid
					$(this).addClass('error');
				} else if ($(this).hasClass('error') && val.length !== 0) {
					// Was empty/invalid before this, now isn't
					$(this).removeClass('error');
				}
			}

		});
	});

	$('.expand-contract-icon').click(function() {
		$(this).parent().siblings('.minimizable-content').first().toggle(400);

		// Toggle the icons
		$(this).attr('src', $(this).attr('src') === icons.collapse ? icons.expand : icons.collapse);
	});
});