<?php
	use anorrl\Place;
	use anorrl\Database;

	header("Content-Type: application/json");

	if(SESSION) {
		if(isset($_GET['placeId'])) {
			$place = Place::FromID(intval($_GET['placeId']));

			if($place != null) {
				$servers = $place->getServers();

				$data = [];

				$concurrentplayers = 0;

				foreach($servers as $server) {

					$sessions = $server->getSessions();

					$players = [];

					foreach($sessions as $session) {
						$player = $session->player;
						$players[] = [
							"id" => $player->id,
							"name" => $player->name
						];
					}

					$concurrentplayers += count($players);

					$data[] = [
						"id" => $server->id,
						"playercount" => $server->player_count,
						"maxplayercount" => $server->max_count,
						"players" => $players
					];
				}

				Database::singleton()->run(
					"UPDATE `places` SET `currently_playing_count` = :concurrentplayers WHERE `id` = :placeid",
					[
						":concurrentplayers" => $concurrentplayers,
						":placeid" => $place->id
					]
				);

				die(json_encode([
					"is_owner" => $place->isOwner(SESSION->user),
					"servers" => $data
				]));
			}
		}
	}
?>