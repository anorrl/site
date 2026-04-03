<?php

	use anorrl\Place;

	if(isset($_GET['placeId'])) {
		$placeid = intval($_GET['placeId']);
	} else {
		$placeid = intval($_GET['placeid']);
	}
	
	$place = Place::FromID($placeid);

	if($place != null) {
		echo json_encode([
			"UniverseId" => $placeid,
			"GameId" => $placeid,
			"PlaceId" => $placeid,
			"openGameFromPlaceId" => $placeid,
			"updateFromPlaceId" => $placeid,
		]);
	}
	

?>