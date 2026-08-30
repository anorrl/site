<?php

	namespace anorrl;

	use anorrl\enums\AssetType;
	use anorrl\utilities\Utilities;
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

		/**
		 * if the user uploaded it for free and the item REQUIRES money for it to be uploaded.
		 * this flag is enabled and it can NEVER. EVER. be put on sale.
		 * @var bool
		 */
		public bool $owner_only;
		public bool        $onsale;
		public int         $price;
		public int         $sales_count;

		public Asset|null  $relatedasset;
		public bool        $notcatalogueable;
		public int         $current_version;

		public int         $universe = -1;

		public \DateTime    $last_updatetime;
		public \DateTime    $created_at;

		public static function Exists(?int $id): bool {
			if(!is_int($id))
				return false;
			
			$row = Database::singleton()->run(
				"SELECT `id` FROM `assets` WHERE `id` = :id LIMIT 1",
				[ ":id" => $id ]
			)->fetchObject();

			return !is_null($row);
		}

		/**
		 * Attempts to grab an asset given from ID (yes)
		 * 
		 * @param int $id 
		 * @return Asset|null Null if asset was not found.
		 */
		public static function FromID(?int $id): Asset|null {
			if(!is_int($id))
				return null;
			
			$row = Database::singleton()->run(
				"SELECT * FROM `assets` WHERE `id` = :id LIMIT 1",
				[ ":id" => $id ]
			)->fetchObject();

			return $row ? new self($row) : null;
		}

		public static function FromName(string $name): Asset|null {
			$row = Database::singleton()->run(
				"SELECT `id` FROM `assets` WHERE `name` LIKE :name LIMIT 1",
				[ ":name" => $name ]
			)->fetchObject();

			return $row ? self::FromID($row->id) : null;
		}

		protected function __construct(object|int $rowdata) {
			if(is_object($rowdata)) {
				$this->id = $rowdata->id;
				$this->creator = User::FromID($rowdata->creator);
				$this->type = AssetType::index($rowdata->type);
				$this->name = htmlspecialchars($rowdata->name);
				$this->description = htmlspecialchars($rowdata->description);
				$this->public = boolval($rowdata->public);

				$this->favourites_count = $rowdata->favourites_count;
				$this->comments_enabled = boolval($rowdata->comments_enabled);
	
				$this->owner_only = boolval($rowdata->owner_only);
				$this->onsale = boolval($rowdata->onsale);
				$this->price = $rowdata->price;
				$this->sales_count = $rowdata->sales_count;

				$this->notcatalogueable = boolval($rowdata->nevershow);
				$this->relatedasset = Asset::FromID($rowdata->relatedid);
				$this->current_version = $rowdata->currentversion;

				// if the universe is not null, its most likely a developer product or place.
				$this->universe = is_null($rowdata->universe) ? -1 : $rowdata->universe;
	
				$this->last_updatetime = \DateTime::createFromFormat("Y-m-d H:i:s", $rowdata->lastedited);
				$this->created_at      = \DateTime::createFromFormat("Y-m-d H:i:s", $rowdata->created);
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
	
				$this->owner_only = $asset_data->owner_only;
				$this->onsale = $asset_data->onsale;
				$this->price = $asset_data->price;
				$this->sales_count = $asset_data->sales_count;
				
				$this->notcatalogueable = $asset_data->notcatalogueable;
				$this->relatedasset = $asset_data->relatedasset;
				$this->current_version = $asset_data->current_version;

				$this->universe = $asset_data->universe;

				$this->last_updatetime = $asset_data->last_updatetime;
				$this->created_at      = $asset_data->created_at;	
			}
		}

		function getStuffResponse() {
			return [
				"id" => $this->id,
				"name" => $this->name,
				"creator" => [
					"id" => $this->creator->id,
					"name" => $this->creator->name
				],
				"thumbnail" => $this->getThumbsUrl(130),
				"url" => $this->getURL()
			];
		}

		function getFileContents(int $version = -1) {
			if($version > 0) {
				$asset_version = AssetVersion::GetVersionOf($this, $version);

				if($asset_version != null) {
					$filename = get_asset($asset_version->md5sig);
				} else {
					return null;
				}
			} else {
				if($this->getLatestVersionDetails() == null) {
					return null;
				}
				$filename = get_asset($this->getLatestVersionDetails()->md5sig);
			}

			if(file_exists($filename)) {
				if(filesize($filename) == 0 || !filesize($filename)) {
					return null;
				}
				$handle = fopen($filename, "r"); 
				$contents = fread($handle, filesize($filename)); 
				fclose($handle);
				if(!str_starts_with($contents, "<anorrl!") || strlen(\CONFIG->domain) == strlen("www.roblox.com")) {
					$contents = str_replace("www.roblox.com", "{anorrldomain}",$contents);
					$contents = str_replace("api.roblox.com", "{anorrldomain}",$contents);
				}

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
			$result = str_replace(" ", "-", $result);
			$result = Utilities::RecurseRemove($result, "--", "-");
			if($result == "") {
				$result = "unnamed";
			}

			return $result;
		}

		function getURL() {
			return "/catalog/{$this->id}/{$this->getURLTitle()}";
		}

		function getAllVersionsCount() {
			$sql = "SELECT COUNT(`id`) FROM `asset_versions` WHERE `assetid` = :aid";
			$params = [":aid" => $this->id];

			$row = Database::singleton()->run(
				$sql,
				$params
			)->fetch(\PDO::FETCH_ASSOC);

			return $row ? $row['COUNT(`id`)'] : -1;
		}

		function getAllVersions(int $page = -1, int $count = 10): array {
			$paged = $page > 0;
			$sql = "SELECT `id` FROM `asset_versions` WHERE `assetid` = :aid ORDER BY `id` DESC";
			$params = [":aid" => $this->id];

			if($paged) {
				$sql = "$sql LIMIT :page, :count";
				$params[":page"] = (($page-1)*$count);
				$params[":count"] = $count;
			}


			$rows = Database::singleton()->run(
				$sql,
				$params
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
			return Database::singleton()->run(
				"SELECT `id` FROM `asset_versions` WHERE `assetid` = :id ORDER BY `id`",
				[ ":id" => $this->id ]
			)->fetchObject()->id;
		}

		function getMD5HashCurrent(): string {
			return $this->getMD5Hash($this->getVersionID());
		}

		function getMD5Hash(int $version): string {
			return Database::singleton()->run(
				"SELECT `md5sig` FROM `asset_versions` WHERE `id` = :id",
				[ ":id" => $version ]
			)->fetchObject()->md5sig;
		}

		function setVersion(AssetVersion|null $version) {
			if($version != null && $version->asset->id == $this->id) {
				if($version->sub_id != $this->current_version) {
					Database::singleton()->run(
						"UPDATE `assets` SET `currentversion` = :subid WHERE `id` = :id",
						[
							":subid" => $version->sub_id,
							":id" => $this->id
						]
					);

					return ["success" => true];
				}

				return ["success" => false, "reason" => "Version is already set to this?"];
			}

			return ["success" => false, "reason" => "Version was not found and cannot be applied!"];
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
				"SELECT `userid` FROM `favourites` WHERE `assetid` = :id",
				[":id" => $this->id]
			)->rowCount();

			$db->run(
				"UPDATE `assets` SET `favourites_count` = :favcount WHERE `id` = :id",
				[":id" => $this->id, ":favcount" => $favcount]
			);

			$this->favourites_count = $favcount;
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
			$userid = $user;
			if($user instanceof User) {
				$userid = $user->id;
			}

			return Database::singleton()->run(
				"SELECT `assetid` FROM `favourites` WHERE `assetid` = :asset AND `userid` = :user",
				[
					":asset" => $this->id,
					":user" => $userid
				]
			)->fetchObject() != null;
		}

		function getSales(): array {
			$rows = Database::singleton()->run(
				"SELECT `userid` FROM `transactions` WHERE `userid` != `assetcreator` AND `asset` = :id",
				[
					":id" => $this->id
				]
			)->fetchAll(\PDO::FETCH_OBJ);

			$result = [];
			
			foreach($rows as $row) {
				$user = User::FromID($row->userid);

				if($user != null && !$user->isBanned()) {
					$result[] = $user;
				}
			}

			return $result;
		}

		function updateSalesCount() {
			$db = Database::singleton();

			$salescount = $db->run(
				"SELECT `userid` FROM `transactions` WHERE `userid` != `assetcreator` AND `asset` = :id",
				[
					":id" => $this->id
				]
			)->rowCount();
			
			$db->run(
				"UPDATE `assets` SET `sales_count` = :salescount WHERE `id` = :asset",
				[
					":salescount" => $salescount,
					":asset" => $this->id
				]
			);
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
					file_put_contents(get_asset_thumbs($latest_md5), base64_decode($render));
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
					file_put_contents(get_asset_thumbs("3d/{$latest_md5}.json"), $data);
				}
				
			} else {
				if(!file_exists(get_asset_thumbs($latest_md5))) {
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
				if($this->isOwner(\SESSION->user)) {
					$db = Database::singleton();

					$owners = $this->getSales();

					$db->run("DELETE FROM `inventory` WHERE `assetid` = :id", [":id" => $this->id]);
					$db->run("DELETE FROM `transactions` WHERE `asset` = :id", [":id" => $this->id]);
					$db->run("DELETE FROM `favourites` WHERE `assetid` = :id", [":id" => $this->id]);

					$this->checkAndDeleteFiles();

					if($this->type == AssetType::PLACE) {
						$universe = Universe::FromID($this->universe);

						if($universe) {
							if($this->type == AssetType::PLACE) {
								if($universe->starting_place->id == $this->id) {
									$db->run("DELETE FROM `universes` WHERE `id` = :id", [":id" => $universe->id]);
									
									foreach($universe->getAllPlaces() as $place) {
										if($place->id == $this->id)
											continue;

										$place->delete();
									}

									foreach($universe->getAliases() as $alias) {
										$alias->delete();
									}

									foreach($universe->getCloudEditors() as $editor) {
										$universe->removeCloudEditor($editor, true);
									}

									foreach($universe->getDeveloperProducts() as $asset) {
										$asset->setUniverse();
									}
								}
							}
						}

						$db->run("DELETE FROM `visits` WHERE `place` = :id", [":id" => $this->id]);
						$db->run("DELETE FROM `places` WHERE `id` = :id", [":id" => $this->id]);
					}

					foreach($owners as $owner) {
						$owner->updateOutfitHash();
					}

					$db->run("DELETE FROM `assets` WHERE `id` = :id", [":id" => $this->id]);
				}
			}
		}

		/**
		 * remove this and make it seem like it was deleted rather than actually deleted.
		 * @return void
		 */
		private function checkAndDeleteFiles() {
			$directory = $_SERVER['DOCUMENT_ROOT'];
			$assetsdir = "$directory/../assets/";
			$db = Database::singleton();

			$rows = $db->run(
				"SELECT `id` FROM `assets` WHERE `id` = :asset OR `relatedid` = :asset",
				[ ":asset" => $this->id ]
			)->fetchAll(\PDO::FETCH_OBJ);

			$ids = [];
			foreach($rows as $row) {
				$ids[] = $row->id;
			}

			$md5s = [];

			foreach($ids as $key => $value) {
				$row = $db->run(
					"SELECT `md5sig` FROM `asset_versions` WHERE `assetid` = :asset ORDER BY `id` DESC;",
					[ ":asset" => $this->id ]
				)->fetchObject();

				if($row) {
					$md5s["$value"] = $row->md5sig;
				}
			}

			foreach($md5s as $key => $value) {

				$hasOtherAssetsDepending = $db->run(
					"SELECT `id` FROM `asset_versions` WHERE `md5sig` = :value AND `assetid` != :key ORDER BY `id` DESC;",
					[
						":value" => $value,
						":key" => $key
					]
				)->rowCount() != 0;

				if(!$hasOtherAssetsDepending) {
					if(file_exists("$assetsdir/$value")){
						unlink("$assetsdir/$value");
					}

					if(file_exists("$assetsdir/thumbs/$value")){
						unlink("$assetsdir/thumbs/$value");
					}
				}
			}
		
		}

		function getThumbsUrl(int $size_x = -1, int $size_y = -1, bool $nocompress = false): string {
			// maybe in the future these could be used but right now this new cdn/thumbs solution is much better

			/*$size_params = "";
			if($size_x > 0 && $size_y <= 0)
				$size_params = "&sxy=$size_x";
		 	
			else if($size_x > 0 && $size_y > 0)
				$size_params = "&sx=$size_x&sy=$size_y";

			return "/thumbs/?id=" . $this->id . $size_params . ($nocompress ? "&nocompress" : "");*/

			$version = $this->getLatestVersionDetails();
			$md5 = $version->md5sig;
			$thumbsmd5 = $version->md5thumb;

			if(file_exists(get_path_file("assets/thumbs/{$this->id}")))
				$thumbsmd5 = $this->id;

			if($this->type == AssetType::AUDIO && ($thumbsmd5 == "sound" || $md5 == $thumbsmd5))
				return "/public/images/thumbnails/audio.png";
			elseif($this->type == AssetType::AUDIO && !file_exists(get_path_file("assets/thumbs/$thumbsmd5"))) {
				copy(get_path_file("assets/$thumbsmd5"), get_path_file("assets/thumbs/$thumbsmd5"));
			}

			if($this->type == AssetType::EMOTE)
				return "/public/images/thumbnails/emotes.png";

			if($this->type == AssetType::ANIMATION)
				return "/public/images/thumbnails/animation.png";


			if($this->type == AssetType::BADGE && !file_exists(get_path_file("assets/thumbs/$thumbsmd5"))) {
				copy(get_path_file("assets/$md5"), get_path_file("assets/thumbs/$thumbsmd5"));
			}
			
			if(($this->type == AssetType::FACE || $this->type == AssetType::DECAL )) {
				$image = $this->getRelatedAssets()[0];
				$version = $image->getLatestVersionDetails();
				$md5 = $version->md5sig;
				$thumbsmd5 = $version->md5thumb;

				if(!file_exists(get_path_file("assets/thumbs/$thumbsmd5"))) {
					copy(get_path_file("assets/$md5"), get_path_file("assets/thumbs/$thumbsmd5"));
				}

				
			}

			if(!file_exists(get_path_file("assets/thumbs/$thumbsmd5")))
				return "/public/images/thumbnails/unavailable.png";

			return (\CONFIG->prefer_https ? "https":"http")."://thumbs.".\CONFIG->baseurl."/".$thumbsmd5;
		}

		function getThumbnail() {
			$url = $this->getThumbsUrl();
			if(str_contains($url, \CONFIG->baseurl)) {
				$starting = (\CONFIG->prefer_https ? "https":"http")."://thumbs.".\CONFIG->baseurl."/";

				$path = substr($url, strlen($starting));

				return file_get_contents(get_asset_thumbs($path));
			}

			return file_get_contents($url);
		}

		function isOwner(User|null $user, bool $explicit = false) {
			return $user && ($user->id == $this->creator->id || ($user->admin && !$explicit));
		}

		function setUniverse(Universe|int|null $universe = null) {
			if($universe == null) {
				Database::singleton()->run(
					"UPDATE `assets` SET `universe`= NULL WHERE `id` = :id",
					[
						":id" => $this->id
					]
				);
				$this->universe = -1;
			} else {
				Database::singleton()->run(
					"UPDATE `assets` SET `universe`= :uid WHERE `id` = :id",
					[
						":uid" => is_int($universe) ? $universe : $universe->id,
						":id" => $this->id
					]
				);
				$this->universe = is_int($universe) ? $universe : $universe->id;
			}
		}

		function renameTo(string $name) {
			if(strcmp($name, $this->name) == 0)
				return;

			Database::singleton()->run(
				"UPDATE `assets` SET `name`= :name WHERE `id` = :id",
				[
					":id" => $this->id,
					":name" => $name
				]
			);
		}

		function loadEmbed(Page|PageOld $page) {
			$item = $this->type == AssetType::PLACE ? "a place" : "an item";
			$embed_title = htmlspecialchars("\"{$this->name}\" {$item} by {$this->creator->name}", ENT_QUOTES);
			$desc = substr($this->description, 0, 128);
			if(strlen($desc) < strlen($this->description)) {
				$desc .= "...";
			}

			$embed_description = htmlspecialchars($desc, ENT_QUOTES);
			$domain = \CONFIG->domain;
			
			$page->addMeta("title", $embed_title);
			$page->addMeta("description", $embed_description);
			$page->addMeta("og:type", "website");
			$page->addMeta("og:site_name", "ANORRL");
			$page->addMeta("og:url", "https://{$domain}{$this->getURL()}");
			$page->addMeta("og:title", $embed_title);
			$page->addMeta("og:description", $embed_description);
			$page->addMeta("og:image", $this->getThumbsUrl());
		}

		function update(array $settings) {
			if(count($settings) == 0)
				return false;

			$base_settings = [
				"name"				=> "string",
				"description"		=> "string",
				"public"			=> "bool",
				"comments_enabled"	=> "bool",
				"onsale"			=> "bool",
				"price"				=> "int",
			];

			$parsed_settings = [
				"id" => $this->id
			];

			$sql_update = "UPDATE `assets` SET";
			$sql_equals = "`lastedited` = now(), ";
			$sql_where = "WHERE `id` = :id";

			foreach($settings as $key => $value) {
				if(!Utilities::ParseParameters($base_settings, $key, $value)) {
					continue;
				}

				$parsed_settings[$key] = $value;
				
				$sql_equals .= "`$key` = :$key, ";
			}
			$sql_equals = trim($sql_equals);
			if(str_ends_with($sql_equals, ",")) {
				$sql_equals = substr($sql_equals, 0, strlen($sql_equals)-1);
			}

			$pdo = Database::singleton()->run("$sql_update $sql_equals $sql_where", $parsed_settings);

			return $pdo->errorCode() == SQL_ALLOK;
		}

	}
?>
