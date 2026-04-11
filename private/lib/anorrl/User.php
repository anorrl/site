<?php

	namespace anorrl;
	
	use anorrl\enums\TransactionType;
	use CSSValidator\CSSValidator;
	use anorrl\Asset;
	use anorrl\Database;
	use anorrl\Place;
	use anorrl\enums\AssetType;
	use anorrl\utilities\UtilUtils;
	use anorrl\utilities\ImageUtils;
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
			$stmt_getuser = $con->prepare("SELECT * FROM `users` WHERE `user_id` = ?");
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
			$stmt_getuser = $con->prepare("SELECT * FROM `users` WHERE `user_name` LIKE ?");
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
			$stmt_getuser = $con->prepare("SELECT * FROM `users` WHERE `user_security` = ?");
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
			$this->id = intval($rowdata['user_id']);
			$this->name = strval($rowdata['user_name']);
			$this->blurb = str_replace("<", "&lt;", str_replace(">", "&gt;", $rowdata['user_blurb']));
			$this->last_update = \DateTime::createFromFormat("Y-m-d H:i:s", $rowdata['user_lastprofileupdate']);
			$this->setprofilepicture = boolval($rowdata['user_setprofilepicture']);
			$this->currentoutfitmd5 = strval($rowdata['user_currentappearancemd5']);
			$this->join_date = \DateTime::createFromFormat("Y-m-d H:i:s", $rowdata['user_joindate']);
			$this->password = strval($rowdata['user_password']);
			$this->security_key = strval($rowdata['user_security']);
		}

		function GetFriends(): array {
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
		
		function GetFollowers(): array {
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
		
		function GetFollowing(): array {
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

		function GetPendingFriendRequests(): array {
			include $_SERVER['DOCUMENT_ROOT'] . "/private/connection.php";

			$stmt_getfriendreqs = $con->prepare("SELECT * FROM `friends` WHERE `reciever` = ? AND `status` = 0;");
			$stmt_getfriendreqs->bind_param("i", $this->id);
			$stmt_getfriendreqs->execute();

			$result_getfriendreqs = $stmt_getfriendreqs->get_result();
			
			$result = [];

			if($result_getfriendreqs->num_rows != 0) {
				while($row = $result_getfriendreqs->fetch_assoc()) {
					$user = User::FromID($row['sender']);

					if($user != null) {
						$result[] = $user;
					} else {
						$stmt_deletefriendreq = $con->prepare("DELETE FROM `friends` WHERE `sender` = ? AND `reciever` = ? AND `status` = 0;");
						$stmt_deletefriendreq->bind_param("ii", $row['sender'], $this->id);
						$stmt_deletefriendreq->execute();
					}
				}
			}

			return $result;
		}

		function GetPendingFriendRequestsCount() {
			return count($this->GetPendingFriendRequests());
		}

		function GetFriendsCount(): int {
			return count($this->GetFriends());
		}
		
		function GetFollowersCount(): int {
			return count($this->GetFollowers());
		}

		function GetFollowingCount(): int {
			return count($this->GetFollowing());
		}

		/**
		 * Returns paged list of the user's created games
		 * @return void
		 */
		function GetPlaces(bool $teamcreate = false): array {
			$grabbedplaces = $this->GetOwnedAssets(AssetType::PLACE, "", true);
			$result = [];

			$teamcreatedplaces = [];
			
			if($teamcreate) {
				include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
				$stmt_checkiseditor = $con->prepare('SELECT * FROM `cloudeditors` WHERE `cloudeditor_userid` = ?;');
				$stmt_checkiseditor->bind_param('i', $this->id);
				$stmt_checkiseditor->execute();

				$result_checkiseditor = $stmt_checkiseditor->get_result();

				if($result_checkiseditor->num_rows != 0) {
					while($row = $result_checkiseditor->fetch_assoc()) {
						$place = Place::FromID(intval($row['cloudeditor_placeid']));

						if($place != null && $place->creator->id != $this->id) {
							$teamcreatedplaces[] = $place;
						}
					}
				}
			}

			
			foreach($grabbedplaces as $asset) {
				$place = Place::FromID($asset->id);
				if($place instanceof Place) {
					if(($teamcreate && $place->teamcreate_enabled && $place->IsCloudEditor($this)) || (!$teamcreate && !$place->teamcreate_enabled)) {
						$result[] = $place;
					}
				}
			}
			
			return array_merge($result, $teamcreatedplaces);
		}

		function GiveProfileBadge(ANORRLBadge $badge): void {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `profilebadges` WHERE `badge_id` = ? AND `badge_userid` = ?");
			$ordinal = $badge->ordinal();
			$stmt->bind_param('ii', $ordinal, $this->id);
			$stmt->execute();

			if($stmt->get_result()->num_rows == 0) {
				$stmt = $con->prepare("INSERT INTO `profilebadges`(`badge_id`, `badge_userid`) VALUES (?, ?)");
				$ordinal = $badge->ordinal();
				$admin_badge = $badge == ANORRLBadge::ADMINISTRATOR ? 1 : 0;
				$stmt->bind_param('iii`', $ordinal, $this->id, $admin_badge);
				$stmt->execute();
			}
		}

		function HasProfileBadgeOf(ANORRLBadge $badge): bool {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `profilebadges` WHERE `badge_id` = ? AND `badge_userid` = ?");
			$ordinal = $badge->ordinal();
			$stmt->bind_param('ii', $ordinal, $this->id);
			$stmt->execute();

			return $stmt->get_result()->num_rows != 0;
		}

		/**
		 * Returns the system badges (Homestead and the alike)
		 * @return void
		 */
		function GetProfileBadges(): array {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `profilebadges` WHERE `badge_userid` = ? ORDER BY `badge_recieved` DESC, `badge_id` DESC");
			$stmt->bind_param('i',$this->id);
			$stmt->execute();

			$result = $stmt->get_result();

			$badges = [];

			while($row = $result->fetch_assoc()) {
				$badges[] = ANORRLBadge::index($row['badge_id']);
			}

			return $badges;
		}

		/**
		 * Returns badges created by the users (from games)
		 * @return void
		 */
		function GetUserBadges(): array {
			return [];
		}

		function GetLatestStatus(): Status|null {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `statuses` WHERE `status_poster` = ? ORDER BY `status_posted` DESC");
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
		function GetOwnedAssets(AssetType $type, string $query = "", bool $creator_only = false, bool $show_all = true, array $excludedids = [], int $page = -1, int $count = -1): array {
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
				$processedids = "AND `asset_id` NOT IN (";
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
				$sql_extra .= " AND `asset_creator` = ?";
			}

			if(!$show_all) {
				$sql_extra .= " AND `asset_public` = 1";
			}
			
			$sql = "SELECT assets.* FROM `transactions`, `assets` WHERE `transactions`.`asset` = `assets`.`asset_id` AND `userid` = ? AND `asset_type` = ? AND `asset_name` LIKE ? $sql_extra ORDER BY `asset_lastedited` DESC";

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

			$stmt_getassets->execute();

			$result = $stmt_getassets->get_result();

			$result_array = [];

			if($result->num_rows != 0) {
				while($row = $result->fetch_assoc()) {
					$result_array[] = Asset::FromID($row['asset_id']);
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
		function GetOwnedAssetsCount(AssetType $type, string $query = "", bool $creator_only = false, bool $show_all = true, array $excludedids = []): int {
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
				$processedids = "AND `asset_id` NOT IN (";
				foreach($excludedids as $id) {
					$processedids .= $id.",";
				}
				$processedids = substr($processedids, 0, strlen($processedids)-1);
				$processedids .= ")";

				$sql_extra = $processedids;
			}

			if($creator_only) {
				$sql_extra .= " AND `asset_creator` = ?";
			}

			if(!$show_all) {
				$sql_extra .= " AND `asset_public` = 1";
			}
			
			$sql = "SELECT COUNT(`asset_id`) FROM `transactions`, `assets` WHERE `transactions`.`asset` = `assets`.`asset_id` AND `userid` = ? AND `asset_type` = ? AND `asset_name` LIKE ? $sql_extra ORDER BY `date` DESC";

			$stmt_getassets = $con->prepare("$sql");
				
			if($creator_only) {
				$stmt_getassets->bind_param('iisi', $this->id, $sql_assettype, $sql_query, $this->id);
			} else {
				$stmt_getassets->bind_param('iis', $this->id, $sql_assettype, $sql_query);
			}

			$stmt_getassets->execute();

			$result = $stmt_getassets->get_result();
			$row = $result->fetch_assoc();

			return $row['COUNT(`asset_id`)'];
		}

		function GetAllOwnedAssets(): array {
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

		function GetLatestAssetUploaded() {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt_getuser = $con->prepare("SELECT * FROM `assets` WHERE `asset_creator` = ? ORDER BY `asset_id` DESC");
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

		function IsWearing(Asset|int $asset): bool {
			$assetid = $asset;
			if($asset instanceof Asset) {
				$assetid = $asset->id;
			}
			
			if(!$this->owns($asset) || Asset::FromID($assetid) == null) {
				return false;
			}
			
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt_checkinventory = $con->prepare("SELECT * FROM `inventory` WHERE `inv_userid` = ? AND `inv_assetid` = ?;");
			$stmt_checkinventory->bind_param('ii', $this->id, $assetid);
			$stmt_checkinventory->execute();

			$numberrows = $stmt_checkinventory->get_result()->num_rows;
			if($numberrows > 1) {
				$stmt_deleteitem = $con->prepare("DELETE FROM `inventory` WHERE `inv_userid` = ? AND `inv_assetid` = ?;");
				$stmt_deleteitem->bind_param('ii', $this->id, $assetid);
				$stmt_deleteitem->execute();

				$stmt_additem = $con->prepare("INSERT INTO `inventory`(`inv_userid`, `inv_assetid`, `inv_assettype`) VALUES (?, ?, ?)");
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

		function Wear(Asset|int $asset): array {

			$theabsolutelimit = 5;

			$assetid = $asset;
			if($asset instanceof Asset) {
				$assetid = $asset->id;
			}
			
			if(!$this->owns($asset) || Asset::FromID($assetid) == null) {
				return ["error"=>true, "reason"=>"Invalid item"];
			}

			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";

			if($this->IsWearing($asset)) {
				return ["error" => false];
			} else {
				$item = Asset::FromID($assetid);
				$assettype = $item->type->ordinal();
				
				if($item->type->wearable()) {
					if($item->type->wearone()) {
						$stmt_checkinventory = $con->prepare("SELECT * FROM `inventory` WHERE `inv_userid` = ? AND `inv_assettype` = ?;");
						$stmt_checkinventory->bind_param('ii', $this->id, $assettype);
						$stmt_checkinventory->execute();

						if($stmt_checkinventory->get_result()->num_rows == 0) {
							$stmt_additem = $con->prepare("INSERT INTO `inventory`(`inv_userid`, `inv_assetid`, `inv_assettype`) VALUES (?, ?, ?)");
							$assettype = $item->type->ordinal();
							$stmt_additem->bind_param('iii', $this->id, $assetid, $assettype);
							$stmt_additem->execute();
						} else {
							$stmt_replaceitem = $con->prepare("UPDATE `inventory` SET `inv_assetid` = ? WHERE `inv_userid` = ? AND `inv_assettype` = ?");
							$stmt_replaceitem->bind_param('iii', $assetid, $this->id, $assettype);
							$stmt_replaceitem->execute();
						}
					} else {
						/*$stmt_checkinventory = $con->prepare("SELECT * FROM `inventory` WHERE `inv_userid` = ? AND `inv_assettype` = ?;");
						$stmt_checkinventory->bind_param('ii', $this->id, $assettype);
						$stmt_checkinventory->execute();

						if($stmt_checkinventory->get_result()->num_rows < $theabsolutelimit) {
							
						} else {
							return ["error" => true, "reason" => "Too many fucking ".strtolower($item->type->label())."s on"];
						}*/

						$stmt_additem = $con->prepare("INSERT INTO `inventory`(`inv_userid`, `inv_assetid`, `inv_assettype`) VALUES (?, ?, ?)");
						$assettype = $item->type->ordinal();
						$stmt_additem->bind_param('iii', $this->id, $assetid, $assettype);
						$stmt_additem->execute();
					}
				} else {
					return ["error" => true, "reason" => "Invalid item"];
				}

			}

			return ["error" => false];
		}

		function Unwear(Asset|int $asset): array {
			$assetid = $asset;
			if($asset instanceof Asset) {
				$assetid = $asset->id;
			}
			
			if(!$this->owns($asset) || Asset::FromID($assetid) == null) {
				return ["error"=>true, "reason"=>"Invalid item"];
			}

			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";

			if(!$this->IsWearing($asset)) {
				return ["error" => false];
			} else {
				$item = Asset::FromID($assetid);
				$assettype = $item->type->ordinal();

				if($item->type->wearable()) {
					if($item->type->wearone()) {
						$stmt_deleteitem = $con->prepare("DELETE FROM `inventory` WHERE `inv_userid` = ? AND `inv_assettype` = ?;");
						$stmt_deleteitem->bind_param('ii', $this->id, $assettype);
						$stmt_deleteitem->execute();
					} else {
						$stmt_deleteitem = $con->prepare("DELETE FROM `inventory` WHERE `inv_userid` = ? AND `inv_assetid` = ?;");
						$stmt_deleteitem->bind_param('ii', $this->id, $assetid);
						$stmt_deleteitem->execute();
					}
				} else {
					return ["error" => true, "reason" => "Invalid item"];
				}
			}

			return ["error" => false];
		}

		function GetBodyColoursXML() {
			$colours = $this->GetBodyColours();
			$headcolour = $colours['head'];
			$rightarmcolour = $colours['rightarm'];
			$leftlegcolour = $colours['leftleg'];
			$leftarmcolour = $colours['leftarm'];
			$rightlegcolour = $colours['rightleg'];
			$torsocolour = $colours['torso'];
			$domain = \CONFIG->domain;

			return <<<EOT
			<roblox xmlns:xmime="http://www.w3.org/2005/05/xmlmime" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://$domain/roblox.xsd" version="4">
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
			</roblox>
			EOT;
		}

		function GetCharacterAppearance(): string {
			$domain = \CONFIG->domain;
			$getwearing = $this->GetWearingArray();

			$userId = $this->id;
			$parsedshit= "";

			foreach($getwearing as $id) {
				$parsedshit .= ";http://$domain/asset/?id=$id";
			}

			if(str_ends_with($parsedshit, ";")) {
				$parsedshit = substr($parsedshit, 0, strlen($parsedshit)-1);
			}
			$time = time();
			return "http://$domain/Asset/BodyColors.ashx?userId=$userId&t=$time$parsedshit";
		}

		function GetCharacterAppearanceVerbose(): string {
			$domain = \CONFIG->domain;
			$bodycoloursxml = $this->GetBodyColoursXML();
			$getwearing = $this->GetWearingArray(true);

			$userId = $this->id;
			$parsedshit= "";

			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

			foreach($getwearing as $id) {
				$asset = Asset::FromID($id);
				if($asset != null) {
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
						"DELETE FROM `inventory` WHERE `inv_assetid` = :id",
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

		function GetCharacterAppearanceHash() {
			return md5($this->GetCharacterAppearanceVerbose());
		}

		function UpdateOutfitHash() {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
			$md5 = $this->GetCharacterAppearanceHash();

			$stmt = $con->prepare("UPDATE `users` SET `user_currentappearancemd5` = ? WHERE `user_id` = ?");
			$stmt->bind_param("si", $md5, $this->id);
			$stmt->execute();
		}

		function GetWearingArray(bool $ordered = false) {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";

			if($ordered) {
				$stmt_checkinventory = $con->prepare("SELECT * FROM `inventory` WHERE `inv_userid` = ? ORDER BY `inv_assetid`");
				$stmt_checkinventory->bind_param('i', $this->id);
				$stmt_checkinventory->execute();
				$checkinventory_result = $stmt_checkinventory->get_result();
				$ids = [];
			
				if($checkinventory_result->num_rows != 0) {
					while($row = $checkinventory_result->fetch_assoc()) {
						$ids[] = $row['inv_assetid'];
					}
				}

				return $ids;
			}

			$stmt_checkinventory = $con->prepare("SELECT * FROM `inventory` WHERE `inv_userid` = ?");
			$stmt_checkinventory->bind_param('i', $this->id);
			$stmt_checkinventory->execute();
			$checkinventory_result = $stmt_checkinventory->get_result();
			$ids = [];
		
			if($checkinventory_result->num_rows != 0) {
				while($row = $checkinventory_result->fetch_assoc()) {
					$ids[] = $row['inv_assetid'];
				}
			}	

			return $ids;
		}

		function GetBodyColours() {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";

			$stmt_grabcolours = $con->prepare("SELECT * FROM `bodycolours` WHERE `colours_userid` = ?;");
			$stmt_grabcolours->bind_param('i', $this->id);
			$stmt_grabcolours->execute();
			$grabcolours_result = $stmt_grabcolours->get_result();

			if($grabcolours_result->num_rows == 0) {
				$stmt_createcolours = $con->prepare("INSERT INTO `bodycolours`(`colours_userid`) VALUES (?);");
				$stmt_createcolours->bind_param('i', $this->id);
				$stmt_createcolours->execute();

				return $this->GetBodyColours();
			}
			$colours = $grabcolours_result->fetch_assoc();

			return [
				"head" => $colours['colours_head'],
				"torso" => $colours['colours_torso'],
				"leftarm" => $colours['colours_leftarm'],
				"rightarm" => $colours['colours_rightarm'],
				"leftleg" => $colours['colours_leftleg'],
				"rightleg" => $colours['colours_rightleg'],
			];
		}

		function SetBodyColours(int $head, int $torso, int $leftarm, int $rightarm, int $leftleg, int $rightleg) {
			$this->GetBodyColours(); // populate if doesn't exist

			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";

			$stmt_createcolours = $con->prepare("UPDATE `bodycolours` SET `colours_head` = ?, `colours_torso` = ?, `colours_leftarm` = ?, `colours_rightarm` = ?, `colours_leftleg` = ?,`colours_rightleg` = ? WHERE `colours_userid` = ?;");
			$stmt_createcolours->bind_param('iiiiiii', $head, $torso, $leftarm, $rightarm, $leftleg, $rightleg, $this->id);
			$stmt_createcolours->execute();
		}
		
		function Follow(User|int $user) {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}
			if(!$this->IsFollowing($user)) {
				$stmt_getuser = $con->prepare("INSERT INTO `follows`(`follower`, `followed`) VALUES (?, ?);");
				$stmt_getuser->bind_param('ii', $this->id, $userid);
				$stmt_getuser->execute();
			}
		}

		function Unfollow(User|int $user) {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}
			if($this->IsFollowing($user)) {
				$stmt_getuser = $con->prepare("DELETE FROM `follows` WHERE `follower` = ? AND `followed` = ?;");
				$stmt_getuser->bind_param('ii', $this->id, $userid);
				$stmt_getuser->execute();
			}
		}

		function IsFollowing(User|int $user): bool {
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

		function Friend(User|int $user) {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}

			if(!$this->IsFriendsWith($user) && !$this->IsPendingFriendsReq($user) && !$this->IsIncomingFriendsReq($user)) {
				$stmt_addfriend = $con->prepare("INSERT INTO `friends`(`sender`, `reciever`) VALUES (?,?)");
				$stmt_addfriend->bind_param('ii', $this->id, $userid);
				$stmt_addfriend->execute();
			} else if($this->IsIncomingFriendsReq($user)) {
				$stmt_addfriend = $con->prepare("UPDATE `friends` SET `status`= 1 WHERE `reciever` = ? AND `sender` = ?;");
				$stmt_addfriend->bind_param('ii', $this->id, $userid);
				$stmt_addfriend->execute();
			} else {
				$this->Unfriend($user);
			}
		}

		function Unfriend(User|int $user) {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}

			if($this->IsPendingFriendsReq($user) || $this->IsIncomingFriendsReq($user) || $this->IsFriendsWith($user)) {
				$stmt_getuser = $con->prepare("DELETE FROM `friends` WHERE (`reciever` = ? AND `sender` = ?)");
				$stmt_getuser->bind_param('ii', $this->id, $userid);
				$stmt_getuser->execute();

				$stmt_getuser = $con->prepare("DELETE FROM `friends` WHERE (`sender` = ? AND `reciever` = ?)");
				$stmt_getuser->bind_param('ii', $this->id, $userid);
				$stmt_getuser->execute();
			}
		}

		function IsPendingFriendsReq(User|int $user) {
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

		function IsIncomingFriendsReq(User|int $user) {
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

		function IsFriendsWith(User|int $user): bool {
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

		function UpdateBio(string $bio): array {
			if(!$this->isBanned()) {
				// check if user hasn't posted one in 30s

				//$offset = 3600; // windows blehh
				$offset = -3600; //prod


				$difference = (time()-($this->last_update->getTimestamp()+$this->last_update->getOffset()+$offset));

				//die(strval($difference));

				$calculated_time = 30 - $difference; 

				if($difference < 30) {
					return ["error"=> true, "reason" => "You need to wait $calculated_time seconds before updating again."];
				}

				$bio_content = UtilUtils::StripUnicode($bio);

				if(strlen($bio_content) > 1000) {
					return ["error"=> true, "reason" => "Status was too long! (1000 characters maximum)"];
				}

				include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
				$stmt = $con->prepare('UPDATE `users` SET `user_blurb` = ?, `user_lastprofileupdate` = now() WHERE `user_id` = ?;');
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
			return $this->HasProfileBadgeOf(ANORRLBadge::ADMINISTRATOR);
		}

		function isBanned(): bool {
			return false;
		}

		function IsOnline(): bool {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			
			$stmt_user_status_check = $con->prepare('SELECT * FROM `activity` WHERE `userid` = ? AND `action_time` > DATE_SUB(NOW(),INTERVAL 5 MINUTE)');
			$stmt_user_status_check->bind_param('i', $this->id);
			$stmt_user_status_check->execute();
			$activity_result = $stmt_user_status_check->get_result();
			
			$result = $activity_result->num_rows != 0;
			
			$userGameDetails = $this->getUserGameDetails();
			
			if($userGameDetails != null && $this->getServerDetails($userGameDetails['session_serverid']) != null) {
				$result = true;
			}
				
			$stmt_result = $result ? 1 : 0;
	
			$stmt_user_status_check = $con->prepare('UPDATE `users` SET `user_online` = ? WHERE `user_id` = ?');
			$stmt_user_status_check->bind_param('ii', $stmt_result, $this->id);
			$stmt_user_status_check->execute();
			return $result;
		}

		private function getUserGameDetails(): array|null {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

			$stmt_getsessiondetails = $con->prepare("SELECT * FROM `active_players` WHERE `session_playerid` = ? AND `session_status` = 1;");
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

			$stmt_getsessiondetails = $con->prepare("SELECT * FROM `active_servers` WHERE `server_id` = ?");
			$stmt_getsessiondetails->bind_param("s", $serverID);
			$stmt_getsessiondetails->execute();

			$result_getsessiondetails = $stmt_getsessiondetails->get_result();

			if($result_getsessiondetails->num_rows != 0) {
				return $result_getsessiondetails->fetch_assoc();
			}

			return null;
		}

		function GetOnlineActivity(): string {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			
			$userGameDetails = $this->getUserGameDetails();

			if($userGameDetails != null) {
				$server_details = $this->getServerDetails($userGameDetails['session_serverid']);

				if($server_details != null) {
					$place = Place::FromID(intval($server_details['server_placeid']));

					if($place != null) {
						$place_stubname = $place->getURLTitle();
						$place_name = $place->name;
						$place_id = $place->id;

						if($place->public) {
							if($server_details['server_teamcreate'] == 1) {
								return <<<EOT
								[ In Team Create: <a href="/$place_stubname-place?id=$place_id">$place_name</a> ]
								EOT;
							} else {
								return <<<EOT
								[ In Game: <a href="/$place_stubname-place?id=$place_id">$place_name</a> ]
								EOT;
							}
						}
					}
				} else {
					$stmt_getsessiondetails = $con->prepare("DELETE FROM `active_players` WHERE `session_playerid` = ? AND `session_status` = 1;");
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

		function SetProfilePicture(array $file): array {
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

								$stmt_updateuser = $con->prepare("UPDATE `users` SET `user_setprofilepicture` = 1 WHERE `user_id` = ?;");
								$stmt_updateuser->bind_param("i", $this->id);
								$stmt_updateuser->execute();
							}

							return ["error" => false];
						}

						return ["error" => true, "reason" => "Image was wayyy too small! (16x16 minimum)"];
					}
					else {
						list($width, $height, $type, $attr) = getimagesize($file['tmp_name']);

						if($width > 16 && $height > 16 && $width < 420 && $height < 420) {
							move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT']."/../users/profile_".$this->id.".png");

							if(!$this->setprofilepicture) {
								include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

								$stmt_updateuser = $con->prepare("UPDATE `users` SET `user_setprofilepicture` = 1 WHERE `user_id` = ?;");
								$stmt_updateuser->bind_param("i", $this->id);
								$stmt_updateuser->execute();
							}

							return ["error" => false];
						} else {
							if($width < 16 && $height < 16) {
								return ["error" => true, "reason" => "GIF was wayyy too small! (16x16 minimum)"];
							} else if($width > 256 && $height > 256) {
								return ["error" => true, "reason" => "GIF was wayyy too big! (256x256 maximum)"];
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

		function ResetProfilePicture() {
			if($this->setprofilepicture) {
				if(file_exists($_SERVER['DOCUMENT_ROOT']."/../users/profile_{$this->id}.png")) {
					unlink($_SERVER['DOCUMENT_ROOT']."/../users/profile_{$this->id}.png");
				}

				include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

				$stmt_updateuser = $con->prepare("UPDATE `users` SET `user_setprofilepicture` = 0 WHERE `user_id` = ?;");
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
		function getThumbsUrl(): string {
			return "/thumbs/" . ($this->setprofilepicture ? "profile" : "headshot"). "?id=".$this->id;
		}

		function getAccountAge(): int {
			return UtilUtils::GetTimeDifference($this->join_date);
		}

		function getNetLights(): int {
			return $this->getNetAmount(TransactionType::LIGHTS);
		}

		function getNetCones(): int {
			return $this->getNetAmount(TransactionType::CONES);
		}

		function getNetAmount(TransactionType $type): int {
			
			$fetch = Database::singleton()->run(
				"SELECT * FROM `transactions` WHERE (`userid` = :id OR `assetcreator` = :id) AND `method` LIKE :type",
				[
					":id" => $this->id,
					":type" => $type->ordinal()
				]
			)->fetchAll(\PDO::FETCH_OBJ);

			$result_sum = 0;
			
			foreach($fetch as $row) {
				if(!$row->asset || $row->userid != $this->id)
					$result_sum += $row->cost;

				elseif($row->userid == $this->id)
					$result_sum -= $row->cost;
			}

			return $result_sum;
		}

		function canAfford(Asset $asset, TransactionType $type) {
			switch($type) {
				case TransactionType::LIGHTS:
					return $this->getNetLights() - $asset->lights >= 0;
				case TransactionType::CONES:
					return $this->getNetLights() - $asset->cones >= 0;
				default:
					return true;
			}
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

		function pendingStipend() {
			return Database::singleton()->run(
				"SELECT * FROM `subscriptions` WHERE `userid` = :id AND `lastpaytime` > DATE_SUB(NOW(),INTERVAL 1 DAY)",
				[":id" => $this->id]
			)->rowCount() == 0;
		}

	}
?>
