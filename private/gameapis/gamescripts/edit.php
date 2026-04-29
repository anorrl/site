<?php 
	use anorrl\Place;
	use anorrl\Script;

	/*
	--visit:SetPing("http://{domain}/Game/ClientPresence.ashx?version=old&PlaceID=1818&LocationType=Studio", 120)
	--game:HttpGet("http://{domain}/Game/Statistics.ashx?UserID=0&AssociatedCreatorID=0&AssociatedCreatorType=User&AssociatedPlaceID=1818")
	*/

	header("Content-Type: text/plain");

	if(!SESSION || !isset($_GET['placeId']))
		die(http_response_code(503));

	$user = SESSION->user;
	$place = Place::FromID(intval($_GET['placeId']));

	if(!$place)
		die(http_response_code(503));

	if(!$place->isEditable($user))
		die(http_response_code(503));


	$uploadurl = "http://{domain}/Data/Upload.ashx?assetid=".$place->id;
	
	// the fuck?
	if(!$place->copylocked && $place->creator->id != $user->id) {
		$uploadurl = "";
	}

	die(new Script("edit")->sign([
		"placeid" => $place->id,
		"uploadurl" => $uploadurl,
		"creatorid" => $place->creator->id
	]));
?>
