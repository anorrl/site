<?php
	namespace anorrl;

	use anorrl\User;
	use anorrl\Asset;
	use anorrl\utilities\SlurUtils;
	use anorrl\utilities\UtilUtils;
	use anorrl\utilities\UserUtils;

	class Comment  {
		public string $id;
		public User $poster;
		public User|Asset $parent;
		public string $contents;
		public \DateTime $postdate;

		function __construct($rowdata) {
			$this->id = strval($rowdata['comment_id']);
			$this->poster = User::FromID($rowdata['comment_poster']);
			
			if(str_starts_with($rowdata['comment_parent'], 'a!')) {
				$this->parent = Asset::FromID(substr($rowdata['comment_parent'], 2));
			} else {
				$this->parent = User::FromID(substr($rowdata['comment_parent'], 2));
			}

			$this->contents = str_replace("<", "&lt;", str_replace(">", "&gt;", $rowdata['comment_content']));
			$this->postdate = \DateTime::createFromFormat("Y-m-d H:i:s", $rowdata['comment_postdate']);
		
		}

		function PrintComment() {
			$contents = str_replace(PHP_EOL, "<br>", $this->contents);
			$user_id = $this->poster->id;
			$user_name = $this->poster->name;
			$profileurl = $this->poster->setprofilepicture ? "profile" : "headshot";
			$formatted_datetime = $this->postdate->format("d/m/Y");

			$timeago = UtilUtils::GetTimeAgo($this->postdate);

			echo <<<EOT
			<div class="Comment">
				<div id="CommenterAvatar">
					<a href="/users/$user_id/profile">
						<img src="/thumbs/$profileurl?id=$user_id">
					</a>
				</div>
				<div id="CommentPartArea">
					<div id="CommentInfoArea">
						<a href="/users/$user_id/profile">$user_name</a>&nbsp;<span>Posted on $formatted_datetime ($timeago)</span>
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
			include $_SERVER["DOCUMENT_ROOT"]."/core/connection.php";
			
			$id = self::GetRandomString();
			$stmt = $con->prepare('SELECT * FROM `comments` WHERE `comment_id` = ?');
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

		static function GetLatestCommentFromUser(User $user): Comment|null {
			include $_SERVER["DOCUMENT_ROOT"]."/core/connection.php";
			$stmt_getuser = $con->prepare("SELECT * FROM `comments` WHERE `comment_poster` = ? ORDER BY `comment_postdate` DESC");
			$stmt_getuser->bind_param('i', $user->id);
			$stmt_getuser->execute();
			$result = $stmt_getuser->get_result();

			if($result->num_rows == 0) {
				return null;
			}

			return new Comment($result->fetch_assoc());
		}

		public static function Post(Asset|User|null $parent, string $contents): array {
			$user = UserUtils::RetrieveUser();

			if($user == null) {
				return [
					"error" => true,
					"reason" => "User is not authorised to perform this action!"
				];
			}

			if($parent == null) {
				return [
					"error" => true,
					"reason" => "Destination is null!"
				];
			}

			$parent_id = "a!".$parent->id;
			if($parent instanceof User) {
				$parent_id = "u!".$parent->id;
			}

			$blockedchars = array('𒐫', '‮', '﷽', '𒈙', '⸻ ', '꧅');
			$waittime = 5;

			$comment_id = self::GenerateID();
			$comment = trim(str_replace($blockedchars, '', $contents));
			$lastpost = self::GetLatestCommentFromUser($user);
			
			if($lastpost != null) {
				$difference_in_seconds = time() - $lastpost->postdate->getTimestamp();
			} else {
				$difference_in_seconds = 6;
			}
			if($difference_in_seconds > $waittime) {
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
					include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";
					$stmt = $con->prepare('INSERT INTO `comments`(`comment_id`, `comment_parent`, `comment_poster`, `comment_content`) VALUES (?, ?, ?, ?)');
					$stmt->bind_param('ssss',  $comment_id, $parent_id, $user->id, $comment);
					
					$stmt->execute();
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

			include $_SERVER["DOCUMENT_ROOT"]."/core/connection.php";
			$stmt_getuser = $con->prepare("SELECT * FROM `comments` WHERE `comment_parent` = ? ORDER BY `comment_postdate` DESC;");
			$stmt_getuser->bind_param('s', $parent_id);
			$stmt_getuser->execute();
			$result = $stmt_getuser->get_result();

			$comments = [];

			while($row = $result->fetch_assoc()) {
				array_push($comments, new Comment($row));
			}
			return $comments;
		}

	}
?>