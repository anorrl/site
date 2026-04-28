<?php
	use anorrl\Place;

	if(!isset($placeId))
		die(header("Location: /create/"));

	$place = Place::FromID($placeId);

	if(!$place)
		die(header("Location: /create/"));

	if(SESSION->user->id != $place->creator->id)
		die(header("Location: /create"));

	echo $place->name;

?>
