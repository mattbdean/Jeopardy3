<?php
require 'cfg/utils.php';
require 'classes/category.class.php';

$categories = getConfigJson("categories");
$columns = getConfigJson('constants')['categories'];
$sumbitted = isset($_POST['submit']);

/*
 * VALIDATION:
 * game-title: Less than 100 characters
 * game-creator: Between 4 and 35 characters (see http://stackoverflow.com/a/30509/1275092)
 * game-category: One of the categories in /cfg/categories.json
 * [0-5]_[0-5]-[answer/question]: Less than 500 characters
 * [0-5]-cat: Less than 50 characters
 */

/**
 * Tests if a POST variable with a given key is set. If it is, it
 * returns if the length of the value is not 0 and less than 500
 */
function validQuestionAnswer($varName) {
	if (isset($_POST[$varName])) {
		$var = $_POST[$varName];

		// Length is not 0 and less than 500
		return strlen($var) != 0 && strlen($var) < 500;
	}

	return false;
}

/**
 * Tests if a POST variable with a given key is set. If it is, it
 * returns if the length of the value is not 0 and less than 50.
 */
function validCategoryName($varName) {
	if (isset($_POST[$varName])) {
		$var = $_POST[$varName];

		// Not empty and less than 50 characters
		return strlen($var) != 0 && strlen($var) < 50;
	}

	return false;
}

/**
 * Tests if a POST variable with a given key is set. If it is, it
 * returns an HTML-compatible version of the value
 * (via htmlspecialcharacters)
 */
function getBasicValue($key) {
	if (isset($_POST[$key])) {
		return htmlspecialchars($_POST[$key]);
	}

	return '';
}

/**
 * Tests if a string starts with a certain string.
 * See: http://stackoverflow.com/a/834355/1275092
 */
function startsWith($haystack, $needle) {
	// 
	return !strncmp($haystack, $needle, strlen($needle));
}

// var_dump($_POST);

$parsed = [];
for ($i=0; $i < $columns; $i++) { 
	$parsed[$i] = new Category();
}

$gameTitle = getBasicValue('game-title');
$gameCreator = getBasicValue('game-creator');
$gameCategory = getBasicValue('game-category');

// Parse the POST data into a logical array of Category objects ($parsed)
foreach ($_POST as $key => $value) {
	for ($i=0; $i < $columns; $i++) { 
		if (startsWith($key, $i . '-cat')) {
			// Category name
			$parsed[$i]->name = $_POST[$key];
		} else if (startsWith($key, $i . '_')) {
			// Either question or answer
			$index = substr($key, strpos($key, '_') + 1, 1);
			if (strpos($key, 'question') !== false) {
				// It's a question
				$parsed[$i]->questions[$index] = $value;
			} else if (strpos($key, 'answer') !== false) {
				// It's an answer
				$parsed[$i]->answers[$index] = $value;
			}
		}
	}
}

// Data is now parsed into logically organized categories
?>

<!DOCTYPE html>
<html>
<head>
	<title>Create a Game | Classroom Jeopardy</title>
	<link rel="stylesheet" href="res/styles/create.css">
	<link rel="stylesheet" href="res/styles/general.css">
	<link rel="stylesheet" href="res/styles/header.css">
	<link rel="stylesheet" href="res/font/gyparody/stylesheet.css">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<script src="js/jquery-1.10.2.min.js"></script>
	<script src="js/create_hide.js"></script>
</head>
<body>
	<div id="content-wrapper">
		<div id="header-wrapper">
			<?php
			$pageTitle = 'Create a new game';
			require 'common/header.php';
			?>
		</div>
		
		<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
			<section id="game-meta" class="centered">
				<h1>Basic Info</h1>
				<label>Title:</label><input value="<?php echo $gameTitle ?>" name="game-title" type="text" autofocus required><br>
				<label>Your Name:</label><input value="<?php echo $gameCreator ?>" name="game-creator" type="text" required><br>
				<label>Category:</label>
				<select name="game-category" required>
					<option value="" disabled="disabled" selected="selected">Select a category</option>
					<?php
					ksort($categories);

					foreach ($categories as $name => $value) {
						// If the form was already submitted and a category
						// was chosen, then select that one by adding the
						// 'selected' attribute to the option.
						$selected = '';
						if (isset($_POST['game-category'])) {
							if ($value === $_POST['game-category']) {
								$selected = 'selected';
							}
						}
						echo sprintf('<option value="%s" %s>%s</option>', $value, $selected, $name);
					}
					?>
				</select><br>
			</section>
			<hr class="centered">
			<div id="game-data" class="centered">
				<h1>Questions and Answers</h1>
				<?php
				ob_start();
				for ($i = 0; $i < $columns; $i++) {
					echo '<section class="category-container">';

					$catName = 'Category ' . ($i + 1);
					if (isset($parsed[$i]->name)) {
						$catName = $parsed[$i]->name;
					}

					echo sprintf('<input type="text" name="%s-cat" class="category-name" value="%s">', $i, $catName);
					for ($j = 0; $j < 5; $j++) {
						echo '<div class="qa-container-data" data-index="' . $j . '">';
						echo '<p class="qa-label">Answer for $' . (($j + 1) * 100) . ': ';
						echo '<span class="qa-label-hint" style="display: none"></span></p>';
						echo '<div class="qa-container">';

						$answer = '';
						if (isset($parsed[$i]->answers[$j])) {
							$answer = htmlspecialchars($parsed[$i]->answers[$j]);
						}
						echo sprintf('<label>Answer:</label><input value="%s" name="%s_%s-answer" type="text"><br>', $answer, $i, $j);

						$question = '';
						if (isset($parsed[$i]->questions[$j])) {
							$question = htmlspecialchars($parsed[$i]->questions[$j]);
						}
						echo sprintf('<label>Question:</label><input value="%s" name="%s_%s-question" type="text"><br>', $question, $i, $j);

						// End qa-label-container
						echo '</div>';
						// End qa-container
						echo '</div>';
					}
					// End category-container
					echo '</section>';
				}
				ob_end_flush();
				?>
			</div>
			<div class="centered">
				<button id="create-game" name="submit">Create Game</button>
			</div>
		</form>
	</div>
</body>
</html>