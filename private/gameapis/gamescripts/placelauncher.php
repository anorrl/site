<?php

	use anorrl\Database;
	use anorrl\Place;
	use anorrl\GameServer;
	use anorrl\GameSession;
	use anorrl\utilities\Arbiter;
	
	set_content_type(ARLTYPEJSON);


	function errorOut(int $status = 0, string|null $sessionID = null, bool $teamcreate = false) {
		http_response_code(503);
		if($sessionID) {
			Database::singleton()->run(
				"DELETE FROM `active_players` WHERE `id` = :id AND `teamcreate` = :teamcreate",
				[
					":id" => $sessionID,
					":teamcreate" => $teamcreate
				]
			);
		}
		
		die(json_encode([
			"status" => $status,
			"message" => "Wow so much errors!"
		]));
	}

	function createResponse(GameServer $server, GameSession $session) {
		$domain = CONFIG->domain;
		$scheme = CONFIG->prefer_https ? "https" : "http";
		$arbiter_pub_ip = CONFIG->arbiter->location->public;

		$security = urlencode(base64_encode($session->player->security_key));

		$json = json_encode(
			[
				"jobId" => $server->jobid,
				"status" => 2,
				"joinScriptUrl" => "$scheme://$domain/game/join.ashx?serverToken={$server->id}&sessionToken={$session->id}&server=$arbiter_pub_ip",
				"authenticationUrl" => "$scheme://$domain/Login/Negotiate.ashx",
				"authenticationTicket" => $security,
				"message" => "HELLOOOOOOOO!!!!!"
			]
		);

		// i forgot why i did this but it works i guess.
		$json = str_replace("\\\\", "", $json);
		$json = str_replace("\\", "", $json); 

		return $json;
	}

	function startServer(Place $place, bool $teamcreate = false) {
		try {
			$gsr = Arbiter::singleton()->requestGS(
				"start",
				[
					"UniverseId" => $place->universe,
					"PlaceId" => $place->id,
					"MaxPlayers" => $teamcreate ? 100 : $place->server_size,
					"TeamCreate" => $teamcreate
				]
			);

			ob_clean();
			print_r($gsr);
			error_log(ob_get_clean());

			if(!$gsr ||
				($gsr && isset($gsr->message)&& $gsr->message == "killed") ||
				($gsr && (!isset($gsr->jobId) || !isset($gsr->port) || !isset($gsr->pid)))
			) {
				throw new Exception("Failed to create gameserver.");
			}
			
			$port = $gsr->port;
			$pid = $gsr->pid;

			$server = GameServer::Create($gsr->jobId, $place, $port, $pid, $teamcreate);
			
			if(!$server || ($server && !$server->active())) {
				if(!$server)
					$reason = "Server was null";
				else
					$reason = "Server was somehow inactive";
				throw new Exception("Failed to create gameserver. ($reason)");
			}
			return $server;
		} catch(Exception $e) {
			error_log($e);
			errorOut(1);
		}
	}

	//
	// request=RequestGame
	// placeId=1818
	// isPartyLeader=false
	// gender=
	// isTeleport=false

	if(isset($_GET['request'])) {
		if(
			(isset($_GET['placeId']) || isset($_GET['serverId'])) &&
			isset($_GET['isTeleport']) &&
			$_GET['request'] == "RequestGame"
		) {
			$session = null;
			$server = null;
						
			$user = SESSION ? SESSION->user : null;

			if(!$user) {
				errorOut(1);
			}
				
			if(isset($_GET['placeId'])) {
				$place = Place::FromID(intval($_GET['placeId']));

				if(!$place) {
					errorOut(1);
				}
				if($user->isInAGame()) {
					$active_server = $user->getActiveGame();
					if($active_server)
						$active_server->removePlayer($user);
				}
				
				if(!$place->anyActiveServers()) {
					$server = startServer($place);
				} else {
					$server = $place->getAnActiveServer($user);

					if(!$server)
						$server = startServer($place);
				}
			} else if(isset($_GET['serverId'])) {
				$server = GameServer::Get($_GET['serverId']);

				if(!$server) {
					errorOut(1);
				}

				if($user->isInAGame()) {
					$active_server = $user->getActiveGame();
					if($active_server)
						$active_server->removePlayer($user);
				}

				if(!$server->active()) {
					$server = $server->place->getAnActiveServer($user);

					if(!$server)
						$server = startServer($server->place);
				}
			}

			if($server)
				$session = GameSession::Create($server, $user);

			if($session && $server)
				die(createResponse($server, $session));
			else {
				error_log("Session or Server was null");
				errorOut(0);
			}
				

		}
		if($_GET['request'] == "CloudEdit" && isset($_GET['placeId'])) {

			$domain = CONFIG->domain;
			$scheme = CONFIG->prefer_https ? "https" : "http";
			
			$place = Place::FromID(intval($_GET['placeId']));
			$user = SESSION ? SESSION->user : null;

			if(!$place || !$user)
				errorOut(1);

			if($user->isInAGame(true)) {
				$active_server = $user->getActiveGame(true);
				if($active_server)
					$active_server->removePlayer($user);
			}

			$server = null;
			$session = null;

			if(!$place->anyActiveServers(true)) {
				$server = startServer($place, true);
			} else {
				$server = $place->getAnActiveServer($user, true);

				if(!$server)
					$server = startServer($place, true);
			}

			if($server)
				$session = GameSession::Create($server, $user, true);
			
			if($server && $session) {
				$json = json_encode([
					"status" => 2,
					"settings" => [
						"ClientPort" => 0,
						"MachineAddress" => CONFIG->arbiter->location->public,
						"ServerPort" => $server->port,
						"PingUrl" => "",
						"PingInterval" => 120,
						"UserName" => $user->name,
						"UserId" => $user->id,
						"SuperSafeChat" => false,
						"CharacterAppearance" => "$scheme://$domain/Asset/CharacterFetch.ashx?userId={$user->id}",
						"ClientTicket" => $session->id,
						"GameId" =>"00000000-0000-0000-0000-000000000000",
						"PlaceId" => $place->id,
						"MeasurementUrl" => "",
						"WaitingForCharacterGuid" => "16be1dd8-5462-4ca5-a997-0725d997708b",
						"BaseUrl" => "$scheme://$domain/",
						"ChatStyle" => "ClassicAndBubble",
						"VendorId" => 0,
						"CreatorId" => $place->creator->id,
						"AccountAge" => $user->getAccountAge(),
						"SessionId" => base64_encode($user->security_key),
						"UniverseId" => $place->universe,
					]
				]);

				$json = str_replace("\\\\", "",$json);
				$json = str_replace("\\", "", $json); 
				die($json);
			}
			else {
				if(!$session)
					error_log("Session was null");
				if(!$server)
					error_log("Server was null");
				//error_log("Session or Server was null");
			}
		}
	}
	error_log("never made contact");
	errorOut();
	
?>
