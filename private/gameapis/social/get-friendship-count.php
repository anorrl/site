<?php
	use anorrl\User;
	
	disable_cache();
	set_content_type(ARLTYPEJSON);
	
	if(isset($_GET['userId'])) {
		$user = User::FromID(intval($_GET['userId']));

		if($user != null) {
			die(json_encode(
				[
					"success" => true,
					"count" => $user->getFriendsCount()
				]
			));
		} else {
			$user = SESSION->user;

			if($user != null) {
				die(json_encode(
					[
						"success" => true,
						"count" => $user->getFriendsCount()
					]
				));
			} 
		}
	} else {
		$user = SESSION->user;

		if($user != null) {
			die(json_encode(
				[
					"success" => true,
					"count" => $user->getFriendsCount()
				]
			));
		} 
	}

	die(json_encode(
		[
			"success" => false
		]
	));
?>