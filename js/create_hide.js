/*global $*/
/*jslint browser: true, white:true, devel:true, plusplus:true */

var icons = {};
icons.minus = 'res/img/minus.png';
icons.plus = 'res/img/plus.png';


function toggleIcon($icon) {
	"use strict";
	var newIcon = $icon.attr('src') === icons.minus ? icons.plus : icons.minus;
	// Set the "src" attribute to the new icon
	$icon.attr('src', newIcon);
}


function changeAnswerContainer($clickedIcon, mode) {
	"use strict";

	$clickedIcon.siblings('.question-answer-label-container[data-index]').each(function() {
		if ($(this).attr('data-index') === $clickedIcon.attr('data-index')) {
			switch (mode) {
				case 'toggle':
					$(this).toggle('medium');
					toggleIcon($clickedIcon);
					break;
				case 'hide':
					$(this).hide('medium');
					$clickedIcon.attr('src', icons.minus);
					break;
				case 'show':
					$(this).show('medium');
					$clickedIcon.attr('src', icons.plus);
					break;
			}
		}
	});
}

function toggleCategory($clickedIcon) {
	"use strict";
	$clickedIcon.siblings('.expand-contract-icon-2').each(function() {
		if ($(this).is(':hidden')) {
			changeAnswerContainer($(this), 'show');
		} else {
			changeAnswerContainer($(this), 'hide');
		}
		toggleIcon($(this));
	});

	// Toggle the visibility of all the answer container icons, question/answer labels, and question/answer label containers
	$clickedIcon.siblings(".expand-contract-icon-2, .question-answer-label").toggle('medium');

	toggleIcon($clickedIcon);
}

$(function() {
	"use strict";
	// Look for Category icon presses
	// (icons next to categories do not have the expand-contract-icon-2 class)
	$('.expand-contract-icon:not(.expand-contract-icon-2)').click(function() {
		toggleCategory($(this));
	});

	// Look for icons next to "Answer for"
	$('.expand-contract-icon.expand-contract-icon-2').click(function() {
		changeAnswerContainer($(this), 'toggle');
	});
});