<?php
// Temporary variables for testing

$gameId = "0";

function makeGameFor($gameId) {
	// Parse an XML file from games/${id}.xml
	$file = "games/$gameId.xml";
	$xml = simplexml_load_file($file) or die("Could not load file");

	// ob_start();

	// For each column element,
	$columnAmount = count($xml->children());
	foreach ($xml->children() as $column) {
		echo '<div class="column" style="max-width: ' . 100 / $columnAmount . '%">';
		echo '<div class="column-header">';
		echo $column->attributes()['name'];
		// End column-header
		echo '</div>';
		// For each answerContainer in the column,
		// for ($column->children() as $answerContainer) {
		$columnChildren = $column->children();
		for ($i = 0; $i < count($columnChildren); $i++) {
			$answerContainer = $columnChildren[$i];
			echo '<div class="column-answer">';
			echo '$' . (($i + 1) * 100) . '<br>';
			// End column-answer
			echo '</div>';
		}

		// End column
		echo '</div>';
	}
	// ob_end_flush();
}
?>

<html>
<head>
	<title>Jeopardy 3</title>
	<link rel="stylesheet" href="res/styles/index.css">
</head>
<body>
	<div id="content-wrapper">
		<div id="header">
			<h1 id="header-text">Jeopardy!</h1>
		</div>
		<div id="gameboard">
			<?php makeGameFor($gameId) ?>
		</div>
	</div>
</body>
</html>