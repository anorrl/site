<?php
	
	use anorrl\Place;
	use anorrl\User;

	header("Content-Type: application/json");

	$place_id = intval($_GET['universeId']);
	$usertoadd_id = intval($_GET['userId']);

	$place = Place::FromID($place_id);
	$user = SESSION ? SESSION->user : null;

	if($place != null && $user != null && ($user->id == $place->creator->id || $user->IsAdmin())) {
		$userToAdd = User::FromID($usertoadd_id);
		if($userToAdd != null)
			$place->AddCloudEditor($userToAdd);
	}
?>