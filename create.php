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
		
		<section id="game-meta" class="centered">
			<h1>Basic Info</h1>
			<label>Title:</label><input type="text" autofocus><br>
			<label>Your Name:</label><input type="text"><br>
			<label>Category</label>
			<select>
				<option value="" disabled="disabled" selected="selected">Select a category</option>
				<?php
				$categories = [
					"English" => "english",
					"Music" => "music",
					"Science" => "science",
					"Math" => "math",
					"Other" => "other",
					"Technology" => "technology",
					"History" => "history"
				];
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
			$columns = 5;
			ob_start();
			for ($i = 0; $i < $columns; $i++) {
				echo '<section class="category-container">';
				echo '<p class="category-name" contenteditable="true">Category ' . ($i + 1) . '</p>';
				for ($j = 0; $j < 5; $j++) {
					echo '<div class="qa-container-data" data-index="' . $j . '">';
					echo '<div class="qa-label-header">';
					echo '<p class="qa-label">Answer for $' . (($j + 1) * 100) . ': ';
					echo '<span class="qa-label-hint">(answer, question)</span></p>';
					// End qa-label-header
					echo '</div>';
					echo '<div class="qa-container">';
					echo '<label>Answer:</label><input type="text"><br>';
					echo '<label>Question:</label><input type="text"><br>';

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
	</div>
</body>
</html>