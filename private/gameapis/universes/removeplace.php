<?php
	use anorrl\Place;
	use anorrl\Universe;
	
	if(!SESSION || !isset($universeId) || !isset($_GET['placeId']))
		exit_http(403);

	$universe = Universe::FromID(intval($universeId));
	$place = Place::FromID(intval($_GET['placeId']));

	if(!$universe || !$place)
		exit_http(503);

	if(!$universe->isOwner(SESSION->user) || !$place->isOwner(SESSION->user) || $place->universe != $universe->id)
		exit_http(403);

	if($place->id == $universe->starting_place->id)
		exit_http(503);

	$place->delete();
?>
