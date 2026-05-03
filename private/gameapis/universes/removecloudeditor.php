<?php
	use anorrl\Place;
	use anorrl\User;
	
	header("Content-Type: application/json");

	if(!SESSION || !isset($universeId))
		die(http_response_code(503));


	$place = Place::FromID(intval($universeId));
	
	if(!$place)
		die(http_response_code(503));
	
	$user = SESSION->user;

	if(!$place->isOwner($user))
		die(http_response_code(503));

	$userToAdd = User::FromID(intval($_GET['userId']));
	if($userToAdd) {
		$place->removeCloudEditor($userToAdd);
		echo "{}";
	}
?>
