<?php
	use anorrl\Asset;

	$user = SESSION ? SESSION->user : null;

	header("Content-Type: application/json");

	$result = ["error" => true, "reason" => "Request failed."];
	
	if(!isset($_POST['id'])) {
		die(json_encode($result));
	}

	$asset = Asset::FromID(intval($_POST['id']));

	if(!$asset) {
		$result['reason'] = "Failed to retrieve asset.";
		die(json_encode($result));
	}

	if(!$asset->isOwner($user)) {
		$result['reason'] = "You are not authorised to perform this action.";
		die(json_encode($result));
	}

	//$asset->delete();

	die(json_encode([
		"error" => true,
		"reason" => "This function has not been implemented yet."
	]));
?>
