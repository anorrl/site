<?php
	namespace anorrl;

	use anorrl\Database;
	use anorrl\Universe;

	class Datastore {

		private Universe $universe;

		function __construct(Universe $universe) {
			$this->universe = $universe;
		}

		//
		function keyExists(string $key, string $target, string $scope = "global", string $type = "standard") {
			$row = Database::singleton()->run(
				"SELECT `id` FROM `datastores` WHERE `universe`= :universe AND `scope` = :scope AND `key` = :key AND `target` = :target AND `type` = :type",
				[
					"universe" => $this->universe->id,
					"scope" => $scope,
					"key" => $key,
					"target" => $target,
					"type" => $type,
				]
			)->rowCount();

			return $row != null;
		}

		function get(string $key, string $target = "", string $scope = "global", string $type = "standard") {
			$result = Database::singleton()->run(
				"SELECT * FROM `datastores` WHERE `universe`=:universe AND `scope`=:scope AND `key`=:key AND `target`=:target AND `type` = :type",
				[
					"key" => $key,
					"universe" => $this->universe->id,
					"scope" => $scope,
					"target" => $target,
					"type" => $type,
				]
			)->fetchAll(\PDO::FETCH_ASSOC);

			$values = [];
			foreach($result as $data){
				array_push($values,array("Value"=>$data["value"],"Scope"=>$data["scope"],"Key"=>$data["key"],"Target"=>$data["target"]));
			}

			return $values;
		}

		function getordered(string $key, string $target = "", string $scope = "global", string $type = "sorted", int $page_size, bool $ascending) {
			$current_page = 0;
			$continue = true;

			while($continue) {
				$result = Database::singleton()->run(
					"SELECT `target`, `value` FROM `datastores` WHERE `universe` = :universe AND `scope`=:scope AND `key`=:key AND `target`=:target AND `type` = :type LIMIT :page, :pagesize ORDER BY `value`".($ascending ? "ASC" :"DESC"),
					[
						"key" => $key,
						"universe" => $this->universe->id,
						"scope" => $scope,
						"target" => $target,
						"type" => $type,
						"page" => $current_page * $page_size
					]
				);

				if($result->rowCount() < $page_size)
					break;

				$rows = $result->fetchAll(\PDO::FETCH_ASSOC);

				$current_page += 1;
			}
			

			$values = [];
			foreach($result as $data){
				array_push($values,array("Value"=>$data["value"],"Scope"=>$data["scope"],"Key"=>$data["key"],"Target"=>$data["target"]));
			}

			return $values;
		}

		function set(string $key, string $value, string $target = "", string $scope = "global", string $type): bool {
			$result = Database::singleton()->run(
				$this->keyExists($key, $target, $scope, $type) ? 
					"UPDATE `datastores` SET `value` = :value WHERE `universe` = :universe AND `scope` = :scope AND `key` = :key AND `target` = :target AND `type` = :type" : 
					"INSERT INTO `datastores`(`key`, `universe`, `scope`, `target`, `value`, `type`) VALUES (:key, :universe, :scope, :target, :value, :type)",
				[
					"key" => $key,
					"universe" => $this->universe->id,
					"scope" => $scope,
					"target" => $target,
					"value" => $value,
					"type" => $type,
				]
			);

			return $result->errorCode() == SQL_ALLOK;
		}

		function increment(string $key, int $value, string $target = "", string $scope = "global"): bool {
			$result = Database::singleton()->run(
				$this->keyExists($key, $target, $scope) ? 
					"UPDATE `datastores` SET `value` = value+:value WHERE `universe` = :universe AND `scope` = :scope AND `key` = :key AND `target` = :target" : 
					"INSERT INTO `datastores`(`key`, `universe`, `scope`, `target`, `value`) VALUES (:key, :universe, :scope, :target, :value)",
				[
					"key" => $key,
					"universe" => $this->universe->id,
					"scope" => $scope,
					"target" => $target,
					"value" => $value,
				]
			);

			return $result->errorCode() == SQL_ALLOK;
		}

	}
?>