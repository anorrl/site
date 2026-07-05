<?php
	use anorrl\Place;
	use anorrl\Universe;
	
	if(!SESSION || !isset($_GET['universeid']) || !isset($_GET['placeid']))
		exit_http(403);

	$universe = Universe::FromID(intval($_GET['universeid']));
	$place = Place::FromID(intval($_GET['placeid']));

	if(!$universe || !$place)
		exit_http(503);

	if(!$universe->isOwner(SESSION->user) || !$place->isOwner(SESSION->user) || $place->universe != $universe->id)
		exit_http(403);

	$result = $universe->setStartingPlace($place);

	if(!$result)
		exit_http(503);
?>
