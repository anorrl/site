<?php
	use anorrl\GameServer;
	use anorrl\User;
	use anorrl\utilities\ClientDetector;

	if(!ClientDetector::HasAccess()) {
		exit_http(403, "NOT OK");
	}

	// to-do: use json?

	if(isset($_GET['jobID']) && isset($_GET['userID'])) {
		$gameserver = GameServer::GetFromJobID($_GET['jobID']);

		$user = User::FromID(intval($_GET['userID']));

		if($gameserver && $user && !$user->isBanned()) {
			$gameserver->addPlayer($user);

			die("OK");
		}
	}

	exit_http(503, "NOT OK");
?>
