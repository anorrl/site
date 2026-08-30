<?php
	namespace anorrl;

	use anorrl\Database;
	use anorrl\User;
	use anorrl\utilities\Utilities;

	class Status {

		public string $id;
		public User $poster;
		public string $content;
		public \DateTime $time_posted;

		private static function GetRandomString(): string {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randomString = '';
			
			for ($i = 0; $i < 20; $i++) {
				$index = rand(0, strlen($characters) - 1);
				$randomString .= $characters[$index];
			}
	
			return $randomString;
		}

		private static function GenerateID() {
			$id = self::GetRandomString(); //id
			
			return self::FromID($id) ? self::GenerateID() : $id;
		}

		public static function FromID(string $id): self|null {
			$row = Database::singleton()->run(
				"SELECT * FROM `statuses` WHERE `id` = :id",
				[ ":id" => $id]
			)->fetchObject();

			return $row ? new self($row) : null;
		}

		public static function Send(int $userid, string $contents) {
			$user = User::FromID($userid);

			if($user != null && !$user->isBanned()) {
				$latest_status = $user->getLatestStatus();
				if($latest_status != null) {
					// check if user hasn't posted one in 30s

					$difference = Utilities::GetSecondsElapsedFrom($latest_status->time_posted);

					//die(strval($difference));

					$calculated_time = 30 - $difference; 

					if($difference < 30) {
						return ["success"=> false, "reason" => "You need to wait $calculated_time seconds before posting again."];
					}
				}

				$status_id = self::GenerateID();
				$status_content = Utilities::StripUnicode($contents);

				if(strlen($status_content) < 4) {
					return ["success"=> false, "reason" => "Status was too short! (4 characters minimum)"];
				}
				if(strlen($status_content) > 256) {
					return ["success"=> false, "reason" => "Status was too long! (256 characters maximum)"];
				}

				Database::singleton()->run(
					"INSERT INTO `statuses`(`id`, `poster`, `content`) VALUES (:id, :poster, :content)",
					[
						":id" => $status_id,
						":poster" => $user->id,
						":content" => $status_content
					]
				);
				
				return ["success" => true];
			} else {
				return ["success"=> false, "reason" => "User is not logged in."];
			}
		}

		public static function GetLatestFeedsPaged(int $page, int $count): array {

			$rows = Database::singleton()->run(
				"SELECT `id` FROM `statuses` ORDER BY `posted` DESC LIMIT :page, :count",
				[
					":page" => (($page-1)*$count),
					":count" => $count
				]
			)->fetchAll(\PDO::FETCH_OBJ);

			$result_array = [];

			foreach($rows as $row) {
				$result_array[] = self::FromID($row->id);
			}

			return $result_array;
		}

		public static function GetLatestFeedsCount(): int {
			return Database::singleton()->run("SELECT `id` FROM `statuses`")->rowCount();
		}

		function __construct(Object $rowdata) {
			$this->id = $rowdata->id;
			$this->poster = User::FromID($rowdata->poster);
			$this->content = str_replace("<", "&lt;", str_replace(">", "&gt;", $rowdata->content));
			$this->time_posted = \DateTime::createFromFormat("Y-m-d H:i:s", $rowdata->posted);
		}

	}
?>