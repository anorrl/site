<?php
	use anorrl\User;
	
	disable_cache();
	set_content_type(ARLTYPEJSON);

	$user = SESSION->user;
	
	if(isset($_GET['userId']) && isset($_GET['followerUserId'])) {
		$user = User::FromID(intval($_GET['userId']));
		$userToCheck = User::FromID(intval($_GET['followerUserId']));

		if($user != null && $userToCheck != null) {
			die(json_encode(
				[
					"success" => true,
					"isFollowing" => $userToCheck->isFollowing($user)
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