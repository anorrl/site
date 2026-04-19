<?php

	namespace anorrl\utilities;

	use anorrl\User;
	use anorrl\Database;

	/**
	 * Utilities for User stuff<br>
	 * Paging, Logging, Registering etc.
	 */
	class UserUtils {
		
		/**
		 * Creates a 255 long random strings from a character set to be used for the security of a user
		 * @return string Security key
		 */
		public static function GenerateSecurityKey(): string {
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
		public static function RegisterUser(string $username, string $password, string $confirm_password, string $accesskey): string|array {
			$errors = [];

			if(self::IsUsernameValid($username)) {
				if(!self::IsUsernameAvailable($username)) {
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
				return $errors;
			}

			$discordid = self::UseAccessKey($accesskey);
			$hashedpass = password_hash($password, PASSWORD_DEFAULT);
			$securitykey = self::GenerateSecurityKey();

			include $_SERVER['DOCUMENT_ROOT'].'/private/connection.php';

			$stmt_insertuser = $con->prepare("INSERT INTO `users`(`name`, `blurb`, `discord`, `password`, `security`) VALUES (?,'',?,?,?);");
			$stmt_insertuser->bind_param('ssss', $username, $discordid, $hashedpass, $securitykey);
			if($stmt_insertuser->execute()) {
				self::SetCookies($securitykey);
				return "success";
			}

			return ['unknown'=>"Something went wrong!"];
		}

		/**
		 * Verify details given and set cookies to allow logins.
		 * @param mixed $username
		 * @param mixed $password
		 * @return string|array
		 */
		public static function LoginUser(string $username, string $password): string|array {
			$errors = [];

			include $_SERVER['DOCUMENT_ROOT'].'/private/connection.php';
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
				return $errors;
			}

			// login user
			$stmt_grabuser = $con->prepare('SELECT * FROM `users` WHERE `name` = ?;');
			$stmt_grabuser->bind_param('s', $username);
			$stmt_grabuser->execute();
			$result_grabuser = $stmt_grabuser->get_result();

			if($result_grabuser->num_rows == 1) {
				$user_row = $result_grabuser->fetch_assoc();

				if(password_verify($pass_password, $user_row['password'])) {
					self::SetCookies($user_row['security']);
					if(session_status() != PHP_SESSION_ACTIVE) {
						session_start();
					}

					$_SESSION['SESSION_TOKEN_YAA'] = $user_row['security'];
					return  ['login' => $user_row['security']];
				}
			}

			return ['login' => "Incorrect details provided!"];
		}

		/**
		 * Summary of IsValidKey
		 * @param mixed $accesskey
		 * @return bool
		 */
		static function IsValidKey(string $accesskey): bool {
			include $_SERVER['DOCUMENT_ROOT'].'/private/connection.php';
			$stmt_checkkey = $con->prepare('SELECT `key` FROM `accesskeys` WHERE `key` = ?;');
			$stmt_checkkey->bind_param('s', $accesskey);
			$stmt_checkkey->execute();
			$result_checkkey = $stmt_checkkey->get_result();
			return $result_checkkey->num_rows != 0;
		}

		/**
		 * Uses the access key provided. Will return the discord user id it was created for.
		 * @param string $accesskey
		 * @return string|null
		 */
		static function UseAccessKey(string $accesskey): string|null {
			include $_SERVER['DOCUMENT_ROOT'].'/private/connection.php';
			$stmt_checkkey = $con->prepare('SELECT `discorduid` FROM `accesskeys` WHERE `key` = ?;');
			$stmt_checkkey->bind_param('s', $accesskey);
			$stmt_checkkey->execute();
			$result_checkkey = $stmt_checkkey->get_result();

			$discorduid = $result_checkkey->fetch_assoc()['discorduid'];

			$stmt_usekey = $con->prepare('DELETE FROM `accesskeys` WHERE `key` = ?;');
			$stmt_usekey->bind_param('s', $accesskey);
			$stmt_usekey->execute();

			return $discorduid;
		}

		/**
		 * Checks if given username is not being already used.
		 * @param string $username
		 * @return bool True if it's not being used
		 */
		public static function IsUsernameAvailable(string $username): bool {
			include $_SERVER['DOCUMENT_ROOT'].'/private/connection.php';
			$stmt_checkusername = $con->prepare('SELECT `name` FROM `users` WHERE `name` LIKE ?;');
			$stmt_checkusername->bind_param('s', $username);
			$stmt_checkusername->execute();
			$result_checkusername = $stmt_checkusername->get_result();
			return $result_checkusername->num_rows == 0;
		}

		public static function IsUsernameValid(string $username): bool {
			return preg_match("/^[a-zA-Z0-9]{3,20}$/", $username);
		}

		private static function StringContainsFromArray(array $array, string $string) {
			foreach($array as $item) {
				if(str_contains($string, $item)) {
					return true;
				}
			}

			return false;
		}
		
		public static function RetrieveUser(): User|null {
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
				self::RemoveCookies();
			}

			if($user) {
				$user->registerAction("Website");
			}
			
			return $user;
		}

		static function SetCookies(string $security): void {
			unset($_COOKIE['ANORRLSECURITY']);
			setcookie("ANORRLSECURITY", $security, time() + (460800* 30), "/", ".lambda.cam");
		}

		public static function RemoveCookies(): void {
			unset($_COOKIE['ANORRLSECURITY']);
			setcookie("ANORRLSECURITY", "", -1, "/", ".lambda.cam");
		}

		public static function GetRandomUsers(int $count): array {
			$fetch_users = Database::singleton()->run(
				"SELECT id FROM `users` ORDER BY RAND() LIMIT :limit",
				[ ":limit" => $count ]
			)->fetchAll(\PDO::FETCH_OBJ);

			$users =  [];
			foreach($fetch_users as $obj_user) {
				$users[] = User::FromID($obj_user->id);
			}

			return $users;
		}


		public static function GetLatestUsers(int $count): array {
			$fetch_users = Database::singleton()->run(
				"SELECT * FROM `users` ORDER BY `joindate` DESC LIMIT :limit",
				[ ":limit" => $count ]
			)->fetchAll(\PDO::FETCH_OBJ);

			$users =  [];
			foreach($fetch_users as $obj_user) {
				$users[] = User::FromID($obj_user->id);
			}

			return $users;
		}

		public static function GetAllUsersPaged(int $page, int $count, string $query = ""): array|null {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$queryfiltered = "%$query%";
			if($queryfiltered == "%%") {
				$queryfiltered = "%";
			}

			$db = Database::singleton();

			$fetch_users = $db->run("SELECT `id` FROM `users`")->fetchAll(\PDO::FETCH_OBJ);
			
			foreach($fetch_users as $obj_user) {
				User::FromID($obj_user->id)->isOnline();
			}
			
			$userids = $db->run(
				"SELECT `users`.`id` FROM `users`, `activity` WHERE `activity`.`userid` = `users`.`id` WHERE `name` LIKE :query ORDER BY `users`.`online` DESC, `activity`.`action_time` DESC LIMIT :page, :rows",
				[
					":query" => $queryfiltered,
					":page" => (($page-1)*$count),
					":rows" => $count
				]
			)->fetchAll(\PDO::FETCH_OBJ);

			$users = [];

			foreach($userids as $row) {
				$users[] = User::FromID($row->id);
			}

			return $users;
		}

		public static function GetAllUsers(string $query = ""): array|null {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$queryfiltered = "%$query%";
			$stmt_getallusers = $con->prepare("SELECT * FROM `users` WHERE `name` LIKE ?");
			$stmt_getallusers->bind_param('s', $queryfiltered);
			$stmt_getallusers->execute();
			$result = $stmt_getallusers->get_result();
			$result_array = [];

			if($result->num_rows != 0) {
				while($row = $result->fetch_assoc()) {
					$result_array[] = new User($row);
				}
				
			}
			return $result_array;
		}
	}

?>
