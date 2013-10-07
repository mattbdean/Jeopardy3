<?php
require 'cfg/utils.php';

/*
 * VALIDATION:
 * game-title: Less than 100 characters
 * game-creator: Less than 100 characters
 * game-category: One of the categories in /cfg/categories.json
 * [answer/question]-[0-5]_[0-5]: Less than 500 characters
 */

// var_dump($_POST);
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
		
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
			<section id="game-meta" class="centered">
				<h1>Basic Info</h1>
				<label>Title:</label><input name="game-title" type="text" autofocus><br>
				<label>Your Name:</label><input name="game-creator" type="text"><br>
				<label>Category</label>
				<select name="game-category">
					<option value="" disabled="disabled" selected="selected">Select a category</option>
					<?php
					$categories = getConfigJson("categories");
					ksort($categories);

					foreach ($categories as $name => $value) {
						echo sprintf('<option value="%s">%s</option>', $value, $name);
					}
					?>
				</select><br>
			</section>
			<hr class="centered">
			<div id="game-data" class="centered">
				<h1>Questions and Answers</h1>
				<?php
				$columns = getConfigJson('constants')['categories'];
				ob_start();
				for ($i = 0; $i < $columns; $i++) {
					echo '<section class="category-container">';
					echo '<input type="text" class="category-name" value="Category ' . ($i + 1) . '">';
					for ($j = 0; $j < 5; $j++) {
						echo '<div class="qa-container-data" data-index="' . $j . '">';
						echo '<p class="qa-label">Answer for $' . (($j + 1) * 100) . ': ';
						echo '<span class="qa-label-hint" style="display: none"></span></p>';
						echo '<div class="qa-container">';
						echo sprintf('<label>Answer:</label><input name="answer-%s_%s" type="text"><br>', $i, $j);
						echo sprintf('<label>Question:</label><input name="question-%s_%s" type="text"><br>', $i, $j);

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
				<button id="create-game">Create Game</button>
			</div>
		</form>
	</div>
</body>
</html>