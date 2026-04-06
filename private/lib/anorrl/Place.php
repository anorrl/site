<?php

	namespace anorrl;

	use anorrl\Asset;
	use anorrl\Database;
	use anorrl\enums\AssetType;
	use anorrl\utilities\AssetUtils;

	class Place extends Asset {
		/** is the same as Asset::public */
		public bool $friends_only;
		public bool $copylocked;
		public int $server_size;
		public int  $visit_count;
		public int  $current_playing_count;
		public bool $is_original;
		public bool $gears_enabled;
		public bool $teamcreate_enabled;

		public static function UpdatePlaceStats(int $placeID) {
			$place = Place::FromID($placeID);

			if($place != null) {
				$fetch_servers = Database::singleton()->run(
					"SELECT * FROM `active_servers` WHERE `server_placeid` = :placeid AND `server_teamcreate` = 0;",
					[ ":placeid" => $place->id ]
				)->fetchAll(\PDO::FETCH_OBJ);

				$concurrentplayers = 0;

				foreach($fetch_servers as $server_row) {
					$fetch_players = Database::singleton()->run(
						"SELECT COUNT(`session_id`) FROM `active_players` WHERE `session_serverid` = :serverid AND `session_status` = 1;",
						[ ":serverid" => $server_row->server_id ]
					)->fetch(\PDO::FETCH_ASSOC);

					$concurrentplayers += $fetch_players['COUNT(`session_id`)'];
				}

				$fetch_servers = Database::singleton()->run(
					"UPDATE `asset_places` SET `place_currently_playing` = :playerscount WHERE `place_id` = :placeid",
					[
						":placeid" => $place->id,
						":playerscount" => $concurrentplayers
					]
				);
			}
		}

		public static function UpdateAllPlaces() {
			foreach(AssetUtils::Get(AssetType::PLACE) as $place) {
				if($place instanceof Place) {
					$visits = $place->visit_count;
					
					if($visits > 100 && !$place->creator->HasProfileBadgeOf(ANORRLBadge::HOMESTEAD)) {
						$place->creator->GiveProfileBadge(ANORRLBadge::HOMESTEAD);
					}

					if($visits > 1000 && !$place->creator->HasProfileBadgeOf(ANORRLBadge::BRICKSMITH)) {
						$place->creator->GiveProfileBadge(ANORRLBadge::BRICKSMITH);
					}

					self::UpdatePlaceStats($place->id);
				}
				
			}
		}

		public static function FromID(int $id): Place|null {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt_getuser = $con->prepare("SELECT * FROM `asset_places` WHERE `place_id` = ?");
			$stmt_getuser->bind_param('i', $id);
			$stmt_getuser->execute();
			$result = $stmt_getuser->get_result();

			if($result->num_rows == 1) {
				return new self($result->fetch_assoc());
			} else {
				return null;
			}
		}

		function __construct($rowdata) {
			parent::__construct(intval($rowdata['place_id']));

			$this->friends_only = $this->public;
			$this->copylocked = boolval($rowdata['place_copylocked']);
			$this->server_size = intval($rowdata['place_serversize']);
			$this->visit_count = intval($rowdata['place_visit_count']);
			$this->current_playing_count = intval($rowdata['place_currently_playing']);
			$this->teamcreate_enabled = boolval($rowdata['place_teamcreate_enabled']);

			$this->is_original = boolval($rowdata['place_original']);
			$this->gears_enabled = boolval($rowdata['place_gears_enabled']);
		}

		function EnableTeamCreate() {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt_enableteamcreate = $con->prepare('UPDATE `asset_places` SET `place_teamcreate_enabled` = 1 WHERE `place_id` = ?');
			$stmt_enableteamcreate->bind_param('i', $this->id);
			$stmt_enableteamcreate->execute();

			if(!$this->IsCloudEditor($this->creator)) {
				$this->AddCloudEditor($this->creator);
			}
		}

		function DisableTeamCreate() {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt_disableteamcreate = $con->prepare('UPDATE `asset_places` SET `place_teamcreate_enabled` = 0 WHERE `place_id` = ?');
			$stmt_disableteamcreate->bind_param('i', $this->id);
			$stmt_disableteamcreate->execute();

			if($this->teamcreate_enabled) {
				$stmt_checkiseditor = $con->prepare('DELETE FROM `cloudeditors` WHERE `cloudeditor_userid` != ? AND `cloudeditor_placeid` = ?;');
				$stmt_checkiseditor->bind_param('ii', $this->creator->id, $this->id);
				$stmt_checkiseditor->execute();

				$stmt_getactiveservers = $con->prepare("SELECT * FROM `active_servers` WHERE `server_placeid` = ? AND `server_teamcreate` = 1");
				$stmt_getactiveservers->bind_param("i", $this->id);
				$stmt_getactiveservers->execute();

				$result_getactiveservers = $stmt_getactiveservers->get_result();

				if($result_getactiveservers->num_rows != 0) {
					$row = $result_getactiveservers->fetch_assoc();

					$jobID = $row['server_jobid'];

					$data = json_encode([
						"pid" => $row['server_pid']
					]);

					$arbiter_ip = \CONFIG->arbiter->location->private;
					$arbiter_token = \CONFIG->arbiter->token;

					$ch = curl_init("http://$arbiter_ip/api/v1/gameserver/kill");
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
						die(http_response_code(503));
					}

					include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
					$stmt_createnewserver = $con->prepare("DELETE FROM `active_servers` WHERE `server_jobid` = ?;");
					$stmt_createnewserver->bind_param("s", $jobID);
					$stmt_createnewserver->execute();
				}

				
			}
		}

		function IsCloudEditor(User $user) {
			if($this->teamcreate_enabled) {
				include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
				$stmt_checkiseditor = $con->prepare('SELECT * FROM `cloudeditors` WHERE `cloudeditor_userid` = ? AND `cloudeditor_placeid` = ?;');
				$stmt_checkiseditor->bind_param('ii', $user->id, $this->id);
				$stmt_checkiseditor->execute();

				return $stmt_checkiseditor->get_result()->num_rows != 0;
			}
			return false;
		}

		function AddCloudEditor(User $user) {
			if(!$this->IsCloudEditor($user)) {
				include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
				$stmt_addeditor = $con->prepare('INSERT INTO `cloudeditors`(`cloudeditor_userid`, `cloudeditor_placeid`) VALUES (?, ?)');
				$stmt_addeditor->bind_param('ii', $user->id, $this->id);
				$stmt_addeditor->execute();
			}	
		}

		function RemoveCloudEditor(User $user) {
			if($this->IsCloudEditor($user) && $user->id != $this->creator->id) {
				include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
				$stmt_addeditor = $con->prepare('DELETE FROM `cloudeditors` WHERE `cloudeditor_userid` = ? AND `cloudeditor_placeid` = ?;');
				$stmt_addeditor->bind_param('ii', $user->id, $this->id);
				$stmt_addeditor->execute();
			}	
		}

		function GetCloudEditors() {
			if($this->teamcreate_enabled) {
				include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
				$stmt_geteditors = $con->prepare('SELECT * FROM `cloudeditors` WHERE `cloudeditor_placeid` = ?;');
				$stmt_geteditors->bind_param('i', $this->id);
				$stmt_geteditors->execute();

				$result_geteditors = $stmt_geteditors->get_result();

				$result = [];

				while($row = $result_geteditors->fetch_assoc()) {
					$user = User::FromID(intval($row['cloudeditor_userid']));

					if($user != null && !$user->IsBanned()) {
						array_push($result, $user);
					}
				}

				return $result;
			}
			return [];
		}

		function Visit(User|int $user) {
			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}

			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";

			$placeid = $this->id;

			$stmt_checkvisit = $con->prepare('SELECT * FROM `visit` WHERE `visit_place` = ? AND `visit_player` = ? AND `visit_time` >= CURDATE() - INTERVAL 1 HOUR;');
			$stmt_checkvisit->bind_param('ii', $placeid, $userid);
			$stmt_checkvisit->execute();

			if($stmt_checkvisit->get_result()->num_rows == 0) {
				$stmt_addvisit = $con->prepare('INSERT INTO `visit`(`visit_place`, `visit_player`) VALUES (?, ?)');
				$stmt_addvisit->bind_param('ii', $placeid, $userid);
				$stmt_addvisit->execute();

				// Update

				$stmt_visitcount = $con->prepare('SELECT * FROM `visit` WHERE `visit_place` = ?;');
				$stmt_visitcount->bind_param('i', $placeid);
				$stmt_visitcount->execute();
	
				$visits = $stmt_visitcount->get_result()->num_rows;

				if($visits > 100 && !$this->creator->HasProfileBadgeOf(ANORRLBadge::HOMESTEAD)) {
					$this->creator->GiveProfileBadge(ANORRLBadge::HOMESTEAD);
				}

				if($visits > 1000 && !$this->creator->HasProfileBadgeOf(ANORRLBadge::BRICKSMITH)) {
					$this->creator->GiveProfileBadge(ANORRLBadge::BRICKSMITH);
				}
	
				$stmt = $con->prepare('UPDATE `asset_places` SET `place_visit_count` = ? WHERE `place_id` = ?;');
				$stmt->bind_param('ii', $visits, $placeid);
				$stmt->execute();
			}
		}

		function GetBadges(): array {
			return [];
		}
	}

?>