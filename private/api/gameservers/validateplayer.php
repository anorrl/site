<?php
	use anorrl\GameServer;
	use anorrl\User;
	use anorrl\utilities\ClientDetector;

	if(!ClientDetector::HasAccess()) {
		echo "NOT OK";
		exit(http_response_code(503));
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

	echo "NOT OK";
	http_response_code(503);
?>
