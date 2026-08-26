<?php
	use anorrl\User;

	set_content_type(ARLTYPEJSON);

	$session = ARLAUTH ? SESSION->user : null;

	$result = ["success" => false, "reason" => "Request failed.", "result" => 0];

	if(!$session || !isset($id))
		die(json_encode($result));

	$user = User::FromID($id);

	if(!$user) {
		$result['reason'] = "Failed to find user.";
		die(json_encode($result));
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if(!$session->isFollowing($user))
			$session->follow($user);
		else
			$session->unfollow($user);

		die(json_encode(['success' => true, 'result' => $session->isFollowing($user), 'count' => $user->getFollowersCount()]));
	}
	else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		die(json_encode(['success' => true, 'result' => $session->isFollowing($user), 'count' => $user->getFollowersCount()]));
	}

	die(json_encode($result));
?>