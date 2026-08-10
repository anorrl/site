<?php
	use anorrl\Universe;

	set_content_type(ARLTYPEJSON);
	if(isset($universeId)) {
		$universe = Universe::FromID(intval($universeId));

		if($universe != null) {
			$places = [];

			foreach($universe->getAllPlaces() as $place) {
				$places[] = [
					"PlaceId" => $place->id,
					"Name" => $place->name
				];
			}

			die(json_encode([
				"FinalPage" => true,
				"Places" => $places,
				"PageSize" => count($places)
			]));
		}
	}
?>
