<?php
	use anorrl\GameServer;
	use anorrl\Place;

	set_content_type(ARLTYPEJSON);

	if(!SESSION || (!isset($_POST['serverID']) && !isset($_POST['placeID'])))
		die(json_encode([ "error" => true, "reason" => "You are not authorised to perform this action." ]));

	
	if(isset($_POST['serverID'])) {
		$gameserver = GameServer::Get($_POST['serverID']);
		if(!$gameserver)
			die(json_encode([ "error" => true, "reason" => "Gameserver not found."]));

		if($gameserver->place->isOwner(SESSION->user)) {
			$gameserver->shutdown();
			die(json_encode([ "error" => false ]));
		}
		else 
			die(json_encode([ "error" => true, "reason" => "You are not authorised to perform this action." ]));
	} else if(isset($_POST['placeID'])) {
		$place = Place::FromID(intval($_POST['placeID']));

		if(!$place)
			die(json_encode([ "error" => true, "reason" => "Place not found."]));

		if(!$place->isOwner(SESSION->user))
			die(json_encode([ "error" => true, "reason" => "You are not authorised to perform this action." ]));

		$place->shutdown();

		die(json_encode([ "error" => false ]));
	}
	
	die(json_encode([ "error" => true, "reason" => "Something went wrong!" ]));
?>