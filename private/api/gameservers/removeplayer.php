<?php
	use anorrl\GameServer;
	use anorrl\utilities\Arbiter;
	use anorrl\utilities\ClientDetector;

	if(!ClientDetector::HasAccess())
		exit_http(403);

	if(isset($_GET['jobID']) && isset($_GET['userID'])) {
		$gameserver = GameServer::GetFromJobID($_GET['jobID']);

		if($gameserver) {
			$gameserver->removePlayer(intval($_GET['userID']));
			die();
		} else {
			$job = Arbiter::singleton()->getGSMJob($_GET['jobID']);

			if($job) 
				Arbiter::singleton()->requestGS("kill", ["pid" => $job->pid]);
		}
	}
	exit_http(503);
?>
