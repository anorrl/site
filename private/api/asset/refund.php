<?php
	use anorrl\Asset;

	$user = SESSION ? SESSION->user : null;

	set_content_type(ARLTYPEJSON);

	$result = ["success" => false, "reason" => "Request failed."];
	
	if(!isset($id)) {
		die(json_encode($result));
	}

	$asset = Asset::FromID(intval($_POST['id']));

	if(!$asset) {
		$result['reason'] = "Failed to retrieve asset.";
		die(json_encode($result));
	}

	die(json_encode($user->remove($asset)));
?>
