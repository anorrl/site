<?php
	use anorrl\Asset;
	use anorrl\enums\AssetType;
	
	set_content_type(ARLTYPEJSON);

	if(!SESSION)
		die(json_encode(["success" => false, "message" => "User is not logged in."]));


	$user = SESSION->user;
	if(!$user->isBanned() && isset($_POST['asset_id'])) {
		$asset = Asset::FromID(intval($_POST['asset_id']));

		// nuh uh no badge buying for you!
		if(!$asset || $asset && $asset->type == AssetType::BADGE)
			die(json_encode(["success" => false, "message" => "Invalid purchase method."]));
		
		die(json_encode($user->purchase($asset)));
	} else {
		die(json_encode(["success" => false, "message" => "User is not authorised to perform this action."]));
	}

?>
