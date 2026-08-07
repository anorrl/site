<?php
	use anorrl\GameServer;
	use anorrl\User;
	use anorrl\utilities\ClientDetector;

	if(!ClientDetector::HasAccess())
		exit_http(403, "NOT OK");

	if(!isset($jobID) || !isset($player))
		exit_http(503, "NOT OK");

	$gameserver = GameServer::GetFromJobID($jobID);

	$user = User::FromID(intval($player));

	if($gameserver && $user && !$user->isBanned()) {
		$gameserver->addPlayer($user);

		die("OK");
	}

	exit_http(503, "NOT OK");
	
?>
