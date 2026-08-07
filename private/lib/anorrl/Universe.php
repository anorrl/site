<?php
	namespace anorrl;

	use anorrl\Alias;
	use anorrl\Place;
	use anorrl\User;
	use anorrl\Database;
	use anorrl\enums\AssetType;

	class Universe {

		public int $id;
		public Place $starting_place;
		public User $creator;
		public bool $public;
		public bool $original;
		public bool $teamcreate;
		public bool $active;
		

		public static function Create(Place $place, bool $public = true, bool $original = true): self|null {
			if($place->universe == -1) {
				Database::singleton()->run(
					"INSERT INTO `universes`(`starting_place`, `creator`, `public`, `original`) VALUES (:placeid, :creator, :public, :original)",
					[
						":placeid" => $place->id,
						":creator" => $place->creator->id,
						":public" => $public,
						":original" => $original
					]
				);

				$id = intval(Database::singleton()->lastInsertId());
				if($id == 0)
					return null;

				$place->setUniverse($id);

				return self::FromID($id);
			}

			return null;
		}

		public static function FromID(?int $id): self|null {
			if(!is_int($id))
				return null;

			$row = Database::singleton()->run(
				"SELECT * FROM `universes` WHERE `id` = :id",
				[ ":id" => $id ]
			)->fetchObject();

			return $row ? new self($row) : null;
		}

		private function __construct($data) {
			$this->id = $data->id;
			$this->starting_place = Place::FromID($data->starting_place);
			$this->creator = User::FromID($data->creator);
			$this->public = $data->public;
			$this->original = $data->original;
			$this->teamcreate = $data->teamcreate;
			$this->active = boolval($data->active);
		}

		function getAllPlaces() {
			$rows = Database::singleton()->run(
				"SELECT `id` FROM `assets` WHERE `universe` = :id AND `type` = :type",
				[ ":id" => $this->id, ":type" => AssetType::PLACE->ordinal() ]
			)->fetchAll(\PDO::FETCH_OBJ);

			$places = [];

			foreach($rows as $row) {
				$place = Place::FromID($row->id);

				if($place)
					$places[] = $place;
			}

			return $places;
		}

		function shutdown(string $reason = "This game has been shutdown by the creator") {
			foreach($this->getAllPlaces() as $place) {
				$place->shutdown($reason);
			}
		}

		function getDeveloperProducts(AssetType|null $type = null) {
			if($type != null) {
				$rows = Database::singleton()->run(
					"SELECT `id` FROM `assets` WHERE `universe` = :id AND `type` != :placetype AND `type` = :type",
					[ ":id" => $this->id, ":placetype" => AssetType::PLACE->ordinal(), ":type" => $type->ordinal() ]
				)->fetchAll(\PDO::FETCH_OBJ);
			} else {
				$rows = Database::singleton()->run(
					"SELECT `id` FROM `assets` WHERE `universe` = :id AND `type` != :placetype",
					[ ":id" => $this->id, ":placetype" => AssetType::PLACE->ordinal() ]
				)->fetchAll(\PDO::FETCH_OBJ);
			}

			$products = [];

			foreach($rows as $row) {
				$product = Asset::FromID($row->id);

				if($product)
					$products[] = $product;
			}

			return $products;
		}

		function getBadges(int $page = -1, int $count = -1): array {
			if($page > 0 && $count > 0) {
				$rows = Database::singleton()->run(
					"SELECT `id` FROM `assets` WHERE `universe` = :id AND `type` = :badgetype LIMIT :page, :count",
					[
						":id" => $this->id,
						":badgetype" => AssetType::BADGE->ordinal(),
						":page" => (($page-1)*$count),
						":count" => $count
					]
				)->fetchAll(\PDO::FETCH_OBJ);
			} else {
				$rows = Database::singleton()->run(
					"SELECT `id` FROM `assets` WHERE `universe` = :id AND `type` = :badgetype",
					[
						":id" => $this->id,
						":badgetype" => AssetType::BADGE->ordinal()
					]
				)->fetchAll(\PDO::FETCH_OBJ);
			}

			$badges = [];

			foreach($rows as $row) {
				$badge = Badge::FromID($row->id);

				if($badge)
					$badges[] = $badge;
			}

			return $badges;
		}

		function getAliases() {
			$rows = Database::singleton()->run(
				"SELECT `id` FROM `aliases` WHERE `universe` = :id",
				[ ":id" => $this->id ]
			)->fetchAll(\PDO::FETCH_OBJ);

			$aliases = [];

			foreach($rows as $row) {
				$alias = Alias::FromID($row->id);

				if($alias)
					$aliases[] = $alias;
			}

			return $aliases;
		}

		function enableTeamCreate() {
			Database::singleton()->run("UPDATE `universes` SET `teamcreate` = 1 WHERE `id` = :id", [ ":id" => $this->id ]);

			if(!$this->isCloudEditor($this->creator)) {
				$this->addCloudEditor($this->creator);
			}
		}

		function disableTeamCreate() {

			$db = Database::singleton();

			$db->run("UPDATE `universes` SET `teamcreate` = 0 WHERE `id` = :id", [":id" => $this->id]);

			if($this->teamcreate) {
				$db->run(
					"DELETE FROM `cloudeditors` WHERE `userid` != :creator AND `universe` = :universe;",
					[
						":creator" => $this->creator->id,
						":universe" => $this->id
					]
				);

				foreach($this->getAllPlaces() as $place) {
					$teamcreate_servers = $place->getServers(true);

					foreach($teamcreate_servers as $server) {
						$server->destroy();
					}
				}
			}
		}

		function isCloudEditor(User $user) {
			if($this->teamcreate) {
				return Database::singleton()->run(
					"SELECT `id` FROM `cloudeditors` WHERE `userid` = :uid AND `universe` = :id",
					[
						":uid" => $user->id,
						":id" => $this->id
					]
				)->rowCount() != 0;
			}
			return false;
		}

		function addCloudEditor(User $user) {
			if(!$this->isCloudEditor($user) && !$user->isBanned()) {
				return Database::singleton()->run(
					"INSERT INTO `cloudeditors`(`userid`, `universe`) VALUES (:uid, :id)",
					[
						":uid" => $user->id,
						":id" => $this->id
					]
				);
			}	
		}

		function removeCloudEditor(User $user, bool $force = false) {
			if($this->isCloudEditor($user) && $user->id != $this->creator->id || $force) {
				return Database::singleton()->run(
					"DELETE FROM `cloudeditors` WHERE `userid` = :uid AND `universe` = :id",
					[
						":uid" => $user->id,
						":id" => $this->id
					]
				);
			}	
		}

		function getCloudEditors() {
			if($this->teamcreate) {
				if(!$this->isCloudEditor($this->creator)) {
					$this->addCloudEditor($this->creator);
				}

				$rows = Database::singleton()->run(
					"SELECT `userid` FROM `cloudeditors` WHERE `universe` = :id",
					[ ":id" => $this->id ]
				)->fetchAll(\PDO::FETCH_OBJ);
				
				$editors = [];
				$past_editors = [];

				foreach($rows as $row) {
					if(in_array($row->userid, $past_editors))
						continue;

					$user = User::FromID($row->userid);

					if(!$user)
						continue;

					if(!$user->isBanned())
						$editors[] = $user;
					else
						$this->removeCloudEditor($user);

					array_push($past_editors, $row->userid);
				}

				return $editors;
			}
			return [];
		}

		function isOwner(User $user) {
			return $user->id == $this->creator->id || $user->isAdmin();
		}

		function hasAccess(User|null $user = null) {
			if(!$user)
				return false;

			return $this->isOwner($user) || $this->teamcreate && $this->isCloudEditor($user);
		}

		function update(bool $public, bool $original) {
			Database::singleton()->run(
				"UPDATE `universes` SET `public` = :public, `original` = :original WHERE `id` = :universe",
				[
					":public" => $public,
					":original" => $original,
					":universe" => $this->id,
				]
			);
		}

		function setStartingPlace(Place $place): bool {
			if(count($this->getAllPlaces()) == 1 && $place->id != $this->starting_place->id)
				return false;

			if($place->universe != $this->id)
				return false;

			Database::singleton()->run(
				"UPDATE `universes` SET `starting_place` = :place WHERE `id` = :universe",
				[
					":place" => $place->id,
					":universe" => $this->id,
				]
			);

			return true;
		}
	}
?>
