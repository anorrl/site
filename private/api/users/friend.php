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

	if($user->id == $session->id || $session->isBanned() || $user->isBanned())
		die(json_encode($result));

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$session->friend($user);

		$status = 0;

		if($session->isFriendsWith($user)) {
			$status = 3;
		}
		else {
			if($session->isPendingFriendsReq($user)) {
				$status = 1;
			} else {
				if($session->isIncomingFriendsReq($user)) {
					$status = 2;
				}
			}
		}

		die(json_encode(['success' => true, 'result' => $status, 'count' => $user->getFriendsCount()]));
	}
	else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		// check if blocked?
		$status = 0;
		
		if($session->isFriendsWith($user)) {
			$status = 3;
		}
		else {
			if($session->isPendingFriendsReq($user)) {
				$status = 1;
			} else {
				if($session->isIncomingFriendsReq($user)) {
					$status = 2;
				}
			}
		}

		die(json_encode(['success' => true, 'result' => $status, 'count' => $user->getFriendsCount()]));
	}

	die(json_encode($result));
?>