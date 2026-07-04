<?php
	namespace anorrl;

	use anorrl\User;
	use anorrl\Asset;
	use anorrl\utilities\SlurUtils;
	use anorrl\utilities\UtilUtils;
	use anorrl\Session;

	class Comment  {
		public string $id;
		public User $poster;
		public User|Asset $parent;
		public string $contents;
		public \DateTime $postdate;

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

		function PrintComment() {
			$contents = str_replace(PHP_EOL, "<br>", $this->contents);
			$user_id = $this->poster->id;
			$formatted_datetime = $this->postdate->format("d/m/Y");

			$timeago = UtilUtils::GetTimeAgo($this->postdate);

			echo <<<EOT
			<div class="Comment">
				<div id="CommenterAvatar">
					<a href="/users/$user_id/profile">
						<img src="{$this->poster->getThumbsUrl()}">
					</a>
				</div>
				<div id="CommentPartArea">
					<div id="CommentInfoArea">
						<a href="/users/$user_id/profile">{$this->poster->name}</a>&nbsp;<span>Posted on $formatted_datetime ($timeago)</span>
					</div>
					<code>$contents</code>
				</div>
				<div style="float: none; clear: both;"></div>
			</div>
			EOT;
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
					"error" => true,
					"reason" => "User is not authorised to perform this action!"
				];

			if(!$parent)
				return [
					"error" => true,
					"reason" => "Destination is null!"
				];

			$parent_id = "a!".$parent->id;
			if($parent instanceof User) {
				$parent_id = "u!".$parent->id;
			}

			$waittime = 5;
			$lastpost = self::GetLatestCommentFromUser($user);
			
			if($lastpost != null) {
				$difference_in_seconds = UtilUtils::GetSecondsElapsedFrom($lastpost->postdate);
			} else {
				$difference_in_seconds = 6;
			}
			if($difference_in_seconds > $waittime) {
				$comment_id = self::GenerateID();
				$comment = UtilUtils::StripUnicode($contents);

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
						"error" => false,
						"id"    => $comment_id
					];
				} else {
					return [
						"error"  => true,
						"reason" => $error_msg
					];
				}
			
			} else {
				$sec_calc = $waittime-$difference_in_seconds;
				return ['error'=>true, "reason" => "Wait $sec_calc seconds before replying again!"];
			}

			
		}

		public static function GetCommentsOn(User|Asset $parent) {
			$parent_id = "a!".$parent->id;
			if($parent instanceof User) {
				$parent_id = "u!".$parent->id;
			}

			$rows = Database::singleton()->run(
				"SELECT * FROM `comments` WHERE `parent` = :parent ORDER BY `postdate` DESC;",
				[ ":parent" => $parent_id ]
			)->fetchAll(\PDO::FETCH_OBJ);

			$comments = [];

			foreach($rows as $row) {
				$comments[] = new Comment($row);
			}
			return $comments;
		}

	}
?>