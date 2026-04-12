<?php
	use anorrl\Place;
	use anorrl\User;

	header("Content-Type: application/json");

	include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";


	if(SESSION) {
		if(isset($_GET['placeId'])) {
			$place = Place::FromID(intval($_GET['placeId']));

			if($place != null) {
				$stmt_checkserver = $con->prepare("SELECT * FROM `active_servers` WHERE `placeid` = ? AND `teamcreate` = 0;");
				$stmt_checkserver->bind_param("i", $place->id);
				$stmt_checkserver->execute();

				$result_checkserver = $stmt_checkserver->get_result();

				$data = [];

				$concurrentplayers = 0;

				while($server_row = $result_checkserver->fetch_assoc()) {

					$stmt_checkplayersfromserver = $con->prepare("SELECT * FROM `active_players` WHERE `serverid` = ? AND `status` = 1 AND `teamcreate` = 0;");
					$stmt_checkplayersfromserver->bind_param("s", $server_row['id']);
					$stmt_checkplayersfromserver->execute();

					$result_checkplayersfromserver = $stmt_checkplayersfromserver->get_result();

					$players = [];

					if($result_checkplayersfromserver->num_rows != 0) {
						while($session_row = $result_checkplayersfromserver->fetch_assoc()) {
							$player = User::FromID(intval($session_row['playerid']));
							$players[] = [
								"id" => $player->id,
								"name" => $player->name
							];
						}
					}

					$concurrentplayers += count($players);

					$data[] = [
						"id" => $server_row['id'],
						"playercount" => $server_row['playercount'],
						"maxplayercount" => $server_row['maxcount'],
						"players" => $players
					];
				}

				$stmt_updateplayercount = $con->prepare("UPDATE `places` SET `currently_playing` = ? WHERE `id` = ?");
				$stmt_updateplayercount->bind_param("ii", $concurrentplayers, $place->id);
				$stmt_updateplayercount->execute();

				die(json_encode($data));
			}
		}
	}
?>