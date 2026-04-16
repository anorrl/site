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

		function shutdown(string $reason = "This game has been shutdown by the creator") {
			// make new api endpoint on anrsal or something
		}

		function getPlayers(): array {
			return [];
		}

		function addPlayer(User|int $user) {

		}

		function removePlayer(User|int $user) {
			
		}

		function renewLease(int $time = 60) {
			Arbiter::singleton()->request("renewlease", [
				"jobId" => $this->jobid,
				"seconds" => $time
			]);
		}

		function destroy() {
			Arbiter::singleton()->request("gameserver/kill", ["pid" => $this->pid]);
		}

	}
?>