<?php
	use anorrl\Place;

	header("Content-Type: application/json");
	if(isset($_GET['universeId'])) {
		$place = Place::FromID(intval($_GET['universeId']));

		if($place != null) {
			die(json_encode([
				"FinalPage" => true,
				"RootPlace" => $place->id,
				"Places" => [
					"PlaceId" => $place->id,
					"Name" => $place->name
				],
				"PageSize" => 1
			]));
		}
	}
?>