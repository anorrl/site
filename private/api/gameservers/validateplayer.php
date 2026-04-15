<?php
	use anorrl\User;
	use anorrl\Place;
	
	function getServerDetailsFromJobID(string $jobID): array|null {
		include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

		$stmt_getsessiondetails = $con->prepare("SELECT * FROM `active_servers` WHERE `jobid` = ?");
		$stmt_getsessiondetails->bind_param("s", $jobID);
		$stmt_getsessiondetails->execute();

		$result_getsessiondetails = $stmt_getsessiondetails->get_result();

		if($result_getsessiondetails->num_rows != 0) {
			return $result_getsessiondetails->fetch_assoc();
		}

		return null;
	}

	if(isset($_GET['access']) && isset($_GET['jobID']) && isset($_GET['userID'])) {
		if($_GET['access'] == CONFIG->asset->key) {
			$server_details = getServerDetailsFromJobID($_GET['jobID']);
			$user = User::FromID(intval($_GET['userID']));

			if($server_details != null && $user != null) {
				$place = Place::FromID(intval($server_details['placeid']));
				if($place != null) {
					include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
					$stmt_checkplayer = $con->prepare("SELECT * FROM `active_players` WHERE `serverid` = ? AND `playerid` = ?;");
					$stmt_checkplayer->bind_param("si", $server_details['id'], $user->id);
					$stmt_checkplayer->execute();

					$result_checkplayer = $stmt_checkplayer->get_result();

					if($result_checkplayer->num_rows != 0) {
						$stmt_checkplayer = $con->prepare("UPDATE `active_players` SET `status` = 1 WHERE `serverid` = ? AND `playerid` = ?;");
						$stmt_checkplayer->bind_param("si", $server_details['id'], $user->id);

						if($stmt_checkplayer->execute()) {
							$place->visit($user);
							echo "OK";
						}
					}
				}
			}
		}
	}
?>
