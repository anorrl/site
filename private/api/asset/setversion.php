<?php
	use anorrl\Asset;
	use anorrl\AssetVersion;

	$user = SESSION ? SESSION->user : null;

	set_content_type(ARLTYPEJSON);

	$result = ["success" => false, "reason" => "Request failed."];
	
	if(!isset($id) || !isset($vid)) {
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
	
	die(json_encode($asset->setVersion(AssetVersion::FromID($vid))));
?>
