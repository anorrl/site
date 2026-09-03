<?php 
	use anorrl\Place;
	use anorrl\Script;

	/*
	--visit:SetPing("http://{domain}/Game/ClientPresence.ashx?version=old&PlaceID=1818&LocationType=Studio", 120)
	--game:HttpGet("http://{domain}/Game/Statistics.ashx?UserID=0&AssociatedCreatorID=0&AssociatedCreatorType=User&AssociatedPlaceID=1818")
	*/

	set_content_type(ARLTYPEPLAIN);

	if(!SESSION || !isset($_GET['placeId']) && !isset($_GET['PlaceID']))
		exit_http(403);

	$user = SESSION->user;
	$place = Place::FromID(intval(isset($_GET['placeId']) ? $_GET['placeId'] : $_GET['PlaceID']));

	if(!$place)
		exit_http(403);

	if(!$place->isEditable($user))
		exit_http(403);


	$uploadurl = "{scheme}://{domain}/Data/Upload.ashx?assetid=".$place->id;
	
	// the fuck?
	if(!$place->copylocked && $place->creator->id != $user->id) {
		$uploadurl = "";
	}

	die(new Script("edit")->sign([
		"placeid" => $place->id,
		"universeid" => $place->universe,
		"uploadurl" => $uploadurl,
		"creatorid" => $place->creator->id
	]));
?>
