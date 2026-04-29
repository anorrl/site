<?php
	use anorrl\GameServer;
	use anorrl\GameSession;
	use anorrl\utilities\UserUtils;
	use anorrl\Script;

	header("Content-Type: text/plain");

	$serverToken = $_GET['serverToken'] ?? '';
	$sessionToken = $_GET['sessionToken'] ?? '';
	$server = $_GET['server'] ?? "localhost";

	$port = 53640;
	$user_name = "Player";
	$user_id = 0;
	$user_age = 0;
	$user_ticket = "";
	$session_id = "";
	$roblox_place = false;
	$place_id = 0;
	$place_creator_id = 0;
	$place_chat_type = "ClassicAndBubble"; // $place->getChatType()->name();
	$unknown = true;
	$game_id = "00000000-0000-0000-0000-000000000000";
	$ping_url = "";
	
	$serverDetails = GameServer::Get($serverToken);
	$sessionDetails = GameSession::Get($sessionToken);

	if($serverDetails && $sessionDetails) {
		
		$player = $sessionDetails->player;
		$place = $serverDetails->place;
		
		if($player && !$player->isBanned() && $place) {

			if(UserUtils::RetrieveUser() == null) {
				UserUtils::SetCookies($player->security_key);
			}

			$port = $serverDetails->port;
			$user_name = $player->name;
			$user_id = $player->id;
			$user_age = $player->getAccountAge();
			$session_id = base64_encode($player->security_key);
			$user_ticket = $sessionDetails->id;
			$roblox_place = true;
			$place_id = $place->id;
			$place_creator_id = $place->creator->id;
			$unknown = false;
			$game_id = $serverDetails->jobid;
			$ping_url = "http://{domain}/Game/GamerPinger.ashx?serverID={$serverDetails->id}&jobID={$game_id}";
		}
	}
	
	$joinscript = [
		"ClientPort" => 0,
		"MachineAddress" => $server,
		"ServerPort" => $port,
		"PingUrl" => $ping_url,
		"PingInterval" => 120,
		"UserName" => $user_name,
		"SeleniumTestMode" => false,
		"UserId" => (int)$user_id,
		"SuperSafeChat" => $unknown,
		"CharacterAppearance" => "http://{domain}/Asset/CharacterFetch.ashx?userId={$user_id}",
		"ClientTicket" => $user_ticket,
		"GameId" => $game_id,
		"PlaceId" => $place_id,
		"MeasurementUrl" => "",
		"WaitingForCharacterGuid" => "16be1dd8-5462-4ca5-a997-0725d997708b",
		"BaseUrl" => "http://{domain}/",
		"ChatStyle" => $place_chat_type, // move this to place soon
		"CreatorId" => $place_creator_id,
		"CreatorTypeEnum" => "User",
		"MembershipType" => "None", // maybe
		"AccountAge" => $user_age,
		"CookieStoreEnabled" => false,
		"IsRobloxPlace" => $roblox_place,
		"GenerateTeleportJoin" => false,
		"IsUnknownOrUnder13" => $unknown,
		"SessionId" => $session_id,
		"UniverseId" => $place_id,
		"characterAppearanceId" => $user_id
	];

	die(Script::SignNonScript(json_encode($joinscript)));
?>
