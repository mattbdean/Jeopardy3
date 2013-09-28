<?php
require 'answer.class.php';
// Temporary variables for testing

$gameId = "sample";

function makeGameFor($gameId) {
	$ROW_COUNT = 5;

	// Parse an XML file from games/${id}.xml
	$xml = simplexml_load_file("games/$gameId.xml") or die("I couldn't seem to find that game. Sorry :\\");

	ob_start();
	echo '<table id="gameboard">';

	// Print the category headers
	echo '<tr>';
	foreach ($xml->column as $column) {
		echo '<th>' . $column->attributes()['name'] . '</th>';
	}
	echo '</tr>';

	// Print the questions
	for ($i = 0; $i < $ROW_COUNT; $i++) {
		echo '<tr>';
		foreach ($xml->column as $column) {
			echo '<td>' . $column->answerContainer[$i]->answer . '</td>';
		}
		echo '</tr>';
	}

	echo '</table>';

	ob_end_flush();
}
?>

<html>
<head>
	<title>Jeopardy 3</title>
	<link href='http://fonts.googleapis.com/css?family=Droid+Serif' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="res/font/gyparody/stylesheet.css">
	<link rel="stylesheet" href="res/styles/index.css">

</head>
<body>
	<div id="content-wrapper">
		<div id="header">
			<h1 id="header-text">Jeopardy!</h1>
		</div>
		<?php makeGameFor($gameId) ?>
	</div>
</body>
</html>