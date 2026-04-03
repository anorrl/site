<?php
	use anorrl\User;
	
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header("Content-Type: application/json");

	$user = SESSION ? SESSION->user : null;
	
	if(isset($_GET['userId']) && isset($_GET['followerUserId'])) {
		$user = User::FromID(intval($_GET['userId']));
		$userToCheck = User::FromID(intval($_GET['followerUserId']));

		if($user != null && $userToCheck != null) {
			die(json_encode(
				[
					"success" => true,
					"isFollowing" => $userToCheck->IsFollowing($user)
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