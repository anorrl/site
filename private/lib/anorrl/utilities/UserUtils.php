<?php

	namespace anorrl\utilities;

	use anorrl\User;
	use anorrl\Database;

	/**
	 * Utilities for User stuff<br>
	 * Paging, Logging, Registering etc.
	 */
	class UserUtils {
		


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
				"SELECT `id` FROM `users` ORDER BY `joindate` DESC LIMIT :limit",
				[ ":limit" => $count ]
			)->fetchAll(\PDO::FETCH_OBJ);

			$users =  [];
			foreach($fetch_users as $obj_user) {
				$users[] = User::FromID($obj_user->id);
			}

			return $users;
		}

		public static function GetAllUsersPaged(int $page, int $count, string $query = ""): array|null {
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
				"SELECT `users`.`id` FROM `users`, `activity` WHERE `activity`.`userid` = `users`.`id` AND `name` LIKE :query ORDER BY `users`.`online` DESC, `activity`.`action_time` DESC LIMIT :page, :rows",
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
			$queryfiltered = "%$query%";

			$result_array = [];

			$getallusers = Database::singleton()->run(
				"SELECT `id` FROM `users` WHERE `name` LIKE :query",
				[
					":query" => $queryfiltered
				]
			)->fetchAll(\PDO::FETCH_OBJ);

			foreach($getallusers as $user) {
				$result_array[] = User::FromID($user->id);
			}
			
			return $result_array;
		}

		public static function GetUserCount(): int {
			return Database::singleton()->run("SELECT COUNT(`id`) FROM `users`")->fetch(\PDO::FETCH_ASSOC)['COUNT(`id`)'];
		}
	}

?>
