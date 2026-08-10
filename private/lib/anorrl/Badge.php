<?php

	namespace anorrl;

	use anorrl\Asset;
	use anorrl\Database;
	use anorrl\User;

	class Badge extends Asset {

		public bool $secret;
		
		public static function FromID(?int $id): Badge|null {
			if(!is_int($id))
				return null;
			
			$row = Database::singleton()->run(
				"SELECT * FROM `assets` WHERE `id` = :id AND `type` = 21 LIMIT 1",
				[ ":id" => $id ]
			)->fetchObject();

			return $row ? new self($row) : null;
		}

		function __construct(int|object $rowdata) {
			parent::__construct($rowdata);
			$this->secret = !$this->public;
		}

		function awardTo(User $user) {
			if($user->isBanned() || $user->owns($this)) {
				return false;
			}

			return !$this->purchase($user)["error"];
		}

		function toggleSecret() {
			$toggled_secret = !$this->public;

			Database::singleton()->run(
				"UPDATE `assets` SET `public` = :public WHERE `id` = :id",
				[
					":id" => $this->id,
					":public" => $toggled_secret
				]
			);
		}

		// Stubs \\

		function getRarity() {
			return 0.0;
		}

		function getWonYesterdayTimes() {
			return 0;
		}

		function getWonEverTimes() {
			return 0;
		}

	}

?>
