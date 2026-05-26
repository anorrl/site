<?php
	use anorrl\User;
	
	disable_cache();
	set_content_type(ARLTYPEJSON);

	$user = SESSION->user;

	if(isset($_GET['recipientUserId']) && $user != null) {
		$toFriendUser = User::FromID(intval($_GET['recipientUserId']));

		if($toFriendUser != null) {
			if(!$user->IsFriendsWith($toFriendUser)) {
				$user->friend($toFriendUser);

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