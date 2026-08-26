<?php
	use anorrl\User;
	use anorrl\UserSettings;

	set_content_type(ARLTYPEJSON);

	$userid = null;
	if(isset($_GET['userId'])) {
		$userid = intval($_GET['userId']);
	} else {
		if(ARLAUTH)
			$userid = SESSION->user->id;
	}
	if(!$userid) {
		die(json_encode([
			"success" => false,
			"reason" => "the fuck is ur user doofus?"
		]));
	}

	// even if the user is null, it will return a null asset by default
	$user_settings = UserSettings::Get(User::FromID($userid));
	$icon = $user_settings->playerlisticon;

	die(json_encode([
		"success" => true,
		"icon" => $icon ? $icon->id : -1
	]));
?>
