<?php

	use anorrl\Database;
	use anorrl\Place;
	use anorrl\GameServer;
	use anorrl\GameSession;
	use anorrl\utilities\Arbiter;
	
	header("Content-Type: application/json");


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
		$arbiter_pub_ip = CONFIG->arbiter->location->public;

		$security = urlencode(base64_encode($session->player->security_key));

		$json = json_encode(
			[
				"jobId" => $server->jobid,
				"status" => 2,
				"joinScriptUrl" => "http://$domain/game/join.ashx?serverToken={$server->id}&sessionToken={$session->id}&server=$arbiter_pub_ip",
				"authenticationUrl" => "https://$domain/Login/Negotiate.ashx",
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
			$gsr = Arbiter::singleton()->request(
				"gameserver",
				[
					"PlaceId" => $place->id,
					"MaxPlayers" => $teamcreate ? 100 : $place->server_size,
					"TeamCreate" => $teamcreate
				]
			);

			if(!$gsr || ($gsr && $gsr->status == "killed"))
				throw new Exception("Failed to create gameserver.");

			$port = $gsr->port;
			$pid = $gsr->pid;

			$server = GameServer::Create($gsr->jobId, $place, $port, $pid);
			
			if(!$server || ($server && !$server->active()))
				throw new Exception("Failed to create gameserver.");
			
			return $server;
		} catch(Exception $e) {
			error_log("Failed to start gameserver {$e->getMessage()}");
			errorOut(1);
		}
	}

	//
	// request=RequestGame
	// placeId=1818
	// isPartyLeader=false
	// gender=
	// isTeleport=false

	if(
		isset($_GET['request'])
	) {
		if(
			(isset($_GET['placeId']) || isset($_GET['serverId'])) &&
			isset($_GET['isTeleport']) &&
			$_GET['request'] == "RequestGame"
		) {
			$session = null;
			$server = null;
						
			$user = SESSION ? SESSION->user : null;

			if(!$user) {
				error_log("User is not existing ahh");
				errorOut(1);
			}
				
			if(isset($_GET['placeId'])) {
				$place = Place::FromID(intval($_GET['placeId']));

				if(!$place) {
					error_log("Place was null");
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
					error_log("Server was null");
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
				

		} else if($_GET['request'] == "CloudEdit" && isset($_GET['placeId'])) {

			$domain = CONFIG->domain;
			
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
						"ServerPort" => $port,
						"PingUrl" => "",
						"PingInterval" => 120,
						"UserName" => $user->name,
						"UserId" => $user->id,
						"SuperSafeChat" => false,
						"CharacterAppearance" => "http://$domain/Asset/CharacterFetch.ashx?userId={$user->id}",
						"ClientTicket" => $sessionID,
						"GameId" =>"00000000-0000-0000-0000-000000000000",
						"PlaceId" => $place->id,
						"MeasurementUrl" => "",
						"WaitingForCharacterGuid" => "16be1dd8-5462-4ca5-a997-0725d997708b",
						"BaseUrl" => "http://$domain/",
						"ChatStyle" => "ClassicAndBubble",
						"VendorId" => 0,
						"CreatorId" => $place->creator->id,
						"AccountAge" => $user->getAccountAge(),
						"SessionId" => base64_encode($user->security_key),
						"UniverseId" => $place->id,
					]
				]);

				$json = str_replace("\\\\", "",$json);
				$json = str_replace("\\", "", $json); 
				die($json);
			}
		}
	}
	error_log("never made contact");
	errorOut();
	
?>