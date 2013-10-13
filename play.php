<?php
require 'cfg/utils.php';
define('ROW_COUNT', 5);
define('GAME_ID', $_GET['game']);

if (GAME_ID == 'sample') {
	$teams = [];
	for ($i=0; $i < 5; $i++) { 
		$teams[$i] = 'Team ' . ($i + 1);
	}
} else {
	$teams = $_GET['teams'];
}

// Check if the game ID is "sample" or not equal to 0 (a non-integer string input)
if (!($_GET['game'] === "sample" || intval($_GET['game']) != 0)) {
	echo "Uh oh!";
	die;
}

define('GAME_FILE', htmlspecialchars('games/' . $_GET['game'] . '.xml'));
define('TOTAL', count(simplexml_load_file(GAME_FILE)->column) * ROW_COUNT);

function makeGameFor($gameId, $teams) {

	// Parse an XML file from games/${id}.xml
	$xml = simplexml_load_file(GAME_FILE) or die("I couldn't seem to find that game. Sorry :\\");

	$total = ROW_COUNT * count($xml->column);

	ob_start();
	echo '<table class="gameboard">';

	// Print the category headers
	echo '<tr>';
	foreach ($xml->column as $column) {
		echo '<th>' . $column->attributes()['name'] . '</th>';
	}
	echo '</tr>';

	// Print the questions
	for ($i = 0; $i < ROW_COUNT; $i++) {
		echo '<tr>';
		for ($j=0; $j < count($xml->column); $j++) { 
			echo sprintf('<td class="jeoButton" data-row="%s" data-column="%s">$%s</td>', $i, $j, (($i + 1) * 100));
		}
		
		echo '</tr>';
	}

	echo '</table>';

	// Print the score
	echo '<div class="teams-container">';
	echo '<table class="teams">';

	// Team names
	echo '<tr>';
	foreach ($teams as $team) {
		echo '<th>' . htmlspecialchars($team) . '</th>';
	}
	echo '</tr>';

	// Default points of "$0"
	echo '<tr>';
	for ($i = 0; $i < count($teams); $i++) {
		echo sprintf('<td data-team-id="%s">$0</td>', $i);
	}
	echo '</tr>';
	echo '</table>';

	// End teams-container
	echo '</div>';

	ob_end_flush();
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Classroom Jeopardy</title>
	<!-- Google Fonts -->
	<link href='http://fonts.googleapis.com/css?family=Alfa+Slab+One' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Alegreya:700' rel='stylesheet' type='text/css'>
	<!-- Custom fonts -->
	<link rel="stylesheet" href="res/font/gyparody/stylesheet.css">
	<link rel="stylesheet" href="res/font/korinna/stylesheet.css">
	<!-- Stylesheets -->
	<link rel="stylesheet" href="res/styles/game/game.css">
	<link rel="stylesheet" href="res/styles/game/gameboard.css">
	<link rel="stylesheet" href="res/styles/game/scores.css">
	<link rel="stylesheet" href="res/styles/game/popup.css">
	<!-- jQuery -->
	<script src="js/jquery-1.10.2.min.js"></script>
	<!-- Scripts -->
	<script>
	// Expose the constants to JavaScript
	var GAME_ID = "<?php echo GAME_ID ?>";
	var TOTAL = <?php echo TOTAL ?>;
	<?php
	$js_teams = json_encode($teams);
	echo "var TEAMS = " . $js_teams . ";\n";
	?>
	</script>
	<script src="js/getdata.js"></script>
</head>
<body>
	<div id="content-wrapper">
		<div id="header">
			<h1 id="header-text">Jeopardy!</h1>
		</div>
		<?php makeGameFor(GAME_ID, $teams) ?>
		<div id="darkness"></div>
		<div id="popupQuestion">
			<p id="popupQuestionContent"></p>
			<div class="buttonContainer">
				<div class="showAnswerContainer">
					<button class="popupButton showAnswer">Show answer</button>
				</div>					<table class="teamButtonContainer">
				<?php
				function printTeamButtons($teams, $class) {
					for ($i=0; $i < count($teams); $i++) {
						echo sprintf('<td><button class="popupButton teamButton %s" data-team-id="%s">%s</button><td>', $class, $i, $teams[$i]);
					}
					echo '</tr>';
				}

				printTeamButtons($teams, "correct");
				printTeamButtons($teams, "incorrect");
				?>
			</table>
		</div>
	</div>
	<div id="endGameContent">
		<h1 id="endGameHeader"></h1>
		<h3 id="endGameSubheader"></h3>
		<!-- <p id="endGameSummary"></p> -->
	</div>
</div>
</body>
</html>