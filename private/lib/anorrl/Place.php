<?php

	namespace anorrl;

	use anorrl\Asset;
	use anorrl\Database;
	use anorrl\enums\AssetType;
	use anorrl\enums\ANORRLBadge;
	use anorrl\enums\ChatOption;
	use anorrl\enums\Genre;
	use anorrl\enums\GearType;
	use anorrl\utilities\AssetUtils;
	use anorrl\GameServer;
	use anorrl\Universe;

	class Place extends Asset {
		public int  $server_size;
		public int  $visit_count;
		public int  $current_playing_count;
		public bool $copylocked;
		public bool $gears_enabled;
		public array|null|GearType $gear_types;
		public Genre $genre;
		public ChatOption $chat_option;

		public static function UpdatePlaceStats(int $placeID) {
			$place = Place::FromID($placeID);

			if($place != null) {
				$fetch_servers = Database::singleton()->run(
					"SELECT `id` FROM `active_servers` WHERE `placeid` = :placeid AND `teamcreate` = 0;",
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

				Database::singleton()->run(
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

		public static function FromID(int|null $id, bool $dont_create_universe = false): Place|null {
			if(!is_int($id))
				return null;

			$row = Database::singleton()->run(
				"SELECT * FROM `places` WHERE `id` = :id",
				[
					":id" => $id
				]
			)->fetch(\PDO::FETCH_OBJ);

			return $row ? new self($row, $dont_create_universe) : null;
		}

		private function __construct(object $rowdata, bool $dont_create_universe = false) {
			parent::__construct($rowdata->id);

			$this->server_size = $rowdata->serversize;
			$this->visit_count = $rowdata->visit_count;
			$this->current_playing_count = $rowdata->currently_playing_count;

			$this->copylocked = $rowdata->copylocked;
			$this->gears_enabled = $rowdata->gears_enabled;

			if($this->universe == -1 && !$dont_create_universe) {
				$universe = Universe::Create($this);
				if($universe)
					$this->universe = $universe->id;
			}

			$this->genre = Genre::index($rowdata->genre);
			$this->chat_option = ChatOption::index($rowdata->chat_option);
			$this->gear_types = !$this->gears_enabled ? null : null;
		}

		function isStartingPlace() {
			return Universe::FromID($this->universe)->starting_place->id == $this->id;
		}

		function getStuffResponse() {
			
			return [
				"id" => $this->id,
				"name" => $this->name,
				"creator" => [
					"id" => $this->creator->id,
					"name" => $this->creator->name
				],
				"thumbnail" => $this->getThumbsUrl(200, 112),
				"url" => $this->getURL(),
				"updated" => $this->last_updatetime->format("d/m/Y"),
				"slot" => Universe::IsActive($this->universe) ? "active" : "inactive",
				"visits" => $this->visit_count,
				"weekly_visits" => $this->getWeeklyVisitCount(),
				"universe" => $this->universe
			];
		}

		function getWeeklyVisitCount() {
			return Database::singleton()->run(
				'SELECT `place` FROM `visits` WHERE `place` = :id AND `time` >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY);',
				[":id" => $this->id]
			)->rowCount();
		}

		function getURL() {
			return "/games/{$this->id}/{$this->getURLTitle()}";
		}

		function updateVisitCount() {
			$db = Database::singleton();

			$visits = $db->run(
				'SELECT `place` FROM `visits` WHERE `place` = :id',
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

			if($this->visit_count > 100) {
				$this->creator->giveProfileBadge(ANORRLBadge::HOMESTEAD);
			}

			if($this->visit_count > 1000) {
				$this->creator->giveProfileBadge(ANORRLBadge::BRICKSMITH);
			}
		}

		function getServers(bool $teamcreate = false): array {
			$rows = Database::singleton()->run(
				"SELECT `id` FROM `active_servers` WHERE `placeid` = :placeid AND `teamcreate` = :teamcreate",
				[
					":placeid" => $this->id,
					":teamcreate" => $teamcreate
				]
			)->fetchAll(\PDO::FETCH_OBJ);

			$result = [];

			foreach($rows as $row) {
				$server = GameServer::Get($row->id, $teamcreate);

				if($server->active())
					$result[] = $server;
			}

			return $result;
		}

		function shutdown(string $reason = "This game has been shutdown by the creator") {
			foreach($this->getServers() as $server) {
				$server->shutdown($reason);
			}
		}

		function isEditable(User $user): bool {
			return 
				$this->isOwner($user) ||
				!$this->copylocked ||
				Universe::FromID($this->universe)->hasAccess($user);
		}

		function anyActiveServers(bool $teamcreate = false): bool {
			return Database::singleton()->run(
				"SELECT `id` FROM `active_servers` WHERE `placeid` = :placeid AND `playercount` != `maxcount` AND `teamcreate` = :teamcreate",
				[
					":placeid" => $this->id,
					":teamcreate" => $teamcreate
				]
			)->rowCount() != 0;
		}

		function getAnActiveServer(User $user, bool $teamcreate = false): GameServer|null {
			$row = Database::singleton()->run(
				"SELECT `id` FROM `active_servers` WHERE `placeid` = :placeid AND `playercount` < `maxcount` AND `teamcreate` = :teamcreate",
				[
					":placeid" => $this->id,
					":teamcreate" => $teamcreate
				]
			)->fetch(\PDO::FETCH_OBJ);

			if(!$row)
				return null;

			$gameserver = GameServer::Get($row->id, $teamcreate);

			if(!$gameserver)
				return null;

			return $gameserver->active() && !$gameserver->isPlayerInServer($user) ? $gameserver : null;
		}

		function update(bool $copylocked, int $server_size, bool $gears) {
			if($this->universe == -1) {
				return;
			}

			Database::singleton()->run(
				"UPDATE `places` SET `copylocked` = :copylocked, `serversize` = :serversize, `gears_enabled` = :gears WHERE `id` = :placeid",
				[
					":copylocked" => $copylocked,
					":serversize" => $server_size,
					":gears" => $gears,
					":placeid" => $this->id,
				]
			);
		}

		function getLastVisited(User $user) {
			$row = Database::singleton()->run(
				"SELECT `time` FROM `visits` WHERE `player` = :id AND `place` = :place", 
				[
					":id" => $user->id,
					":place" => $this->id
				]
			)->fetchObject();

			return $row ? \DateTime::createFromFormat("Y-m-d H:i:s", $row->time) : null;
		}
	}

?>
