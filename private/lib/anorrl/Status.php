<?php
	namespace anorrl;

	use anorrl\User;

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

		public static function GenerateID() {
			include $_SERVER["DOCUMENT_ROOT"]."/core/connection.php";
			$id = self::GetRandomString(); //id
			$stmt = $con->prepare('SELECT * FROM `statuses` WHERE `status_id` = ?');
			$stmt->bind_param('s', $id);
			$stmt->execute();
			$stmt->store_result();
			
			$instances = $stmt->num_rows;
			
			if($instances != 0) {
				self::GenerateID();
			} else {
				return $id;
			}
		}

		public static function Send(int $userid, string $contents) {
			$user = User::FromID($userid);

			if($user != null && !$user->IsBanned()) {
				$latest_status = $user->GetLatestStatus();
				if($latest_status != null) {
					// check if user hasn't posted one in 30s

					//$offset = 3600; // windows blehh
					$offset = -3600; //prod

					$difference = (time()-($latest_status->time_posted->getTimestamp()+$offset));

					//die(strval($difference));

					$calculated_time = 30 - $difference; 

					if($difference < 30) {
						return ["error"=> true, "reason" => "You need to wait $calculated_time seconds before posting again."];
					}
				}

				$blockedchars = array('𒐫', '‮', '﷽', '𒈙', '⸻ ', '꧅');
				$status_id = self::GenerateID();
				$status_content = str_replace($blockedchars, '', trim($contents));

				if(strlen($status_content) < 4) {
					return ["error"=> true, "reason" => "Status was too short! (4 characters minimum)"];
				}
				if(strlen($status_content) > 64) {
					return ["error"=> true, "reason" => "Status was too long! (64 characters maximum)"];
				}

				include $_SERVER["DOCUMENT_ROOT"]."/core/connection.php";
				$stmt = $con->prepare('INSERT INTO `statuses`(`status_id`, `status_poster`, `status_content`) VALUES (?, ?, ?)');
				$stmt -> bind_param('sis',  $status_id, $user->id, $status_content);
				$stmt -> execute();

				return ["error" => false];
			} else {
				return ["error"=> true, "reason" => "User is not logged in."];
			}
		}

		public static function GetLatestFeedsPaged(int $pagenum, int $count): array {

			include $_SERVER["DOCUMENT_ROOT"]."/core/connection.php";
			$stmt_getallusers = $con->prepare("SELECT * FROM `statuses` ORDER BY `status_posted` DESC LIMIT ?, ?");
			$page = (($pagenum-1)*$count);
			$stmt_getallusers->bind_param('ii', $page, $count);
			$stmt_getallusers->execute();
			$result = $stmt_getallusers->get_result();
			$result_array = [];

			if($result->num_rows != 0) {
				while($row = $result->fetch_assoc()) {
					array_push($result_array, new Status($row));
				}
				return $result_array;
			}
			return [];
		}

		public static function GetLatestFeedsCount(): int {
			include $_SERVER["DOCUMENT_ROOT"]."/core/connection.php";
			$stmt_getallusers = $con->prepare("SELECT * FROM `statuses`");
			$stmt_getallusers->execute();
			$result = $stmt_getallusers->get_result();
			return $result->num_rows;
		}

		function __construct($rowdata) {
			$this->id = ($rowdata['status_id']);
			$this->poster = User::FromID(intval($rowdata['status_poster']));
			$this->content = str_replace("<", "&lt;", str_replace(">", "&gt;", $rowdata['status_content']));
			$this->time_posted = \DateTime::createFromFormat("Y-m-d H:i:s", $rowdata['status_posted']);
		}

	}
?>