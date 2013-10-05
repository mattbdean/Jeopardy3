<?php
	$game = "sample";
	$teams = [];
	for ($i = 0; $i < 5; $i++) {
		$teams[$i] = "Team " . ($i + 1);
	}

	header("Location: play.php?" . http_build_query(['teams' => $teams, 'game' => $game]));
?>

<html>
<head>
	<title>Classroom Jeopardy</title>
</head>
<body>
	
</body>
</html>