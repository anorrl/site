<?php
	use anorrl\Place;

	if(!isset($_GET['placeId']) || !SESSION)
		exit_http(503);

	set_content_type(ARLTYPEJSON);

	$place = Place::FromID(intval($_GET['placeId']));
	
	if(!$place)
		exit_http(503);

	if(!$place->isEditable(SESSION->user))
		exit_http(503);


	$badges = [];

	foreach($place->getBadges() as $badge) {
		$badges[] = [
			"BadgeAssetId" => $badge->id,
			"PlaceId" => $place->id,
			"Name" => $badge->name,
			"Thumbnail" => [
				"Url" => "http://".CONFIG->domain.$badge->getThumbsUrl()
			]
		];
	}

	// thanks cubp
	die(json_encode([
		"GameBadges" => $badges
	]));
?>
