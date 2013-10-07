<!DOCTYPE html>
<html>
<head>
	<title>Classroom Jeopardy | Welcome!</title>
	<link rel="stylesheet" href="res/styles/index.css">
	<link rel="stylesheet" href="res/styles/general.css">
	<link rel="stylesheet" href="res/font/gyparody/stylesheet.css">
	<link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
</head>
<body>
	<div id="content-wrapper">
		<div id="header-wrapper">
			<header>
				<?php
				$parts = explode(' ', 'Welcome to Classroom Jeopardy');

				foreach ($parts as $word) {
					echo sprintf('<p class="header-word" id="header-%s">%s</p>', strtolower($word), $word);
				}
				?>
			</header>
		</div>
		<div id="navbar">
			<?php
			$sampleTeams = [];
			for ($i = 0; $i < 5; $i++) {
				$sampleTeams[$i] = 'Team ' . ($i + 1);
			}
			$navbarItems = [
			"Sample Game" => "play.php?" . http_build_query(["teams" => $sampleTeams, "game" => "sample"]),
			"Create a Game" => "create.php",
			"About" => "about.php"
			];

			foreach ($navbarItems as $name => $location) {
				echo sprintf('<a class="navbar-item" href="%s">%s</a>', $location, $name);
			}
			?>
		</div>

		<div id="latest-games">
			
		</div>
	</div>
</body>
</html>