/*global jQuery, $*/
/*jslint browser: true, white: true, devel:true */

$(document).ready(function() {
	"use strict";
	$(".jeobutton").mouseup(function() {
		var $button = $(this);
		$('#darkness').fadeTo(200, 1);
		$('#popupQuestion').fadeTo(200, 1);
		$.ajax({ url: 'getdata.php',
			data: {id: id, row: $button.attr("data-row"), column: $button.attr("data-column")},
			type: 'get',
			dataType: 'json',
			success: function(data) {
				// Answer: data.answer
				// Question: data.question
				$button.css('opacity', '0');
				$('#popupQuestionContent').text(data.answer);
			},
			error: function(xhr) {
				alert("Error: " + xhr.responseText);
				console.error(xhr.responseText);
			}
		});
	});
});