<?php
require 'cfg/utils.php';
?>

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
			require 'classes/navbar_item.class.php';

			$sampleTeams = [];
			for ($i = 0; $i < 5; $i++) {
				$sampleTeams[$i] = 'Team ' . ($i + 1);
			}
			$sampleURL = "play.php?" . http_build_query(["game" => "sample"]);
			$navbarItems = array(
				new NavbarItem("Sample Game", $sampleURL, true),
				new NavbarItem("Create a Game", "create.php"),
				new NavbarItem("About", "about.php")
				);

			foreach ($navbarItems as $item) {
				echo sprintf('<a class="navbar-item" href="%s" %s>%s</a>', $item->location, $item->newTab ? 'target="_blank"' : '', $item->name);
			}
			?>
		</div>

		<div id="recent-games" class="centered">
			<h2>Recent games</h2>
			<?php
			try {
				$hostname = 'localhost';
				$username = 'jeopardy';
				$password = 'jeopardy';
				$dbh = new PDO("mysql:host=$hostname;dbname=jeopardy", $username, $password);
				$result = $dbh->query('SELECT game_name,category,date_created,game_id FROM `games` ORDER BY date_created DESC LIMIT 0,5')->fetchAll();
			} catch (PDOException $e) {
				echo $e->getMessage();
			}

			if (count($result) > 0) {
				// Only display it if there are more than one games
				ob_start();

				$categories = array_flip(getConfigJson('categories'));
				foreach ($result as $game) {
					echo sprintf('<a class="recent-game" href="gameinfo.php?%s">', http_build_query(['id' => $game['game_id']]));
					echo '<b>"' . htmlspecialchars($game['game_name']) . '"</b>';
					echo ' <i>(' . htmlspecialchars($categories[$game['category']]) . ')</i>, created ';

					$created = new DateTime($game['date_created']);
					echo getDateString($created, true, false);
					echo '</a><br>';
				}

				ob_end_flush();
			}
			
			?>
		</div>
	</div>
</body>
</html>