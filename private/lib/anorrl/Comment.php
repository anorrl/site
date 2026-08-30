<?php
	namespace anorrl;

	use anorrl\User;
	use anorrl\Asset;
	use anorrl\utilities\SlurUtils;
	use anorrl\utilities\Utilities;
	use anorrl\Session;

	class Comment  {
		public string $id;
		public User $poster;
		public User|Asset $parent;
		public string $contents;
		public \DateTime $postdate;

		public static function FromID(string $id): Comment|null {
			
			$row = Database::singleton()->run(
				"SELECT * FROM `comments` WHERE `id` = :id LIMIT 1",
				[ ":id" => $id ]
			)->fetchObject();

			return $row ? new self($row) : null;
		}

		function __construct(object $rowdata) {
			$this->id = $rowdata->id;
			$this->poster = User::FromID($rowdata->poster);
			
			if(str_starts_with($rowdata->parent, 'a!')) {
				$this->parent = Asset::FromID(substr($rowdata->parent, 2));
			} else {
				$this->parent = User::FromID(substr($rowdata->parent, 2));
			}

			$this->contents = str_replace("<", "&lt;", str_replace(">", "&gt;", $rowdata->content));
			$this->postdate = \DateTime::createFromFormat("Y-m-d H:i:s", $rowdata->postdate);
		
		}

		static function GetRandomString(): string {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randomString = '';
			
			for ($i = 0; $i < 11; $i++) {
				$index = rand(0, strlen($characters) - 1);
				$randomString .= $characters[$index];
			}

			return $randomString;
		}

		static function GenerateID() {
			$id = self::GetRandomString();
			$instances = Database::singleton()->run(
				"SELECT * FROM `comments` WHERE `id` = :id",
				[":id" => $id]
			)->rowCount();

			return $instances == 0 ? $id : self::GenerateID();
		}

		static function GetLatestCommentFromUser(User $user): Comment|null {
			$row = Database::singleton()->run(
				"SELECT * FROM `comments` WHERE `poster` = :poster ORDER BY `postdate` DESC",
				[":poster" => $user->id]
			)->fetch(\PDO::FETCH_OBJ);

			return $row ? new Comment($row) : null;
		}

		public static function Post(Asset|User|null $parent, string $contents): array {
			$user = Session::retrieveUser();

			if(!$user)
				return [
					"success" => false,
					"reason" => "User is not authorised to perform this action!"
				];

			if(!$parent)
				return [
					"success" => false,
					"reason" => "Destination is null!"
				];

			$parent_id = "a!".$parent->id;
			if($parent instanceof User) {
				$parent_id = "u!".$parent->id;
			}

			$waittime = 5;
			$lastpost = self::GetLatestCommentFromUser($user);
			
			if($lastpost != null) {
				$difference_in_seconds = Utilities::GetSecondsElapsedFrom($lastpost->postdate);
			} else {
				$difference_in_seconds = 6;
			}
			if($difference_in_seconds > $waittime) {
				$comment_id = self::GenerateID();
				$comment = Utilities::StripUnicode($contents);

				$error_check = false;
				if(strlen($comment) < 4) {
					$error_check = true;
					$error_msg = "Comment was too short! (4 characters minimum)";
				}
				if(strlen($comment) > 256) { 
					$error_check = true;
					$error_msg = "Comment was too long! (256 characters maximum)";
				}

				$comment = SlurUtils::ProcessText($comment);

				if(!$error_check) {
					Database::singleton()->run(
						"INSERT INTO `comments`(`id`, `parent`, `poster`, `content`) VALUES (:id, :pid, :poster, :contents)",
						[
							":id" => $comment_id,
							":pid" => $parent_id,
							":poster" => $user->id,
							":contents" => $comment
						]
					);

					return [
						"success" => true,
						"id"    => $comment_id
					];
				} else {
					return [
						"success"  => false,
						"reason" => $error_msg
					];
				}
			
			} else {
				$sec_calc = $waittime-$difference_in_seconds;
				return ['success'=>false, "reason" => "Wait $sec_calc seconds before replying again!"];
			}

			
		}

		public static function GetCommentsOn(User|Asset $parent, int $page = -1, int $limit = 10) {
			$parent_id = "a!".$parent->id;
			if($parent instanceof User) {
				$parent_id = "u!".$parent->id;
			}

			$paged = $page > 0;
			$sql = "SELECT `id` FROM `comments` WHERE `parent` = :parent ORDER BY `postdate` DESC";
			$params = [ ":parent" => $parent_id ];

			if($paged) {
				$sql = "$sql LIMIT :page, :count";
				$params[":page"] = (($page-1)*$limit);
				$params[":count"] = $limit;
			}

			$rows = Database::singleton()->run($sql, $params)->fetchAll(\PDO::FETCH_OBJ);

			$comments = [];

			foreach($rows as $row) {
				$comments[] = Comment::FromID($row->id);
			}
			return $comments;
		}

		public static function GetCommentCountOn(User|Asset $parent) {
			$parent_id = "a!".$parent->id;
			if($parent instanceof User) {
				$parent_id = "u!".$parent->id;
			}

			$sql = "SELECT COUNT(`id`) FROM `comments` WHERE `parent` = :parent";
			$params = [":parent" => $parent_id];

			$row = Database::singleton()->run($sql, $params)->fetch(\PDO::FETCH_ASSOC);

			return $row ? $row['COUNT(`id`)'] : -1;
		}

		function getJSON() {
			return [
				"id" => $this->id,
				"poster" => [
					"id" => $this->poster->id,
					"url" => $this->poster->getURL(),
					"name" => $this->poster->name,
					"img" => $this->poster->getThumbsUrl()
				],
				"contents" => str_replace(PHP_EOL, "<br>", $this->contents),
				"date" => Utilities::GetTimeAgo($this->postdate)
			];
		}
	}
?>