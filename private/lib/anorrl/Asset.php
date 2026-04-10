<?php

	namespace anorrl;

	use anorrl\enums\AssetType;
	use anorrl\enums\TransactionType;
	use anorrl\utilities\AssetTypeUtils;
	use anorrl\utilities\TransactionUtils;
	use anorrl\utilities\UtilUtils;
	use anorrl\User;
	use anorrl\AssetVersion;

	enum CharacterMeshType {
		case HEAD;
		case TORSO;
		case RIGHTARM;
		case LEFTARM;
		case LEFTLEG;
		case RIGHTLEG;

		public static function index(int $ordinal): CharacterMeshType {
			return match($ordinal) {
				0 => CharacterMeshType::HEAD,
				1 => CharacterMeshType::TORSO,
				2 => CharacterMeshType::LEFTARM,
				3 => CharacterMeshType::RIGHTARM,
				4 => CharacterMeshType::LEFTLEG,
				5 => CharacterMeshType::RIGHTLEG,
			};
		}

		public function ordinal(): int {
			return match($this) {
				CharacterMeshType::HEAD 	    => 0,
				CharacterMeshType::TORSO 		=> 1,
				CharacterMeshType::LEFTARM 		=> 2,
				CharacterMeshType::RIGHTARM 	=> 3,
				CharacterMeshType::LEFTLEG 		=> 4,			
				CharacterMeshType::RIGHTLEG 	=> 5,
			};
		}

		public function assettype(): AssetType {
			return match($this) {
				CharacterMeshType::HEAD 	    => AssetType::HEAD,
				CharacterMeshType::TORSO 		=> AssetType::HEAD,
				CharacterMeshType::RIGHTARM 	=> AssetType::HEAD,
				CharacterMeshType::LEFTARM 		=> AssetType::HEAD,
				CharacterMeshType::LEFTLEG 		=> AssetType::HEAD,
				CharacterMeshType::RIGHTLEG 	=> AssetType::HEAD,
				default => false
			};
		}

		public function label(): string {
			return match($this) {
				CharacterMeshType::HEAD 	    => "Head",
				CharacterMeshType::TORSO 		=> "Torso",
				CharacterMeshType::RIGHTARM 	=> "Right Arm",
				CharacterMeshType::LEFTARM 		=> "Left Arm",
				CharacterMeshType::LEFTLEG 		=> "Left Leg",
				CharacterMeshType::RIGHTLEG 	=> "Right Leg",
			};
		}
	}

	/**
	 * Abstract class for assets
	*/
	class Asset {
		public int         $id;
		public User        $creator;
		public AssetType   $type;
		public string      $name;
		public string      $description;
		public bool        $public;

		public int         $favourites_count;
		public bool        $comments_enabled;

		public bool        $onsale;
		public int         $sales_count;

		public Asset|null  $relatedasset;
		public bool        $notcatalogueable;
		public int         $current_version;
		

		public \DateTime    $last_updatetime;
		public \DateTime    $created_at;

		/**
		 * Attempts to grab an asset given from ID (yes)
		 * 
		 * @param int $id 
		 * @return Asset|null Null if asset was not found.
		 */
		public static function FromID(int $id): Asset|null {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt_getuser = $con->prepare("SELECT * FROM `assets` WHERE `asset_id` = ?");
			$stmt_getuser->bind_param('i', $id);
			$stmt_getuser->execute();
			$result = $stmt_getuser->get_result();

			if($result->num_rows == 1) {
				return new self($result->fetch_assoc());
			} else {
				return null;
			}
		}

		function __construct(array|int $rowdata) {
			if(is_array($rowdata)) {
				$this->id = intval($rowdata['asset_id']);
				$this->creator = User::FromID($rowdata['asset_creator']);
				$this->type = AssetType::index(intval($rowdata['asset_type'])); // temp
				$this->name = str_replace("<", "&lt;", str_replace(">", "&gt;", $rowdata['asset_name']));
				$this->description = str_replace("<", "&lt;", str_replace(">", "&gt;", $rowdata['asset_description']));
				$this->public = boolval($rowdata['asset_public']);

				$this->favourites_count = intval( $rowdata['asset_favourites_count']);
				$this->comments_enabled = boolval($rowdata['asset_comments_enabled']);
	
				$this->onsale = boolval($rowdata['asset_onsale']);
				$this->sales_count = intval($rowdata['asset_sales_count']);

				$this->notcatalogueable = boolval($rowdata['asset_nevershow']);
				$this->relatedasset = Asset::FromID(intval($rowdata['asset_relatedid']));
				$this->current_version = intval($rowdata['asset_currentversion']);
	
				$this->last_updatetime = \DateTime::createFromFormat("Y-m-d H:i:s", $rowdata['asset_lastedited']);
				$this->created_at      = \DateTime::createFromFormat("Y-m-d H:i:s", $rowdata['asset_created']);	
			} else {
				// for extended classes
				$asset_data = Asset::FromID($rowdata);
				
				$this->id = $asset_data->id;
				$this->creator = $asset_data->creator;
				$this->type = $asset_data->type;
				$this->name = $asset_data->name;
				$this->description = $asset_data->description;
				$this->public = $asset_data->public;

				$this->favourites_count = $asset_data->favourites_count;
				$this->comments_enabled = $asset_data->comments_enabled;
	
				$this->onsale = $asset_data->onsale;
				$this->sales_count = $asset_data->sales_count;
				
				$this->notcatalogueable = $asset_data->notcatalogueable;
				$this->relatedasset = $asset_data->relatedasset;
				$this->current_version = $asset_data->current_version;

				$this->last_updatetime = $asset_data->last_updatetime;
				$this->created_at      = $asset_data->created_at;	
			}
		}

		function purchase(TransactionType $type, User|null $user = \SESSION->user): array {
			
			if(!$user)
				return ["error" => true, "reason" => "User not authorised to perform this action!"];

			if(!($this->onsale && AssetTypeUtils::IsSellable($this->type)))
				return ["error" => true, "reason" => "Item is not purchasable!"];
			

			$successful_navigation = false;

			switch($type) {
				case TransactionType::FREE:
					//if($this->cones_cost == 0 && $this->lights_cost == 0)
					$successful_navigation = true;
					//else
					//successful_navigation = false
					break;
				case TransactionType::CONES:
					break;
				case TransactionType::LIGHTS:
					break;
				default:
					$successful_navigation = false;
			}

			if(!$successful_navigation)
				return ["error" => true, "reason" => "Invalid purchasing method!"];

			// transact and shiiit (TransactionUtils be useful)

			$cost = 0; // free since i need to implement the rest

			TransactionUtils::CommitTransaction($this, $user, $type, $cost);

			return ["error" => false];
		}

		function getFileContents(int $version = -1) {
			if($version > 0) {
				$asset_version = AssetVersion::GetVersionOf($this, $version);

				if($asset_version != null) {
					$filename = $_SERVER['DOCUMENT_ROOT']."/../assets/".$asset_version->md5sig;
				} else {
					return null;
				}
			} else {
				if($this->GetLatestVersionDetails() == null) {
					return null;
				}
				$filename = $_SERVER['DOCUMENT_ROOT']."/../assets/".$this->GetLatestVersionDetails()->md5sig;
			}

			if(file_exists($filename)) {
				if(filesize($filename) == 0 || !filesize($filename)) {
					return null;
				}
				$handle = fopen($filename, "r"); 
				$contents = fread($handle, filesize($filename)); 
				fclose($handle);
				$contents = str_replace("www.roblox.com", "{anorrldomain}",$contents);
				$contents = str_replace("api.roblox.com", "{anorrldomain}",$contents);

				return str_replace("{anorrldomain}", \CONFIG->domain, $contents);
			}
			
			return null;
		}

		function IsUsable(): bool {
			$contents = $this->getFileContents();
			if(AssetVersion::GetLatestVersionOf($this) == null || !$contents) {
				return false;
			}
			return strlen(trim($contents)) > 0;
		}

		function GetURLTitle() {
			$result = strtolower(trim(preg_replace('/[^a-zA-Z0-9 ]/', "", $this->name)));
			$result = UtilUtils::RecurseRemove($result, "  ", " ");
			$result = str_replace(" ", "-", $result);
			if($result == "") {
				$result = "unnamed";
			}

			return $result;
		}

		function GetAllVersions(): array {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$stmt_getuser = $con->prepare("SELECT * FROM `asset_versions` WHERE `version_assetid` = ? ORDER BY `version_id` DESC");
			$stmt_getuser->bind_param('i', $this->id);
			$stmt_getuser->execute();

			$result = $stmt_getuser->get_result();

			$result_array = [];

			if($result->num_rows != 0) {
				while($row = $result->fetch_assoc()) {
					array_push($result_array, new AssetVersion($row));
				}
			}

			return $result_array;
		}

		function GetLatestVersionDetails(): AssetVersion|null {
			return AssetVersion::GetLatestVersionOf($this);
		}

		function GetVersionID(): int {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `asset_versions` WHERE `version_assetid` = ? ORDER BY `version_id`");
			$stmt->bind_param("i", $this->id);
			$stmt->execute();

			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			return $row["version_id"];
		}

		function GetMD5HashCurrent(): string {
			return $this->GetMD5Hash($this->GetVersionID());
		}

		function GetMD5Hash(int $version): string {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `asset_versions` WHERE `version_id` = ?");
			$stmt->bind_param("i", $version);
			$stmt->execute();

			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			return $row["version_md5sig"];
		}

		function SetVersion(AssetVersion|null $version) {
			if($version != null && $version->asset->id == $this->id) {
				if($version->sub_id != $this->current_version) {
					include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
					$stmt = $con->prepare("UPDATE `assets` SET `asset_currentversion` = ? WHERE `asset_id` = ?");
					$stmt->bind_param("ii", $version->sub_id, $this->id);
					$stmt->execute();

					return ["error" => false];
				}

				return ["error" => true, "reason" => "Version is already set to this?"];
			}

			return ["error" => true, "reason" => "Version was not found and cannot be applied!"];
		}

		function Favourite(User|int $user) {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}

			if(!$this->HasUserFavourited($user)) {
				$stmt = $con->prepare("INSERT INTO `favourites`(`fav_assetid`, `fav_userid`, `fav_assettype`) VALUES (?, ?, ?);");
				$type = $this->type->ordinal();
				$stmt->bind_param("iii", $this->id, $userid, $type);
				$stmt->execute();

				$this->UpdateFavouritesCount();
			}
		}

		private function UpdateFavouritesCount() {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `favourites` WHERE `fav_assetid` = ?;");
			$stmt->bind_param("i", $this->id);
			$stmt->execute();

			$favcount = $stmt->get_result()->num_rows;

			$stmt = $con->prepare("UPDATE `assets` SET `asset_favourites_count` = ? WHERE `asset_id` = ?");
			$stmt->bind_param("ii", $favcount, $this->id);
			$stmt->execute();
		}

		function Unfavourite(User|int $user) {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}

			if($this->HasUserFavourited($user)) {
				$stmt = $con->prepare("DELETE FROM `favourites` WHERE `fav_assetid` = ? AND `fav_userid` = ?;");
				$stmt->bind_param("ii", $this->id, $userid);
				$stmt->execute();

				$this->UpdateFavouritesCount();
			}
		}

		function HasUserFavourited(User|int $user) {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}

			$stmt = $con->prepare("SELECT * FROM `favourites` WHERE `fav_assetid` = ? AND `fav_userid` = ?;");
			$stmt->bind_param("ii", $this->id, $userid);
			$stmt->execute();

			return $stmt->get_result()->num_rows != 0;
		}

		function GetSales(): array {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `transactions` WHERE `ta_userid` != `ta_assetcreator` AND `ta_asset` = ?;");
			$stmt->bind_param("i", $this->id);
			$stmt->execute();

			$sales = $stmt->get_result();

			$result = [];
			
			while($row = $sales->fetch_assoc()) {
				$user = User::FromID(intval($row['ta_userid']));

				if($user != null && !$user->IsBanned()) {
					array_push($result, $user);
				}
			}

			return $result;
		}

		function UpdateSalesCount() {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `transactions` WHERE `ta_userid` != `ta_assetcreator` AND `ta_asset` = ?;");
			$stmt->bind_param("i", $this->id);
			$stmt->execute();

			$salescount = $stmt->get_result()->num_rows;

			$stmt = $con->prepare("UPDATE `assets` SET `asset_sales_count` = ? WHERE `asset_id` = ?");
			$stmt->bind_param("ii", $salescount, $this->id);
			$stmt->execute();
		}

		function GetRelatedAssets() {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

			$stmt = $con->prepare("SELECT `asset_id` FROM `assets` WHERE `asset_relatedid` = ?");
			$stmt->bind_param("i", $this->id);
			$stmt->execute();
			
			$stmt_result = $stmt->get_result();

			$result = [];

			while($row = $stmt_result->fetch_assoc()) {
				$asset = Asset::FromID(intval($row['asset_id']));
				if($asset != null) {
					array_push($result, $asset);
				}
			}

			return $result;
		}

		function GetAssetIDSafe() : int {
			$assets = $this->GetRelatedAssets();

			if(count($assets) > 0) {
				return $assets[0]->id;
			}

			return $this->id;
		}

		function SetThumbnailTo(Asset $asset) {
			if($this->type == AssetType::AUDIO && ($asset->type == AssetType::DECAL || $asset->type == AssetType::IMAGE)) {
				AssetVersion::GetLatestVersionOf($this)->SetThumbnail($asset);
			}
		}
	}
?>