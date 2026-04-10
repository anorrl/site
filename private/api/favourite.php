<?php
	use anorrl\Asset;

	header("Content-Type: application/json");

	if(SESSION) {
		$user = SESSION->user;
		if(isset($_POST['asset'])) {
			$assetid = intval($_POST['asset']);
			$asset = Asset::FromID($assetid);

			if($asset != null) {

				if(!$asset->hasUserFavourited($user)) {
					$asset->favourite($user);
				} else {
					$asset->unfavourite($user);
				}

				die(json_encode(["error" => false, "favourited" => $asset->hasUserFavourited($user)]));
			}
		} else {
			die(json_encode(["error" => true, "reason" => "Invalid request."]));
		}
	} else {
		die(json_encode(["error" => true, "reason" => "User not logged in."]));
	}
?>