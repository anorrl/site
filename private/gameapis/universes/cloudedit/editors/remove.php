<?php
	use anorrl\Universe;
	use anorrl\User;
	
	set_content_type(ARLTYPEJSON);

	if(!SESSION || !isset($universeId))
		exit_http(503);

	$universe = Universe::FromID(intval($universeId));
	
	if(!$universe)
		exit_http(503);
	
	$user = SESSION->user;

	if(!$universe->isOwner($user))
		exit_http(503);

	$userToAdd = User::FromID(intval($_GET['userId']));
	if($userToAdd) {
		$universe->removeCloudEditor($userToAdd);
		echo "{}";
	}
?>
