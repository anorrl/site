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
		function keyExists(string $key, string $target, string $scope = "global") {
			$row = Database::singleton()->run(
				"SELECT `id` FROM `datastores` WHERE `universe`= :universe AND `scope` = :scope AND `key` = :key AND `target` = :target",
				[
					"universe" => $this->universe->id,
					"scope" => $scope,
					"key" => $key,
					"target" => $target
				]
			)->rowCount();

			return $row != null;
		}

		function get(string $key, string $target = "", string $scope = "global") {
			$result = Database::singleton()->run(
				"SELECT * FROM `datastores` WHERE `universeId`=:pid AND `scope`=:scope AND `type`=:type AND `key`=:key AND `target`=:target",
				[
					"key" => $key,
					"universe" => $this->universe->id,
					"scope" => $scope,
					"target" => $target,
				]
			)->fetchAll(\PDO::FETCH_ASSOC);

			$values = [];
			foreach($result as &$data){
				array_push($values,array("Value"=>$data["value"],"Scope"=>$data["scope"],"Key"=>$data["key"],"Target"=>$data["target"]));
			}

			return $values;
		}
		function set(string $key, string $value, string $target = "", string $scope = "global"): bool {
			$result = Database::singleton()->run(
				$this->keyExists($key, $target, $scope) ? 
					"UPDATE `datastores` SET `value` = :value WHERE `universe` = :universe AND `scope` = :scope AND `key` = :key AND `target` = :target" : 
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
		function increment() {}

	}
?>