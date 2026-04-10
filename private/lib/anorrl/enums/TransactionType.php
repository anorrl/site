<?php
	namespace anorrl\enums;

	enum TransactionType {
		case CONES;
		case LIGHTS;
		case FREE;

		public static function index(int $ordinal): TransactionType {
			return match($ordinal) {
				1 => TransactionType::CONES,
				2 => TransactionType::LIGHTS,
				3 => TransactionType::FREE,
			};
		}

		function ordinal(): int {
			return match($this) {
				TransactionType::CONES => 1,
				TransactionType::LIGHTS => 2,
				TransactionType::FREE => 3,
			};
		}
	}
?>