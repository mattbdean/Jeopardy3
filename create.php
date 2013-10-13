<?php
require 'cfg/utils.php';
require 'classes/category.class.php';

$categories = getConfigJson("categories");
$columns = getConfigJson('constants')['categories'];
$submitted = isset($_POST['submit']);
$containsError = false;

/**
 * Returns the reason why this value is valid, or if it is valid,
 * an empty string.
 */
function validQuestionAnswer($value) {
	if (strlen($value) === 0) {
		return 'No text given!';
	} else if (strlen($value) > 1000) {
		return "This value can't be over 1000 characters long!";
	}

	return '';
}

/**
 * Returns the reason why this value is valid, or if it is valid,
 * an empty string.
 */
function validCategoryName($catName) {
	if (strlen($catName) === 0) {
		return 'No text given!';
	} else if (strlen($catName) > 250) {
		return "The category name can't be over 250 characters long!";
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

function categoryHasErrors($cat) {
	if (strlen($cat->name->error) > 0) {
		return true;
	}

	// Find all the errors in the answers
	foreach ($cat->answers as $qadata) {
		if (strlen($qadata->error) > 0) {
			return true;
		}
	}

	// Find all the errors in the questions
	foreach ($cat->questions as $qadata) {
		if (strlen($qadata->error) > 0) {
			// Contains an error
			return true;
		}
	}

	return false;
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
			$parsed[$catIndex]->name = new QAData($value, validCategoryName($value));
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

	// Check for errors
	foreach ($parsed as $category) {
		if (categoryHasErrors($category)) {
			$containsError = true;
			break;
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
		<?php if ($submitted && $containsError) { ?>
		<div class="error error-container">
			<p>Please review your game and check for any warnings. This message indicates some of the fields need to be corrected.</p>
			<p>Some possible reasons for this message:</p>
			<ul>
				<li>Empty fields</li>
				<li>Questions or answers longer than 1,000 characters</li>
				<li>Category names longer than 250 characters</li>
			</ul>
		</div>
		<?php } ?>
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
			<div id="game-data" class="centered">
				<h1>Questions and Answers</h1>
				
				<?php
				ob_start();
				for ($i = 0; $i < $columns; $i++) {
					echo '<section class="category-container">';

					$catName = '';
					if (isset($parsed[$i]->name->value)) {
						$catName = $parsed[$i]->name->value;
					}
					$error = false;
					if (isset($parsed[$i]->name->error)) {
						$error = strlen($parsed[$i]->name->error) > 0;
					}
					
					$hideAtRender = false;
					$catHasErrors = $submitted ? categoryHasErrors($parsed[$i]) : false;
					if ($submitted) {
						if (!$catHasErrors) {
							$hideAtRender = true;
						}
					} else {
						// When the page is first loaded hide everything except the first category
						$hideAtRender = $i !== 0;
					}

					echo '<div class="category-header">';
					echo sprintf('<img class="expand-contract-icon" src="res/img/%s.png">', $hideAtRender ? 'collapse' : 'expand');
					echo sprintf('<label class="category-name-label">Category %s: </label><input type="text" name="cat-%s-name" class="%s category-name" value="%s">', $i + 1, $i, $error ? 'error' : '', $catName);
					if (!$catHasErrors && $submitted) {
						echo '<img class="icon valid-category-icon" src="res/img/check.png">';
					};
					// End category-header
					echo '</div>';

					echo sprintf('<div class="minimizable-content" %s>', $hideAtRender ? 'style="display: none;"' : '');
					for ($j = 0; $j < 5; $j++) {
						echo '<div class="qa-container-data">';
						echo '<p>For $' . (($j + 1) * 100) . ': ';
						echo '<span class="qa-label-hint" style="display: none"></span></p>';
						echo '<ul class="qa-container">';

						$error = false;
						if (isset($parsed[$i]->answers[$j]->error)) {
							if (strlen($parsed[$i]->answers[$j]->error) > 0) {
								$error = true;
							}
						}
						// Display the answer
						$answer = '';
						// Check if the value is set
						if (isset($parsed[$i]->answers[$j]->value)) {
							$answer = htmlspecialchars($parsed[$i]->answers[$j]->value);
						}
						
						echo sprintf('<li><label class="qa-label">Answer:</label><input class="%s qa-input" value="%s" name="answers-%s[]" type="text">', $error ? 'error' : '', $answer, $i);

						// Display the question
						$question = '';
						// Check if the value is set
						if (isset($parsed[$i]->questions[$j]->value)) {
							$question = htmlspecialchars($parsed[$i]->questions[$j]->value);
						}

						$error = false;
						if (isset($parsed[$i]->questions[$j]->error)) {
							if (strlen($parsed[$i]->questions[$j]->error) > 0) {
								$error = true;
							}
						}
						
						echo sprintf('<li><label class="qa-label">Question:</label><input class="%s qa-input" value="%s" name="questions-%s[]" type="text"><br>', $error ? 'error' : '', $question, $i, $j);

						// End qa-label-container
						echo '</ul>';
						// End qa-container
						echo '</div>';
					}
					// End minimizable-content
					echo '</div>';
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
