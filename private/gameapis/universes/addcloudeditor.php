<?php
	
	use anorrl\Place;
	use anorrl\User;

	header("Content-Type: application/json");

	$place_id = intval($universeId);
	$usertoadd_id = intval($_GET['userId']);

	$place = Place::FromID($place_id);
	$user = SESSION->user;

	if($place != null && $user != null && $place->isOwner($user)) {
		$userToAdd = User::FromID($usertoadd_id);
		if($userToAdd != null) {
			$place->addCloudEditor($userToAdd);
			echo "{}";
		}
	}
?>
