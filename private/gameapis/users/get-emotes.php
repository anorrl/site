<?php
	use anorrl\User;
	use anorrl\enums\AssetType;

	set_content_type(ARLTYPEJSON);

	$userid = 1;

	if(isset($_GET['userId'])) {
		$userid = intval($_GET['userId']);
	} else {
		if(SESSION)
			$userid = SESSION->user->id;
	}

	$user = User::FromID($userid);

	if(!$user && SESSION) {
		$user = SESSION->user;
	}

	if(!$user)
		die(json_encode([
			"success" => false,
			"reason" => "User not found!"
		]));

	$emotes = [];

	foreach($user->getWearing(AssetType::EMOTE) as $emote) {
		$emotes[] = [
			"id" => $emote->id,
			"name" => $emote->name,
		];
	}

	die(json_encode([
		"success" => true,
		"emotes" => $emotes
	]));

?>