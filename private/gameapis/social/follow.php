<?php
	use anorrl\User;
	
	disable_cache();
	set_content_type(ARLTYPEJSON);

	$user = SESSION->user;

	if(isset($_POST['followedUserId']) && $user != null) {
		$toFollowUser = User::FromID(intval($_POST['followedUserId']));

		if($toFollowUser != null) {
			if(!$user->isFollowing($toFollowUser)) {
				$user->follow($toFollowUser);

				die(json_encode(
					[
						"success" => true
					]
				));
			} else {
				die(json_encode(
					[
						"success" => false
					]
				));
			}
		}
	}
	http_response_code(420);
	die(json_encode(
		[
			"success" => false
		]
	));
?>