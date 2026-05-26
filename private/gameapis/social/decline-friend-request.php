<?php
	use anorrl\User;
	
	disable_cache();
	set_content_type(ARLTYPEJSON);

	$user = SESSION->user;

	if(isset($_GET['requesterUserId']) && $user != null) {
		$toFriendUser = User::FromID(intval($_GET['requesterUserId']));

		if($toFriendUser != null) {
			$user->unfriend($toFriendUser);

			die(json_encode(
				[
					"success" => true
				]
			));
		}
	}
	http_response_code(420);
	die(json_encode(
		[
			"success" => false
		]
	));
?>