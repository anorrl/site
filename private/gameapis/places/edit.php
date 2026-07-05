<?php
	use anorrl\Place;

	if(!SESSION || !isset($placeId))
		exit_http(403);

	$place = Place::FromID($placeId);

	if(!$place)
		exit_http(503);

	if(!$place->isEditable(SESSION->user))
		exit_http(503);

	$jsonstuff = json_decode(file_get_contents("php://input"));

	if(!$jsonstuff)
		exit_http(500);

	if(
		!isset($jsonstuff->ID) || 
		!isset($jsonstuff->AssetId) || 
		!isset($jsonstuff->ProductId) || 
		!isset($jsonstuff->TargetId) ||
		!isset($jsonstuff->Name))
			exit_http(500);

	if(
		$jsonstuff->ID != $place->id ||
		$jsonstuff->AssetId != $place->id ||
		$jsonstuff->ProductId != $place->id ||
		$jsonstuff->TargetId != $place->id ||
		strlen(trim($jsonstuff->Name)) == 0)
			exit_http(500);
	
	$place->renameTo($jsonstuff->Name)

?>