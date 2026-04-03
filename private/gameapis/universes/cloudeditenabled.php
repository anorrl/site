<?php
	use anorrl\Place;

	header("Content-Type: application/json");

	// dont cache this shit!
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	$place_id = intval($_GET['universeId']);

	$place = Place::FromID($place_id);

	if($place != null) {
		echo json_encode([
			"enabled" => $place->teamcreate_enabled
		]);
	} else {
		echo "{}";
	}
?>