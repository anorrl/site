<?php
	namespace anorrl;

	use anorrl\Asset;
	use anorrl\enums\AssetType;
	use anorrl\Database;
	use anorrl\utilities\AssetTypeUtils;
	use anorrl\utilities\Utilities;
	use anorrl\utilities\ClientDetector;

	use CSSValidator\CSSValidator;

	class UserSettings {

		public int $user;
		public bool $headshots;
		public bool $nightbg;
		public Asset|null $background_music = null;
		public Asset|null $playerlisticon = null;
		public string $css = "";
		public bool $profile_music;
		public \DateTime $last_username_change;

		public static function Get(User|int|null $user = null) {
			$user_id = $user;
			if($user && !is_int($user))
				$user_id = $user->id;
			else
				$user_id = -1;

			if($user_id <= 0) {
				return new self((Object)[
					"userid" => -1,
					"accessbility" => false,
					"headshots" => false,
					"nightbg" => false,
					"bgm" => -1,
					"plicon" => -1,
					"css" => "",
					"profilemusic" => true,
					"last_username_change" => false,
				]);
			}

			$db = Database::singleton();

			$raw_settings = $db->run(
				"SELECT * FROM `users_settings` WHERE `userid` = :id",
				[":id" => $user_id]
			)->fetch(\PDO::FETCH_OBJ);

			if($raw_settings) {
				return new self($raw_settings);
			} else {
				$db->run(
					"INSERT INTO `users_settings`(`userid`) VALUES (:id);",
					[":id" => $user_id]
				);

				return self::Get($user);
			}
		}

		private function CreateColumn(string $name, bool $default) {
			//ALTER TABLE `users_settings` ADD `test` INT(1) NOT NULL DEFAULT '1';
			try {
				Database::singleton()->run(
					"ALTER TABLE `users_settings` ADD `$name` INT(1) NOT NULL DEFAULT :value",
					[
						":value" => $default
					]
				);
			} catch(\PDOException $e) {
				error_log("Failed to create default value for $name!!!!");
			}

			return $default;
		}

		function __construct(Object $rowdata) {
			if(!isset($rowdata->userid)) {
				throw new \Exception("Missing user_settings table");
			}

			$this->user = intval($rowdata->userid);
			$this->headshots = !isset($rowdata->headshots) ? self::CreateColumn("headshots", false) : $rowdata->headshots;
			$this->nightbg = !isset($rowdata->nightbg) ? self::CreateColumn("nightbg", false) : $rowdata->nightbg;
			$this->profile_music = !isset($rowdata->profilemusic) ? self::CreateColumn("profilemusic", true) : $rowdata->profilemusic;
			$this->background_music = $rowdata->bgm <= 0 ? null : Asset::FromID($rowdata->bgm);
			$playericon_id = !isset($rowdata->plicon) ? self::CreateColumn("plicon", true) : $rowdata->plicon;
			$this->playerlisticon = $playericon_id ? Asset::FromID($playericon_id) : null;
			$this->css = $rowdata->css;

			$this->last_username_change = !$rowdata->last_username_change ? 
				new \DateTime("@0") :
				\DateTime::createFromFormat("Y-m-d H:i:s", $rowdata->last_username_change);
			
			if($this->background_music && $this->background_music->type != AssetType::AUDIO || !$this->background_music->isUsable())
				$this->background_music = null;
			
			if($this->playerlisticon && !AssetTypeUtils::IsActualImage($this->playerlisticon->type) || !$this->playerlisticon->isUsable())
				$this->playerlisticon = null;
		}

		function setValue(string $name, bool|int $value) {
			$stmt_value = null;

			if(is_bool($value))
				$stmt_value = $value ? 1 : 0; 
			elseif(is_string($value))
				$stmt_value = trim($stmt_value);
			elseif(is_int($value))
				$stmt_value = $value;

			try {
				Database::singleton()->run(
					"UPDATE `users_settings` SET `$name` = :value WHERE `userid` = :id;",
					[
						":value" => $stmt_value,
						":id" => $this->user
					]
				);
			} catch(\PDOException $e) {
				error_log("Failed to set value for $name!");
				throw new \Exception("Failed to set value for $name! Missing column?");
			}
		}

		function setNightBGEnabled(bool $value) {
			$this->setValue("nightbg", $value);
			$this->nightbg = $value;
		}

		function setHeadshotsEnabled(bool $value) {
			$this->setValue("headshots", $value);
			$this->headshots = $value;
		}
		
		function setBackgroundMusic(Asset|int|null $asset = null) {
			$parsed_asset = is_int($asset) ? Asset::FromID($asset) : $asset;

			if($parsed_asset && $parsed_asset->type == AssetType::AUDIO) {
				$this->setValue("bgm", $parsed_asset->id);
				$this->background_music = $parsed_asset;
			} else {
				$this->setValue("bgm", -1);
				$this->background_music = null;
			}
		}

		function setPlayerListIcon(Asset|int|null $asset = null) {
			$parsed_asset = is_int($asset) ? Asset::FromID($asset) : $asset;
			if($parsed_asset && AssetTypeUtils::IsActualImage($parsed_asset->type)) {
				$this->setValue("plicon", $parsed_asset->getAssetIDSafe());
				$this->background_music = $parsed_asset;
			} else {
				$this->setValue("plicon", -1);
				$this->background_music = null;
			}
		}
		
		function setCSS(string $data = ""): bool {
			$validator = new CSSValidator();

			$result = $validator->validateFragment($data);

			if($result->isValid()) {

				if(!Utilities::IsValidCSS($data)) {
					return false;
				}

				Database::singleton()->run(
					"UPDATE `users` SET `css` = :css WHERE `id` = :id;",
					[
						":id" => $this->user,
						":css" => $data
					]
				);

				return true;
			}

			return false;
		}

		function setProfileMusicEnabled(bool $value) {
			$this->setValue("profilemusic", $value);
			$this->profile_music = $value;
		}
	}
?>
