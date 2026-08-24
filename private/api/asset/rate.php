<?php
	use anorrl\Place;
	use anorrl\Rating;
	set_content_type(ARLTYPEJSON);

	$result = ["success" => false, "reason" => "Request failed."];

	if(!isset($id))
		die(json_encode($result));

	$asset = Place::FromID($id);

	if(!$asset) {
		$result['reason'] = "Failed to retrieve place.";
		die(json_encode($result));
	}

	die(json_encode(Rating::Rate($asset, isset($_POST['ANORRL$Rate$Positive']))));
?>