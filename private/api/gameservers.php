<?php
	header("Content-Type: application/json");

	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/userutils.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/assetutils.php";
	include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";

	$user = UserUtils::RetrieveUser();

	if($user != null) {
		if(isset($_GET['placeId'])) {
			$place = Place::FromID(intval($_GET['placeId']));

			if($place != null) {
				$stmt_checkserver = $con->prepare("SELECT * FROM `active_servers` WHERE `server_placeid` = ? AND `server_teamcreate` = 0;");
				$stmt_checkserver->bind_param("i", $place->id);
				$stmt_checkserver->execute();

				$result_checkserver = $stmt_checkserver->get_result();

				$data = [];

				$concurrentplayers = 0;

				while($server_row = $result_checkserver->fetch_assoc()) {

					$stmt_checkplayersfromserver = $con->prepare("SELECT * FROM `active_players` WHERE `session_serverid` = ? AND `session_status` = 1 AND `session_teamcreate` = 0;");
					$stmt_checkplayersfromserver->bind_param("s", $server_row['server_id']);
					$stmt_checkplayersfromserver->execute();

					$result_checkplayersfromserver = $stmt_checkplayersfromserver->get_result();

					$players = [];

					if($result_checkplayersfromserver->num_rows != 0) {
						while($session_row = $result_checkplayersfromserver->fetch_assoc()) {
							$player = User::FromID(intval($session_row['session_playerid']));
							array_push($players, [
								"id" => $player->id,
								"name" => $player->name
							]);
						}
					}

					$concurrentplayers += count($players);

					array_push($data, [
						"id" => $server_row['server_id'],
						"playercount" => $server_row['server_playercount'],
						"maxplayercount" => $server_row['server_maxcount'],
						"players" => $players
					]);
				}

				$stmt_updateplayercount = $con->prepare("UPDATE `asset_places` SET `place_currently_playing` = ? WHERE `place_id` = ?");
				$stmt_updateplayercount->bind_param("ii", $concurrentplayers, $place->id);
				$stmt_updateplayercount->execute();

				die(json_encode($data));
			}
		}
	}
?>