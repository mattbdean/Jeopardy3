<?php
// var_dump($_GET);
if (!(isset($_GET['id']) || isset($_GET['row']) || isset($_GET['column']))) {
	echo 'Empty parameter(s)!';
	die;
}
if (!isValidGameId($_GET['id'])) {
	echo 'Invalid game ID: ' . $_GET['id'];
	die;
} else if (!isValidInt($_GET['row'])) {
	echo 'Invalid row!';
	die;
} else if (!isValidInt($_GET['column'])) {
	echo 'Invalid column!';
	die;
}

// Change response type to JSON
header('Content-Type: application/json');
$xml = simplexml_load_file('games/' . $_GET['id'] . '.xml');
$container = $xml->column[(int) $_GET['column']]->answerContainer[(int) $_GET['row']];

// echo "{}";
echo json_encode(array('answer' => (string) $container->answer, 'question' => (string) $container->question));

/*
 * Returns false if the given input is not set, not an int, or is empty.
 */
function isValidInt($input) {
	if (!isset($input) || $input === "" || !is_numeric($input)) {
		return false;
	}

	return true;
}

/*
 * Returns true if the input equals "sample", else returns the value of isValidInt()
 */
function isValidGameId($input) {
	if ($input == "sample") {
		return true;
	}
	
	return isValidInt($input);
}
?>