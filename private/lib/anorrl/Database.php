<?php
	namespace anorrl;
	
	define("SQL_ALLOK", "00000");

	/**
	 * Lifted from fubuki by parakeet
	 */
	#[\AllowDynamicProperties]
	class Database {
		private static self|null $instance = null;
		public \PDO $pdo;

		public static function singleton(): self {
			if (!self::$instance) {
				self::$instance = new Database();
			}

			return self::$instance;
		}

		function __construct() {
			$this->pdo = new \PDO(
				"mysql:host=" . \CONFIG->database->hostname . ";
				dbname=" . \CONFIG->database->name . ";
				charset=utf8mb4", 
				\CONFIG->database->username, 
				\CONFIG->database->password
			);

			$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			$this->pdo->setAttribute(\PDO::ATTR_PERSISTENT, true);
		}

		private function getPDOType(mixed $data): int {
			if(is_int($data) || is_bool($data))
				return \PDO::PARAM_INT;

			return \PDO::PARAM_STR;
		}

		private function evaluateValue(mixed $data): mixed {
			if(is_bool($data))
				return $data ? 1 : 0;

			if(is_a($data, "anorrl\\enums\\ChatOption"))
				return $data->ordinal();

			if(is_a($data, "anorrl\\enums\\Genre"))
				return $data->ordinal();

			if(is_a($data, "anorrl\\enums\\GearType"))
				return $data->ordinal();

			return $data;
		}

		function run($sql, $args = null): \PDOStatement {
			if (!$args) return $this->pdo->query($sql);
			
			$stmt = $this->pdo->prepare($sql);

			foreach ($args as $param => $value) {
				$evaluated_value = $this->evaluateValue($value);
				$stmt->bindValue(
					is_int($param) ? $param + 1 : (!str_starts_with($param, ":") ? ":$param" : $param),
					$evaluated_value, 
					$this->getPDOType($evaluated_value)
				);
			}

			$stmt->execute();

			return $stmt;
		}

		function lastInsertId(): string {
			return $this->pdo->lastInsertId();
		}
	}
?>