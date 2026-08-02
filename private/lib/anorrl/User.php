<?php

	namespace anorrl;
	
	use anorrl\Asset;
	use anorrl\Database;
	use anorrl\Place;
	use anorrl\Universe;
	use anorrl\enums\AssetType;
	use anorrl\utilities\AssetTypeUtils;
	use anorrl\utilities\UtilUtils;
	use anorrl\utilities\ImageUtils;
	use anorrl\utilities\Renderer;
	use anorrl\enums\ANORRLBadge;

	/**
	 * Data of the user.
	 */
	class User {
		public int $id;
		public string $name;
		public string $blurb;
		public string $password;
		public string $security_key;
		public bool $has_pfp_set;
		public bool $has_banner_set;
		public string $currentoutfitmd5;
		public \DateTime $join_date;
		public \DateTime $last_username_change_date;
		public \DateTime $last_pfp_change_date;
		public \DateTime $last_banner_change_date;
		
		/**
		 * Attempts to grab userdata from given id.<br>
		 * Returns null if user of id was not found.
		 * @param int $id
		 * @return User|null
		 */
		public static function FromID(int $id): self|null {
			$row = Database::singleton()->run(
				"SELECT * FROM `users` WHERE `id` = :id",
				[ ":id" => $id]
			)->fetchObject();

			return $row ? new self($row) : null;
		}

		/**
		 * Attempts to grab userdata from given id.<br>
		 * Returns null if user of id was not found.
		 * @param string $name
		 * @return User|null
		 */
		public static function FromName(string $name) {
			$row = Database::singleton()->run(
				"SELECT `id` FROM `users` WHERE `name` LIKE :name",
				[":name" => $name]
			)->fetch(\PDO::FETCH_OBJ);

			return $row ? self::FromID($row->id) : null;
		}

		/**
		 * Attempts to grab userdata from given id.<br>
		 * Returns null if user of id was not found.
		 * @param string $name
		 * @return User|null
		 */
		public static function FromNamePercise(string $name) {
			$row = Database::singleton()->run(
				"SELECT `id` FROM `users` WHERE `name` = :name",
				[":name" => $name]
			)->fetch(\PDO::FETCH_OBJ);

			return $row ? self::FromID($row->id) : null;
		}

		/**
		 * Attempts to grab userdata from given security key.<br>
		 * Returns null if user of security key was not found.
		 * @param int $security
		 * @return User|null
		 */
		public static function FromSecurityKey(string $security) {
			$row = Database::singleton()->run(
				"SELECT `id` FROM `users` WHERE `security` = :security",
				[":security" => $security]
			)->fetch(\PDO::FETCH_OBJ);

			return $row ? self::FromID($row->id) : null;
		}

		/**
		 * Check if that user id even exists (For presence checking)
		 * @param int $id
		 * @return bool
	 	 */
		public static function Exists(int $id) {
			return self::FromID($id) != null;
		}

		private function __construct(Object $rowdata) {
			$this->id = $rowdata->id;
			$this->name = $rowdata->name;
			$this->blurb = str_replace("<", "&lt;", str_replace(">", "&gt;", $rowdata->blurb));
			$this->has_pfp_set = boolval($rowdata->has_pfp_set);
			$this->has_banner_set = boolval($rowdata->has_banner_set);
			$this->currentoutfitmd5 = $rowdata->currentappearancemd5;
			$this->join_date = \DateTime::createFromFormat("Y-m-d H:i:s", $rowdata->joindate);
			$this->last_banner_change_date = \DateTime::createFromFormat("Y-m-d H:i:s", $rowdata->last_banner_time);
			$this->password = $rowdata->password;
			$this->security_key = $rowdata->security;
		}

		function getFriends(): array {
			$fetch = Database::singleton()->run(
				"SELECT `sender`, `reciever` FROM `friends` WHERE (`sender` LIKE :id OR `reciever` LIKE :id) AND `status` = 1;",
				[ ":id" => $this->id ]
			)->fetchAll(\PDO::FETCH_OBJ);

			$friends = [];

			foreach($fetch as $row) {
				$friends[] = User::FromID($row->sender == $this->id ? $row->reciever : $row->sender);
			}

			return $friends;
		}
		
		function getFollowers(): array {
			$fetch = Database::singleton()->run(
				"SELECT `follower` FROM `follows` WHERE `followed` = :id",
				[ ":id" => $this->id ]
			)->fetchAll(\PDO::FETCH_OBJ);

			$followers = [];

			foreach($fetch as $row) {
				$followers[] = User::FromID($row->follower);
			}

			return $followers;
		}
		
		function getFollowing(): array {
			$fetch = Database::singleton()->run(
				"SELECT `followed` FROM `follows` WHERE `follower` = :id",
				[ ":id" => $this->id ]
			)->fetchAll(\PDO::FETCH_OBJ);

			$following = [];

			foreach($fetch as $row) {
				$following[] = User::FromID($row->followed);
			}

			return $following;
		}

		function getPendingFriendRequests(): array {
			$db = Database::singleton();

			$get_friend_reqs = $db->run(
				"SELECT `sender` FROM `friends` WHERE `reciever` = :id AND `status` = 0;",
				[":id" => $this->id]
			)->fetchAll(\PDO::FETCH_OBJ);

			
			$result = [];

			foreach($get_friend_reqs as $row) {
				$user = User::FromID($row->sender);

				if($user) {
					$result[] = $user;
				} else {
					$db->run(
						"DELETE FROM `friends` WHERE `sender` = :sender AND `reciever` = :id AND `status` = 0;",
						[
							":sender" => $row['sender'], 
							":id" => $this->id
						]
					);
				}
			}

			return $result;
		}

		function getPendingFriendRequestsCount() {
			return count($this->getPendingFriendRequests());
		}

		function getFriendsCount(): int {
			return count($this->getFriends());
		}
		
		function getFollowersCount(): int {
			return count($this->getFollowers());
		}

		function getFollowingCount(): int {
			return count($this->getFollowing());
		}

		/**
		 * Returns paged list of the user's created games
		 * @return void
		 */
		function getPlaces(bool $teamcreate = false): array {
			$grabbedplaces = [];
			$teamcreatedplaces = [];

			if($teamcreate) {
				$rows = Database::singleton()->run(
					"SELECT `universe` FROM `cloudeditors` WHERE `userid` = :user;",
					[ ":user" => $this->id ]
				)->fetchAll(\PDO::FETCH_OBJ);

				foreach($rows as $row) {
					$universe = Universe::FromID($row->universe);
					if(!$universe)
						continue;

					if(!$universe->starting_place->isOwner($this, true)) {
						$teamcreatedplaces[] = $universe->starting_place;
					}
				}
			}

			foreach($this->getOwnedAssets(AssetType::PLACE, '', true) as $place) {
				$universe = Universe::FromID($place->universe);

				if(!$universe)
					continue;

				if($universe->starting_place->id == $place->id)
					$grabbedplaces[] = $place;
			}
			
			return $teamcreate ? $teamcreatedplaces : $grabbedplaces;
		}

		function giveProfileBadge(ANORRLBadge $badge): void {
			if(!$this->hasProfileBadgeOf($badge)) {
				Database::singleton()->run(
					"INSERT INTO `profilebadges`(`badgeid`, `userid`) VALUES (:badge, :user)",
					[
						":badge" => $badge->ordinal(),
						":user" => $this->id
					]
				);
			}
		}

		function hasProfileBadgeOf(ANORRLBadge $badge): bool {
			return Database::singleton()->run(
				"SELECT `badgeid` FROM `profilebadges` WHERE `userid` = :id AND `badgeid` = :badge",
				[ ":id" => $this->id, ":badge" => $badge->ordinal() ]
			)->rowCount() != 0;
		}

		/**
		 * Returns the system badges (Homestead and the alike)
		 * @return void
		 */
		function getProfileBadges(): array {

			$rows = Database::singleton()->run(
				"SELECT `badgeid` FROM `profilebadges` WHERE `userid` = :id ORDER BY `recieved_at` DESC, `badgeid` DESC",
				[ ":id" => $this->id ]
			)->fetchAll(\PDO::FETCH_OBJ);

			$badges = [];

			foreach($rows as $row) {
				$badges[] = ANORRLBadge::index($row->badgeid);
			}

			return $badges;
		}

		/**
		 * Returns badges created by the users (from games)
		 * @return array
		 */
		function getUserBadges(): array {
			return $this->getOwnedAssets(AssetType::BADGE);
		}

		function getLatestStatus(): Status|null {
			$row = Database::singleton()->run(
				"SELECT `id` FROM `statuses` WHERE `poster` = :uid ORDER BY `posted` DESC",
				[ ":uid" => $this->id ]
			)->fetchObject();

			return $row ? Status::FromID($row->id) : null;
		}

		/**
		 * This is a catch all function to grab the user's owned assets.
		 * 
		 * Should be easier to do shit now...
		 * 
		 * @param AssetType $type
		 * @param string $query
		 * @param bool $creator_only
		 * @param array $excludedids
		 * @param int $page
		 * @param int $count
		 * @return void
		 */
		function getOwnedAssets(AssetType $type, string $query = "", bool $creator_only = false, bool $show_all = true, array $excludedids = [], int $page = -1, int $count = -1): array {
		
			$sql_query = trim($query);
			if(strlen($sql_query) > 0) {
				$sql_query = "%$sql_query%";
			} else {
				$sql_query = "%";
			}
			
			$sql_extra = "";

			// this could DEF be done better.
			if(count($excludedids) > 0) {
				$processedids = "AND `assets`.`id` NOT IN (";
				foreach($excludedids as $id) {
					$processedids .= $id.",";
				}
				$processedids = substr($processedids, 0, strlen($processedids)-1);
				$processedids .= ")";

				$sql_extra = $processedids;
			}

			// places are not buyable and never should be!
			if($type == AssetType::PLACE) {
				$creator_only = true;
			}

			if($creator_only) {
				$sql_extra .= " AND `assets`.`creator` = :user";
			}

			if(!$show_all) {
				$sql_extra .= " AND `public` = 1";
			}

			$sql_types = "AND `assets`.`type` = :type";
			if($type == AssetType::GAME || $type == AssetType::PLACE) {
				$sql_types = "";
			}
			else if($type == AssetType::BODYPARTS) {
				$type_head = AssetType::HEAD->ordinal();
				$type_torso = AssetType::TORSO->ordinal();
				$type_leftarm = AssetType::LEFTARM->ordinal();
				$type_rightarm = AssetType::RIGHTARM->ordinal();
				$type_leftleg = AssetType::LEFTLEG->ordinal();
				$type_rightleg = AssetType::RIGHTLEG->ordinal();

				$sql_types = "(`type` = $type_head OR `type` = $type_torso OR `type` = $type_leftarm OR `type` = $type_rightarm OR `type` = $type_leftleg OR `type` = $type_rightleg)";
			}

			$sql_select = "SELECT assets.id FROM `transactions`, `assets`";
			$sql_where = "WHERE `transactions`.`asset` = `assets`.`id` AND `userid` = :user $sql_types AND `assets`.`name` LIKE :query $sql_extra ORDER BY `lastedited` DESC";

			if($type == AssetType::PLACE)
				$sql_select .= " INNER JOIN `places`    ON `assets`.`id` = `places`.`id`";
			elseif($type == AssetType::GAME)
				$sql_select .= " INNER JOIN `universes` ON `assets`.`id` = `universes`.`starting_place`";
		
			$sql = "$sql_select $sql_where";

			$db = Database::singleton();

			$params = [ ":user" => $this->id, ":query" => $sql_query ];

			if($page > 0 && $count > 0) {
				$sql = "$sql LIMIT :page, :count";
				$params[":page"] = (($page-1)*$count);
				$params[":count"] = $count;
			}

			if($type != AssetType::GAME && $type != AssetType::PLACE && $type != AssetType::BODYPARTS)
				$params[":type"] = $type->ordinal();			

			$rows = $db->run(
				$sql,
				$params
			)->fetchAll(\PDO::FETCH_OBJ);

			$result_array = [];

			foreach($rows as $row) {
				if($type != AssetType::PLACE && $type != AssetType::GAME) {
					$result_array[] = Asset::FromID($row->id);
				}
				else {
					$result_array[] = Place::FromID($row->id);
				}
			}
			return $result_array;
		}

		/**
		 * This is a catch all function to grab the user's owned assets.
		 * 
		 * Should be easier to do shit now...
		 * 
		 * @param AssetType $type
		 * @param string $query
		 * @param bool $creator_only
		 * @param array $excludedids
		 * @return void
		 */
		function getOwnedAssetsCount(AssetType $type, string $query = "", bool $creator_only = false, bool $show_all = true, array $excludedids = []): int {
			
			$sql_query = trim($query);
			if(strlen($sql_query) > 0) {
				$sql_query = "%$sql_query%";
			} else {
				$sql_query = "%";
			}

			$sql_extra = "";

			// this could DEF be done better.
			if(count($excludedids) > 0) {
				$processedids = "AND `assets`.`id` NOT IN (";
				foreach($excludedids as $id) {
					$processedids .= $id.",";
				}
				$processedids = substr($processedids, 0, strlen($processedids)-1);
				$processedids .= ")";

				$sql_extra = $processedids;
			}

			if($creator_only) {
				$sql_extra .= " AND `assets`.`creator` = `userid`";
			}

			if(!$show_all) {
				$sql_extra .= " AND `public` = 1";
			}

			$sql_types = "AND `assets`.`type` = :type";
			if($type == AssetType::GAME || $type == AssetType::PLACE) {
				$sql_types = "";
			}
			else if($type == AssetType::BODYPARTS) {
				$type_head = AssetType::HEAD->ordinal();
				$type_torso = AssetType::TORSO->ordinal();
				$type_leftarm = AssetType::LEFTARM->ordinal();
				$type_rightarm = AssetType::RIGHTARM->ordinal();
				$type_leftleg = AssetType::LEFTLEG->ordinal();
				$type_rightleg = AssetType::RIGHTLEG->ordinal();

				$sql_types = "(`type` = $type_head OR `type` = $type_torso OR `type` = $type_leftarm OR `type` = $type_rightarm OR `type` = $type_leftleg OR `type` = $type_rightleg)";
			}

			$sql_select = "SELECT COUNT(`transactions`.`id`) FROM `transactions`, `assets`";
			$sql_where = "WHERE `transactions`.`asset` = `assets`.`id` AND `userid` = :user $sql_types AND `assets`.`name` LIKE :query $sql_extra ORDER BY `lastedited` DESC";

			if($type == AssetType::PLACE)
				$sql_select .= " INNER JOIN `places`    ON `assets`.`id` = `places`.`id`";
			elseif($type == AssetType::GAME)
				$sql_select .= " INNER JOIN `universes` ON `assets`.`id` = `universes`.`starting_place`";
		
			$sql = "$sql_select $sql_where";
			$params = [ ":user" => $this->id, ":query" => $sql_query ];

			if($type != AssetType::GAME && $type != AssetType::PLACE && $type != AssetType::BODYPARTS)
				$params[":type"] = $type->ordinal();			

			$row = Database::singleton()->run($sql, $params)->fetch(\PDO::FETCH_ASSOC);

			return $row ? $row['COUNT(`transactions`.`id`)'] : -1;
		}

		function getAllOwnedAssets(): array {
			$rows = Database::singleton()->run(
				"SELECT `asset` FROM `transactions` WHERE `userid` = :user ORDER BY `date` DESC",
				[
					":user" => $this->id
				]
			)->fetchAll(\PDO::FETCH_OBJ);

			$result_array = [];

			foreach($rows as $row) {
				$result_array[] = Asset::FromID($row->asset);
			}
			return $result_array;
		}

		function getLatestAssetUploaded(): Asset|null {
			$row = Database::singleton()->run(
				"SELECT `id` FROM `assets` WHERE `creator` = :id ORDER BY `id` DESC",
				[ ":id" => $this->id ]
			)->fetch(\PDO::FETCH_OBJ);

			return $row ? Asset::FromID($row->id) : null;
		}

		function isWearing(Asset|int $asset): bool {
			$assetid = $asset;
			if($asset instanceof Asset) {
				$assetid = $asset->id;
			}
			
			if(!$this->owns($asset) || Asset::FromID($assetid) == null) {
				$this->forceTakeOff($assetid, false);
				return false;
			}

			$db = Database::singleton();

			$rows = $db->run(
				"SELECT `assetid` FROM `inventory` WHERE `userid` = :user AND `assetid` = :asset",
				[
					":user" => $this->id,
					":asset" => $assetid
				]
			)->rowCount();

			if($rows > 1) {
				$this->forceTakeOff($assetid, false);
				$this->wear($assetid);
			}

			return $rows != 0;
		}

		function wear(Asset|int $asset): array {

			$assetid = $asset;
			if($asset instanceof Asset) {
				$assetid = $asset->id;
			}
			
			if(!$this->owns($asset) || Asset::FromID($assetid) == null) {
				return ["error"=>true, "reason"=>"Invalid item"];
			}

			$db = Database::singleton();

			if($this->isWearing($asset)) {
				return ["error" => false];
			} else {
				$item = Asset::FromID($assetid);

				if($item->type->wearable()) {
					if($item->type->wearone()) {
						$is_wearing_type = $db->run(
							"SELECT `assetid` FROM `inventory` WHERE `userid` = :userid AND `assettype` = :assettype",
							[
								":userid" => $this->id,
								":assettype" => $item->type->ordinal()
							]
						)->rowCount() != 0;

						if(!$is_wearing_type) {
							$db->run(
								"INSERT INTO `inventory`(`userid`, `assetid`, `assettype`) VALUES (:userid, :assetid, :assettype)",
								[
									":userid" => $this->id,
									":assetid" => $item->id,
									":assettype" => $item->type->ordinal()
								]
							);

						} else {
							$db->run(
								"UPDATE `inventory` SET `assetid` = :assetid WHERE `userid` = :userid AND `assettype` = :assettype",
								[
									":userid" => $this->id,
									":assetid" => $item->id,
									":assettype" => $item->type->ordinal()
								]
							);
						}
					} else {
						$limit = AssetTypeUtils::WearableLimit($item->type);

						$limitless = $limit == -1;
						$wearable = $limitless;

						if(!$limitless) {
							$item_count = $db->run(
								"SELECT `assetid` FROM `inventory` WHERE `userid` = :userid AND `assettype` = :assettype",
								[
									":userid" => $this->id,
									":assettype" => $item->type->ordinal()
								]
							)->rowCount();

							$wearable = $item_count < $limit;
						}

						if($wearable) {
							$db->run(
								"INSERT INTO `inventory`(`userid`, `assetid`, `assettype`) VALUES (:userid, :assetid, :assettype)",
								[
									":userid" => $this->id,
									":assetid" => $item->id,
									":assettype" => $item->type->ordinal()
								]
							);
						} else {
							return ["error" => true, "reason" => "Too many fucking ".strtolower($item->type->label())."s on"];
						}
					}
				} else {
					return ["error" => true, "reason" => "Invalid item"];
				}

			}

			return ["error" => false];
		}

		private function forceTakeOff(int $id, bool $wearone = true) {
			$typacolumn = $wearone ? "type" : "id";

			Database::singleton()->run(
				"DELETE FROM `inventory` WHERE `userid` = :userid AND `asset$typacolumn` = :asset$typacolumn",
				[
					":userid" => $this->id,
					":asset$typacolumn" => $id
				]
			);
		}

		function takeOff(Asset|int $asset): array {
			$assetid = $asset;
			if($asset instanceof Asset) {
				$assetid = $asset->id;
			}
			
			if(!$this->owns($asset) || Asset::FromID($assetid) == null)
				return ["error"=>true, "reason"=>"Invalid item"];

			if(!$this->isWearing($asset))
				return ["error" => false];
			
			$item = Asset::FromID($assetid);

			if(!$item->type->wearable())
				return ["error" => true, "reason" => "Invalid item"];
			
			$this->forceTakeOff($item->type->wearone() ? $item->type->ordinal() : $item->id, $item->type->wearone());

			return ["error" => false];
		}

		function getBodyColoursXML() {
			$colours = $this->getBodyColours();
			$headcolour = $colours['head'];
			$rightarmcolour = $colours['rightarm'];
			$leftlegcolour = $colours['leftleg'];
			$leftarmcolour = $colours['leftarm'];
			$rightlegcolour = $colours['rightleg'];
			$torsocolour = $colours['torso'];
			$domain = \CONFIG->domain;

			return <<<EOT
			<anorrl xmlns:xmime="http://www.w3.org/2005/05/xmlmime" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://$domain/anorrl.xsd" version="4">
				<External>null</External>
				<External>nil</External>
				<Item class="BodyColors" referent="RBX0">
					<Properties>
						<int name="HeadColor">$headcolour</int>
						<int name="LeftArmColor">$rightarmcolour</int>
						<int name="LeftLegColor">$leftlegcolour</int>
						<string name="Name">Body Colors</string>
						<int name="RightArmColor">$leftarmcolour</int>
						<int name="RightLegColor">$rightlegcolour</int>
						<int name="TorsoColor">$torsocolour</int>
					</Properties>
				</Item>
			</anorrl>
			EOT;
		}

		function getCharacterAppearance(): string {
			$domain = \CONFIG->domain;
			$getwearing = $this->getWearing();

			$userId = $this->id;
			$parsed = "";

			foreach($getwearing as $asset) {
				if($asset->type != AssetType::EMOTE)
					$parsed .= ";http://$domain/asset/?id={$asset->id}";
			}

			if(str_ends_with($parsed, ";")) {
				$parsed = substr($parsed, 0, strlen($parsed)-1);
			}
			$time = time();
			return "http://$domain/Asset/BodyColors.ashx?userId=$userId&t=$time$parsed";
		}

		function getCharacterAppearanceVerbose(): string {
			$domain = \CONFIG->domain;
			$bodycoloursxml = $this->getBodyColoursXML();
			$getwearing = $this->getWearingArray(true);

			$parsed= "";
			
			foreach($getwearing as $id) {
				$asset = Asset::FromID($id);
				if($asset != null) {
					if($asset->type == AssetType::EMOTE)
						continue;
					
					$version = $asset->current_version;
					$parsed .= "http://$domain/asset/?id=$id&version=$version;";

					$relatedassets = $asset->getRelatedAssets();

					if(count($relatedassets) != 0) {
						foreach($relatedassets as $relatedasset) {
							$subversion = $relatedasset->current_version;
							$parsed .= "http://$domain/asset/?id=$id&version=$subversion;";
						}
					}
				} else {
					// remove from everyone... OMG WHY HAVEN'T YOU IMPLEMENTED THIS YET YOU FAT FUCK
					Database::singleton()->run(
						"DELETE FROM `inventory` WHERE `assetid` = :id",
						[":id" => $id]
					);

					// transactions MAYBE but i wont delete assets completely
				}
			}

			if(str_ends_with($parsed, ";")) {
				$parsed = substr($parsed, 0, strlen($parsed)-1);
			}

			$bodycoloursxml_encoded = base64_encode($bodycoloursxml);

			return "$bodycoloursxml_encoded;$parsed";
		}

		function getCharacterAppearanceHash() {
			return md5($this->getCharacterAppearanceVerbose());
		}

		function updateOutfitHash() {
			$md5 = $this->getCharacterAppearanceHash();

			Database::singleton()->run(
				"UPDATE `users` SET `currentappearancemd5` = :md5 WHERE `id` = :uid",
				[
					":md5" => $md5,
					":uid" => $this->id
				]
			);
		}

		function getWearingArray(bool $ordered = false) {
			$rows = Database::singleton()->run(
				"SELECT `assetid` FROM `inventory` WHERE `userid` = :id".($ordered ? " ORDER BY `assetid`" : ""),
				[ ":id" => $this->id ]
			)->fetchAll(\PDO::FETCH_OBJ);

			$ids = [];
		
			foreach($rows as $row) {
				$ids[] = $row->assetid;
			}	

			return $ids;
		}

		function getWearing(AssetType|null $type = null, bool $exclude_emotes = false): array {
			$db = Database::singleton();
			
			if($type) {
				$items = $db->run(
					"SELECT DISTINCT `assetid` FROM `inventory` WHERE `userid` = :userid AND `assettype` = :assettype",
					[
						":userid" => $this->id,
						":assettype" => $type->ordinal()
					]
				)->fetchAll(\PDO::FETCH_OBJ);
			} else {
				$items = $db->run(
					"SELECT DISTINCT `assetid` FROM `inventory` WHERE `userid` = :userid AND `assettype` != :assettype",
					[ ":userid" => $this->id, ":assettype" => $exclude_emotes ? AssetType::EMOTE->ordinal() : -1 ]
				)->fetchAll(\PDO::FETCH_OBJ);
			}
			
			$assets = [];

			foreach($items as $item) {
				$assets[] = Asset::FromID($item->assetid);
			}

			return $assets;
		}

		function getBodyColours() {
			$db = Database::singleton();

			$row = $db->run(
				"SELECT * FROM `bodycolours` WHERE `userid` = :user",
				[ ":user" => $this->id ]
			)->fetchObject();

			if(!$row) {
				$db->run(
					"INSERT INTO `bodycolours`(`userid`) VALUES (:user)",
					[ ":user" => $this->id ]
				)->fetchObject();

				return $this->getBodyColours();
			}
			$colours = $row;

			return [
				"head" => $colours->head,
				"torso" => $colours->torso,
				"leftarm" => $colours->leftarm,
				"rightarm" => $colours->rightarm,
				"leftleg" => $colours->leftleg,
				"rightleg" => $colours->rightleg,
			];
		}

		function setBodyColours(int $head, int $torso, int $leftarm, int $rightarm, int $leftleg, int $rightleg) {
			$this->getBodyColours(); // populate if doesn't exist

			Database::singleton()->run(
				"UPDATE `bodycolours` SET `head` = :head, `torso` = :torso, `leftarm` = :larm, `rightarm` = :rarm, `leftleg` = :lleg,`rightleg` = :rleg WHERE `userid` = :user",
				[
					":head" => $head,
					":torso" => $torso,
					":larm" => $leftarm,
					":rarm" => $rightarm,
					":lleg" => $leftleg,
					":rleg" => $rightleg,
					":user" => $this->id
				]
			);
		}
		
		function follow(User $user) {
			if(!$this->isFollowing($user) && !$user->isBanned()) {
				Database::singleton()->run(
					"INSERT INTO `follows`(`follower`, `followed`) VALUES (:this, :other)",
					[
						":this" => $this->id,
						":other" => $user->id
					]
				);
			}
		}

		function unfollow(User $user) {
			if($this->isFollowing($user)) {
				Database::singleton()->run(
					"DELETE FROM `follows` WHERE `follower` = :this AND `followed` = :other",
					[
						":this" => $this->id,
						":other" => $user->id
					]
				);
			}
		}

		function isFollowing(User $user): bool {
			return Database::singleton()->run(
				"SELECT * FROM `follows` WHERE `follower` = :this AND `followed` = :other",
				[
					":this" => $this->id,
					":other" => $user->id
				]
			)->rowCount() != 0;
		}
		
		function friend(User $user) {
			$db = Database::singleton();

			if($user->isBanned()) {
				$this->unfriend($user);
				return;
			}

			if(!$this->isFriendsWith($user) && !$this->isPendingFriendsReq($user) && !$this->isIncomingFriendsReq($user)) {
				$db->run(
					"INSERT INTO `friends`(`sender`, `reciever`) VALUES (:this, :other)",
					[
						":this" => $this->id,
						":other" => $user->id
					]
				);
			} else if($this->isIncomingFriendsReq($user)) {
				$db->run(
					"UPDATE `friends` SET `status`= 1 WHERE `reciever` = :this AND `sender` = :other",
					[
						":this" => $this->id,
						":other" => $user->id
					]
				);
			} else {
				$this->unfriend($user);
			}
		}

		function unfriend(User $user) {
			if($this->isPendingFriendsReq($user) || $this->isIncomingFriendsReq($user) || $this->isFriendsWith($user) || $user->isBanned()) {
				Database::singleton()->run(
					"DELETE FROM `friends` WHERE (`sender` = :this AND `reciever` = :other) OR (`reciever` = :this AND `sender` = :other)",
					[
						":this" => $this->id,
						":other" => $user->id
					]
				);
			}
		}

		function isPendingFriendsReq(User $user) {
			return Database::singleton()->run(
				"SELECT * FROM `friends` WHERE `sender` = :this AND `reciever` = :other AND `status` = 0;",
				[
					":this" => $this->id,
					":other" => $user->id
				]
			)->rowCount() != 0;
		}

		function isIncomingFriendsReq(User $user) {
			return Database::singleton()->run(
				"SELECT * FROM `friends` WHERE `reciever` = :this AND `sender` = :other AND `status` = 0;",
				[
					":this" => $this->id,
					":other" => $user->id
				]
			)->rowCount() != 0;
		}

		function isFriendsWith(User $user): bool {
			return Database::singleton()->run(
				"SELECT * FROM `friends` WHERE ((`reciever` = :this AND `sender` = :other) OR (`sender` = :this AND `reciever` = :other)) AND `status` = 1;",
				[
					":this" => $this->id,
					":other" => $user->id
				]
			)->rowCount() != 0;
		}

		function updateBio(string $bio): array {
			if(!$this->isBanned()) {
				$bio_content = UtilUtils::StripUnicode($bio);

				if(strlen($bio_content) > 1000) {
					return ["error"=> true, "reason" => "Status was too long! (1000 characters maximum)"];
				}

				Database::singleton()->run(
					"UPDATE `users` SET `blurb` = :blurb WHERE `id` = :id",
					[
						":blurb" => $bio_content,
						":id" => $this->id
					]
				);

				return ["error" => false];
			} else {
				return ["error"=> true, "reason" => "Unauthorized."];
			}
		}

		function owns(Asset|int $asset): bool {
			$assetid = $asset;
			if($asset instanceof Asset) {
				$assetid = $asset->id;
			}

			return Database::singleton()->run(
				"SELECT * FROM `transactions` WHERE `userid` = :user AND `asset` = :asset",
				[
					":user" => $this->id,
					":asset" => $assetid
				]
			)->rowCount() != 0;
		}

		function isAdmin(): bool {
			return $this->hasProfileBadgeOf(ANORRLBadge::ADMINISTRATOR);
		}

		function isBanned(): bool {
			return false;
		}

		function isOnline(): bool {
			$db = Database::singleton();

			$result = $db->run(
				"SELECT `userid` FROM `activity` WHERE `userid` = :id AND `action_time` > DATE_SUB(NOW(),INTERVAL 5 MINUTE)",
				[ ":id" => $this->id ]
			)->rowCount() != 0;
			
			if($this->isInAnyGame()) {
				$result = true;
			}

			$db->run(
				"UPDATE `users` SET `online` = :online WHERE `id` = :id",
				[ 
					":id" => $this->id,
					":online" => $result
				]
			);

			return $result;
		}

		function getOnlineActivity(): string {

			$db = Database::singleton();
			
			$server_details = $this->getAnyActiveGame();

			if($server_details != null) {
				$place = $server_details->place;

				if($place != null) {
					$place_name = $place->name;

					if($place->public) {
						if($server_details->teamcreate) {
							return <<<EOT
							[ In Team Create: <a href="{$place->getURL()}">$place_name</a> ]
							EOT;
						} else {
							return <<<EOT
							[ In Game: <a href="{$place->getURL()}">$place_name</a> ]
							EOT;
						}
					}
				}
			} else {
				$db->run(
					"DELETE FROM `active_players` WHERE `playerid` = :id AND `status` = 1;",
					[ ":id" => $this->id ]
				);
			}

			$online_activity = $db->run(
				"SELECT `action` FROM `activity` WHERE `userid` = :id AND `action_time` > DATE_SUB(NOW(),INTERVAL 5 MINUTE)",
				[ ":id" => $this->id ]
			)->fetchObject();

			if($online_activity) {
				return $online_activity->action;
			} else {
				$row = $db->run("SELECT `action`, `action_time` FROM `activity` WHERE `userid` = :id", [":id" => $this->id])->fetchObject();

				if($row) {
					return "Was last seen: {$row->action}, ".UtilUtils::getTimeAgo(\DateTime::createFromFormat("Y-m-d H:i:s", $row->action_time));
				} else {
					return "Was never online I guess :[";
				}
			}
		}

		function setProfilePicture(array $file): array {
			if($file['error'] == 0 && $file['size'] > 0 && $file['size'] <= 524288) { // 512kb cap
				$file_contents = file_get_contents($file['tmp_name']);
				$file_type = ImageUtils::checkMimeType($file_contents);
				if(str_starts_with($file_type,"image/")) {
					$pre_image = imagecreatefromstring($file_contents);
					
					if(!($pre_image instanceof \GdImage)) {
						return ["success" => false, "reason" => "That wasn't an image brochacho!"];
					}
					
					$width = imagesx($pre_image);
					$height = imagesy($pre_image);

					if($width > 16 && $height > 16) {
						$size = $width;

						if($width == $height) {
							$size = $width;
						} else if($height < $width) {
							$size = $height;
						}

						$image = imagescale(ImageUtils::cropAlign($pre_image, $size, $size), 420, 420);
						
						imagepng($image, get_user_profile_path($this->id), 9);

						if(!$this->has_pfp_set) {
							Database::singleton()->run(
								"UPDATE `users` SET `has_pfp_set` = 1 WHERE `id` = :user",
								[ ":user" => $this->id ]
							);
						}

						return ["success" => true];
					}

					return ["success" => false, "reason" => "Image was wayyy too small! (16x16 minimum)"];
				}
				return ["success" => false, "reason" => "Something went wrong when uploading! ($file_type)"];
			}
			
			if($file['size'] > 524288) {
				return ["success" => false, "reason" => "Image too large! 512kb max!"];
			} else {
				return ["success" => false, "reason" => "Something went wrong when uploading!"];
			}
			
		}

		function setBannerPicture(array $file): array {
			if($file['error'] == 0 && $file['size'] > 0 && $file['size'] <= 5242880) { // 512kb cap
				$file_contents = file_get_contents($file['tmp_name']);
				$file_type = ImageUtils::checkMimeType($file_contents);
				if(str_starts_with($file_type,"image/")) {
					$pre_image = imagecreatefromstring($file_contents);
					
					if(!($pre_image instanceof \GdImage)) {
						return ["success" => false, "reason" => "That wasn't an image brochacho!"];
					}
					
					$width = imagesx($pre_image);
					$height = imagesy($pre_image);

					if($width > 97 && $height > 22) {
						//$size = $width;

						//$image = imagescale(ImageUtils::cropAlign($pre_image, $size, $size), 420, 420);
						
						if($width < 970 && $height < 220)
							imagepng($pre_image, get_user_banner_path($this->id),9);
						else
							imagejpeg($pre_image, get_user_banner_path($this->id));

						if(!$this->has_banner_set) {
							Database::singleton()->run(
								"UPDATE `users` SET `has_banner_set` = 1, `last_banner_time` = now() WHERE `id` = :user",
								[ ":user" => $this->id ]
							);
						}
						else {
							Database::singleton()->run(
								"UPDATE `users` SET `last_banner_time` = now() WHERE `id` = :user",
								[ ":user" => $this->id ]
							);
						}

						return ["success" => true];
					}

					return ["success" => false, "reason" => "Image was wayyy too small! (97x22 minimum)"];
				}
				return ["success" => false, "reason" => "Something went wrong when uploading! ($file_type)"];
			}
			
			if($file['size'] > 5242880) {
				return ["success" => false, "reason" => "Image too large! 512kb max!"];
			} else {
				return ["success" => false, "reason" => "Something went wrong when uploading!"];
			}
			
		}

		function resetProfilePicture() {
			if($this->has_pfp_set) {
				if(file_exists(get_user_profile_path($this->id))) {
					unlink(get_user_profile_path($this->id));
				}

				Database::singleton()->run(
					"UPDATE `users` SET `has_pfp_set` = 0 WHERE `id` = :user",
					[ ":user" => $this->id ]
				);
			}
		}

		function resetBannerPicture() {
			if($this->has_banner_set) {
				if(file_exists(get_user_banner_path($this->id))) {
					unlink(get_user_banner_path($this->id));
				}

				Database::singleton()->run(
					"UPDATE `users` SET `has_banner_set` = 0 WHERE `id` = :user",
					[ ":user" => $this->id ]
				);
			}
		}

		function getThumbsUrl(int $size_x = -1, int $size_y = -1): string {
			if(\SESSION)
				$settings = \SESSION->settings;
			else
				$settings = UserSettings::Get();

			$type = ($this->has_pfp_set ? 
					($settings->headshots ? "headshot" : "profile")
					: "headshot");
			$path = "/profiles/{$this->id}.png";
			if($type == "headshot")
				$path = "/renders/headshots/{$this->currentoutfitmd5}";
			
			if(!file_exists(get_path_file("users{$path}")))
				return "/public/images/thumbnails/unavailable.jpg";

			return (\CONFIG->prefer_https ? "https":"http")."://cdn.".\CONFIG->baseurl.$path;
		}

		function getThumbsUrlProfile(int $size_x = -1, int $size_y = -1): string {

			if(!file_exists(get_user_profile_path($this->id))) {
				$pictures = UtilUtils::GetFilesArray("/public/images/profile_pictures/");
					
				$rand_pic = 1+rand(0, count($pictures) - 1);
				
				"/public/images/profile_pictures/pfp_$rand_pic.png";
			}
			
			return (\CONFIG->prefer_https ? "https":"http")."://cdn.".\CONFIG->baseurl."/profiles/{$this->id}.png";
		}

		function getThumbsUrlBanner(): string {

			if(!file_exists(get_user_banner_path($this->id)))
				return "/api/background?t=".md5($this->name);
			
			return (\CONFIG->prefer_https ? "https":"http")."://cdn.".\CONFIG->baseurl."/banners/{$this->id}?t=".$this->last_banner_change_date->getTimestamp();
		}

		function getThumbsUrlAvatar(int $size_x = -1, int $size_y = -1): string {

			/*$size_params = "";
			if($size_x > 0 && $size_y <= 0)
				$size_params = "&sxy=$size_x";
		 	
			else if($size_x > 0 && $size_y > 0)
				$size_params = "&sx=$size_x&sy=$size_y";

			return "/thumbs/$service?id={$this->id}{$size_params}";*/

			if(!file_exists(get_user_render_path($this->currentoutfitmd5, ARLRENDER)))
				return "/public/images/thumbnails/unavailable.jpg";

			return (\CONFIG->prefer_https ? "https":"http")."://cdn.".\CONFIG->baseurl."/renders/{$this->currentoutfitmd5}/image.png";
		}

		function getAccountAge(): int {
			return UtilUtils::GetTimeDifference($this->join_date);
		}

		/**
		 * Track user activity (aka set current time when they entered new page)
		 * @param mixed $action What action took place?
		 * @return void
		 */
		function registerAction(string $action = "Website"): void {
			$db = Database::singleton();
			// Check if row exists
			
			$num_rows = $db->run(
				"SELECT `userid` FROM `activity` WHERE `userid` = :id LIMIT 1",
				[":id" => $this->id]
			)->rowCount();
			

			// If it doesn't then create one
			if($num_rows == 0) {
				$db->run(
					"INSERT INTO `activity`(`userid`, `action`, `action_time`) VALUES (:id, :action, now())",
					[
						":id" => $this->id,
						":action" => $action,
					]
				);
			} else {
				// Else, Update row
				$db->run(
					"UPDATE `activity` SET `action` = :action,`action_time` = now() WHERE `userid` = :id",
					[
						":id" => $this->id,
						":action" => $action,
					]
				);
			}
		}

		function getActiveGame(bool $teamcreate = false) {
			if(!$this->isInAGame())
				return null;

			$rows = Database::singleton()->run(
				"SELECT `id`,`serverid` FROM `active_players` WHERE `playerid` = :playerid AND `teamcreate` = :teamcreate", 
				[
					":playerid" => $this->id,
					":teamcreate" => $teamcreate
				]
			)->fetchAll(\PDO::FETCH_OBJ);

			$server = null;

			foreach($rows as $row) {
				if(!$row)
					continue;

				$grab_server = GameServer::Get($row->serverid);

				if(!$grab_server) {
					$session = GameSession::Get($row->id);

					if($session)
						$session->kick("");

					continue;
				}

				if($grab_server->active()) {
					if(!$server) {
						if($row->status == 1)
							$server = $grab_server;
						else
							$grab_server->removePlayer($this);
					} else {
						$server->removePlayer($this);
						$server = null;
						$grab_server->removePlayer($this);
					}
				}
				else {
					$grab_server->destroy();
				}
			}

			return $server->active() ? $server : null;
		}

		function getAnyActiveGame() {
			if(!$this->isInAnyGame())
				return null;

			$rows = Database::singleton()->run(
				"SELECT `id`,`serverid` FROM `active_players` WHERE `playerid` = :playerid", 
				[ ":playerid" => $this->id ]
			)->fetchAll(\PDO::FETCH_OBJ);

			$server = null;

			foreach($rows as $row) {
				if(!$row)
					continue;
				
				$grab_server = GameServer::Get($row->serverid);

				if(!$grab_server) {
					$session = GameSession::Get($row->id);

					if($session)
						$session->kick("");

					continue;
				}

				if($grab_server->active()) {
					if(!$server) {
						if($row->status == 1)
							$server = $grab_server;
						else
							$grab_server->removePlayer($this);
					} else {
						$server->removePlayer($this);
						$server = null;
						$grab_server->removePlayer($this);
					}
				}
				else {
					$grab_server->destroy();
				}
			}

			return $server ? ($server->active() ? $server : null) : null;
		}

		function isInAGame(bool $teamcreate = false) {
			return
				Database::singleton()->run(
					"SELECT `id` FROM `active_players` WHERE `playerid` = :playerid AND `status` = 1 AND `teamcreate` = :teamcreate", 
					[
						":playerid" => $this->id,
						":teamcreate" => $teamcreate
					]
				)->rowCount() != 0;
		}

		function isInAnyGame() {
			return 
				Database::singleton()->run(
					"SELECT `id` FROM `active_players` WHERE `playerid` = :playerid AND `status` = 1", 
					[ ":playerid" => $this->id ]
				)->rowCount() != 0;
		}

		function getSettings(): UserSettings {
			return UserSettings::Get($this);
		}

		function getRecentlyPlayedGames(int $limit = 2): array {
			$rows = Database::singleton()->run(
				"SELECT DISTINCT `place` FROM `visits` WHERE `player` = :id ORDER BY `time` DESC LIMIT :limit", 
				[
					":id" => $this->id,
					":limit" => $limit
				]
			)->fetchAll(\PDO::FETCH_OBJ);
			
			$places = [];

			foreach($rows as $row) {
				$places[] = Place::FromID($row->place);
			}

			return $places;
		}

		function render(bool $headshot = false, bool $is3D = false) {
			if($headshot && $is3D) {
				return;
			}

			$type = null;
			
			if($is3D) {
				$type = ARLRENDER3D;
			} else if($headshot) {
				$type = ARLRENDERHEADSHOT;
			} else {
				$type = ARLRENDER;
			}

			$path = get_user_render_path($this->currentoutfitmd5, $type);

			$render = Renderer::RenderUser($this->id, $headshot, $is3D);
			if($render != null) {
				
				if(!$is3D) {
					$data = base64_decode($render);
					$render_image = imagecreatefromstring($data);
					imagesavealpha($render_image, true);
					imagepng($render_image, $path);
				} else {
					$data = trim($render);
					$data = str_replace("\"x\":+", "\"x\":-", $data);
					$data = str_replace("\"y\":+", "\"y\":-", $data);
					$data = str_replace("\"z\":+", "\"z\":-", $data);

					if(!str_ends_with($data, "}")) {
						while(!str_ends_with($data, "}")) {
							$data = substr($data, 0, strlen($data)-1);
						}
					}

					file_put_contents($path, $data);
				}

				$this->updateOutfitHash();
			}
		}

		function has3DRender(): bool {
			return file_exists($this->getJsonRenderPath());
		}

		private function getJsonRenderPath(): string {
			return get_user_render_path($this->currentoutfitmd5, ARLRENDER3D);
		}

		/**
		 * ... In the past hour...
		 * @param Place $place
		 * @param int $hours Default 1 hour
		 * @return bool
		 */
		function hasVisited(Place $place, int $hours = 1) {
			return Database::singleton()->run(
				"SELECT `place` FROM `visits` WHERE `place` = :place AND `player` = :player AND `time` >= CURDATE() - INTERVAL :hours HOUR;",
				[
					":place" => $place->id,
					":player" => $this->id,
					":hours" => $hours
				]
			)->rowCount() != 0;
		}

		function visit(Place $place) {
			if($place->isOwner($this, true)) {
				if(rand(0, 5) > 3) { // why not
					return;
				}
			}

			if(!$this->hasVisited($place)) {
				Database::singleton()->run(
					"INSERT INTO `visits`(`place`, `player`) VALUES (:place, :player)",
					[ ":place" => $place->id, ":player" => $this->id ]
				);

				$place->updateVisitCount();
			}
		}

		function updateUsername(string $new_name) {
			$processed_new_name = trim($new_name);

			if(strcmp($processed_new_name, $this->name) == 0) {
				return ["error" => false];
			}

			if(!Session::isUsernameValid($processed_new_name))
				return ["error" => true, "reason" => "Username must be a-z A-Z 0-9 and 3-20 characters only!"];

			if(!Session::isUsernameAvailable($processed_new_name))
				return ["error" => true, "reason" => "Username has already been taken!"];

			$difference = UtilUtils::GetSecondsElapsedFrom(UserSettings::Get($this)->last_username_change);

			$minute = 60;
			$hour = $minute * 60;

			$day = $hour * 24;

			$calculated_time = $day - $difference; 
			$label = "seconds";

			if($calculated_time > $hour) {
				$calculated_time /= 60;
				$calculated_time /= 60;
				$label = "hours";
			} else {
				if($calculated_time < $hour) {
					$calculated_time /= 60;
					$label = "minutes";
				}
			}

			$calculated_time = round($calculated_time);

			if($difference < $day) {
				return ["error"=> true, "reason" => "You need to wait $calculated_time $label before updating again."];
			}

			Database::singleton()->run(
				"UPDATE `users_settings` SET `last_username_change` = now() WHERE `userid` = :id",
				[ ":id" => $this->id ]
			);

			Database::singleton()->run(
				"UPDATE `users` SET `name`= :name WHERE `id` = :id",
				[
					":name" => $processed_new_name,
					":id" => $this->id
				]
			);

			return ["error" => false];

		}
	}
?>
