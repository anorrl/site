<?php
	enum AssetStatus {
		case PENDING;
		case ACCEPTED;
		case REJECTED;

		public function ordinal(): int {
			return match($this) {
				AssetStatus::PENDING => 0,
				AssetStatus::ACCEPTED => 1,
				AssetStatus::REJECTED => 2,
			};
		}

		public static function index(int $badge): AssetStatus {
			return match($badge) {
				0 => AssetStatus::PENDING,
				1 => AssetStatus::ACCEPTED,
				2 => AssetStatus::REJECTED,
			};
		}
	}
?>