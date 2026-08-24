<?php
	namespace anorrl\enums;

	enum TransactionType {
		case CONES;
		case LIGHTS;
		case FREE;

		public static function index(int $ordinal): TransactionType {
			return match($ordinal) {
				1 => TransactionType::CONES,
				2 => TransactionType::FREE,
			};
		}

		function ordinal(): int {
			return match($this) {
				TransactionType::CONES => 1,
				TransactionType::FREE => 2,
			};
		}

		function label(): string {
			return match($this) {
				TransactionType::CONES => "cones",
				TransactionType::FREE => "free"
			};
		}
	}
?>