<?php
	use anorrl\User;
	use anorrl\Place;

	header("Content-Type: application/json");

	if(isset($userId) && isset($placeId)) {
		$user = User::FromID($userId);
		$place = Place::FromID($placeId);

		if($place != null && $user != null) {
			die(json_encode([
				"Success" => true,
				"CanManage" => $place->isOwner($user)
			]));
		}
		
	}

	die(json_encode([
		"Success" => false,
		"CanManage" => false 
	]));
?>