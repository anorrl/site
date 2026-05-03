<?php
	namespace anorrl;

	use anorrl\Place;
	use anorrl\Database;
	use anorrl\utilities\Arbiter;

	class GameServer {

		public string $id;
		public int $pid;
		public string $jobid;
		public Place|null $place = null;
		public int $player_count;
		public int $max_count;
		public int $port;
		public bool $teamcreate;

		private static function GetRandomString(): string {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randomString = '';
			
			for ($i = 0; $i < 11; $i++) {
				$index = rand(0, strlen($characters) - 1);
				$randomString .= $characters[$index];
			}

			return $randomString;
		}

		public static function Get(string $id, bool $teamcreate = false): self|null {
			$row = Database::singleton()->run(
				"SELECT * FROM `active_servers` WHERE `id` = :id AND `teamcreate` = :teamcreate",
				[
					":id" => $id,
					":teamcreate" => $teamcreate
				]
			)->fetch(\PDO::FETCH_OBJ);

			if($row)
				return new self($row);

			return null;
		}

		public static function GetFromJobID(string $jobid): self|null {
			$row = Database::singleton()->run(
				"SELECT * FROM `active_servers` WHERE `jobid` = :jobid",
				[
					":jobid" => $jobid,
				]
			)->fetch(\PDO::FETCH_OBJ);

			if($row)
				return new self($row);

			return null;
		}

		public static function Create(string $jobID, Place $place, int $port, int $pid, bool $teamcreate = false): self|null {
			$id = self::GetRandomString();

			Database::singleton()->run(
				"INSERT INTO `active_servers` (`id`, `jobid`, `placeid`, `maxcount`, `port`, `pid`, `teamcreate`) VALUES (:id, :jobid, :placeid, :maxcount, :port, :pid, :teamcreate)",
				[
					":id" => $id,
					":jobid" => $jobID,
					":placeid" => $place->id,
					":maxcount" => $place->server_size,
					":port" => $port,
					":pid" => $pid,
					":teamcreate" => $teamcreate
				]
			);
			
			return self::Get($id);
		}

		function __construct(Object $rowdata) {
			$this->id = $rowdata->id;
			$this->pid = $rowdata->pid;
			$this->jobid = $rowdata->jobid;
			$this->place = Place::FromID($rowdata->placeid);
			$this->player_count = $rowdata->playercount;
			$this->max_count = $rowdata->maxcount;
			$this->port = $rowdata->port;
			$this->teamcreate = boolval($rowdata->teamcreate);

			if(
				!$this->place ||
				($this->place && $this->place->creator->isBanned())
			)
				$this->destroy();
		}

		function active() {
			return Arbiter::singleton()->getGSMJob($this->jobid) != null;
		}

		function shutdown(string $reason = "This game has been shutdown by the creator") {
			if(!$this->active()) { $this->destroy(); return; }

			foreach($this->getSessions() as $session) {
				$session->kick($reason);
			}

			// maybe not?
			//$this->destroy();
		}

		function getSessions(): array {
			if(!$this->active()) { $this->destroy(); return []; }

			$rows = Database::singleton()->run(
				"SELECT `id` FROM `active_players` WHERE `serverid` = :id AND `status` = 1 AND `teamcreate` = :teamcreate;",
				[
					":id" => $this->id,
					":teamcreate" => $this->teamcreate
				]
			)->fetchAll(\PDO::FETCH_OBJ);

			$sessions = [];
			$playerids = [];

			foreach($rows as $row) {
				$session = GameSession::Get($row->id);

				if($session->player){
					if(!in_array($session->player->id, $playerids)) {
						$sessions[] = $session;
						$playerids[] = $session->player->id; // stupid fucking hack for stupid shit
					}
				}
			}

			return $sessions;
		}

		function isPlayerInServer(User|int $user): bool {
			return GameSession::GetPlayerInServer(is_int($user) ? $user : $user->id, $this->id) != null;
		}

		function addPlayer(User $user) {
			if(!$this->active()) { $this->destroy(); return; }

			if($this->isPlayerInServer($user)) return;

			Database::singleton()->run(
				"UPDATE `active_players` SET `status` = 1 WHERE `serverid` = :id AND `playerid` = :playerid",
				[
					":id" => $this->id,
					":playerid" => $user->id
				]
			);

			$this->place->visit($user);
		}

		function removePlayer(User|int $user, string|null $reason = null) {
			if(!$this->active()) { 
				$this->destroy();
				error_log("Server of jobid: {$this->jobid} tried to removePlayer while DEAD");
			}
			if(!$this->isPlayerInServer($user)) return;

			$userid = is_int($user) ? $user : $user->id;

			$session = GameSession::GetPlayerInServer($userid, $this->id);

			if($session)
				$session->kick($reason ?? '');
		}

		function renewLease(int $time = 60) {
			if(!$this->active()) { $this->destroy(); return; }

			Arbiter::singleton()->requestGS("renewlease", [
				"gameId" => $this->jobid,
				"expirationInSeconds" => $time
			]);
		}

		function destroy() {
			Arbiter::singleton()->requestGS("kill", ["pid" => $this->pid]);

			Database::singleton()->run(
				"DELETE FROM `active_servers` WHERE `id` = :id",
				[
					":id" => $this->id
				]
			);

			Database::singleton()->run(
				"DELETE FROM `active_players` WHERE `serverid` = :id",
				[
					":id" => $this->id
				]
			);
		}

	}
?>
