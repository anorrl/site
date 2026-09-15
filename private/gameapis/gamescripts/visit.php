<?php
	use anorrl\Script;
	use anorrl\Place;

	set_content_type(ARLTYPEPLAIN);

	$username = "Player";
	$userid = 1;
	$userage = 0;
	$place_id = 0;
	$universe_id = 0;
	$creatorid = 0;

	if(SESSION) {
		$user = SESSION->user;
		$username = $user->name;
		$userid = $user->id;
		$userage = $user->getAccountAge();
		$pid = get_header("ANORRL-Place-Id");

		if($pid) {
			$place_id = intval($pid);
			$place = Place::FromID($place_id);

			if(!$place)
				$place_id = 0;
			else {
				$universe_id = $place->universe;
				$creatorid = $place->creator->id;
			}
		}
	}

	die(Script::load("visit")->prepend("preload")->sign(
	[
		"userid" => $userid,
		"username" => $username,
		"accountage" => $userage,
		"placeid" => $place_id,
		"universeid" => $universe_id,
		"creatorid" => $creatorid,
		"changehistory" => false
	]));
?>
