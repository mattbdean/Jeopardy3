<?php
require 'cfg/utils.php';
require 'classes/category.class.php';

$categories = getConfigJson("categories");
$columns = getConfigJson('constants')['categories'];
$submitted = isset($_POST['submit']);
$containsError = false;

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
function validQuestionAnswer($value) {
	if (strlen($value) === 0) {
		return 'No text given!';
	} else if (strlen($value) > 500) {
		return "This value can't be over 500 characters long!";
	}

	return '';
}

/**
 * Tests if a POST variable with a given key is set. If it is, it
 * returns if the length of the value is not 0 and less than 50.
 */
function validCategoryName($varName) {
	if (isset($_POST[$varName])) {
		$var = $_POST[$varName];

		if (strlen($var) === 0) {
			return 'No text given!';
		} else if (strlen($var) > 50) {
			return "The category name can't be over 50 characters long!";
		}
	}

	return '';
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
	return !strncmp($haystack, $needle, strlen($needle));
}


function displayError($message) {
	echo '<div class="error-message-container">';
	echo '<img class="error-icon" src="res/img/error.png">';
	echo sprintf('<p>%s</p>', $message);
	// End error-message-container
	echo '</div>';
}

$parsed = [];
for ($i=0; $i < $columns; $i++) { 
	$parsed[$i] = new Category();
}

$gameTitle = getBasicValue('game-title');
$gameCreator = getBasicValue('game-creator');
$gameCategory = getBasicValue('game-category');

if ($submitted) {

	// Parse the POST data into a logical array of Category objects ($parsed)
	foreach ($_POST as $key => $value) {
		if (preg_match("/cat-[0-5]-name/", $key)) {
			// Is a category name
			$catIndex = intval(substr($key, strlen('cat-'), 1));
			$parsed[$catIndex]->name = new QAData($value);
		} else if (preg_match("/questions-[0-5]/", $key)) {
			// Question array
			$catIndex = intval(substr($key, strlen('questions-'), 1));

			// For every question in the questions array
			for ($i=0; $i < count($value); $i++) {
				$parsed[$catIndex]->questions[$i] = new QAData($value[$i], validQuestionAnswer($value[$i]));
			}
		} else if (preg_match("/answers-[0-5]/", $key)) {
			// Answers array

			// For every answer in the answers array
			for ($i=0; $i < count($value); $i++) {
				$parsed[$catIndex]->answers[$i] = new QAData($value[$i], validQuestionAnswer($value[$i]));
			}
		}
	}
}

// Check for errors
foreach ($parsed as $category) {
	if (strlen($category->name->error) > 0) {
		$containsError = true;
	}

	// Stop searching if an error has been found
	if ($containsError) break;

	// Find all the errors in the answers
	foreach ($category->answers as $qadata) {
		// Stop searching if an error has been found
		if ($containsError) break;
		if (strlen($qadata->error) > 0) {
			// Contains an error
			$containsError = true;
		}
	}

	// Stop searching if an error has been found
	if ($containsError) break;
	// Find all the errors in the questions
	foreach ($category->questions as $qadata) {
		// Stop searching if an error has been found
		if ($containsError) break;
		if (strlen($qadata->error) > 0) {
			// Contains an error
			$containsError = true;
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
<?php if ($containsError || !$submitted) { ?>
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
					if (is_object($parsed[$i]->name)) {
						if (strlen($parsed[$i]->name->value) != 0) {
							$catName = $parsed[$i]->name->value;
						} else if (strlen($parsed[$i]->name->error) != 0) {
							displayError($parsed[$i]->name->error);
						}
					}
					
					echo sprintf('<input type="text" name="cat-%s-name" class="category-name" value="%s">', $i, $catName);
					for ($j = 0; $j < 5; $j++) {
						echo '<div class="qa-container-data" data-index="' . $j . '">';
						echo '<p class="qa-label">Answer for $' . (($j + 1) * 100) . ': ';
						echo '<span class="qa-label-hint" style="display: none"></span></p>';
						echo '<div class="qa-container">';

						// Display the answer
						$answer = '';
						// Check if the value is set
						if (isset($parsed[$i]->answers[$j]->value)) {
							$answer = htmlspecialchars($parsed[$i]->answers[$j]->value);
						}
						// Check if there's an error
						if (isset($parsed[$i]->answers[$j]->error)) {
							if (strlen($parsed[$i]->answers[$j]->error) != 0) {
								displayError($parsed[$i]->answers[$j]->error);
							}
						}
						
						echo sprintf('<label>Answer:</label><input value="%s" name="answers-%s[]" type="text"><br>', $answer, $i);

						// Display the question
						$question = '';
						// Check if the value is set
						if (isset($parsed[$i]->questions[$j]->value)) {
							$question = htmlspecialchars($parsed[$i]->questions[$j]->value);
						}
						// Check if there's an error
						if (isset($parsed[$i]->questions[$j]->error)) {
							if (strlen($parsed[$i]->questions[$j]->error) != 0) {
								displayError($parsed[$i]->questions[$j]->error);
							}
						}
						
						echo sprintf('<label>Question:</label><input value="%s" name="questions-%s[]" type="text"><br>', $question, $i, $j);

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
<?php } else { ?>

<body>
	<div class="centered-text">
		<p>Please wait, I'm processing your game now...</p>

		<?php
		// Error free!
		// Do some work in the DB
		$hostname = 'localhost';
		$username = 'jeopardy';
		$passowrd = 'jeopardy';
		$id = mt_rand(0, 1000000);

		try {
			$dbh = new PDO("mysql:host=$hostname;dbname=jeopardy", $username, $passowrd);
			$prepared = $dbh->prepare('INSERT INTO games (game_id, file_name, date_created, category, game_name, creator_name) VALUES (:game_id, :file_name, :date_created, :category, :game_name, :creator_name);');
			$gameFile = $id . '.xml';
			$prepared->execute([
				':game_id' => $id,
				':file_name' => $gameFile,
				':date_created' => date('Y-m-d H:i:s', time()),
				':category' => $gameCategory,
				':game_name' => $gameTitle,
				':creator_name' => $gameCreator
				]);

			// http://stackoverflow.com/a/60496/1275092
			$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// Close the db connection
			$dbh = null;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}

		// Generate the XML file

		// Create an array that organizes the question/answer like the XML file would.
		// For each category, there are 5 answer containers. The contain 2 elements,
		// an answer, and a question.
		$xmlReady = [];
		for ($i = 0; $i < count($parsed); $i++) { 
			$xmlReady[$i] = [];
			for ($j = 0; $j < 5; $j++) {
				// Represents the answerContainer element
				$xmlReady[$i][$j] = [$parsed[$i]->answers[$j]->value, $parsed[$i]->questions[$j]->value];
			}
		}

		// Prepare yourself. Messy DOM work is coming.
		$xml = new DOMDocument();
		$xmlRoot = $xml->appendChild($xml->createElement('jeopardy'));
		for ($i = 0; $i < count($parsed); $i++) {
			$category = $parsed[$i];

			$xmlCategory = $xml->createElement('column');
			$xmlCategory->setAttribute('name', $category->name->value);

			// Iterate over each answerContainer
			for ($j=0; $j < count($xmlReady[$i]); $j++) { 
				$xmlAnswerContainer = $xml->createElement('answerContainer');
				$xmlAnswer = $xml->createElement('answer');
				$xmlAnswer->appendChild($xml->createTextNode($xmlReady[$i][$j][0]));
				$xmlAnswerContainer->appendChild($xmlAnswer);

				$xmlQuestion = $xml->createElement('question');
				$xmlQuestion->appendChild($xml->createTextNode($xmlReady[$i][$j][1]));
				$xmlAnswerContainer->appendChild($xmlQuestion);

				$xmlCategory->appendChild($xmlAnswerContainer);
			}

			$xmlRoot->appendChild($xmlCategory);
		}

		// Enable this to see pretty XML. Disable to conserve disk space/read speeds
		$xml->formatOutput = true;
		$xml->save('games/' . $gameFile);

		$redirectURL = 'gameinfo.php?' . http_build_query(array('id' => $id));
		echo sprintf('<p>Please click <a href=%s>here</a> if you are not automatically redirected</p>', $redirectURL);
		header('Location: ' . $redirectURL);
		?>

		<?php } ?>
	</div>
</body>
</html>
