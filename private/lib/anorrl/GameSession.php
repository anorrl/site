<?php
	namespace anorrl;

	use anorrl\GameServer;
	use anorrl\Database;
	use anorrl\User;

	class GameSession {
		public string $id;
		public GameServer|null $server = null;
		public User|null $player = null;
		public bool $in_game;
		public bool $teamcreate;
		public \DateTime $time_started;

		public static function Get(string $id, bool $teamcreate = false): self|null {
			$row = Database::singleton()->run(
				"SELECT * FROM `active_players` WHERE `id` = :id AND `teamcreate` = :teamcreate",
				[
					":id" => $id,
					":teamcreate" => $teamcreate
				]
			)->fetch(\PDO::FETCH_OBJ);

			if($row)
				return new self($row);

			return null;
		}

		public static function GetPlayerInServer(int $id, string $serverID): self|null {
			$row = Database::singleton()->run(
				"SELECT * FROM `active_players` WHERE `playerid` = :playerid AND `serverid` = :serverid",
				[
					":playerid" => $id,
					":serverid" => $serverID
				]
			)->fetch(\PDO::FETCH_OBJ);

			if($row)
				return new self($row);

			return null;
		}

		function __construct(Object $rowdata) {
			$this->id = $rowdata->id;
			$this->player = User::FromID(intval($rowdata->playerid));
			$this->server = GameServer::Get($rowdata->serverid);
			$this->in_game = boolval($rowdata->status);
			$this->teamcreate = boolval($rowdata->teamcreate);

			if(
				!$this->player ||
				!$this->server ||
				($this->player && $this->player->isBanned())
			) {
				$this->kick();
			}
		}

		function kick(string $reason = "You have been kicked from the session because the owner hates you") {

		}

	}
?>