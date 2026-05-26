<?php 
	use anorrl\User;

	// dont cache this shit!
	disable_cache();
	set_content_type(ARLTYPEPLAIN);

	$userId = (int)$_GET['userId'];

	$user = User::FromID($userId);

	$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
	$otherUserIds = [];
	$parameters = explode('&', $queryString);
	foreach ($parameters as $parameter) {
		list($key, $value) = explode('=', $parameter);
		if ($key === 'otherUserIds') {
			$otherUser = User::FromID(intval($value));
			if($otherUser != null && !$otherUser->isBanned()) {
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