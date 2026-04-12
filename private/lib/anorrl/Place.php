<?php

	namespace anorrl;

	use anorrl\Asset;
	use anorrl\Database;
	use anorrl\enums\AssetType;
	use anorrl\enums\ANORRLBadge;
	use anorrl\utilities\AssetUtils;

	class Place extends Asset {
		/** is the same as Asset::public */
		public bool $friends_only;
		public bool $copylocked;
		public int  $server_size;
		public int  $visit_count;
		public int  $current_playing_count;
		public bool $is_original;
		public bool $gears_enabled;
		public bool $teamcreate_enabled;

		public static function UpdatePlaceStats(int $placeID) {
			$place = Place::FromID($placeID);

			if($place != null) {
				$fetch_servers = Database::singleton()->run(
					"SELECT * FROM `active_servers` WHERE `placeid` = :placeid AND `teamcreate` = 0;",
					[ ":placeid" => $place->id ]
				)->fetchAll(\PDO::FETCH_OBJ);

				$concurrentplayers = 0;

				foreach($fetch_servers as $server_row) {
					$fetch_players = Database::singleton()->run(
						"SELECT COUNT(`id`) FROM `active_players` WHERE `serverid` = :serverid AND `status` = 1;",
						[ ":serverid" => $server_row->server_id ]
					)->fetch(\PDO::FETCH_ASSOC);

					$concurrentplayers += $fetch_players['COUNT(`id`)'];
				}

				$fetch_servers = Database::singleton()->run(
					"UPDATE `places` SET `currently_playing_count` = :playerscount WHERE `id` = :placeid",
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
					
					if($visits > 100 && !$place->creator->hasProfileBadgeOf(ANORRLBadge::HOMESTEAD)) {
						$place->creator->giveProfileBadge(ANORRLBadge::HOMESTEAD);
					}

					if($visits > 1000 && !$place->creator->hasProfileBadgeOf(ANORRLBadge::BRICKSMITH)) {
						$place->creator->giveProfileBadge(ANORRLBadge::BRICKSMITH);
					}

					self::UpdatePlaceStats($place->id);
				}
				
			}
		}

		public static function FromID(int $id): Place|null {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt_getuser = $con->prepare("SELECT * FROM `places` WHERE `id` = ?");
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
			parent::__construct(intval($rowdata['id']));

			$this->friends_only = $this->public;
			$this->copylocked = boolval($rowdata['copylocked']);
			$this->server_size = intval($rowdata['serversize']);
			$this->visit_count = intval($rowdata['visit_count']);
			$this->current_playing_count = intval($rowdata['currently_playing_count']);
			$this->teamcreate_enabled = boolval($rowdata['teamcreate_enabled']);

			$this->is_original = boolval($rowdata['original']);
			$this->gears_enabled = boolval($rowdata['gears_enabled']);
		}

		function enableTeamCreate() {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt_enableteamcreate = $con->prepare('UPDATE `places` SET `teamcreate_enabled` = 1 WHERE `id` = ?');
			$stmt_enableteamcreate->bind_param('i', $this->id);
			$stmt_enableteamcreate->execute();

			if(!$this->isCloudEditor($this->creator)) {
				$this->addCloudEditor($this->creator);
			}
		}

		function disableTeamCreate() {

			$db = Database::singleton();

			$db->run("UPDATE `places` SET `teamcreate_enabled` = 0 WHERE `id` = :placeid", [":placeid" => $this->id]);

			if($this->teamcreate_enabled) {
				$db->run(
					"DELETE FROM `cloudeditors` WHERE `userid` != :creator AND `placeid` = :placeid;",
					[
						":creator" => $this->creator->id,
						":placeid" => $this->id
					]
				);

				// rewrite to pdo later
				include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
				$stmt_getactiveservers = $con->prepare("SELECT * FROM `active_servers` WHERE `placeid` = ? AND `teamcreate` = 1");
				$stmt_getactiveservers->bind_param("i", $this->id);
				$stmt_getactiveservers->execute();

				$result_getactiveservers = $stmt_getactiveservers->get_result();

				if($result_getactiveservers->num_rows != 0) {
					$row = $result_getactiveservers->fetch_assoc();

					$jobID = $row['jobid'];

					$data = json_encode([
						"pid" => $row['pid']
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

					$db->run("DELETE FROM `active_servers` WHERE `jobid` = :jobid;", [ ":jobid" => $jobID ]);
				}
			}
		}

		function isCloudEditor(User $user) {
			if($this->teamcreate_enabled) {
				include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
				$stmt_checkiseditor = $con->prepare('SELECT * FROM `cloudeditors` WHERE `userid` = ? AND `placeid` = ?;');
				$stmt_checkiseditor->bind_param('ii', $user->id, $this->id);
				$stmt_checkiseditor->execute();

				return $stmt_checkiseditor->get_result()->num_rows != 0;
			}
			return false;
		}

		function addCloudEditor(User $user) {
			if(!$this->isCloudEditor($user)) {
				include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
				$stmt_addeditor = $con->prepare('INSERT INTO `cloudeditors`(`userid`, `placeid`) VALUES (?, ?)');
				$stmt_addeditor->bind_param('ii', $user->id, $this->id);
				$stmt_addeditor->execute();
			}	
		}

		function removeCloudEditor(User $user) {
			if($this->isCloudEditor($user) && $user->id != $this->creator->id) {
				include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
				$stmt_addeditor = $con->prepare('DELETE FROM `cloudeditors` WHERE `userid` = ? AND `placeid` = ?;');
				$stmt_addeditor->bind_param('ii', $user->id, $this->id);
				$stmt_addeditor->execute();
			}	
		}

		function getCloudEditors() {
			if($this->teamcreate_enabled) {
				include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
				$stmt_geteditors = $con->prepare('SELECT `userid` FROM `cloudeditors` WHERE `placeid` = ?;');
				$stmt_geteditors->bind_param('i', $this->id);
				$stmt_geteditors->execute();

				$result_geteditors = $stmt_geteditors->get_result();

				$result = [];

				while($row = $result_geteditors->fetch_assoc()) {
					$user = User::FromID(intval($row['userid']));

					if($user && !$user->isBanned()) {
						$result[] = $user;
					}
				}

				return $result;
			}
			return [];
		}

		function updateVisitCount() {
			$db = Database::singleton();

			$visits = $db->run(
				'SELECT * FROM `visits` WHERE `place` = :id',
				[":id" => $this->id]
			)->rowCount();

			$db->run(
				'UPDATE `places` SET `visit_count` = :visits WHERE `id` = :id',
				[
					":visits" => $visits,
					":id" => $this->id
				]
			);

			$this->visit_count = $visits;
		}

		function visit(User|int $user) {
			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}

			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";

			$placeid = $this->id;

			$stmt_checkvisit = $con->prepare('SELECT * FROM `visits` WHERE `place` = ? AND `player` = ? AND `time` >= CURDATE() - INTERVAL 1 HOUR;');
			$stmt_checkvisit->bind_param('ii', $placeid, $userid);
			$stmt_checkvisit->execute();

			if($stmt_checkvisit->get_result()->num_rows == 0) {
				$stmt_addvisit = $con->prepare('INSERT INTO `visits`(`place`, `player`) VALUES (?, ?)');
				$stmt_addvisit->bind_param('ii', $placeid, $userid);
				$stmt_addvisit->execute();

				// Update

				$this->updateVisitCount();

				if($this->visit_count > 100) {
					$this->creator->giveProfileBadge(ANORRLBadge::HOMESTEAD);
				}

				if($this->visit_count > 1000) {
					$this->creator->giveProfileBadge(ANORRLBadge::BRICKSMITH);
				}
			}
		}

		function getBadges(): array {
			return [];
		}
	}

?>