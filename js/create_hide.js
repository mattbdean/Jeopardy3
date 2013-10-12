/*global $*/
/*jslint browser: true, white:true, devel:true, plusplus:true, vars:true */

function makeHint(answer, question, $hint) {
	"use strict";

	var cutoff = 30;
	if (answer.length > cutoff) {
		answer = answer.substring(0, cutoff) + "...";
	}
	if (question.length > cutoff) {
		question = question.substring(0, cutoff) + "...";
	}

	if (answer.length === 0) {
		answer = '<span class="red">empty</span>';
	}
	if (question.length === 0) {
		question = '<span class="red">empty</span>';
	}

	$hint.html('(' + answer + ', ' + question + ')');
}

$(function() {
	"use strict";
	$('.qa-label').click(function() {
		var $qaLabel = $(this);
		$(this).siblings('.qa-container').toggle('medium');

		var $hint = $qaLabel.children('.qa-label-hint');
		$hint.toggle('medium');

		var $inputs = $qaLabel.siblings('.qa-container').children('input[type="text"]');
		var answer = $inputs[0].value;
		var question = $inputs[1].value;
		makeHint(answer, question, $hint);
	});

	$('#game-data input[type="text"], #game-meta input[type="text"]').keyup(function() {
		if ($(this).val().length === 0) {
			// Now empty/invalid
			$(this).addClass('error');
		} else if ($(this).hasClass('error') && $(this).val().length !== 0) {
			// Was empty/invalid before this, now isn't
			$(this).removeClass('error');
		}
	});
});