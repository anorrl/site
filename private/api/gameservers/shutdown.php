<?php
	use anorrl\GameServer;

	header("Content-Type: application/json");

	if(!SESSION || !isset($_GET['serverID']))
		die(json_encode([ "error" => true, "reason" => "You are not authorised to perform this action." ]));

	$gameserver = GameServer::Get($_GET['serverID']);

	if(!$gameserver)
		die(json_encode([ "error" => true, "reason" => "Gameserver not found."]));

	if($gameserver->place->isOwner(SESSION->user)) {
		$gameserver->destroy();
		die(json_encode([ "error" => false ]));
	}
	else 
		die(json_encode([ "error" => true, "reason" => "You are not authorised to perform this action." ]));

?>