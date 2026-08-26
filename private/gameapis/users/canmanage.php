<?php
	use anorrl\User;
	use anorrl\Place;

	set_content_type(ARLTYPEJSON);

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