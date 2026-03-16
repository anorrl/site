<?php

	/**
	 * I dont fucking know bruh this is like cancer in a way
	 * 
	 * GOOD THING THIS IS FRIENDS ONLY OTHER WISE I WOULD BE COOKED. 
	 * I CANNOT BE ASKED TO DO SECURITY STUFF AS MUCH AS I WANT TO.
	 */

	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/assetutils.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/renderer.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/userutils.php";

	$settings = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../settings.env", true);
	$rcc_settings = $settings['renderer'];

	$access = $settings['asset']['ACCESSKEY'];
	
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
		include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";

		$stmt_teamcreate = $teamcreate ? 1 : 0;

		$stmt_getactiveservers = $con->prepare("SELECT * FROM `active_servers` WHERE `server_placeid` = ? AND `server_playercount` != `server_maxcount` AND `server_teamcreate` = ?");
		$stmt_getactiveservers->bind_param("ii", $placeID, $stmt_teamcreate);
		$stmt_getactiveservers->execute();

		$result_getactiveservers = $stmt_getactiveservers->get_result();

		return $result_getactiveservers->num_rows;
	}

	function getAnActiveServer(int $placeID, bool $teamcreate = false): array|null {
		include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";

		$stmt_teamcreate = $teamcreate ? 1 : 0;

		$stmt_getactiveservers = $con->prepare("SELECT * FROM `active_servers` WHERE `server_placeid` = ? AND `server_playercount` < `server_maxcount` AND `server_teamcreate` = ?");
		$stmt_getactiveservers->bind_param("ii", $placeID, $stmt_teamcreate);
		$stmt_getactiveservers->execute();

		$result_getactiveservers = $stmt_getactiveservers->get_result();

		if($result_getactiveservers->num_rows != 0) {
			return $result_getactiveservers->fetch_assoc();
		}

		return null;
	}

	function isUserInAGame(int $userID, bool $teamcreate = false): bool {
		include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";

		$stmt_teamcreate = $teamcreate ? 1 : 0;

		$stmt_getsessiondetails = $con->prepare("SELECT * FROM `active_players` WHERE `session_playerid` = ? AND `session_teamcreate` = ?");
		$stmt_getsessiondetails->bind_param("ii", $userID, $stmt_teamcreate);
		$stmt_getsessiondetails->execute();

		$result_getsessiondetails = $stmt_getsessiondetails->get_result();

		return $result_getsessiondetails->num_rows != 0;
	}

	function getSessionDetails(string $sessionID, bool $teamcreate = false): array|null {
		include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";

		$stmt_teamcreate = $teamcreate ? 1 : 0;

		$stmt_getsessiondetails = $con->prepare("SELECT * FROM `active_players` WHERE `session_id` = ? AND `session_teamcreate` = ?");
		$stmt_getsessiondetails->bind_param("si", $sessionID, $stmt_teamcreate);
		$stmt_getsessiondetails->execute();

		$result_getsessiondetails = $stmt_getsessiondetails->get_result();

		if($result_getsessiondetails->num_rows != 0) {
			return $result_getsessiondetails->fetch_assoc();
		}

		return null;
	}

	function updatePlaceOfSession(string $sessionID, string $placeID, bool $teamcreate = false): array|null {
		include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";

		$stmt_teamcreate = $teamcreate ? 1 : 0;

		$stmt_getsessiondetails = $con->prepare("UPDATE `active_players` SET `session_serverid` = ? WHERE `session_id` = ? AND `session_teamcreate` = ?");
		$stmt_getsessiondetails->bind_param("ssi", $placeID, $sessionID, $stmt_teamcreate);
		$stmt_getsessiondetails->execute();

		$result_getsessiondetails = $stmt_getsessiondetails->get_result();

		if($result_getsessiondetails->num_rows != 0) {
			return $result_getsessiondetails->fetch_assoc();
		}

		return null;
	}

	function getServerDetails(string $serverID, bool $teamcreate = false): array|null {
		include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";

		$stmt_teamcreate = $teamcreate ? 1 : 0;

		$stmt_getsessiondetails = $con->prepare("SELECT * FROM `active_servers` WHERE `server_id` = ? AND `server_teamcreate` = ?");
		$stmt_getsessiondetails->bind_param("si", $serverID, $stmt_teamcreate);
		$stmt_getsessiondetails->execute();

		$result_getsessiondetails = $stmt_getsessiondetails->get_result();

		if($result_getsessiondetails->num_rows != 0) {
			return $result_getsessiondetails->fetch_assoc();
		}

		return null;
	}

	$arbiter_ip = $settings['arbiter']['LOC']; //"37.114.46.52";
    $arbiter_pub_ip = $settings['arbiter']['PUB_LOC']; //"37.114.46.52";
    $arbiter_token = $settings['arbiter']['token']; //"37.114.46.52";

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
		isset($_GET['gender']) &&
		isset($_GET['isTeleport']) &&
		$_GET['request'] == "RequestGame" &&
		$_GET['gender'] == "") {
			$place = Place::FromID(intval($_GET['placeId']));
			$user = UserUtils::RetrieveUser();

			if($place != null && $user != null) {
				$playerID = $user->id;
				if(isUserInAGame($user->id)) {
					include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";
					$stmt_deletesession = $con->prepare("DELETE FROM `active_players` WHERE `session_playerid` = ?");
					$stmt_deletesession->bind_param("i", $playerID);
					$stmt_deletesession->execute();
				}

				$server = getAnActiveServer($place->id);

				if($server != null) {
					$serverID = $server['server_id'];
				} else {
					$serverID = strval($place->id);
				}
				$sessionID = getRandomString(25);
				
				include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";
				$stmt_createnewsession = $con->prepare("INSERT INTO `active_players`(`session_id`, `session_serverid`, `session_playerid`, `session_status`) VALUES (?,?,?,0)");
				$stmt_createnewsession->bind_param("ssi", $sessionID, $serverID, $playerID);
				$stmt_createnewsession->execute();

				$dont_load = false;
				if(getActiveServersCount($place->id) == 0) {
					try {
						$placeId = $place->id;

						$data = json_encode([
							"PlaceId" => $placeId,
							"MaxPlayers" => $place->server_size,
							"TeamCreate" => false
						]);

						$ch = curl_init("http://$arbiter_ip/api/v1/gameserver");

						curl_setopt($ch, CURLOPT_HTTPHEADER, [
							"Authorization: Bearer $arbiter_token",
							"Content-Type: application/json",
							"User-Agent: ANORRL/1.0"
						]);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

						$response = curl_exec($ch);
						$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
						curl_close($ch);

						if ($code != 200) {
							include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";
							$stmt = $con->prepare("DELETE FROM `active_players` WHERE `session_id` = ?");
							$stmt->bind_param("s", $sessionID);
							$stmt->execute();

							die(json_encode([
								"status" => 0,
								"error" => "Wow so much errors!"
							]));
						}

						$json = json_decode($response, true);

						$serverid = getRandomString();
						$jobid = $json['jobId'];
						$port = $json['fakeahport'];
						$pid = $json['pid'];

						$strPort = strval($port);

						include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";

						$stmt_createnewserver = $con->prepare("INSERT INTO `active_servers` (`server_id`, `server_jobid`, `server_placeid`, `server_maxcount`, `server_port`, `server_pid`) VALUES (?,?,?,?,?,?)");
						$stmt_createnewserver->bind_param("ssiiss", $serverid, $jobid, $placeId, $place->server_size, $strPort, $pid);
						$stmt_createnewserver->execute();

						updatePlaceOfSession($sessionID, $serverid);

					} catch(SoapFault $e) {
						include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";
						$stmt_createnewserver = $con->prepare("DELETE FROM `active_players` WHERE `session_id` = ?;");
						$stmt_createnewserver->bind_param("s", $sessionID);
						$stmt_createnewserver->execute();
						die(json_encode([
							"status" => 1,
							"message" => "Wow so much errors!"
						]));
					}
				} else {
					$server_data = getAnActiveServer($place->id);

					if($server_data != null) {
						$serverid = $server_data['server_id'];
					} else {
						$dont_load = true;
					}
				}

				if(!$dont_load) {
					$jobIDThingy = md5(rand());
					$json = json_encode(
						[
							"jobId" => "$jobIDThingy",
							"status" => 2,
							"joinScriptUrl" => "http://arl.lambda.cam/game/join.ashx?serverToken=$serverid&sessionToken=$sessionID&server=$arbiter_pub_ip",
							"authenticationUrl" => "https://arl.lambda.cam/Login/Negotiate.ashx",
							"authenticationTicket" => "$sessionID",
							"message" => "HELLOOOOOOOO!!!!!"
						]
					);
					$json = str_replace("\\\\", "", $json);
					$json = str_replace("\\", "", $json); 
					die($json);
				}

			}
		} else if($_GET['request'] == "CloudEdit" && isset($_GET['placeId'])) {
			
			$place = Place::FromID(intval($_GET['placeId']));
			$user = UserUtils::RetrieveUser();

			if($place != null && $user != null) {
				$playerID = $user->id;
				if(isUserInAGame($user->id, true)) {
					include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";
					$stmt_deletesession = $con->prepare("DELETE FROM `active_players` WHERE `session_playerid` = ? AND `session_teamcreate` = 1");
					$stmt_deletesession->bind_param("i", $playerID);
					$stmt_deletesession->execute();
				}

				$server = getAnActiveServer($place->id, true);

				if($server != null) {
					$serverID = $server['server_id'];
				} else {
					$serverID = strval($place->id);
				}
				$sessionID = getRandomString(25);
				
				include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";
				$stmt_createnewsession = $con->prepare("INSERT INTO `active_players`(`session_id`, `session_serverid`, `session_playerid`, `session_status`, `session_teamcreate`) VALUES (?,?,?,0,1)");
				$stmt_createnewsession->bind_param("ssi", $sessionID, $serverID, $playerID);
				$stmt_createnewsession->execute();

				$dont_load = false;
				if(getActiveServersCount($place->id, true) == 0) {
					try {
						$placeId = $place->id;
						
						$data = json_encode([
							"PlaceId" => $placeId,
							"MaxPlayers" => 100,
							"TeamCreate" => true
						]);

						$ch = curl_init("http://$arbiter_ip/api/v1/gameserver");

						curl_setopt($ch, CURLOPT_HTTPHEADER, [
							"Authorization: Bearer $arbiter_token",
							"Content-Type: application/json",
							"User-Agent: ANORRL/1.0"
						]);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
						$response = curl_exec($ch);
						$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
						curl_close($ch);

						if($code != 200) {
							http_response_code(503);
							die(json_encode([
								"status" => 0,
								"error" => "Wow so much errors!"
							]));
						}

						$json = json_decode($response, true);
						$serverid = getRandomString();
						$jobid = $json['jobId'];
						$port = $json['fakeahport'];
						$pid = $json['pid'];
						$strPort = strval($port);
						
						include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";
						$stmt_createnewserver = $con->prepare("INSERT INTO `active_servers` (`server_id`, `server_jobid`, `server_placeid`, `server_maxcount`, `server_port`, `server_pid`, `server_teamcreate`) VALUES (?,?,?,?,?,?,1)");
						$stmt_createnewserver->bind_param("ssiiss", $serverid, $jobid, $placeId, $place->server_size, $strPort, $pid);
						$stmt_createnewserver->execute();

						updatePlaceOfSession($sessionToken, $serverid, true);

					} catch(SoapFault $e) {
						
						include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";
						$stmt_createnewserver = $con->prepare("DELETE FROM `active_players` WHERE `session_id` = ? AND `session_teamcreate` = 1;");
						$stmt_createnewserver->bind_param("s", $sessionID);
						$stmt_createnewserver->execute();
						
						die(json_encode([
							"status" => 0,
							"error" => "Wow so much errors!"
						]));
					}
				} else {
					$server_data = getAnActiveServer($place->id, true);

					if($server_data != null) {
						$serverid = $server_data['server_id'];
						$port = $server_data['server_port'];
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
									"CharacterAppearance" => "http://arl.lambda.cam/Asset/CharacterFetch.ashx?userId=".$user->id,
									"ClientTicket" => $sessionID,
									"GameId" =>"00000000-0000-0000-0000-000000000000",
									"PlaceId" => $place->id,
									"MeasurementUrl" => "",
									"WaitingForCharacterGuid" => "16be1dd8-5462-4ca5-a997-0725d997708b",
									"BaseUrl" => "http://arl.lambda.cam/",
									"ChatStyle" => "ClassicAndBubble",
									"VendorId" => 0,
									"ScreenShotInfo" => "",
									"VideoInfo" => "",
									"CreatorId" => $place->creator->id,
									"CreatorTypeEnum" => "User",
									"MembershipType" => "None",
									"AccountAge" => 256,
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

			$place = Place::FromID(intval($session_data['session_serverid']));
			
			if($place == null) {
				$server_details = getServerDetails($session_data['session_serverid']);
				if($server_details != null) {
					$place = Place::FromID(intval($server_details['server_placeid']));
				} else {
					$place = null;
				}
				
			}
			
			$user = User::FromID(intval($session_data['session_playerid']));

			if($place != null && $user != null && !$user->IsBanned()) {
				if(UserUtils::RetrieveUser() == null) {
					UserUtils::SetCookies($user->security_key);
				}
				$dont_load = false;
                if(getActiveServersCount($place->id) == 0) {
					try {
						$placeId = $place->id;
						
						$data = json_encode([
							"PlaceId" => $placeId,
							"MaxPlayers" => $place->server_size,
							"TeamCreate" => false
						]);

						$ch = curl_init("http://$arbiter_ip/api/v1/gameserver");

						curl_setopt($ch, CURLOPT_HTTPHEADER, [
							"Authorization: Bearer $arbiter_token",
							"Content-Type: application/json",
							"User-Agent: ANORRL/1.0"
						]);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
						$response = curl_exec($ch);
						$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
						curl_close($ch);

						if($code != 200) {
							http_response_code(503);
                            die(json_encode([
                                "status" => 0,
                                "error" => "Wow so much errors!"
                            ]));
						}

						$json = json_decode($response, true);
						$serverid = getRandomString();
                        $jobid = $json['jobId'];
						$port = $json['fakeahport'];
						$pid = $json['pid'];
						$strPort = strval($port);

						include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";
						$stmt_createnewserver = $con->prepare("INSERT INTO `active_servers`(`server_id`, `server_jobid`, `server_placeid`, `server_maxcount`, `server_port`, `server_pid`) VALUES (?,?,?,?,?,?)");
						$stmt_createnewserver->bind_param("ssiiss", $serverid, $jobid, $placeId, $place->server_size, $strPort, $pid);
						$stmt_createnewserver->execute();

						updatePlaceOfSession($sessionToken, $serverid);

					} catch(Exception $e) {
						include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";
						$stmt_createnewserver = $con->prepare("DELETE FROM `active_players` WHERE `session_id` = ?;");
						$stmt_createnewserver->bind_param("s", $sessionToken);
						$stmt_createnewserver->execute();
						die(json_encode([
							"status" => 0,
							"error" => "Wow so much errors!"
						]));
					}
				} else {
					$server_data = getAnActiveServer($place->id);

					if($server_data != null) {
						$serverid = $server_data['server_id'];
					} else {
						$dont_load = true;
					}
				}

				if(!$dont_load) {
					$jobIDThingy = md5(rand());
					die(json_encode(
						[
							"jobId" => "$jobIDThingy",
							"status" => 2,
							"joinScriptUrl" => "http://arl.lambda.cam/game/join.ashx?serverToken=$serverid&sessionToken=$sessionToken&server=$arbiter_pub_ip",//",
							"authenticationUrl" => "https://arl.lambda.cam/Login/Negotiate.ashx",
							"authenticationTicket" => "$sessionToken",
							"message" => "HELLOOOOOOOO!!!!!"
						]
					));
				}
				
			}
		}
	}

	die(json_encode([
		"status" => 0,
		"error" => "Wow so much errors!"
	]));
	

?>
