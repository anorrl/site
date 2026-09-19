<?php
	use anorrl\GameServer;
	use anorrl\GameSession;
	use anorrl\Session;
	use anorrl\Script;

	set_content_type(ARLTYPEPLAIN);

	$serverToken = $_GET['serverToken'] ?? '';
	$sessionToken = $_GET['sessionToken'] ?? '';
	$server = $_GET['server'] ?? "localhost";

	$user = Session::retrieveUser();

	$port = 53640;
	$user_name = "Player";
	$user_id = 0;
	$user_age = is_null($user) ? 0 : $user->getAccountAge();
	$user_ticket = "";
	$session_id = "";
	$anorrl_place = is_null($user);
	$place_id = 0;
	$universe_id = $_GET['universeId'] ?? 0;
	$place_creator_id = 0;
	$place_chat_type = is_null($user) ? "Classic" : "ClassicAndBubble";
	$unknown = is_null($user);
	$game_id = "00000000-0000-0000-0000-000000000000";
	$ping_url = "";
	$character_fetch_url = "{scheme}://{domain}/Asset/CharacterFetch.ashx?userId=1&placeId=0";
	$chatfont = "VCR";

	$debug = true;
	if($debug) {
		$user_id = rand(1,1818);
		$user_ticket = md5(rand());
		$session_id = md5(rand());
		$place_id = 1818;
		$universe_id = 1;
	}

	$serverDetails = GameServer::Get($serverToken);
	$sessionDetails = GameSession::Get($sessionToken);

	if($serverDetails && $sessionDetails) {
		
		$player = $sessionDetails->player;
		$place = $serverDetails->place;
		
		if($player && !$player->isBanned() && $place) {
			if(!Session::retrieveUser())
				Session::setCookies($player->security_key);

			$port = $serverDetails->port;
			$user_name = $player->name;
			$user_id = $player->id;
			$user_age = $player->getAccountAge();
			$session_id = $sessionDetails->id; //base64_encode($player->security_key);
			$user_ticket = $sessionDetails->getTicket();
			$anorrl_place = true;
			$place_id = $place->id;
			$universe_id = $place->universe;
			$place_creator_id = $place->creator->id;
			$unknown = false;
			$game_id = $serverDetails->jobid;
			$ping_url = "{scheme}://{domain}/Game/GamerPinger.ashx?serverID={$serverDetails->id}&jobID={$game_id}";
			$place_chat_type = $place->chat_option->internallabel();
			$character_fetch_url = $player->getCharacterFetchURL();
		}
	}
	
	$joinscript = [
		"MachineAddress" => $server,
		"ServerPort" => $port,
		"PingUrl" => $ping_url,
		"PingInterval" => 120,
		"UserName" => $user_name,
		"UserId" => (int)$user_id,
		"SuperSafeChat" => $unknown,
		"CharacterAppearance" => $character_fetch_url,
		"ClientTicket" => $user_ticket,
		"GameId" => $game_id,
		"PlaceId" => $place_id,
		"BaseUrl" => "{scheme}://{domain}/",
		"ChatStyle" => $place_chat_type,
		"CreatorId" => $place_creator_id,
		"CreatorTypeEnum" => "User",
		"MembershipType" => "None", // maybe
		"AccountAge" => $user_age,
		"CookieStoreEnabled" => true,
		"IsANORRLPlace" => $anorrl_place,
		"GenerateTeleportJoin" => false,
		"SessionId" => $session_id,
		"UniverseId" => (int)$universe_id,
		"ChatFont" => $chatfont,
	];

	die(Script::SignNonScript(json_encode($joinscript)));
?>
