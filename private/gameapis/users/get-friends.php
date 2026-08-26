<?php

	use anorrl\User;

	set_content_type(ARLTYPEJSON);
	$user = User::FromID($id);

	if($user != null) {
		$friends = $user->getFriends();
		$result = [];
		foreach($friends as $friend) {
			$result[] = [
				"Id" => $friend->id,
				"Username" => $friend->name
			];
		}

		die(json_encode($result));
	}

	echo "{}";

?>
