<html>
<head>
	<title>Welcome to Classroom Jeopardy!</title>
	<link rel="stylesheet" href="res/styles/index.css">
</head>
<body>
	<div id="content-wrapper">
		<div id="header-wrapper">
			<header>
				<p>Welcome to Classroom Jeopardy</p>
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
			<?php
			$files = glob('games/*.xml');
			natsort($files);
			foreach ($files as $file) {
				// TODO: Needs work/styling/DB connection
				// echo sprintf('<a href="playsetup.php">%s</a><br>', $file);
			}
			?>
		</div>
	</div>
</body>
</html>