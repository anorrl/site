<?php

	namespace anorrl;

	class Session {
		public User $user;
		public UserSettings $settings;

		function __construct(User|int $user) {
			if(is_int($user)) {
				$this->user = User::FromID($user);
			}
			else {
				$this->user = $user;
			}

			$this->settings = UserSettings::Get($this->user);
		}

				/**
		 * Creates a 255 long random strings from a character set to be used for the security of a user
		 * @return string Security key
		 */
		public static function generateSecurityKey(): string {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_?-/=;#!';
			$randomString = '';
			
			for ($i = 0; $i < 255; $i++) {
				$index = rand(0, strlen($characters) - 1);
				$randomString .= $characters[$index];
			}
	
			return $randomString;
		}

		/**
		 * Creates a user and does checks to ensure that all data given is correct.
		 * 
		 * If some data is invalid, it will return an array of the errors.
		 * @param string $username
		 * @param string $password
		 * @param string $confirm_password
		 * @param string $accesskey
		 * @return array|string
		 */
		public static function register(string $username, string $password, string $confirm_password, string $accesskey): string|array {
			$errors = [];

			if(self::isUsernameValid($username)) {
				if(!self::isUsernameAvailable($username)) {
					$errors["username"] = "Username has already been taken!";
				}
			} else {
				$errors["username"] = "a-z A-Z 0-9 and 3-20 characters only!";
			}

			if(strlen($password) >= 7) {
				if(strcmp($password, $confirm_password) !== 0) {
					$errors["password"] = "Passwords do not match!";
				}
			} else {
				$errors["password"] = "Password must be minimum 7 characters!";
			}

			if(!self::IsValidKey($accesskey)) {
				$errors["accesskey"] = "Invalid access key.";
			}

			if(sizeof($errors) != 0) {
				return ["success" => false, "errors" => $errors];
			}

			$discordid = self::useAccessKey($accesskey);
			$hashedpass = password_hash($password, PASSWORD_ARGON2ID);
			$securitykey = self::generateSecurityKey();
			
			if(Database::singleton()->run(
				"INSERT INTO `users`(`name`, `blurb`, `discord`, `password`, `security`) VALUES (:name,'',:discord,:password,:security);",
				[
					":name" => $username,
					":discord" => $discordid,
					":password" => $hashedpass,
					":security" => $securitykey
				]
			)->errorInfo()[0] == SQL_ALLOK) {
				self::setCookies($securitykey);
				User::FromNamePercise($username)->updateOutfitHash();
				return ["success" => true];
			}

			return ["success" => false, "errors" => ['unknown'=>"Something went wrong!"]];
		}

		/**
		 * Verify details given and set cookies to allow logins.
		 * @param mixed $username
		 * @param mixed $password
		 * @return string|array
		 */
		public static function login(string $username, string $password): string|array {
			$errors = [];

			$pass_username = trim($username);
			$pass_password = trim($password);

			$pass_username_length = strlen($pass_username);
			$pass_password_length = strlen($pass_password);

			if($pass_username_length == 0) {
				$errors["username"] = "Username field cannot be empty!";
			} 
			else if(!preg_match("/^[a-zA-Z0-9]{3,20}$/", $pass_username)) {
				$errors["username"] = "a-z A-Z 0-9 and 3-20 characters only!";
			}

			if($pass_password_length == 0) {
				$errors["password"] = "Password field cannot be empty!";
			}

			if(sizeof($errors) != 0) {
				return ["success" => false, "errors" => $errors];
			}

			$user = User::FromNamePercise($username);

			if($user) {
				if(password_verify($pass_password, $user->password)) {
					self::setCookies($user->security_key);
					if(session_status() != PHP_SESSION_ACTIVE) {
						session_start();
					}

					$_SESSION['SESSION_TOKEN_YAA'] = $user->security_key;
					return  ["success" => true];
				}
			}

			return ["success" => false, 'errors' => ['login' => "Incorrect details provided!"]];
		}

		/**
		 * Summary of IsValidKey
		 * @param mixed $accesskey
		 * @return bool
		 */
		static function isValidKey(string $accesskey): bool {
			return Database::singleton()->run(
				'SELECT `key` FROM `accesskeys` WHERE `key` = :key',
				[":key" => $accesskey]
			)->rowCount() != 0;
		}

		/**
		 * Uses the access key provided. Will return the discord user id it was created for.
		 * @param string $accesskey
		 * @return string|null
		 */
		static function useAccessKey(string $accesskey): string|null {
			$db = Database::singleton();
			// yup
			$discorduid =  $db->run("SELECT `discorduid` FROM `accesskeys` WHERE `key` = :key", [":key" => $accesskey])->fetchObject()->discorduid;
			/* use key */  $db->run("DELETE FROM `accesskeys` WHERE `key` = :key", [":key" => $accesskey]);

			return $discorduid;
		}

		/**
		 * Checks if given username is not being already used.
		 * @param string $username
		 * @return bool True if it's not being used
		 */
		public static function isUsernameAvailable(string $username): bool {
			return User::FromName($username) == null;
		}

		public static function isUsernameValid(string $username): bool {
			return preg_match("/^[a-zA-Z0-9]{3,20}$/", $username);
		}
		
		public static function retrieveUser(): User|null {
			if(session_status() != PHP_SESSION_ACTIVE) {
				session_start();
			}

			$user = null;

			if(isset($_COOKIE['ANORRLSECURITY'])) {
				$user = User::FromSecurityKey(urldecode($_COOKIE['ANORRLSECURITY']));	
			} else if(isset($_SESSION['SESSION_TOKEN_YAA'])) {
				$user = User::FromSecurityKey($_SESSION['SESSION_TOKEN_YAA']);	
			}

			if((isset($_COOKIE['ANORRLSECURITY']) || isset($_SESSION['SESSION_TOKEN_YAA'])) && $user == null) {
				self::removeCookies();
			}

			if($user) {
				$user->registerAction("Website");
			}
			
			return $user;
		}

		static function setCookies(string $security): void {
			unset($_COOKIE['ANORRLSECURITY']);
			setcookie("ANORRLSECURITY", $security, time() + (460800* 30), "/", \CONFIG->domain);
		}

		public static function removeCookies(): void {
			unset($_COOKIE['ANORRLSECURITY']);
			setcookie("ANORRLSECURITY", "", -1, "/", \CONFIG->domain);
		}
	}

?>