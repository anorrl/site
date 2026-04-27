<?php

	namespace anorrl;
	
	use anorrl\Asset;
	use anorrl\Database;
	use anorrl\Place;
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
		public \DateTime $last_update;
		/**
		 * How do you name this better...
		 * @var bool
		 */
		public bool $setprofilepicture;
		public string $currentoutfitmd5;
		public \DateTime $join_date;
		
		/**
		 * Attempts to grab userdata from given id.<br>
		 * Returns null if user of id was not found.
		 * @param int $id
		 * @return User|null
		 */
		public static function FromID(int $id) {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt_getuser = $con->prepare("SELECT * FROM `users` WHERE `id` = ?");
			$stmt_getuser->bind_param('i', $id);
			$stmt_getuser->execute();
			$result = $stmt_getuser->get_result();

			if($result->num_rows == 1) {
				return new self($result->fetch_assoc());
			} else {
				return null;
			}
		}

		/**
		 * Attempts to grab userdata from given id.<br>
		 * Returns null if user of id was not found.
		 * @param string $name
		 * @return User|null
		 */
		public static function FromName(string $name) {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt_getuser = $con->prepare("SELECT * FROM `users` WHERE `name` LIKE ?");
			$stmt_getuser->bind_param('s', $name);
			$stmt_getuser->execute();
			$result = $stmt_getuser->get_result();

			if($result->num_rows == 1) {
				return new self($result->fetch_assoc());
			} else {
				return null;
			}
		}

		/**
		 * Attempts to grab userdata from given security key.<br>
		 * Returns null if user of security key was not found.
		 * @param int $security
		 * @return User|null
		 */
		public static function FromSecurityKey(string $security) {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt_getuser = $con->prepare("SELECT * FROM `users` WHERE `security` = ?");
			$stmt_getuser->bind_param('s', $security);
			$stmt_getuser->execute();
			$result = $stmt_getuser->get_result();

			if($result->num_rows == 1) {
				return new self($result->fetch_assoc());
			} else {
				return null;
			}
		}

		/**
		 * Check if that user id even exists (For presence checking)
		 * @param int $id
		 * @return bool
	 	 */
		public static function Exists(int $id) {
			return self::FromID($id) != null;
		}

		function __construct($rowdata) {
			$this->id = intval($rowdata['id']);
			$this->name = strval($rowdata['name']);
			$this->blurb = str_replace("<", "&lt;", str_replace(">", "&gt;", $rowdata['blurb']));
			$this->last_update = \DateTime::createFromFormat("Y-m-d H:i:s", $rowdata['lastprofileupdate']);
			$this->setprofilepicture = boolval($rowdata['setprofilepicture']);
			$this->currentoutfitmd5 = strval($rowdata['currentappearancemd5']);
			$this->join_date = \DateTime::createFromFormat("Y-m-d H:i:s", $rowdata['joindate']);
			$this->password = strval($rowdata['password']);
			$this->security_key = strval($rowdata['security']);
		}

		function getFriends(): array {
			$fetch = Database::singleton()->run(
				"SELECT * FROM `friends` WHERE (`sender` LIKE :id OR `reciever` LIKE :id) AND `status` = 1;",
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
				"SELECT * FROM `follows` WHERE `followed` = :id",
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
				"SELECT * FROM `follows` WHERE `follower` = :id",
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
				"SELECT * FROM `friends` WHERE `reciever` = :id AND `status` = 0;",
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
			$grabbedplaces = $this->getOwnedAssets(AssetType::PLACE, "", true);
			$result = [];

			$teamcreatedplaces = [];
			
			if($teamcreate) {
				include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
				$stmt_checkiseditor = $con->prepare('SELECT * FROM `cloudeditors` WHERE `userid` = ?;');
				$stmt_checkiseditor->bind_param('i', $this->id);
				$stmt_checkiseditor->execute();

				$result_checkiseditor = $stmt_checkiseditor->get_result();

				if($result_checkiseditor->num_rows != 0) {
					while($row = $result_checkiseditor->fetch_assoc()) {
						$place = Place::FromID(intval($row['placeid']));

						if($place != null && $place->creator->id != $this->id) {
							$teamcreatedplaces[] = $place;
						}
					}
				}
			}

			
			foreach($grabbedplaces as $asset) {
				$place = Place::FromID($asset->id);
				if($place instanceof Place) {
					if(($teamcreate && $place->teamcreate_enabled && $place->isCloudEditor($this)) || (!$teamcreate && !$place->teamcreate_enabled)) {
						$result[] = $place;
					}
				}
			}
			
			return array_merge($result, $teamcreatedplaces);
		}

		function giveProfileBadge(ANORRLBadge $badge): void {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";

			if(!$this->hasProfileBadgeOf($badge)) {
				$stmt = $con->prepare("INSERT INTO `profilebadges`(`badgeid`, `userid`) VALUES (?, ?)");
				$ordinal = $badge->ordinal();
				$stmt->bind_param('ii`', $ordinal, $this->id);
				$stmt->execute();
			}
		}

		function hasProfileBadgeOf(ANORRLBadge $badge): bool {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `profilebadges` WHERE `badgeid` = ? AND `userid` = ?");
			$ordinal = $badge->ordinal();
			$stmt->bind_param('ii', $ordinal, $this->id);
			$stmt->execute();

			return $stmt->get_result()->num_rows != 0;
		}

		/**
		 * Returns the system badges (Homestead and the alike)
		 * @return void
		 */
		function getProfileBadges(): array {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `profilebadges` WHERE `userid` = ? ORDER BY `recieved_at` DESC, `badgeid` DESC");
			$stmt->bind_param('i',$this->id);
			$stmt->execute();

			$result = $stmt->get_result();

			$badges = [];

			while($row = $result->fetch_assoc()) {
				$badges[] = ANORRLBadge::index($row['badgeid']);
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
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `statuses` WHERE `poster` = ? ORDER BY `posted` DESC");
			$stmt->bind_param('i', $this->id);
			$stmt->execute();
			$result = $stmt->get_result();

			if($result->num_rows == 0) {
				return null;
			} else {
				return new Status($result->fetch_assoc());
			}
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
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
		
			$sql_assettype = $type->ordinal();
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
				$sql_extra .= " AND `creator` = ?";
			}

			if(!$show_all) {
				$sql_extra .= " AND `public` = 1";
			}

			$sql_types = "`type` = ?";
			if($type == AssetType::BODYPARTS) {
				$type_head = AssetType::HEAD->ordinal();
				$type_torso = AssetType::TORSO->ordinal();
				$type_leftarm = AssetType::LEFTARM->ordinal();
				$type_rightarm = AssetType::RIGHTARM->ordinal();
				$type_leftleg = AssetType::LEFTLEG->ordinal();
				$type_rightleg = AssetType::RIGHTLEG->ordinal();

				$sql_types = "(`type` = $type_head OR `type` = $type_torso OR `type` = $type_leftarm OR `type` = $type_rightarm OR `type` = $type_leftleg OR `type` = $type_rightleg)";
			}
			
			$sql = "SELECT assets.* FROM `transactions`, `assets` WHERE `transactions`.`asset` = `assets`.`id` AND `userid` = ? AND $sql_types AND `name` LIKE ? $sql_extra ORDER BY `lastedited` DESC";

			if($type == AssetType::BODYPARTS) {
				if($page <= -1 || $count <= 0) {
					$stmt_getassets = $con->prepare("$sql");
					
					if($creator_only) {
						$stmt_getassets->bind_param('isi', $this->id, $sql_query, $this->id);
					} else {
						$stmt_getassets->bind_param('is', $this->id, $sql_query);
					}
				} else {
					$sql_page = (($page-1)*$count);
					$stmt_getassets = $con->prepare("$sql LIMIT ?, ?");
					
					if($creator_only) {
						$stmt_getassets->bind_param('isiii', $this->id, $sql_query, $this->id, $sql_page, $count);
					} else {
						$stmt_getassets->bind_param('isii', $this->id, $sql_query, $sql_page, $count);
					}
				}
			} else {
				if($page <= -1 || $count <= 0) {
					$stmt_getassets = $con->prepare("$sql");
					
					if($creator_only) {
						$stmt_getassets->bind_param('iisi', $this->id, $sql_assettype, $sql_query, $this->id);
					} else {
						$stmt_getassets->bind_param('iis', $this->id, $sql_assettype, $sql_query);
					}
				} else {
					$sql_page = (($page-1)*$count);
					$stmt_getassets = $con->prepare("$sql LIMIT ?, ?");
					
					if($creator_only) {
						$stmt_getassets->bind_param('iisiii', $this->id, $sql_assettype, $sql_query, $this->id, $sql_page, $count);
					} else {
						$stmt_getassets->bind_param('iisii', $this->id, $sql_assettype, $sql_query, $sql_page, $count);
					}
				}
			}
			

			$stmt_getassets->execute();

			$result = $stmt_getassets->get_result();

			$result_array = [];

			if($result->num_rows != 0) {
				while($row = $result->fetch_assoc()) {
					$result_array[] = Asset::FromID($row['id']);
				}
				return $result_array;
			}

			return [];
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
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
		
			$sql_assettype = $type->ordinal();
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
				$sql_extra .= " AND `creator` = ?";
			}

			if(!$show_all) {
				$sql_extra .= " AND `public` = 1";
			}

			$sql_types = "`type` = ?";
			if($type == AssetType::BODYPARTS) {
				$type_head = AssetType::HEAD->ordinal();
				$type_torso = AssetType::TORSO->ordinal();
				$type_leftarm = AssetType::LEFTARM->ordinal();
				$type_rightarm = AssetType::RIGHTARM->ordinal();
				$type_leftleg = AssetType::LEFTLEG->ordinal();
				$type_rightleg = AssetType::RIGHTLEG->ordinal();

				$sql_types = "(`type` = $type_head OR `type` = $type_torso OR `type` = $type_leftarm OR `type` = $type_rightarm OR `type` = $type_leftleg OR `type` = $type_rightleg)";
			}
			
			
			$sql = "SELECT COUNT(`transactions`.`id`) FROM `transactions`, `assets` WHERE `transactions`.`asset` = `assets`.`id` AND `userid` = ? AND $sql_types AND `name` LIKE ? $sql_extra ORDER BY `date` DESC";

			$stmt_getassets = $con->prepare("$sql");
			
			if($type == AssetType::BODYPARTS) {
				if($creator_only) {
					$stmt_getassets->bind_param('isi', $this->id, $sql_query, $this->id);
				} else {
					$stmt_getassets->bind_param('is', $this->id, $sql_query);
				}
			} else {
				if($creator_only) {
					$stmt_getassets->bind_param('iisi', $this->id, $sql_assettype, $sql_query, $this->id);
				} else {
					$stmt_getassets->bind_param('iis', $this->id, $sql_assettype, $sql_query);
				}
			}
			

			$stmt_getassets->execute();

			$result = $stmt_getassets->get_result();
			$row = $result->fetch_assoc();

			return $row['COUNT(`transactions`.`id`)'];
		}

		function getAllOwnedAssets(): array {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt_getuser = $con->prepare("SELECT * FROM `transactions` WHERE `userid` = ? ORDER BY `date` DESC");
			$stmt_getuser->bind_param('i', $this->id);
			$stmt_getuser->execute();

			$result = $stmt_getuser->get_result();

			$result_array = [];

			if($result->num_rows != 0) {
				while($row = $result->fetch_assoc()) {
					$result_array[] = Asset::FromID($row['asset']);
				}
				return $result_array;
			}

			return [];
		}

		function getLatestAssetUploaded(): Asset|null {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt_getuser = $con->prepare("SELECT * FROM `assets` WHERE `creator` = ? ORDER BY `id` DESC");
			$stmt_getuser->bind_param('i', $this->id);
			$stmt_getuser->execute();

			$result = $stmt_getuser->get_result();

			$result_array = [];

			if($result->num_rows != 0) {
				$row = $result->fetch_assoc();
				return new Asset($row);
			} else {
				return null;
			}
		}

		function isWearing(Asset|int $asset): bool {
			$assetid = $asset;
			if($asset instanceof Asset) {
				$assetid = $asset->id;
			}
			
			if(!$this->owns($asset) || Asset::FromID($assetid) == null) {
				return false;
			}
			
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt_checkinventory = $con->prepare("SELECT * FROM `inventory` WHERE `userid` = ? AND `assetid` = ?;");
			$stmt_checkinventory->bind_param('ii', $this->id, $assetid);
			$stmt_checkinventory->execute();

			$numberrows = $stmt_checkinventory->get_result()->num_rows;
			if($numberrows > 1) {
				$stmt_deleteitem = $con->prepare("DELETE FROM `inventory` WHERE `userid` = ? AND `assetid` = ?;");
				$stmt_deleteitem->bind_param('ii', $this->id, $assetid);
				$stmt_deleteitem->execute();

				$stmt_additem = $con->prepare("INSERT INTO `inventory`(`userid`, `assetid`, `assettype`) VALUES (?, ?, ?)");
				$assettype = 0;

				if($asset instanceof Asset) {
					$assettype = $asset->type->ordinal();
				} else {
					$assettype = Asset::FromID($assetid)->type->ordinal();
				}

				$stmt_additem->bind_param('iii', $this->id, $assetid, $assettype);
				$stmt_additem->execute();
			}

			return $numberrows != 0;
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
							"SELECT * FROM `inventory` WHERE `userid` = :userid AND `assettype` = :assettype",
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
								"SELECT * FROM `inventory` WHERE `userid` = :userid AND `assettype` = :assettype",
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

		function takeOff(Asset|int $asset): array {
			$assetid = $asset;
			if($asset instanceof Asset) {
				$assetid = $asset->id;
			}
			
			if(!$this->owns($asset) || Asset::FromID($assetid) == null) {
				return ["error"=>true, "reason"=>"Invalid item"];
			}

			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";

			if(!$this->isWearing($asset)) {
				return ["error" => false];
			} else {
				$item = Asset::FromID($assetid);
				$assettype = $item->type->ordinal();

				if($item->type->wearable()) {
					if($item->type->wearone()) {
						$stmt_deleteitem = $con->prepare("DELETE FROM `inventory` WHERE `userid` = ? AND `assettype` = ?;");
						$stmt_deleteitem->bind_param('ii', $this->id, $assettype);
						$stmt_deleteitem->execute();
					} else {
						$stmt_deleteitem = $con->prepare("DELETE FROM `inventory` WHERE `userid` = ? AND `assetid` = ?;");
						$stmt_deleteitem->bind_param('ii', $this->id, $assetid);
						$stmt_deleteitem->execute();
					}
				} else {
					return ["error" => true, "reason" => "Invalid item"];
				}
			}

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
			<anorrl xmlns:xmime="http://www.w3.org/2005/05/xmlmime" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://$domain/roblox.xsd" version="4">
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
			$parsedshit= "";

			foreach($getwearing as $asset) {
				if($asset->type != AssetType::EMOTE)
					$parsedshit .= ";http://$domain/asset/?id={$asset->id}";
			}

			if(str_ends_with($parsedshit, ";")) {
				$parsedshit = substr($parsedshit, 0, strlen($parsedshit)-1);
			}
			$time = time();
			return "http://$domain/Asset/BodyColors.ashx?userId=$userId&t=$time$parsedshit";
		}

		function getCharacterAppearanceVerbose(): string {
			$domain = \CONFIG->domain;
			$bodycoloursxml = $this->getBodyColoursXML();
			$getwearing = $this->getWearingArray(true);

			$userId = $this->id;
			$parsedshit= "";

			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

			foreach($getwearing as $id) {
				$asset = Asset::FromID($id);
				if($asset != null) {
					if($asset->type == AssetType::EMOTE)
						continue;
					
					$version = $asset->current_version;
					$parsedshit .= "http://$domain/asset/?id=$id&version=$version;";

					$relatedassets = $asset->getRelatedAssets();

					if(count($relatedassets) != 0) {
						foreach($relatedassets as $relatedasset) {
							$subversion = $relatedasset->current_version;
							$parsedshit .= "http://$domain/asset/?id=$id&version=$subversion;";
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

			if(str_ends_with($parsedshit, ";")) {
				$parsedshit = substr($parsedshit, 0, strlen($parsedshit)-1);
			}

			$bodycoloursxml_encoded = base64_encode($bodycoloursxml);

			return "$bodycoloursxml_encoded;$parsedshit";
		}

		function getCharacterAppearanceHash() {
			return md5($this->getCharacterAppearanceVerbose());
		}

		function updateOutfitHash() {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
			$md5 = $this->getCharacterAppearanceHash();

			$stmt = $con->prepare("UPDATE `users` SET `currentappearancemd5` = ? WHERE `id` = ?");
			$stmt->bind_param("si", $md5, $this->id);
			$stmt->execute();
		}

		function getWearingArray(bool $ordered = false) {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";

			if($ordered) {
				$stmt_checkinventory = $con->prepare("SELECT * FROM `inventory` WHERE `userid` = ? ORDER BY `assetid`");
				$stmt_checkinventory->bind_param('i', $this->id);
				$stmt_checkinventory->execute();
				$checkinventory_result = $stmt_checkinventory->get_result();
				$ids = [];
			
				if($checkinventory_result->num_rows != 0) {
					while($row = $checkinventory_result->fetch_assoc()) {
						$ids[] = $row['assetid'];
					}
				}

				return $ids;
			}

			$stmt_checkinventory = $con->prepare("SELECT * FROM `inventory` WHERE `userid` = ?");
			$stmt_checkinventory->bind_param('i', $this->id);
			$stmt_checkinventory->execute();
			$checkinventory_result = $stmt_checkinventory->get_result();
			$ids = [];
		
			if($checkinventory_result->num_rows != 0) {
				while($row = $checkinventory_result->fetch_assoc()) {
					$ids[] = $row['assetid'];
				}
			}	

			return $ids;
		}

		function getWearing(AssetType|null $type = null): array {
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
					"SELECT DISTINCT `assetid` FROM `inventory` WHERE `userid` = :userid",
					[ ":userid" => $this->id ]
				)->fetchAll(\PDO::FETCH_OBJ);
			}
			
			$assets = [];

			foreach($items as $item) {
				$assets[] = Asset::FromID($item->assetid);
			}

			return $assets;
		}

		function getBodyColours() {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";

			$stmt_grabcolours = $con->prepare("SELECT * FROM `bodycolours` WHERE `userid` = ?;");
			$stmt_grabcolours->bind_param('i', $this->id);
			$stmt_grabcolours->execute();
			$grabcolours_result = $stmt_grabcolours->get_result();

			if($grabcolours_result->num_rows == 0) {
				$stmt_createcolours = $con->prepare("INSERT INTO `bodycolours`(`userid`) VALUES (?);");
				$stmt_createcolours->bind_param('i', $this->id);
				$stmt_createcolours->execute();

				return $this->getBodyColours();
			}
			$colours = $grabcolours_result->fetch_assoc();

			return [
				"head" => $colours['head'],
				"torso" => $colours['torso'],
				"leftarm" => $colours['leftarm'],
				"rightarm" => $colours['rightarm'],
				"leftleg" => $colours['leftleg'],
				"rightleg" => $colours['rightleg'],
			];
		}

		function setBodyColours(int $head, int $torso, int $leftarm, int $rightarm, int $leftleg, int $rightleg) {
			$this->getBodyColours(); // populate if doesn't exist

			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";

			$stmt_createcolours = $con->prepare("UPDATE `bodycolours` SET `head` = ?, `torso` = ?, `leftarm` = ?, `rightarm` = ?, `leftleg` = ?,`rightleg` = ? WHERE `userid` = ?;");
			$stmt_createcolours->bind_param('iiiiiii', $head, $torso, $leftarm, $rightarm, $leftleg, $rightleg, $this->id);
			$stmt_createcolours->execute();
		}
		
		function follow(User|int $user) {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}
			if(!$this->isFollowing($user)) {
				$stmt_getuser = $con->prepare("INSERT INTO `follows`(`follower`, `followed`) VALUES (?, ?);");
				$stmt_getuser->bind_param('ii', $this->id, $userid);
				$stmt_getuser->execute();
			}
		}

		function unfollow(User|int $user) {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}
			if($this->isFollowing($user)) {
				$stmt_getuser = $con->prepare("DELETE FROM `follows` WHERE `follower` = ? AND `followed` = ?;");
				$stmt_getuser->bind_param('ii', $this->id, $userid);
				$stmt_getuser->execute();
			}
		}

		function isFollowing(User|int $user): bool {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}

			$stmt_getuser = $con->prepare("SELECT * FROM `follows` WHERE `follower` = ? AND `followed` = ?;");
			$stmt_getuser->bind_param('ii', $this->id, $userid);
			$stmt_getuser->execute();
			$result = $stmt_getuser->get_result();

			return $result->num_rows != 0;
		}

		function friend(User|int $user) {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}

			if(!$this->isFriendsWith($user) && !$this->isPendingFriendsReq($user) && !$this->isIncomingFriendsReq($user)) {
				$stmt_addfriend = $con->prepare("INSERT INTO `friends`(`sender`, `reciever`) VALUES (?,?)");
				$stmt_addfriend->bind_param('ii', $this->id, $userid);
				$stmt_addfriend->execute();
			} else if($this->isIncomingFriendsReq($user)) {
				$stmt_addfriend = $con->prepare("UPDATE `friends` SET `status`= 1 WHERE `reciever` = ? AND `sender` = ?;");
				$stmt_addfriend->bind_param('ii', $this->id, $userid);
				$stmt_addfriend->execute();
			} else {
				$this->unfriend($user);
			}
		}

		function unfriend(User|int $user) {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}

			if($this->isPendingFriendsReq($user) || $this->isIncomingFriendsReq($user) || $this->isFriendsWith($user)) {
				$stmt_getuser = $con->prepare("DELETE FROM `friends` WHERE (`reciever` = ? AND `sender` = ?)");
				$stmt_getuser->bind_param('ii', $this->id, $userid);
				$stmt_getuser->execute();

				$stmt_getuser = $con->prepare("DELETE FROM `friends` WHERE (`sender` = ? AND `reciever` = ?)");
				$stmt_getuser->bind_param('ii', $this->id, $userid);
				$stmt_getuser->execute();
			}
		}

		function isPendingFriendsReq(User|int $user) {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}

			$stmt_getuser = $con->prepare("SELECT * FROM `friends` WHERE `sender` = ? AND `reciever` = ? AND `status` = 0;");
			$stmt_getuser->bind_param('ii', $this->id, $userid);
			$stmt_getuser->execute();
			$result = $stmt_getuser->get_result();

			return $result->num_rows != 0;
		}

		function isIncomingFriendsReq(User|int $user) {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}

			$stmt_getuser = $con->prepare("SELECT * FROM `friends` WHERE `reciever` = ? AND `sender` = ? AND `status` = 0;");
			$stmt_getuser->bind_param('ii', $this->id, $userid);
			$stmt_getuser->execute();
			$result = $stmt_getuser->get_result();

			return $result->num_rows != 0;
		}

		function isFriendsWith(User|int $user): bool {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}

			$stmt_getuser = $con->prepare("SELECT * FROM `friends` WHERE ((`reciever` = ? AND `sender` = ?) OR (`sender` = ? AND `reciever` = ?)) AND `status` = 1;");
			$stmt_getuser->bind_param('iiii', $this->id, $userid, $this->id, $userid);
			$stmt_getuser->execute();
			$result = $stmt_getuser->get_result();

			return $result->num_rows != 0;
		}

		function updateBio(string $bio): array {
			if(!$this->isBanned()) {
				// check if user hasn't posted one in 30s

				$offset = -3600; //prod

				// lord save me what the fuck is this
				$difference = (time()-($this->last_update->getTimestamp()+$this->last_update->getOffset()+$offset));

				$calculated_time = 30 - $difference; 

				if($difference < 30) {
					return ["error"=> true, "reason" => "You need to wait $calculated_time seconds before updating again."];
				}

				$bio_content = UtilUtils::StripUnicode($bio);

				if(strlen($bio_content) > 1000) {
					return ["error"=> true, "reason" => "Status was too long! (1000 characters maximum)"];
				}

				include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
				$stmt = $con->prepare('UPDATE `users` SET `blurb` = ?, `lastprofileupdate` = now() WHERE `id` = ?;');
				$stmt -> bind_param('si',  $bio_content, $this->id);
				$stmt -> execute();

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
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt = $con->prepare('SELECT * FROM `transactions` WHERE `userid` = ? AND `asset` = ?;');
			$stmt -> bind_param('ii', $this->id, $assetid);
			$stmt -> execute();

			return $stmt->get_result()->num_rows != 0;
		}

		function isAdmin(): bool {
			return $this->hasProfileBadgeOf(ANORRLBadge::ADMINISTRATOR);
		}

		function isBanned(): bool {
			return false;
		}

		function isOnline(): bool {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			
			$stmt_user_status_check = $con->prepare('SELECT * FROM `activity` WHERE `userid` = ? AND `action_time` > DATE_SUB(NOW(),INTERVAL 5 MINUTE)');
			$stmt_user_status_check->bind_param('i', $this->id);
			$stmt_user_status_check->execute();
			$activity_result = $stmt_user_status_check->get_result();
			
			$result = $activity_result->num_rows != 0;
			
			$userGameDetails = $this->getUserGameDetails();
			
			if($userGameDetails != null && $this->getServerDetails($userGameDetails['serverid']) != null) {
				$result = true;
			}
				
			$stmt_result = $result ? 1 : 0;
	
			$stmt_user_status_check = $con->prepare('UPDATE `users` SET `online` = ? WHERE `id` = ?');
			$stmt_user_status_check->bind_param('ii', $stmt_result, $this->id);
			$stmt_user_status_check->execute();
			return $result;
		}

		private function getUserGameDetails(): array|null {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

			$stmt_getsessiondetails = $con->prepare("SELECT * FROM `active_players` WHERE `playerid` = ? AND `status` = 1;");
			$stmt_getsessiondetails->bind_param("i", $this->id);
			$stmt_getsessiondetails->execute();

			$result_getsessiondetails = $stmt_getsessiondetails->get_result();

			if($result_getsessiondetails->num_rows == 1) {
				return $result_getsessiondetails->fetch_assoc();
			}

			return null;
		}

		private function getServerDetails(string $serverID): array|null {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

			$stmt_getsessiondetails = $con->prepare("SELECT * FROM `active_servers` WHERE `id` = ?");
			$stmt_getsessiondetails->bind_param("s", $serverID);
			$stmt_getsessiondetails->execute();

			$result_getsessiondetails = $stmt_getsessiondetails->get_result();

			if($result_getsessiondetails->num_rows != 0) {
				return $result_getsessiondetails->fetch_assoc();
			}

			return null;
		}

		function getOnlineActivity(): string {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			
			$userGameDetails = $this->getUserGameDetails();

			if($userGameDetails != null) {
				$server_details = $this->getServerDetails($userGameDetails['serverid']);

				if($server_details != null) {
					$place = Place::FromID(intval($server_details['placeid']));

					if($place != null) {
						$place_name = $place->name;
						$place_id = $place->id;

						if($place->public) {
							if($server_details['teamcreate'] == 1) {
								return <<<EOT
								[ In Team Create: <a href="{$place->getUrl()}">$place_name</a> ]
								EOT;
							} else {
								return <<<EOT
								[ In Game: <a href="{$place->getUrl()}">$place_name</a> ]
								EOT;
							}
						}
					}
				} else {
					$stmt_getsessiondetails = $con->prepare("DELETE FROM `active_players` WHERE `playerid` = ? AND `status` = 1;");
					$stmt_getsessiondetails->bind_param("i", $this->id);
					$stmt_getsessiondetails->execute();
				}
			}

			$stmt_user_status_check = $con->prepare('SELECT * FROM `activity` WHERE `userid` = ? AND `action_time` > DATE_SUB(NOW(),INTERVAL 5 MINUTE)');
			$stmt_user_status_check->bind_param('i', $this->id);
			$stmt_user_status_check->execute();
			$activity_result = $stmt_user_status_check->get_result();
			
			if($activity_result->num_rows != 0) {
				return $activity_result->fetch_assoc()['action'];
			} else {
				$stmt_user_status_check = $con->prepare('SELECT * FROM `activity` WHERE `userid` = ?');
				$stmt_user_status_check->bind_param('i', $this->id);
				$stmt_user_status_check->execute();
				$activity_result = $stmt_user_status_check->get_result();

				if($activity_result->num_rows != 0) {
					$row = $activity_result->fetch_assoc();
					//
					return "Was last seen: ".$row['action'].", ".UtilUtils::getTimeAgo(\DateTime::createFromFormat("Y-m-d H:i:s", $row['action_time']));
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
					if(!str_contains($file_type, "gif")) {
						$pre_image = imagecreatefromstring($file_contents);
						
						if(!($pre_image instanceof \GdImage)) {
							return ["error" => true, "reason" => "That wasn't an image brochacho!"];
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
							
							imagepng($image, $_SERVER['DOCUMENT_ROOT']."/../users/profile_".$this->id.".png", 9);

							if(!$this->setprofilepicture) {
								include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

								$stmt_updateuser = $con->prepare("UPDATE `users` SET `setprofilepicture` = 1 WHERE `id` = ?;");
								$stmt_updateuser->bind_param("i", $this->id);
								$stmt_updateuser->execute();
							}

							return ["error" => false];
						}

						return ["error" => true, "reason" => "Image was wayyy too small! (16x16 minimum)"];
					}
					else {
						list($width, $height, $type, $attr) = getimagesize($file['tmp_name']);

						if($width > 16 && $height > 16 && $width < 420 && $height < 420 && $width == $height) {
							move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT']."/../users/profile_".$this->id.".png");

							if(!$this->setprofilepicture) {
								include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

								$stmt_updateuser = $con->prepare("UPDATE `users` SET `setprofilepicture` = 1 WHERE `id` = ?;");
								$stmt_updateuser->bind_param("i", $this->id);
								$stmt_updateuser->execute();
							}

							return ["error" => false];
						} else {
							if($width < 16 || $height < 16) {
								return ["error" => true, "reason" => "GIF was wayyy too small! (16x16 minimum)"];
							} else if($width > 256 || $height > 256) {
								return ["error" => true, "reason" => "GIF was wayyy too big! (256x256 maximum)"];
							} else if($width != $height) {
								return ["error" => true, "reason" => "Must be a damn square! SQUARE!!!"];
							} else {
								return ["error" => true, "reason" => "I hate your image. (what the fuck is this resolution)"];
							}
							
						}
					}
				}
				return ["error" => true, "reason" => "Something went wrong when uploading! ($file_type)"];
			}
			
			if($file['size'] > 524288) {
				return ["error" => true, "reason" => "Image too large! 512kb max!"];
			} else {
				return ["error" => true, "reason" => "Something went wrong when uploading!"];
			}
			
		}

		function resetProfilePicture() {
			if($this->setprofilepicture) {
				if(file_exists($_SERVER['DOCUMENT_ROOT']."/../users/profile_{$this->id}.png")) {
					unlink($_SERVER['DOCUMENT_ROOT']."/../users/profile_{$this->id}.png");
				}

				include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

				$stmt_updateuser = $con->prepare("UPDATE `users` SET `setprofilepicture` = 0 WHERE `id` = ?;");
				$stmt_updateuser->bind_param("i", $this->id);
				$stmt_updateuser->execute();
			}
		}

		function getThumbnail(): mixed {
			return null;
		}

		/**
		 * Lowkey start using this more
		 */
		function getThumbsUrl(int $size_x = -1, int $size_y = -1): string {
			if(\SESSION)
				$settings = \SESSION->settings;
			else
				$settings = UserSettings::Get();

			return $this->getThumbsUrlService(
				($this->setprofilepicture ? 
					($settings->headshots ? "headshot" : "profile")
					: "headshot"),
				
				$size_x,
				$size_y
			);
		}

		function getThumbsUrlService(string $service = "headshot", int $size_x = -1, int $size_y = -1): string {

			$size_params = "";
			if($size_x > 0 && $size_y <= 0)
				$size_params = "&sxy=$size_x";
		 	
			else if($size_x > 0 && $size_y > 0)
				$size_params = "&sx=$size_x&sy=$size_y";

			return "/thumbs/$service?id={$this->id}{$size_params}";
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
				"SELECT * FROM `activity` WHERE `userid` = :id LIMIT 1",
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
				"SELECT `serverid` FROM `active_players` WHERE `playerid` = :playerid AND `teamcreate` = :teamcreate", 
				[
					":playerid" => $this->id,
					":teamcreate" => $teamcreate
				]
			)->fetchAll(\PDO::FETCH_OBJ);

			$server = null;

			foreach($rows as $row) {
				$grab_server = GameServer::Get($row->serverid);

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

		function isInAGame(bool $teamcreate = false) {
			return 
				Database::singleton()->run(
					"SELECT * FROM `active_players` WHERE `playerid` = :playerid AND `status` = 1 AND `teamcreate` = :teamcreate", 
					[
						":playerid" => $this->id,
						":teamcreate" => $teamcreate
					]
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

		/* araki, what the fuck am i doing */
		/* paranoia */

		function render(bool $headshot = false, bool $is3D = false) {
			if($headshot && $is3D) {
				return;
			}

			$path = $_SERVER['DOCUMENT_ROOT']."/../renders/";
			if($is3D) {
				$path .= "3d/";
			} else if($headshot) {
				$path .= "headshots/";
			}

			$path .= $this->currentoutfitmd5;
			$path .= $is3D ? ".json" : ".png";

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
			return $_SERVER['DOCUMENT_ROOT']."/../renders/3d/{$this->currentoutfitmd5}.json";
		}
	}
?>
