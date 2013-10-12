/*global $, MIN, MAX*/
/*jslint browser:true, white:true, devel:true, plusplus:true, vars:true */

function getTeamCount() {
	"use strict";
	// Find all the inputs whose name is "teamNames[]" and is visible
	return $("#play-setup-teams").find('input[name="teams[]"]:visible').length;
}

function getTeam(index) {
	"use strict";
	return $($('#play-setup-teams').find('div')[index]);
}

$(function() {
	"use strict";
	$("#play-setup").click(function() {
		$('#play-setup-teams').show('slow');
		$(this).hide('slow');
	});

	$('#add-team').click(function() {
		var teamCount = getTeamCount();
		if (teamCount < MAX) {
			getTeam(teamCount).show(200);

			// Use the new amount of teams to see if the button should be disabled
			if (getTeamCount() === MAX) {
				$(this).attr('disabled', true);
			}
		}

		// Enable the remove-team button if it was disabled
		$('#remove-team[disabled]').attr('disabled', false);
	});

	$('#remove-team').click(function() {
		var teamCount = getTeamCount(),
			$button = $(this);

		if (teamCount > MIN) {
			getTeam(teamCount - 1).hide(200, function() {
				// Update teamCount
				teamCount = getTeamCount();
				if (teamCount === MIN) {
					$button.attr('disabled', true);
				}
			});
		}

		// Enable the remove-team button if it was disabled
		$('#add-team[disabled]').attr('disabled', false);
	});
});