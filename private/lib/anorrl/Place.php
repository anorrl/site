<?php

	namespace anorrl;

	use anorrl\Asset;
	use anorrl\Database;
	use anorrl\enums\AssetType;
	use anorrl\enums\ANORRLBadge;
	use anorrl\utilities\AssetUtils;
	use anorrl\utilities\Arbiter;
	use anorrl\GameServer;
	use anorrl\Universe;

	class Place extends Asset {
		/** is the same as Asset::public */
		// public Universe $universe;
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
						[ ":serverid" => $server_row->id ]
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
			$row = Database::singleton()->run(
				"SELECT * FROM `places` WHERE `id` = :id",
				[
					":id" => $id
				]
			)->fetch(\PDO::FETCH_OBJ);

			return $row ? new self($row) : null;
		}

		private function __construct(object $rowdata) {
			parent::__construct($rowdata->id);

			$this->friends_only = $this->public;
			$this->copylocked = $rowdata->copylocked;
			$this->server_size = $rowdata->serversize;
			$this->visit_count = $rowdata->visit_count;
			$this->current_playing_count = $rowdata->currently_playing_count;
			$this->teamcreate_enabled = $rowdata->teamcreate_enabled;

			$this->is_original = $rowdata->original;
			$this->gears_enabled = $rowdata->gears_enabled;

			/* yes high as hell code
			if(!Universe::GetFromPlace($this))
				$this->universe = Universe::Create($this);
			} else {
				$this->universe = Universe::FromID($rowdata->universe)
			}
			*/
		}

		function enableTeamCreate() {
			Database::singleton()->run("UPDATE `places` SET `teamcreate_enabled` = 1 WHERE `id` = :id", [ ":id" => $this->id ]);

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

				$teamcreate_servers = $this->getServers(true);

				foreach($teamcreate_servers as $server) {
					$server->destroy();
				}
			}
		}

		function isCloudEditor(User $user) {
			if($this->teamcreate_enabled) {
				return Database::singleton()->run(
					"SELECT `id` FROM `cloudeditors` WHERE `userid` = :uid AND `placeid` = :pid",
					[
						":uid" => $user->id,
						":pid" => $this->id
					]
				)->rowCount() != 0;
			}
			return false;
		}

		function addCloudEditor(User $user) {
			if(!$this->isCloudEditor($user) && !$user->isBanned()) {
				return Database::singleton()->run(
					"INSERT INTO `cloudeditors`(`userid`, `placeid`) VALUES (:uid, :pid)",
					[
						":uid" => $user->id,
						":pid" => $this->id
					]
				);
			}	
		}

		function removeCloudEditor(User $user) {
			if($this->isCloudEditor($user) && $user->id != $this->creator->id) {
				return Database::singleton()->run(
					"DELETE FROM `cloudeditors` WHERE `userid` = :uid AND `placeid` = :pid",
					[
						":uid" => $user->id,
						":pid" => $this->id
					]
				);
			}	
		}

		function getCloudEditors() {
			if($this->teamcreate_enabled) {
				$rows = Database::singleton()->run(
					"SELECT `userid` FROM `cloudeditors` WHERE `placeid` = :place",
					[ ":place" => $this->id ]
				)->fetchAll(\PDO::FETCH_OBJ);
				
				$editors = [];

				foreach($rows as $row) {
					$user = User::FromID($row->userid);

					if(!$user)
						continue;

					if(!$user->isBanned())
						$editors[] = $user;
					else
						$this->removeCloudEditor($user);
				}

				return $editors;
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

		function visit(User $user) {
			if(!$user->hasVisited($this)) {
				// insert visit... move to User?
				Database::singleton()->run(
					"INSERT INTO `visits`(`place`, `player`) VALUES (:place, :player)",
					[ ":place" => $this->id, ":player" => $user->id ]
				);

				$this->updateVisitCount();

				if($this->visit_count > 100) {
					$this->creator->giveProfileBadge(ANORRLBadge::HOMESTEAD);
				}

				if($this->visit_count > 1000) {
					$this->creator->giveProfileBadge(ANORRLBadge::BRICKSMITH);
				}
			}
		}

		function getServers(bool $teamcreate = false): array {
			$rows = Database::singleton()->run(
				"SELECT * FROM `active_servers` WHERE `placeid` = :placeid AND `teamcreate` = :teamcreate",
				[
					":placeid" => $this->id,
					":teamcreate" => $teamcreate
				]
			)->fetchAll(\PDO::FETCH_OBJ);

			$result = [];

			foreach($rows as $row) {
				$server = new GameServer($row);

				if($server->active())
					$result[] = $server;
			}

			return $result;
		}

		function isEditable(User $user): bool {
			return 
				$this->isOwner($user) ||
				!$this->copylocked ||
				($this->teamcreate_enabled && $this->isCloudEditor($user));
		}

		function anyActiveServers(bool $teamcreate = false): bool {
			return Database::singleton()->run(
				"SELECT * FROM `active_servers` WHERE `placeid` = :placeid AND `playercount` != `maxcount` AND `teamcreate` = :teamcreate",
				[
					":placeid" => $this->id,
					":teamcreate" => $teamcreate
				]
			)->rowCount() != 0;
		}

		function getAnActiveServer(User $user, bool $teamcreate = false): GameServer|null {
			$row = Database::singleton()->run(
				"SELECT * FROM `active_servers` WHERE `placeid` = :placeid AND `playercount` < `maxcount` AND `teamcreate` = :teamcreate",
				[
					":placeid" => $this->id,
					":teamcreate" => $teamcreate
				]
			)->fetch(\PDO::FETCH_OBJ);

			if(!$row)
				return null;

			$gameserver = new GameServer($row);

			return $gameserver->active() && !$gameserver->isPlayerInServer($user) ? $gameserver : null;
		}
		

		function getBadges(): array {
			return [];
		}

		function update(bool $copylocked, int $server_size, bool $original, bool $gears) {
			Database::singleton()->run(
				"UPDATE `places` SET `copylocked` = :copylocked, `serversize` = :serversize, `original` = :original, `gears_enabled` = :gears WHERE `id` = :placeid",
				[
					":copylocked" => $copylocked,
					":serversize" => $server_size,
					":original" => $original,
					":gears" => $gears,
					":placeid" => $this->id
				]
			);
		}
	}

?>
