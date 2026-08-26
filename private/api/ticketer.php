<?php
	use anorrl\GameServer;
	use anorrl\Place;

	$user = SESSION->user;
	$domain = CONFIG->domain;

	// suddenly i dont care about sessions in this api
	// move to old roblox standard for easier TeleportService shenanigans

	if($user != null) {
		if(isset($_POST['editID'])) {
			$place = Place::FromID(intval($_POST['editID']));

			if($place && $place->isEditable($user)) {
				$placeID = $place->id;
				$clientticket = base64_encode($user->security_key);

				$placelauncherurl = urlencode("http://{$domain}/game/edit.slua?placeId={$place->id}");
				$session = base64_encode($user->security_key);

				die("anorrl-studio:1+script:{$placelauncherurl}+placeid:{$place->id}+launchmode:edit+gameinfo:{$session}");
			} else {
				die(!$place ? "Invalid place!" : "This place is not available for editing!");
			}
		} elseif(isset($_POST['placeID'])) {
			$placeid = intval($_POST['placeID']);
			if(Place::Exists($placeid)) {
				if($user->isInAGame()) {
					$active_server = $user->getActiveGame();
					if($active_server)
						$active_server->removePlayer($user);
				}

				$placelauncherurl = urlencode("http://{$domain}/game/PlaceLauncher.ashx?request=RequestGame&placeId={$placeid}&isTeleport=false");
				$session = base64_encode($user->security_key);

				die("anorrl-player:1+placelauncherurl:{$placelauncherurl}+placeid:{$placeid}+launchmode:play+gameinfo:{$session}");
			}
		} else if(isset($_POST['serverID'])) {
			$server = GameServer::Get($_POST['serverID']);

			if($server) {
				if(!$server->active()) {
					die();
				}

				$place = $server->place;

				if($user->isInAGame()) {
					$active_server = $user->getActiveGame();
					if($active_server)
						$active_server->removePlayer($user);
				}

				$placelauncherurl = urlencode("http://{$domain}/game/PlaceLauncher.ashx?request=RequestGame&serverId={$server->id}&isTeleport=false");
				$session = urlencode(base64_encode($user->security_key));

				die("anorrl-player:1+placelauncherurl:{$placelauncherurl}+placeid:{$place->id}+launchmode:play+gameinfo:{$session}");
			}
			
		}
	}
?>
