<?php
	use anorrl\GameServer;
	use anorrl\utilities\Arbiter;
	use anorrl\utilities\ClientDetector;

	if(!ClientDetector::HasAccess())
		exit_http(403);

	if(!isset($jobID))
		exit_http(503);

	$gameserver = GameServer::GetFromJobID($jobID);

	if($gameserver)
		die($gameserver->renewLease());

	else {
		$job = Arbiter::singleton()->getGSMJob($jobID);
		if($job) 
			Arbiter::singleton()->requestGS("kill", ["pid" => $job->pid]);
	}
?>
