<html>
<head>
	<title>Create a Game | Classroom Jeopardy</title>
	<link rel="stylesheet" href="res/styles/create.css">
	<link rel="stylesheet" href="res/styles/general.css">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<script src="js/jquery-1.10.2.min.js"></script>
	<script src="js/create_hide.js"></script>
</head>
<body>
	<div id="content-wrapper">
		<div id="header-wrapper">
			<header>
				Create a new game
			</header>
		</div>
		
		<section id="game-meta" class="centered">
			<h1>Basic Info</h1>
			<label>Title:</label><input type="text"><br>
			<label>Your Name:</label><input type="text"><br>
		</section>
		<hr class="centered">
		<section id="game-data" class="centered">
			<h1>Questions and Answers</h1>
			<?php
			$columns = 5;
			ob_start();
			for ($i = 0; $i < $columns; $i++) {
				echo '<section class="category-container">';
				echo '<img class="expand-contract-icon" src="res/img/minus.png"><p class="category-name" contenteditable="true">Category ' . ($i + 1) . '</p>';
				for ($j = 0; $j < 5; $j++) {
					echo '<img data-index="' . $j . '" class="expand-contract-icon expand-contract-icon-2" src="res/img/minus.png"><p class="question-answer-label">Answer for $' . (($j + 1) * 100) . ':</p>';
					echo '<div data-index="' . $j . '" class="question-answer-label-container">';
					echo '<label>Answer:</label><input type="text"><br>';
					echo '<label>Question:</label><input type="text"><br>';
					echo '</div>';
				}
				// End category-container
				echo '</section>';
			}
			ob_end_flush();
			?>
		</section>
	</div>
</body>
</html>