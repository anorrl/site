<?php

	namespace anorrl;

	use anorrl\enums\AssetType;
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
			$stmt_getuser = $con->prepare("SELECT * FROM `assets` WHERE `id` = ? LIMIT 1");
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
				$this->id = intval($rowdata['id']);
				$this->creator = User::FromID($rowdata['creator']);
				$this->type = AssetType::index(intval($rowdata['type']));
				$this->name = str_replace("<", "&lt;", str_replace(">", "&gt;", $rowdata['name']));
				$this->description = str_replace("<", "&lt;", str_replace(">", "&gt;", $rowdata['description']));
				$this->public = boolval($rowdata['public']);

				$this->favourites_count = intval( $rowdata['favourites_count']);
				$this->comments_enabled = boolval($rowdata['comments_enabled']);
	
				$this->onsale = boolval($rowdata['onsale']);
				$this->sales_count = intval($rowdata['sales_count']);

				$this->notcatalogueable = boolval($rowdata['nevershow']);
				$this->relatedasset = Asset::FromID(intval($rowdata['relatedid']));
				$this->current_version = intval($rowdata['currentversion']);
	
				$this->last_updatetime = \DateTime::createFromFormat("Y-m-d H:i:s", $rowdata['lastedited']);
				$this->created_at      = \DateTime::createFromFormat("Y-m-d H:i:s", $rowdata['created']);	
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

		function purchase(User|null $user = null): array {
			
			if(!$user)
				return ["error" => true, "reason" => "User not authorised to perform this action!"];

			if($user->owns($this))
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

			TransactionUtils::CommitTransaction($user, $this);

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

		function getURL() {
			$typa = $this instanceof Place ? "place" : "item";
			return "{$this->getURLTitle()}-{$typa}?id={$this->id}";
		}

		function getAllVersions(): array {
			$rows = Database::singleton()->run(
				"SELECT `id` FROM `asset_versions` WHERE `assetid` = ? ORDER BY `id` DESC",
				[ ":aid" => $this->id ]
			)->fetchAll(\PDO::FETCH_OBJ);

			$result_array = [];

			foreach($rows as $row) {
				$result_array[] = AssetVersion::FromID($row->id);
			}

			return $result_array;
		}

		function getLatestVersionDetails(): AssetVersion|null {
			return AssetVersion::GetLatestVersionOf($this);
		}

		function getVersionID(): int {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `asset_versions` WHERE `assetid` = ? ORDER BY `id`");
			$stmt->bind_param("i", $this->id);
			$stmt->execute();

			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			return $row['id'];
		}

		function getMD5HashCurrent(): string {
			return $this->getMD5Hash($this->getVersionID());
		}

		function getMD5Hash(int $version): string {
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
			$stmt = $con->prepare("SELECT * FROM `asset_versions` WHERE `id` = ?");
			$stmt->bind_param("i", $version);
			$stmt->execute();

			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			return $row['md5sig'];
		}

		function setVersion(AssetVersion|null $version) {
			if($version != null && $version->asset->id == $this->id) {
				if($version->sub_id != $this->current_version) {
					include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
					$stmt = $con->prepare("UPDATE `assets` SET `currentversion` = ? WHERE `id` = ?");
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
					"INSERT INTO `favourites`(`assetid`, `userid`, `assettype`) VALUES (:id, :uid, :type);",
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
				"SELECT * FROM `favourites` WHERE `assetid` = :id",
				[":id" => $this->id]
			)->rowCount();

			$db->run(
				"UPDATE `assets` SET `favourites_count` = :favcount WHERE `id` = :id",
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
					"DELETE FROM `favourites` WHERE `assetid` = :id AND `userid` = :uid;",
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

			$stmt = $con->prepare("SELECT * FROM `favourites` WHERE `assetid` = ? AND `userid` = ?;");
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

				if($user != null && !$user->isBanned()) {
					$result[] = $user;
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

			$stmt = $con->prepare("UPDATE `assets` SET `sales_count` = ? WHERE `id` = ?");
			$stmt->bind_param("ii", $salescount, $this->id);
			$stmt->execute();
		}

		function getRelatedAssets() {
			$rows = Database::singleton()->run(
				"SELECT `id` FROM `assets` WHERE `relatedid` = :assetid",
				[ ":assetid" => $this->id ]
			)->fetchAll(\PDO::FETCH_OBJ);

			$result = [];

			foreach($rows as $row) {
				$result[] = Asset::FromID($row->id);
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

		function render(bool $is3D = false) {
			$id = $this->id;
			$type = $this->type;

			if($type == AssetType::SHIRT || $type == AssetType::PANTS) {
				$render = Renderer::RenderClothing($id, $is3D);	
			} else if($type == AssetType::PLACE) {
				$render = Renderer::RenderPlace($id);
			} else if($type == AssetType::MESH) {
				$render = Renderer::RenderMesh($id, $is3D);
			} else if($type == AssetType::MODEL || $type == AssetType::HAT || $type == AssetType::GEAR) {
				$render = Renderer::RenderModel($id, $is3D);
			} else if(
				$type == AssetType::HEAD	 ||
				$type == AssetType::TORSO	 ||
				$type == AssetType::LEFTARM	 ||
				$type == AssetType::RIGHTARM ||
				$type == AssetType::LEFTLEG	 ||
				$type == AssetType::RIGHTLEG
			) {
				$render = Renderer::RenderClothing($id, $is3D);
			}

			$latest_version = AssetVersion::GetLatestVersionOf($this);

			if(!$latest_version)
				return;

			$latest_md5 = $latest_version->md5sig;

			if($render != null) {
				$latest_version->setThumbnail($this);

				if(!$is3D || $type == AssetType::PLACE) {
					$data = base64_decode($render);
					file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/thumbs/{$latest_md5}", $data);
				} else {
					$data = trim($render);
					$data = str_replace("\"x\":+", "\"x\":-", $data);
					$data = str_replace("\"y\":+", "\"y\":-", $data);
					$data = str_replace("\"z\":+", "\"z\":-", $data);

					//$data = preg_replace("/Player([0-9]+)Tex\.png/i", "scene.png", $data);

					if(!str_ends_with($data, "}")) {
						while(!str_ends_with($data, "}")) {
							$data = substr($data, 0, strlen($data)-1);
						}
					}
					file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/3d/{$latest_md5}.json", $data);
				}
				
			} else {
				if(file_exists($_SERVER['DOCUMENT_ROOT']."/../assets/thumbs/{$latest_md5}")) {

				} else {
					Database::singleton()->run(
						"UPDATE `asset_versions` SET `md5thumb` = 'placeholder' WHERE `id` = :versionid",
						[
							":versionid" => $latest_version->id
						]
					);
				}
			}
		}

		function delete() {
			if(\SESSION) {
				if(\SESSION->user->isAdmin() || \SESSION->user->id == $this->creator->id) {
					// update name to [Content Deleted]
					// update description to [Content Deleted]
					// update noncatalogable to true
					// update status to private

					/*$stmt = $con->prepare('DELETE FROM `inventory` WHERE `assetid` = ?');
					$stmt -> bind_param("i", $id);
					$stmt->execute();

					

					$stmt = $con->prepare('DELETE FROM `transactions` WHERE `asset` = ?');
					$stmt -> bind_param("i", $id);
					$stmt->execute();

					$stmt = $con->prepare('DELETE FROM `visits` WHERE `place` = ?');
					$stmt -> bind_param("i", $id);
					$stmt->execute();

					$stmt = $con->prepare('DELETE FROM `favourites` WHERE `assetid` = ?');
					$stmt -> bind_param("i", $id);
					$stmt->execute();
					
					$this->checkAndDeleteFiles();

					$stmt = $con->prepare('DELETE FROM `assets` WHERE `id` = ?');
					$stmt -> bind_param("i", $id);
					$stmt->execute();

					if($asset->type == AssetType::PLACE) {
						$stmt = $con->prepare('DELETE FROM `places` WHERE `id` = ?');
						$stmt -> bind_param("i", $id);
						$stmt->execute();
					}*/
				}
			}
		}

		function getThumbnail(): mixed {

			/*$version = AssetVersion::GetLatestVersionOf($asset);

			if($version == null && $asset->type == AssetType::PLACE) {
				$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/noassets.png");
			} else {
				$md5hash = $version->md5sig;
				$thumbsmd5hash = $version->md5thumb;

				if($asset->type == AssetType::AUDIO && ($thumbsmd5hash == "sound" || $md5hash == $thumbsmd5hash)) {
					$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/audio.png");
				} else if($asset->type == AssetType::LUA) {
					$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/script.png");
				} else if($asset->type == AssetType::ANIMATION) {
					$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/animation.png");
				} else if($thumbsmd5hash == "placeholder" || !$asset->isUsable()) {
					$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/unavailable.png");
				} else {
					// TODO: rewrite this abomination.
					if($asset->type == AssetType::AUDIO && $md5hash != $thumbsmd5hash) {
						if(file_exists($_SERVER['DOCUMENT_ROOT']."/../assets/$thumbsmd5hash")) {
							$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/../assets/$thumbsmd5hash");
							$specialcase = true;
						} else {
							$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/unavailable.png");
						}
					} else {
						if(count($asset->getRelatedAssets()) != 0 && ($asset->type == AssetType::DECAL || $asset->type == AssetType::FACE) || $asset->type == AssetType::IMAGE) {
							if(count($asset->getRelatedAssets()) == 1 && $asset->getRelatedAssets()[0]->type == AssetType::IMAGE && ($asset->type == AssetType::DECAL || $asset->type == AssetType::FACE)) {
								$thumbsmd5hash = $asset->getRelatedAssets()[0]->getLatestVersionDetails()->md5sig;
							}
							
							if(file_exists($_SERVER['DOCUMENT_ROOT']."/../assets/$thumbsmd5hash")) {
								$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/../assets/$thumbsmd5hash");
								$specialcase = true;
							} else {
								$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/unavailable.png");
							}
						} else {
							if(file_exists($_SERVER['DOCUMENT_ROOT']."/../assets/thumbs/$id")) {
								$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/../assets/thumbs/$id");
							}
							else if(file_exists($_SERVER['DOCUMENT_ROOT']."/../assets/thumbs/$thumbsmd5hash")) {
								$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/../assets/thumbs/$thumbsmd5hash");
							}
							else {
								$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/unavailable.png");
							}
						}
					}
					
				}
			}*/
			
			return null;
		}

		private function checkAndDeleteFiles() {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			if($asset != null) {
				$stmt = $con->prepare("SELECT * FROM `assets` WHERE `id` = ? OR `relatedid` = ?;");
				$stmt->bind_param("ii", $this->id, $this->id);
				$stmt->execute();

				$result = $stmt->get_result();

				$ids = [];
				while($row = $result->fetch_assoc()) {
					$ids[] = $row['id'];
				}

				$md5s = [];

				foreach($ids as $key => $value) {
					$stmt = $con->prepare("SELECT * FROM `asset_versions` WHERE `assetid` = ? ORDER BY `id` DESC;");
					$stmt->bind_param("i", $value);
					$stmt->execute();

					$result = $stmt->get_result();
					if($result->num_rows != 0) {
						$row = $result->fetch_assoc();

						$md5s["$value"] = $row['md5sig'];
					}
				}

				foreach($md5s as $key => $value) {
					$stmt = $con->prepare("SELECT * FROM `asset_versions` WHERE `md5sig` = ? AND `assetid` != ? ORDER BY `id` DESC;");
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

		function getThumbsUrl(int $size_x = -1, int $size_y = -1): string {
			$size_params = "";
			if($size_x > 0 && $size_y <= 0)
				$size_params = "&sxy=$size_x";
		 	
			else if($size_x > 0 && $size_y > 0)
				$size_params = "&sx=$size_x&sy=$size_y";

			return "/thumbs/?id=" . $this->id . $size_params;
		}

	}
?>