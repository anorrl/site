<?php
	namespace anorrl;

	use anorrl\GameServer;
	use anorrl\Database;
	use anorrl\User;
	use anorrl\utilities\Arbiter;

	class GameSession {
		public string $id;
		private string $serverid;
		public GameServer|null $server = null;
		private int $playerid;
		public User|null $player = null;
		public bool $in_game;
		public bool $teamcreate;
		public \DateTime $time_started;

		private static function GetRandomString(): string {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randomString = '';
			
			for ($i = 0; $i < 25; $i++) {
				$index = rand(0, strlen($characters) - 1);
				$randomString .= $characters[$index];
			}

			return $randomString;
		}

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
				"SELECT * FROM `active_players` WHERE `playerid` = :playerid AND `serverid` = :serverid AND `status` = 1",
				[
					":playerid" => $id,
					":serverid" => $serverID
				]
			)->fetch(\PDO::FETCH_OBJ);

			if($row)
				return new self($row);

			return null;
		}

		public static function Create(GameServer $server, User $user, bool $teamcreate = false): self|null {
			$id = self::GetRandomString();
			Database::singleton()->run(
				"INSERT INTO `active_players`(`id`, `serverid`, `playerid`, `status`, `teamcreate`) VALUES (:id,:serverid,:playerid,0,:teamcreate)",
				[
					":id" => $id,
					":serverid" => $server->id,
					":playerid" => $user->id,
					":teamcreate" => $teamcreate
				]
			);

			return self::Get($id, $teamcreate);
		}

		function __construct(Object $rowdata) {
			$this->id = $rowdata->id;
			
			$this->playerid = $rowdata->playerid;
			$this->serverid = $rowdata->serverid;
			
			$this->player = User::FromID($this->playerid);
			$this->server = GameServer::Get($this->serverid);

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
			if($this->server && $this->player) {
				Arbiter::singleton()->requestGS(
					"kick", 
					[
						"PlayerId" => $this->player->id, 
						"JobId" => $this->server->jobid,
						"Reason" => $reason
					]
				);
			}

			Database::singleton()->run(
				"DELETE FROM `active_players` WHERE `serverid` = :id AND `playerid` = :playerid",
				[
					":id" => $this->serverid,
					":playerid" => $this->playerid
				]
			);
		}

		function getTicket() {
			if(!$this->server->active())
				return null;
			if(!$this->player || $this->player->isBanned())
				return null;

			// 9/16/2026 10:28:07 AM

			$timestamp = new \DateTime()->format("m/d/Y H:i:s A"); // 09/16/2026 10:28:07 AM   CLOSE ENOUGH!
			$session_ticket = "{$this->player->id}\n{$this->server->jobid}\n{$timestamp}";
			$session_ticket_signature = Script::SignNonScript($session_ticket, null);

			$ticket = "{$this->player->id}\n{$this->player->name}\n{$this->player->getCharacterFetchURL()}\n{$this->server->jobid}\n{$timestamp}";
			$ticket_signature = Script::SignNonScript($ticket, null);

			return "{$timestamp};{$ticket_signature};{$session_ticket_signature}";
		}

	}
?>
