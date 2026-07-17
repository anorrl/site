<?php
	/**
	 *  NO.
	 *  This is not where you create places, rather this is to create ASSETS (like badges) **FOR** places.
	 */

	// TODO: make this studio compatible

	use anorrl\Place;
	use anorrl\utilities\ClientDetector;

	if(!isset($placeId) || !isset($type))
		redirect("/develop/create/");

	if($type != "badge")
		redirect("/develop/create/");

	$place = Place::FromID($placeId);

	if(!$place)
		redirect("/develop/create/");

	if(SESSION->user->id != $place->creator->id)
		redirect("/{$place->getURL()}");

	if(!ClientDetector::IsAClient()) {
		require "views/normal.php";
	}
	else {
		require "views/studio.php";
	}

?>
