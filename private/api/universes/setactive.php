<?php
	use anorrl\Universe;
	use anorrl\enums\AssetType;

	set_content_type(ARLTYPEJSON);

	if(!ARLAUTH || !isset($id))
		die(json_encode([ "success" => false, "reason" => "You are not authorised to perform this action." ]));

	$universe = Universe::FromID(intval($id));

	if(!$universe)
		die(json_encode([ "success" => false, "reason" => "Universe not found."]));

	if(!$universe->isOwner(SESSION->user))
		die(json_encode([ "success" => false, "reason" => "You are not authorised to perform this action." ]));

	$too_many_active = SESSION->user->getOwnedAssetsCount(AssetType::GAME, "", true, false, []) >= 5;

	if($too_many_active && !$universe->active)
		die(json_encode([ "success" => false, "reason" => "You have too many games active right now! Deactivate one of them!" ]));

	$universe->toggleActive();

	die(json_encode([ "success" => true ]));
?>