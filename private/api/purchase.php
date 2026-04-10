<?php
	use anorrl\Asset;
	use anorrl\utilities\TransactionUtils;
	use anorrl\enums\TransactionType;
	
	header("Content-Type: application/json");

	if(!SESSION)
		die(json_encode(["error" => true, "message" => "User is not logged in."]));


	$user = SESSION->user;
	if(!$user->IsBanned() && isset($_POST['asset_id']) && isset($_POST['typatransaction'])) {
		//$type = strtolower(trim($_POST['typatransaction']));
		
		$type = TransactionType::index(intval($_POST['typatransaction']));

		$asset = Asset::FromID(intval($_POST['asset_id']));

		if(!$asset)
			die(json_encode(["error" => true, "message" => "Invalid purchase method."]));
		
		die(json_encode($asset->purchase($type, $user)));
	} else {
		die(json_encode(["error" => true, "message" => "User is not authorised to perform this action."]));
	}

?>
