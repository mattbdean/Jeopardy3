<?php

function getConfigJson($name) {
	return get_object_vars(json_decode(file_get_contents("cfg/$name.json")));
}

function getDateString($datetime, $hoursMinutes = true, $seconds = true) {
	// Desired effect: January<sup>22</sup>, 1998
	// January 22
	$dateFirst = $datetime->format('F j');
	// nd
	$dateAppend = $datetime->format('S');
	// 1998
	$dateYear = $datetime->format(', Y');

	$date = $dateFirst . '<sup>' . $dateAppend . '</sup>' . $dateYear;

	if($hoursMinutes) {
		// Time
		$format = 'g:i';
		if ($seconds) {
			$format .= ':s';
		}
		$format .= ' A';
		$date .= ', ' . $datetime->format($format);
	}

	return $date;
}
?>