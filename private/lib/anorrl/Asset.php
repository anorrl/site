<?php

	namespace anorrl;

	use anorrl\enums\AssetType;
	use anorrl\enums\TransactionType;
	use anorrl\utilities\AssetTypeUtils;
	use anorrl\utilities\TransactionUtils;
	use anorrl\utilities\UtilUtils;
	use anorrl\utilities\Renderer;
	use anorrl\User;
	use anorrl\AssetVersion;

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
		/** cost */
		public int         $cones;
		/** cost */
		public int         $lights;

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
			$stmt_getuser = $con->prepare("SELECT * FROM `assets` WHERE `asset_id` = ? LIMIT 1");
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
				$this->type = AssetType::index(intval($rowdata['asset_type']));
				$this->name = str_replace("<", "&lt;", str_replace(">", "&gt;", $rowdata['asset_name']));
				$this->description = str_replace("<", "&lt;", str_replace(">", "&gt;", $rowdata['asset_description']));
				$this->public = boolval($rowdata['asset_public']);

				$this->favourites_count = intval( $rowdata['asset_favourites_count']);
				$this->comments_enabled = boolval($rowdata['asset_comments_enabled']);
	
				$this->onsale = boolval($rowdata['asset_onsale']);
				$this->sales_count = intval($rowdata['asset_sales_count']);

				$this->lights = intval($rowdata['asset_lights']);
				$this->cones = intval($rowdata['asset_cones']);

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

				$this->lights = $asset_data->lights;
				$this->cones = $asset_data->cones;
				
				$this->notcatalogueable = $asset_data->notcatalogueable;
				$this->relatedasset = $asset_data->relatedasset;
				$this->current_version = $asset_data->current_version;

				$this->last_updatetime = $asset_data->last_updatetime;
				$this->created_at      = $asset_data->created_at;	
			}
		}

		function purchase(TransactionType $type, User|null $user = null): array {
			
			if(!$user)
				return ["error" => true, "reason" => "User not authorised to perform this action!"];

			if($user->Owns($this))
				if(!$this->onsale)
					return ["error" => true, "reason" => "Item is off-sale and beside you already own this?!"];
				else
					return ["error" => true, "reason" => "You already own this item!"];
			
			if(!$this->isUsable())
				return ["error" => true, "reason" => "Item is unusable at this time!"];

			if(!$this->onsale || !AssetTypeUtils::IsSellable($this->type))
				if(!$this->onsale)
					return ["error" => true, "reason" => "Item is off-sale sorry not sorry..."];
				else
					return ["error" => true, "reason" => "Item is not purchasable!"];
			
			$successful_navigation = false;

			switch($type) {
				case TransactionType::FREE:					
					$successful_navigation = $this->cones == 0 && $this->lights == 0;
					break;
				case TransactionType::CONES:
					$successful_navigation = $this->cones > 0;
					break;
				case TransactionType::LIGHTS:
					$successful_navigation = $this->lights > 0;
					break;
				default:
					$successful_navigation = false;
			}
			
			if(!$successful_navigation)
				return ["error" => true, "reason" => "Invalid purchasing method!"];
			
			if(!$user->canAfford($this, $type))
				return ["error" => true, "reason" => "Hey wait you can't buy this item! YOU'RE FUCKING BROOKEEE!!!"];

			TransactionUtils::CommitAssetTransaction($type, $this, $user);

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
				if($this->getLatestVersionDetails() == null) {
					return null;
				}
				$filename = $_SERVER['DOCUMENT_ROOT']."/../assets/".$this->getLatestVersionDetails()->md5sig;
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

		function isUsable(): bool {
			return true;

			$contents = $this->getFileContents();
			if(AssetVersion::GetLatestVersionOf($this) == null || !$contents) {
				return false;
			}
			return strlen(trim($contents)) > 0;
		}

		function getURLTitle() {
			$result = strtolower(trim(preg_replace('/[^a-zA-Z0-9 ]/', "", $this->name)));
			$result = UtilUtils::RecurseRemove($result, "  ", " ");
			$result = str_replace(" ", "-", $result);
			if($result == "") {
				$result = "unnamed";
			}

			return $result;
		}

		function getAllVersions(): array {
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

		function getLatestVersionDetails(): AssetVersion|null {
			return AssetVersion::GetLatestVersionOf($this);
		}

		function getVersionID(): int {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `asset_versions` WHERE `version_assetid` = ? ORDER BY `version_id`");
			$stmt->bind_param("i", $this->id);
			$stmt->execute();

			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			return $row["version_id"];
		}

		function getMD5HashCurrent(): string {
			return $this->getMD5Hash($this->getVersionID());
		}

		function getMD5Hash(int $version): string {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `asset_versions` WHERE `version_id` = ?");
			$stmt->bind_param("i", $version);
			$stmt->execute();

			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			return $row["version_md5sig"];
		}

		function setVersion(AssetVersion|null $version) {
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

		function favourite(User|int $user) {
			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}

			if(!$this->hasUserFavourited($user)) {
				Database::singleton()->run(
					"INSERT INTO `favourites`(`fav_assetid`, `fav_userid`, `fav_assettype`) VALUES (:id, :uid, :type);",
					[
						":id" => $this->id,
						":uid" => $userid,
						":type" => $this->type->ordinal()
					]
				);

				$this->updateFavouritesCount();
			}
		}

		private function updateFavouritesCount() {
			$db = Database::singleton();

			$favcount = $db->run(
				"SELECT * FROM `favourites` WHERE `fav_assetid` = :id",
				[":id" => $this->id]
			)->rowCount();

			$db->run(
				"UPDATE `assets` SET `asset_favourites_count` = :favcount WHERE `asset_id` = :id",
				[":id" => $this->id, ":favcount" => $favcount]
			);
		}

		function unfavourite(User|int $user) {
			
			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}

			if($this->hasUserFavourited($user)) {
				Database::singleton()->run(
					"DELETE FROM `favourites` WHERE `fav_assetid` = :id AND `fav_userid` = :uid;",
					[
						":id" => $this->id,
						":uid" => $userid
					]
				);

				$this->updateFavouritesCount();
			}
		}

		function hasUserFavourited(User|int $user) {
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

		function getSales(): array {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `transactions` WHERE `userid` != `assetcreator` AND `asset` = ?;");
			$stmt->bind_param("i", $this->id);
			$stmt->execute();

			$sales = $stmt->get_result();

			$result = [];
			
			while($row = $sales->fetch_assoc()) {
				$user = User::FromID(intval($row['userid']));

				if($user != null && !$user->IsBanned()) {
					array_push($result, $user);
				}
			}

			return $result;
		}

		function updateSalesCount() {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `transactions` WHERE `userid` != `assetcreator` AND `asset` = ?;");
			$stmt->bind_param("i", $this->id);
			$stmt->execute();

			$salescount = $stmt->get_result()->num_rows;

			$stmt = $con->prepare("UPDATE `assets` SET `asset_sales_count` = ? WHERE `asset_id` = ?");
			$stmt->bind_param("ii", $salescount, $this->id);
			$stmt->execute();
		}

		function getRelatedAssets() {
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

		function getAssetIDSafe() : int {
			$assets = $this->getRelatedAssets();

			if(count($assets) > 0) {
				return $assets[0]->id;
			}

			return $this->id;
		}

		function setThumbnailTo(Asset $asset) {
			if($this->type == AssetType::AUDIO && ($asset->type == AssetType::DECAL || $asset->type == AssetType::IMAGE)) {
				AssetVersion::GetLatestVersionOf($this)->setThumbnail($asset);
			}
		}

		function render() {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

			$id = $this->id;
			$type = $this->type;

			if($type == AssetType::SHIRT || $type == AssetType::PANTS) {
				$render = Renderer::RenderPlayer($id);	
			} else if($type == AssetType::PLACE) {
				$render = Renderer::RenderPlace($id);
			} else if($type == AssetType::MESH) {
				$render = Renderer::RenderMesh($id);
			} else if($type == AssetType::MODEL || $type == AssetType::HAT || $type == AssetType::GEAR) {
				$render = Renderer::RenderModel($id);
			} else if($type == AssetType::TORSO) {
				$render = Renderer::RenderPlayer($id);
			}

			if($render != null) {
				$data = base64_decode($render);
				
				AssetVersion::GetLatestVersionOf($this)->setThumbnail($this);

				file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/thumbs/".AssetVersion::GetLatestVersionOf($this)->md5sig, $data);
			} else {
				if(file_exists($_SERVER['DOCUMENT_ROOT']."/../assets/thumbs/".AssetVersion::GetLatestVersionOf($this)->md5thumb)) {

				} else {
					$stmt = $con->prepare("UPDATE `asset_versions` SET `version_md5thumb` = 'placeholder' WHERE `version_id` = ?");
					$stmt->bind_param('i', AssetVersion::GetLatestVersionOf($this)->id);
					$stmt->execute();
				}
			}
		}

		function delete() {
			if(\SESSION) {
				if(\SESSION->user->isAdmin()) {
					include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
					$stmt = $con->prepare('DELETE FROM `inventory` WHERE `inv_assetid` = ?');
					$stmt -> bind_param("i", $id);
					$stmt->execute();

					// update name to [Content Deleted]
					// update description to [Content Deleted]
					// update noncatalogable to true
					// update status to private

					/*$stmt = $con->prepare('DELETE FROM `transactions` WHERE `asset` = ?');
					$stmt -> bind_param("i", $id);
					$stmt->execute();

					$stmt = $con->prepare('DELETE FROM `visit` WHERE `visit_place` = ?');
					$stmt -> bind_param("i", $id);
					$stmt->execute();

					$stmt = $con->prepare('DELETE FROM `favourites` WHERE `fav_assetid` = ?');
					$stmt -> bind_param("i", $id);
					$stmt->execute();
					
					$this->checkAndDeleteFiles();

					$stmt = $con->prepare('DELETE FROM `assets` WHERE `asset_id` = ?');
					$stmt -> bind_param("i", $id);
					$stmt->execute();

					if($asset->type == AssetType::PLACE) {
						$stmt = $con->prepare('DELETE FROM `asset_places` WHERE `place_id` = ?');
						$stmt -> bind_param("i", $id);
						$stmt->execute();
					}*/
				}
			}

			

		}

		/**
		 * I'm probably going to remove this completely
		 * @return void
		 */
		private function checkAndDeleteFiles() {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			if($asset != null) {
				$stmt = $con->prepare("SELECT * FROM `assets` WHERE `asset_id` = ? OR `asset_relatedid` = ?;");
				$stmt->bind_param("ii", $this->id, $this->id);
				$stmt->execute();

				$result = $stmt->get_result();

				$ids = [];
				while($row = $result->fetch_assoc()) {
					array_push($ids, $row['asset_id']);
				}

				$md5s = [];

				foreach($ids as $key => $value) {
					$stmt = $con->prepare("SELECT * FROM `asset_versions` WHERE `version_assetid` = ? ORDER BY `version_id` DESC;");
					$stmt->bind_param("i", $value);
					$stmt->execute();

					$result = $stmt->get_result();
					if($result->num_rows != 0) {
						$row = $result->fetch_assoc();

						$md5s["$value"] = $row['version_md5sig'];
					}
				}

				foreach($md5s as $key => $value) {
					$stmt = $con->prepare("SELECT * FROM `asset_versions` WHERE `version_md5sig` = ? AND `version_assetid` != ? ORDER BY `version_id` DESC;");
					$stmt->bind_param("si", $value, $key);
					$stmt->execute();

					$result = $stmt->get_result();
					if($result->num_rows == 0) {
						$row = $result->fetch_assoc();

						if(file_exists("$assetsdir/$value")){
							unlink("$assetsdir/$value");
						}

						if(file_exists("$assetsdir/thumbs/$value")){
							unlink("$assetsdir/thumbs/$value");
						}
					}
				}
			}
		}

	}
?>