<?php 
	use anorrl\User;

	// dont cache this shit!
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header("Content-Type: text/plain");

	$userId = (int)$_GET['userId'];

	$user = User::FromID($userId);

	$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
	$otherUserIds = array();
	$parameters = explode('&', $queryString);
	foreach ($parameters as $parameter) {
		list($key, $value) = explode('=', $parameter);
		if ($key === 'otherUserIds') {
			$otherUser = User::FromID(intval($value));
			if($otherUser != null && !$otherUser->IsBanned()) {
				$otherUserIds[] = $otherUser;
			}
		}
	}

	$friendUserIds = [];
	foreach ($otherUserIds as $otherUser) {
		if($user->IsFriendsWith($otherUser)) {
			$friendUserIds[] = $otherUser->id;
		}
	}

	echo "X" . implode(",", $friendUserIds).",";
?>