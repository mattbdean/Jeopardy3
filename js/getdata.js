/*global jQuery, $, GAME_ID, TOTAL, TEAMS*/
/*jslint browser: true, white:true, devel:true, plusplus:true */

var answered = 0;

function JeoButton(row, column, $jQuery) {
	"use strict";
	this.row = parseInt(row, 10);
	this.column = parseInt(column, 10);
	this.value = parseInt($jQuery[0].innerHTML.replace("$", ""), 10);
	this.$jQuery = $jQuery;
}

function Team(name, score) {
	"use strict";
	this.name = name;
	this.score = score;
}

function generateGameReport() {
	"use strict";

	var teams = {};
	var counter = 0;
	$('.teams td[data-team-id]').each(function() {
		teams[counter] = new Team(TEAMS[counter], parseInt($(this).text().replace("$", ""), 10));
		counter++;
	});

	var highestScore = 0;
	// Get the team(s) with the highest scores
	for (var i = 0; i < counter; i++) {
		if (teams[i].score > highestScore) {
			highestScore = teams[i].score;
		}
	}

	var winningTeams = {};
	var winningTeamsCounter = 0;
	for (var i = 0; i < counter; i++) {
		if (teams[i].score === highestScore) {
			winningTeams[++winningTeamsCounter] = teams[i];
		}
	}

	var headerText = 'Congratulations,';
	var subheaderText = '';
	if (winningTeamsCounter === 1) {
		subheaderText = winningTeams[1].name + "!";
	} else if (winningTeamsCounter === 2) {
		subheaderText = winningTeams[1].name + " and " + winningTeams[2].name + "!";
	} else {
		// More than 3 winning teams
		for (var i = 0; i < winningTeamsCounter; i++) {
			if (i === winningTeamsCounter - 1) {
				subheaderText += " and " + winningTeams[i + 1].name;
			} else {
				subheaderText += winningTeams[i + 1].name + ", ";
			}
		}
	}

	$('#endGameHeader').text(headerText);
	$('#endGameSubheader').text(subheaderText);
	$('#endGameContent').show(400);
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
	// Fade the darkness and popup
	$('#darkness, #popupQuestion').fadeTo(200, 0);
	// And when it's done, hide it so that other elements can be
	// clicked on
	$('#darkness, #popupQuestion').promise().done(function() {
		$(this).hide();
	});

	// Check for the end of the game
	answered += 1;
	if (answered === TOTAL) {
		$('.gameboard').fadeOut(400, function() {
			$('#endGameContent').toggle(400);
			generateGameReport();
		});
	}
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
				var valueStr;
				if (newValue < 0) {
					valueStr = "-$" + Math.abs(newValue);
				} else {
					valueStr = "$" + newValue;
				}
				$(this).text(valueStr);
				killPopup();
			}
		});
	});
}

$(function() {
	"use strict";
	$('#endGameContent').hide();
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
			data: {id: GAME_ID, row: jeoButton.row, column: jeoButton.column},
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