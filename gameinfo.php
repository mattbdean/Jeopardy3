<?php
if (!isset($_GET['id'])) {
	// Temporary for now
	header('Location: /');
}

require 'cfg/utils.php';

$hostname = 'localhost';
$username = 'jeopardy';
$passowrd = 'jeopardy';

try {
	$dbh = new PDO("mysql:host=$hostname;dbname=jeopardy", $username, $passowrd);
			// http://stackoverflow.com/a/60496/1275092
	$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt = $dbh->prepare('SELECT date_created,category,game_name,creator_name FROM games WHERE game_id = :game_id');
	$stmt->execute([
		':game_id' => $_GET['id']
		]);

	// Close the db connection
	$dbh = null;

	$data = $stmt->fetch();

	$categoryLookup = array_flip(getConfigJson('categories'));
	define('CATEGORY', htmlspecialchars($categoryLookup[$data['category']]));

	define('CREATOR', htmlspecialchars($data['creator_name']));
	define('NAME', htmlspecialchars($data['game_name']));
	define('FILE', 'games/' . stripslashes($_GET['id']) . '.xml');
	$CREATED = new DateTime($data['date_created']);
} catch (PDOException $e) {
	echo $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="res/styles/gameinfo.css">
	<link rel="stylesheet" href="res/styles/general.css">
	<link rel="stylesheet" href="res/styles/header.css">
	<link rel="stylesheet" href="res/font/gyparody/stylesheet.css">
	<script src="js/jquery-1.10.2.min.js"></script>
	<script>
	<?php
	$json = getConfigJson('min_max_teams');
	echo 'var MIN = ' . $json['min-teams'] . ', MAX = ' . $json['max-teams'] . ';';
	?>
	</script>
	<script src="js/play_setup.js"></script>
	<title>Viewing: <?php echo NAME ?> | Classroom Jeopardy</title>
</head>
<body>
	<div id="header-wrapper">
		<?php
		$pageTitle = 'Game overview';
		require 'common/header.php';
		?>
	</div>
	<div id="content-wrapper" class="centered">
		<h1>Overview of "<?php echo NAME ?>"</h1>

		<section id="stats-basics">
			<h2>The Basics</h2>
			<?php
			// Desired effect: January<sup>22</sup>, 1998
			// January 22
			$dateFirst = $CREATED->format('F j');
			// nd
			$dateAppend = $CREATED->format('S');
			// 1998
			$dateYear = $CREATED->format(', Y');

			echo '<p class="stat">Created ' . $dateFirst . '<sup>' . $dateAppend . '</sup>' . $dateYear . '</p>';
			?>
			<p class="stat">Category: <?php echo CATEGORY ?></p>
			<p class="stat">Creator: <?php echo CREATOR ?></p>

		</section>

		<section id="categories">
			<h2>Game Categories</h2>
			<?php
			$xml = simplexml_load_file(FILE);

			echo '<ol>';
			foreach ($xml->column as $column) {
				echo '<li>' . (string) $column->attributes()['name'];
			}
			echo '</ol>';
			?>
		</section>
		
		<button id="play-setup">Play this game</button>
		
		<div id="play-setup-teams" style="display: none;">
			<form method="get" action="play.php">
				<?php
				$cutoff = 3;
				for ($i = 0; $i < 5; $i++) {
					echo sprintf('<div %s><label>Team %s:</label><input value="" type="text" name="teams[]"></div>', $i >= $cutoff ? 'style="display: none;"' : '', $i + 1, $i);
				}
				?>
				<button type="button" id="add-team">+</button>
				<button type="button" id="remove-team">-</button>
				<input type="hidden" name="game" value="<?php echo htmlspecialchars($_GET['id'])?>"><br>
				<input type="submit" id="play" value="Play!">
			</form>
		</div>
	</div>
</body>
</html>