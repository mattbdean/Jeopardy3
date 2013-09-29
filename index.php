<?php
// Temporary variables for testing

$gameId = "sample";
$teams = [];
for ($i = 0; $i < 5; $i++) {
	$teams[$i] = 'Team ' . ($i + 1);
}

function makeGameFor($gameId, $teams) {
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
		for ($j=0; $j < count($xml->column); $j++) { 
			echo sprintf('<td class="jeobutton" data-row="%s", data-column="%s">$%s</td>', $i, $j, (($i + 1) * 100));
		}
		
		echo '</tr>';
	}

	echo '</table>';

	echo '<table id="teams">';

	echo '<tr>';
	foreach ($teams as $team) {
		echo '<th>' . $team . '</th>';
	}
	echo '</tr>';

	echo '<tr>';
	foreach ($teams as $team) {
		echo '<td>$' . 0 . '</td>';
	}
	echo '</tr>';
	echo '</table>';

	ob_end_flush();
}
?>

<html>
<head>
	<title>Classroom Jeopardy</title>
	<link href='http://fonts.googleapis.com/css?family=Alfa+Slab+One' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Alegreya:700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="res/font/gyparody/stylesheet.css">
	<link rel="stylesheet" href="res/styles/index.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script>
	// Make the id var global to js/getdata.js
	var id = "<?php echo $gameId ?>";
	</script>
	<script src="js/getdata.js"></script>
</head>
<body>
	<div id="content-wrapper">
		<div id="header">
			<h1 id="header-text">Jeopardy!</h1>
		</div>
		<?php makeGameFor($gameId, $teams) ?>
	</div>
</body>
</html>