<?php
	use anorrl\GameServer;
	use anorrl\utilities\Arbiter;

	if(isset($_GET['access']) && isset($_GET['jobID']) && isset($_GET['userID'])) {
		if($_GET['access'] == CONFIG->asset->key) {
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
	}
	http_response_code(503);
?>