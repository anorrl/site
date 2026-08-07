<?php
	use anorrl\Asset;

	$user = SESSION ? SESSION->user : null;

	set_content_type(ARLTYPEJSON);

	$result = ["success" => false, "reason" => "Request failed."];

	if(!$user || !isset($id))
		die(json_encode($result));

	$asset = Asset::FromID($id);

	if(!$asset) {
		$result['reason'] = "Failed to retrieve asset.";
		die(json_encode($result));
	}

	if(!$asset->hasUserFavourited($user)) {
		$asset->favourite($user);
	} else {
		$asset->unfavourite($user);
	}

	die(json_encode(["success" => true, "count" => $asset->favourites_count]));
?>
