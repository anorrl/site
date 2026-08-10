<?php
	use anorrl\Universe;
	use anorrl\Place;

	set_content_type(ARLTYPEJSON);

	if(isset($_GET['universeId'])) {
		$assetid = intval($_GET['universeId']);
		$universe = Universe::FromID($assetid);
		$place = $universe->starting_place;
	} else if(isset($_GET['placeId'])) {
		$assetid = intval($_GET['placeId']);
		$place = Place::FromID($assetid);
		$universe = Universe::FromID($place->universe);
	}

	if($place) {

		echo json_encode([
			"RootPlace" => $universe->starting_place->id,
			"CurrentUserHasEditPermissions" => true,
			"StudioAccessToApisAllowed" => true,
			"TargetId" => $universe->id,
			"ProductType" => "User Product",
			"AssetId" => $place->id,
			"ProductId" => $place->id,
			"Name" => $place->name,
			"Description" => $place->description,
			"AssetTypeId" => $place->type->ordinal(),
			"CreatorId" => $place->creator->id,
			"CreatorName" => $place->creator->id,
			"IconImageAssetId" => $place->id,
			"GameId" => $universe->id,
			"UniverseId" => $universe->id,
			"PlaceId" => $place->id,
			"openGameFromPlaceId" => $place->id,
			"updateFromPlaceId" => $place->id,
		]);

	} else {
		echo "{}";
	}
?>
