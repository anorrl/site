<?php
	use anorrl\GameServer;

	set_content_type(ARLTYPEJSON);

	if(!SESSION || !isset($id))
		die(json_encode([ "success" => false, "reason" => "You are not authorised to perform this action." ]));

	
	$gameserver = GameServer::Get($id);
	if(!$gameserver)
		die(json_encode(["success" => false, "reason" => "Gameserver not found."]));

	if($gameserver->place->isOwner(SESSION->user)) {
		$gameserver->shutdown();
		die(json_encode(["success" => true ]));
	}
	else 
		die(json_encode(["success" => false, "reason" => "You are not authorised to perform this action." ]));
?>