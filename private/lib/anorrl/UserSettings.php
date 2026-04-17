<?php
	namespace anorrl;

	use anorrl\Asset;
	use anorrl\enums\AssetType;
	use anorrl\Database;
	use anorrl\utilities\UtilUtils;

	use CSSValidator\CSSValidator;

	class UserSettings {

		public User|null $user;
		public bool $randoms_enabled;
		public bool $teto_enabled;
		public bool $accessibility_enabled;
		public bool $headshots_enabled;
		public bool $nightbg_enabled;
		public Asset|null $background_music = null;
		public string $css = "";
		public bool $loadingscreens_enabled;
		public bool $profile_music_enabled;

		public static function Get(User|null $user = null) {
			if($user == null) {
				return new self([
					"userid" => -1,
					"randoms" => 1,
					"teto" => 1,
					"accessbility" => 0,
					"headshots" => 0,
					"nightbg" => 0,
					"bgm" => -1,
					"css" => "",
					"loadingscreens" => true,
					"profilemusic" => true
				]);
			}

			$db = Database::singleton();

			$raw_settings = $db->run(
				"SELECT * FROM `users_settings` WHERE `userid` = :id",
				[":id" => $user->id]
			)->fetch(\PDO::FETCH_OBJ);

			if($raw_settings) {
				return new self($raw_settings);
			} else {
				$db->run(
					"INSERT INTO `users_settings`(`userid`) VALUES (:id);",
					[":id" => $user->id]
				);

				return self::Get($user);
			}
		}

		function __construct(array|Object $rowdata) {
			if(is_array($rowdata)) {
				$this->user = User::FromID(intval($rowdata['userid']));
				$this->randoms_enabled = boolval($rowdata['randoms']);
				$this->teto_enabled = boolval($rowdata['teto']);
				$this->accessibility_enabled = boolval($rowdata['accessbility']);
				$this->headshots_enabled = boolval($rowdata['headshots']);
				$this->nightbg_enabled = boolval($rowdata['nightbg']);
				$this->background_music = $rowdata['bgm'] <= 0 ? null : Asset::FromID($rowdata['bgm']);
				$this->css = $rowdata['css'];
				$this->loadingscreens_enabled = boolval($rowdata['loadingscreens']);
				$this->profile_music_enabled = boolval($rowdata['profilemusic']);
			} else {
				$this->user = User::FromID(intval($rowdata->userid));
				$this->randoms_enabled = boolval($rowdata->randoms);
				$this->teto_enabled = boolval($rowdata->teto);
				$this->accessibility_enabled = boolval($rowdata->accessbility);
				$this->headshots_enabled = boolval($rowdata->headshots);
				$this->nightbg_enabled = boolval($rowdata->nightbg);
				$this->background_music = $rowdata->bgm <= 0 ? null : Asset::FromID($rowdata->bgm);
				$this->css = $rowdata->css;
				$this->loadingscreens_enabled = boolval($rowdata->loadingscreens);
				$this->profile_music_enabled = boolval($rowdata->profilemusic);
			}
			
			if($this->background_music && $this->background_music->type != AssetType::AUDIO)
				$this->background_music = null;
		}

		function setValue(string $name, bool|int $value) {
			$stmt_value = null;

			if(is_bool($value))
				$stmt_value = $value ? 1 : 0; 
			elseif(is_string($value))
				$stmt_value = trim($stmt_value);
			elseif(is_int($value))
				$stmt_value = $value;

			Database::singleton()->run(
				"UPDATE `users_settings` SET `$name` = :value WHERE `userid` = :id;",
				[
					":value" => $stmt_value,
					":id" => $this->user->id
				]
			);
		}

		function setRandomsEnabled(bool $value) {
			$this->setValue("randoms", $value);
			$this->randoms_enabled = $value;
		}

		function setTetoEnabled(bool $value) {
			$this->setValue("teto", $value);
			$this->teto_enabled = $value;
		}

		function setNightBGEnabled(bool $value) {
			$this->setValue("nightbg", $value);
			$this->nightbg_enabled = $value;
		}

		function setAccessibilityEnabled(bool $value) {
			$this->setValue("accessbility", $value);
			$this->accessibility_enabled = $value;
		}

		function setHeadshotsEnabled(bool $value) {
			$this->setValue("headshots", $value);
			$this->headshots_enabled = $value;
		}

		function setLoadingScreensEnabled(bool $value) {
			$this->setValue("loadingscreens", $value);
			$this->loadingscreens_enabled = $value;
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

		function setCSS(string $data = ""): bool {
			$validator = new CSSValidator();

			$result = $validator->validateFragment($data);

			if($result->isValid()) {

				if(!UtilUtils::IsValidCSS($data)) {
					return false;
				}

				Database::singleton()->run(
					"UPDATE `users` SET `css` = :css WHERE `id` = :id;",
					[
						":id" => $this->user->id,
						":css" => $data
					]
				);

				return true;
			}

			return false;
		}

		function setProfileMusicEnabled(bool $value) {
			$this->setValue("profilemusic", $value);
			$this->profile_music_enabled = $value;
		}
	}
?>