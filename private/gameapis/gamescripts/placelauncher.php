<?php

	use anorrl\Database;
	use anorrl\Place;
	use anorrl\User;
	use anorrl\utilities\UserUtils;
	use anorrl\utilities\Arbiter;
	
	header("Content-Type: application/json");

	function getRandomString(int $length = 11): string {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		
		for ($i = 0; $i < $length; $i++) {
			$index = rand(0, strlen($characters) - 1);
			$randomString .= $characters[$index];
		}

		return $randomString;
	}

	function getActiveServersCount(int $placeID, bool $teamcreate = false): bool {
		include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

		$stmt_teamcreate = $teamcreate ? 1 : 0;

		$stmt_getactiveservers = $con->prepare("SELECT * FROM `active_servers` WHERE `placeid` = ? AND `playercount` != `maxcount` AND `teamcreate` = ?");
		$stmt_getactiveservers->bind_param("ii", $placeID, $stmt_teamcreate);
		$stmt_getactiveservers->execute();

		$result_getactiveservers = $stmt_getactiveservers->get_result();

		return $result_getactiveservers->num_rows;
	}

	function getAnActiveServer(int $placeID, bool $teamcreate = false): array|null {
		include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

		$stmt_teamcreate = $teamcreate ? 1 : 0;

		$stmt_getactiveservers = $con->prepare("SELECT * FROM `active_servers` WHERE `placeid` = ? AND `playercount` < `maxcount` AND `teamcreate` = ?");
		$stmt_getactiveservers->bind_param("ii", $placeID, $stmt_teamcreate);
		$stmt_getactiveservers->execute();

		$result_getactiveservers = $stmt_getactiveservers->get_result();

		if($result_getactiveservers->num_rows != 0) {
			return $result_getactiveservers->fetch_assoc();
		}

		return null;
	}

	function isUserInAGame(int $userID, bool $teamcreate = false): bool {
		include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

		$stmt_teamcreate = $teamcreate ? 1 : 0;

		$stmt_getsessiondetails = $con->prepare("SELECT * FROM `active_players` WHERE `playerid` = ? AND `teamcreate` = ?");
		$stmt_getsessiondetails->bind_param("ii", $userID, $stmt_teamcreate);
		$stmt_getsessiondetails->execute();

		$result_getsessiondetails = $stmt_getsessiondetails->get_result();

		return $result_getsessiondetails->num_rows != 0;
	}

	function getSessionDetails(string $sessionID, bool $teamcreate = false): array|null {
		include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

		$stmt_teamcreate = $teamcreate ? 1 : 0;

		$stmt_getsessiondetails = $con->prepare("SELECT * FROM `active_players` WHERE `id` = ? AND `teamcreate` = ?");
		$stmt_getsessiondetails->bind_param("si", $sessionID, $stmt_teamcreate);
		$stmt_getsessiondetails->execute();

		$result_getsessiondetails = $stmt_getsessiondetails->get_result();

		if($result_getsessiondetails->num_rows != 0) {
			return $result_getsessiondetails->fetch_assoc();
		}

		return null;
	}

	function updatePlaceOfSession(string $sessionID, string $placeID, bool $teamcreate = false): array|null {
		include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

		$stmt_teamcreate = $teamcreate ? 1 : 0;

		$stmt_getsessiondetails = $con->prepare("UPDATE `active_players` SET `serverid` = ? WHERE `id` = ? AND `teamcreate` = ?");
		$stmt_getsessiondetails->bind_param("ssi", $placeID, $sessionID, $stmt_teamcreate);
		$stmt_getsessiondetails->execute();

		$result_getsessiondetails = $stmt_getsessiondetails->get_result();

		if($result_getsessiondetails->num_rows != 0) {
			return $result_getsessiondetails->fetch_assoc();
		}

		return null;
	}

	function getServerDetails(string $serverID, bool $teamcreate = false): array|null {
		include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

		$stmt_teamcreate = $teamcreate ? 1 : 0;

		$stmt_getsessiondetails = $con->prepare("SELECT * FROM `active_servers` WHERE `id` = ? AND `teamcreate` = ?");
		$stmt_getsessiondetails->bind_param("si", $serverID, $stmt_teamcreate);
		$stmt_getsessiondetails->execute();

		$result_getsessiondetails = $stmt_getsessiondetails->get_result();

		if($result_getsessiondetails->num_rows != 0) {
			return $result_getsessiondetails->fetch_assoc();
		}

		return null;
	}

	$access = CONFIG->asset->key;
	$arbiter_ip = CONFIG->arbiter->location->private;
	$arbiter_pub_ip = CONFIG->arbiter->location->public;
	$arbiter_token = CONFIG->arbiter->token;
	$domain = CONFIG->domain;

	$arbiter = Arbiter::singleton();
	$db = Database::singleton();

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

	function createResponse(string $jobID, string $serverID, string $sessionID) {
		$domain = CONFIG->domain;
		$arbiter_pub_ip = CONFIG->arbiter->location->public;

		$json = json_encode(
			[
				"jobId" => "$jobID",
				"status" => 2,
				"joinScriptUrl" => "http://$domain/game/join.ashx?serverToken=$serverID&sessionToken=$sessionID&server=$arbiter_pub_ip",
				"authenticationUrl" => "https://$domain/Login/Negotiate.ashx",
				"authenticationTicket" => "$sessionID",
				"message" => "HELLOOOOOOOO!!!!!"
			]
		);

		// i forgot why i did this but it works i guess.
		$json = str_replace("\\\\", "", $json);
		$json = str_replace("\\", "", $json); 

		return $json;
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
		if(isset($_GET['placeId']) &&
		isset($_GET['isPartyLeader']) &&
		isset($_GET['isTeleport']) &&
		$_GET['request'] == "RequestGame") {
			$place = Place::FromID(intval($_GET['placeId']));
			$user = UserUtils::RetrieveUser();

			if($place != null && $user != null) {
				$playerID = $user->id;
				if(isUserInAGame($user->id)) {
					include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
					$stmt_deletesession = $con->prepare("DELETE FROM `active_players` WHERE `playerid` = ?");
					$stmt_deletesession->bind_param("i", $playerID);
					$stmt_deletesession->execute();
				}

				$server = getAnActiveServer($place->id);

				if($server != null) {
					$serverID = $server['id'];
				} else {
					$serverID = strval($place->id);
				}
				$sessionID = getRandomString(25);
				
				include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
				$stmt_createnewsession = $con->prepare("INSERT INTO `active_players`(`id`, `serverid`, `playerid`, `status`) VALUES (?,?,?,0)");
				$stmt_createnewsession->bind_param("ssi", $sessionID, $serverID, $playerID);
				$stmt_createnewsession->execute();

				$dont_load = false;
				if(getActiveServersCount($place->id) == 0) {
					try {
						$placeId = $place->id;

						$gsr = $arbiter->request(
							"gameserver", 
							[
								"PlaceId" => $placeId,
								"MaxPlayers" => $place->server_size,
								"TeamCreate" => false
							]
						);

						if(!$gsr)
							throw new Exception("Failed to create gameserver.");

						$serverid = getRandomString();
						$jobID = $gsr->jobId;
						$port = $gsr->fakeahport;
						$pid = $gsr->pid;

						$strPort = strval($port);

						include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

						$stmt_createnewserver = $con->prepare("INSERT INTO `active_servers` (`id`, `jobid`, `placeid`, `maxcount`, `port`, `pid`) VALUES (?,?,?,?,?,?)");
						$stmt_createnewserver->bind_param("ssiiss", $serverid, $jobID, $placeId, $place->server_size, $strPort, $pid);
						$stmt_createnewserver->execute();

						updatePlaceOfSession($sessionID, $serverid);
					} catch(Exception $e) {
						errorOut(1, $sessionID);
					}
				} else {
					$server_data = getAnActiveServer($place->id);

					if($server_data != null) {
						$serverid = $server_data['id'];
						$jobID = $server_data['jobid'];
					} else {
						$dont_load = true;
					}
				}

				if(!$dont_load) {
					die(createResponse($jobID, $serverid, $sessionID));
				}

			}
		} else if($_GET['request'] == "CloudEdit" && isset($_GET['placeId'])) {
			
			$place = Place::FromID(intval($_GET['placeId']));
			$user = UserUtils::RetrieveUser();

			if($place != null && $user != null) {
				$playerID = $user->id;
				if(isUserInAGame($user->id, true)) {
					include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
					$stmt_deletesession = $con->prepare("DELETE FROM `active_players` WHERE `playerid` = ? AND `teamcreate` = 1");
					$stmt_deletesession->bind_param("i", $playerID);
					$stmt_deletesession->execute();
				}

				$server = getAnActiveServer($place->id, true);

				if($server != null) {
					$serverID = $server['id'];
				} else {
					$serverID = strval($place->id);
				}
				$sessionID = getRandomString(25);
				
				include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
				$stmt_createnewsession = $con->prepare("INSERT INTO `active_players`(`id`, `serverid`, `playerid`, `status`, `teamcreate`) VALUES (?,?,?,0,1)");
				$stmt_createnewsession->bind_param("ssi", $sessionID, $serverID, $playerID);
				$stmt_createnewsession->execute();

				$dont_load = false;
				if(getActiveServersCount($place->id, true) == 0) {
					try {
						$placeId = $place->id;

						$gsr = $arbiter->request(
							"gameserver", 
							[
								"PlaceId" => $placeId,
								"MaxPlayers" => 100,
								"TeamCreate" => true
							]
						);

						if(!$gsr)
							throw new Exception("Failed to create gameserver.");

						$serverid = getRandomString();
						$jobid = $gsr->jobId;
						$port = $gsr->fakeahport;
						$pid = $gsr->pid;
						$strPort = strval($port);
						
						include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
						$stmt_createnewserver = $con->prepare("INSERT INTO `active_servers` (`id`, `jobid`, `placeid`, `maxcount`, `port`, `pid`, `teamcreate`) VALUES (?,?,?,?,?,?,1)");
						$stmt_createnewserver->bind_param("ssiiss", $serverid, $jobid, $placeId, $place->server_size, $strPort, $pid);
						$stmt_createnewserver->execute();

						updatePlaceOfSession($sessionToken, $serverid, true);

					} catch(Exception $e) {
						errorOut(1, $sessionID, true);
					}
				} else {
					$server_data = getAnActiveServer($place->id, true);

					if($server_data != null) {
						$serverid = $server_data['id'];
						$port = $server_data['port'];
					} else {
						$dont_load = true;
					}
				}

				if(!$dont_load) {
					$jobIDThingy = md5(rand());
					$json = json_encode(
						[
							"status" => 2,
							"settings" => [
								"ClientPort" => 0,
								"MachineAddress" => $arbiter_pub_ip,
								"ServerPort" => intval($port),
								"PingUrl" => "",
								"PingInterval" => 120,
								"UserName" => $user->name,
								"SeleniumTestMode" => false,
								"UserId" => $user->id,
								"SuperSafeChat" => false,
								"CharacterAppearance" => "http://$domain/Asset/CharacterFetch.ashx?userId=".$user->id,
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
								"SessionId" => "blehhh".rand(),
								"UniverseId" => $place->id,
							]
						]
					);
					$json = str_replace("\\\\", "",$json);
					$json = str_replace("\\", "", $json); 
					die($json);

				}
			}
		}
	} else if(isset($_GET['sessionID'])) {
		
		$sessionToken = $_GET['sessionID'];
		$session_data = getSessionDetails($sessionToken);

		if($session_data != null) {

			$place = Place::FromID(intval($session_data['serverid']));
			
			if($place == null) {
				$server_details = getServerDetails($session_data['serverid']);
				if($server_details != null) {
					$place = Place::FromID(intval($server_details['placeid']));
				} else {
					$place = null;
				}
			}
			
			$user = User::FromID(intval($session_data['playerid']));

			if($place != null && $user != null && !$user->isBanned()) {
				if(UserUtils::RetrieveUser() == null) {
					UserUtils::SetCookies($user->security_key);
				}
				$dont_load = false;
                if(getActiveServersCount($place->id) == 0) {
					try {
						$placeId = $place->id;

						$gsr = $arbiter->request(
							"gameserver", 
							[
								"PlaceId" => $placeId,
								"MaxPlayers" => $place->server_size,
								"TeamCreate" => false
							]
						);

						if(!$gsr)
							throw new Exception("Failed to create gameserver.");

						$serverid = getRandomString();
						$jobID = $gsr->jobId;
						$port = $gsr->fakeahport;
						$pid = $gsr->pid;
						$strPort = strval($port);

						include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
						$stmt_createnewserver = $con->prepare("INSERT INTO `active_servers`(`id`, `jobid`, `placeid`, `maxcount`, `port`, `pid`) VALUES (?,?,?,?,?,?)");
						$stmt_createnewserver->bind_param("ssiiss", $serverid, $jobID, $placeId, $place->server_size, $strPort, $pid);
						$stmt_createnewserver->execute();

						updatePlaceOfSession($sessionToken, $serverid);

					} catch(Exception $e) {
						errorOut(1, $sessionToken, true);
					}
				} else {
					$server_data = getAnActiveServer($place->id);

					if($server_data) {
						$serverid = $server_data['id'];
						$jobID = $server_data['jobid'];
					} else {
						$dont_load = true;
					}
				}

				if(!$dont_load) {
					die(createResponse($jobID, $serverid, $sessionToken));
				}
				
			}
		}
	}

	errorOut();
?>
