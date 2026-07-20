<?php
	use anorrl\Universe;

	set_content_type(ARLTYPEJSON);

	if(!SESSION || !isset($id))
		die(json_encode([ "success" => false, "reason" => "You are not authorised to perform this action." ]));

	$universe = Universe::FromID(intval($id));

	if(!$universe)
		die(json_encode([ "success" => false, "reason" => "Universe not found."]));

	if(!$universe->isOwner(SESSION->user))
		die(json_encode([ "success" => false, "reason" => "You are not authorised to perform this action." ]));

	$universe->shutdown();

	die(json_encode([ "success" => true ]));
?>