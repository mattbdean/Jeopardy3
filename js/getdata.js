/*global jQuery, $, GAMEID*/
/*jslint browser: true, white: true, devel:true */

function JeoButton(row, column, $jQuery) {
	"use strict";
	this.row = parseInt(row, 10);
	this.column = parseInt(column, 10);
	this.value = parseInt($jQuery[0].innerHTML.replace("$", ""), 10);
	this.$jQuery = $jQuery;
}

function resetPopup() {
	"use strict";
	$('.showAnswer').show();
	var $teamButtonContainer = $('.teamButtonContainer');
	$teamButtonContainer.hide();
	// Unbind
	$teamButtonContainer.find('.teamButton').each(function() {
		$(this).unbind('click');
	});
	$('#popupQuestionContent').text('');
}

function killPopup() {
	"use strict";
	console.log('killed');
	// Fade the darkness and popup
	$('#darkness, #popupQuestion').fadeTo(200, 0);
	// And when it's done, hide it so that other elements can be
	// clicked on
	$('#darkness, #popupQuestion').promise().done(function() {
		$(this).hide();
	});
}

function registerTeamButtonHandlers(jeoButton) {
	"use strict";
	var $container = $(".teamButtonContainer");
	$container.find('.teamButton').click(function() {
		var add = $(this).hasClass('correct');
		var teamButtonId = parseInt($(this).attr('data-team-id'), 10);

		// Select the 2nd row of the teams table (the scores)
		$('.teams').find('td[data-team-id]').each(function() {
			// If the team ID's are the same
			if (parseInt($(this).attr('data-team-id'), 10) === teamButtonId) {
				// Get the current and new value
				var oldValue = parseInt($(this).text().replace("$", ""), 10);
				var newValue = add ? oldValue + jeoButton.value : oldValue - jeoButton.value;

				// Set the <td> text to the new dollar value
				$(this).text("$" + newValue);
				killPopup();
			}
		});
	});
}

$(function() {
	"use strict";
	$('.gameboard').find('.jeoButton').click(function() {
		// Unbind the button
		$(this).unbind('click');
		// Construct a JeoButton object
		var jeoButton = new JeoButton($(this).attr('data-row'), $(this).attr('data-column'), $(this));
		$(this).addClass('clicked');
		$(this).text('');
		resetPopup();

		$.ajax({
			url: "getdata.php",
			type: "get",
			dataType: "json",
			data: {id: GAMEID, row: jeoButton.row, column: jeoButton.column},
			success: function(data) {
				$("#darkness, #popupQuestion").fadeTo(200, 1);
				$("#popupQuestionContent").text(data.answer);
				$("#popupQuestion").find('.showAnswer').first().click(function() {
					$(this).unbind('click');
					$(this).hide();
					$("#popupQuestionContent").append("<br><br>" + data.question);
					$(".teamButtonContainer").show();
					registerTeamButtonHandlers(jeoButton);
				});
			},
			error: function(jqXHR) {
				console.error(jqXHR.responseText);
				alert("Error: " + jqXHR.responseText);
			}
		});
	});
});