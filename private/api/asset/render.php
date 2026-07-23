<?php
	use anorrl\Asset;

	$user = SESSION ? SESSION->user : null;

	set_content_type(ARLTYPEJSON);

	$result = ["error" => true, "reason" => "Request failed."];
	
	if(!isset($id)) {
		die(json_encode($result));
	}

	$asset = Asset::FromID($id);

	if(!$asset) {
		$result['reason'] = "Failed to retrieve asset.";
		die(json_encode($result));
	}

	if(!$asset->isOwner($user)) {
		$result['reason'] = "You are not authorised to perform this action.";
		die(json_encode($result));
	}

	$asset->render();
	
	die(json_encode([
		"error" => false
	]));
?>
