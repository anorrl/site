<?php
	namespace anorrl;

	class UserSettings {

		public User|null $user;
		public bool $randoms_enabled;
		public bool $teto_enabled;
		public bool $emotesounds_enabled;
		public bool $accessibility_enabled;
		public bool $headshots_enabled;
		public bool $nightbg_enabled;

		public static function Get(User|null $user = null) {
			if($user == null) {
				return new self([
					"settings_userid" => -1,
					"settings_randoms" => 1,
					"settings_teto" => 1,
					"settings_emotesounds" => 1,
					"settings_accessbility" => 0,
					"settings_headshots" => 0,
					"settings_nightbg" => 0,
				]);
			}

			include $_SERVER["DOCUMENT_ROOT"]."/core/connection.php";
			$stmt_getuser = $con->prepare("SELECT * FROM `users_settings` WHERE `settings_userid` = ?");
			$stmt_getuser->bind_param('i', $user->id);
			$stmt_getuser->execute();
			$result = $stmt_getuser->get_result();

			if($result->num_rows == 1) {
				return new self($result->fetch_assoc());
			} else {
				$stmt_getuser = $con->prepare("INSERT INTO `users_settings`(`settings_userid`) VALUES (?);");
				$stmt_getuser->bind_param('i', $user->id);
				$stmt_getuser->execute();
				return self::Get($user);
			}
		}

		function __construct($rowdata) {
			$this->user = User::FromID(intval($rowdata['settings_userid']));
			$this->randoms_enabled = boolval($rowdata['settings_randoms']);
			$this->teto_enabled = boolval($rowdata['settings_teto']);
			$this->emotesounds_enabled = boolval($rowdata['settings_emotesounds']);
			$this->accessibility_enabled = boolval($rowdata['settings_accessbility']);
			$this->headshots_enabled = boolval($rowdata['settings_headshots']);
			$this->nightbg_enabled = boolval($rowdata['settings_nightbg']);
		}

		function SetRandomsEnabled(bool $value) {
			$stmt_value = $value ? 1 : 0;

			include $_SERVER["DOCUMENT_ROOT"]."/core/connection.php";
			$stmt_updatesetting = $con->prepare("UPDATE `users_settings` SET `settings_randoms` = ? WHERE `settings_userid` = ?;");
			$stmt_updatesetting->bind_param('ii', $stmt_value, $this->user->id);
			$stmt_updatesetting->execute();
			$this->randoms_enabled = $value;
		}

		function SetTetoEnabled(bool $value) {
			$stmt_value = $value ? 1 : 0;

			include $_SERVER["DOCUMENT_ROOT"]."/core/connection.php";
			$stmt_updatesetting = $con->prepare("UPDATE `users_settings` SET `settings_teto` = ? WHERE `settings_userid` = ?;");
			$stmt_updatesetting->bind_param('ii', $stmt_value, $this->user->id);
			$stmt_updatesetting->execute();
			$this->teto_enabled = $value;
		}

		function SetNightBGEnabled(bool $value) {
			$stmt_value = $value ? 1 : 0;
			
			include $_SERVER["DOCUMENT_ROOT"]."/core/connection.php";
			$stmt_updatesetting = $con->prepare("UPDATE `users_settings` SET `settings_nightbg` = ? WHERE `settings_userid` = ?;");
			$stmt_updatesetting->bind_param('ii', $stmt_value, $this->user->id);
			$stmt_updatesetting->execute();
			$this->nightbg_enabled = $value;
		}

		function SetEmoteSoundsEnabled(bool $value) {
			$stmt_value = $value ? 1 : 0;

			include $_SERVER["DOCUMENT_ROOT"]."/core/connection.php";
			$stmt_updatesetting = $con->prepare("UPDATE `users_settings` SET `settings_emotesounds` = ? WHERE `settings_userid` = ?;");
			$stmt_updatesetting->bind_param('ii', $stmt_value, $this->user->id);
			$stmt_updatesetting->execute();
			$this->emotesounds_enabled = $value;
		}

		function SetAccessibilityEnabled(bool $value) {
			$stmt_value = $value ? 1 : 0;

			include $_SERVER["DOCUMENT_ROOT"]."/core/connection.php";
			$stmt_updatesetting = $con->prepare("UPDATE `users_settings` SET `settings_accessbility` = ? WHERE `settings_userid` = ?;");
			$stmt_updatesetting->bind_param('ii', $stmt_value, $this->user->id);
			$stmt_updatesetting->execute();
			$this->accessibility_enabled = $value;
		}

		function SetHeadshotsEnabled(bool $value) {
			$stmt_value = $value ? 1 : 0;

			include $_SERVER["DOCUMENT_ROOT"]."/core/connection.php";
			$stmt_updatesetting = $con->prepare("UPDATE `users_settings` SET `settings_headshots` = ? WHERE `settings_userid` = ?;");
			$stmt_updatesetting->bind_param('ii', $stmt_value, $this->user->id);
			$stmt_updatesetting->execute();
			$this->headshots_enabled = $value;
		}
		
		
	}
?>