<?php

	namespace anorrl\enums;

	enum ChatOption {
		case CLASSIC;
		case BUBBLE;
		case BOTH;

		public static function index(int $ordinal): ChatOption {
			return match($ordinal) {
				0  => ChatOption::CLASSIC,
				1  => ChatOption::BUBBLE,
				2  => ChatOption::BOTH,
			};
		}

		public function ordinal(): int {
			return match($this) {
				ChatOption::CLASSIC		 => 0,
				ChatOption::BUBBLE		 => 1,
				ChatOption::BOTH		 => 2,
			};
		}

		public function label(): string {
			return match($this) {
				ChatOption::CLASSIC			 => "Classic",
				ChatOption::BUBBLE			 => "Bubble",
				ChatOption::BOTH			 => "Both",
			};
		}

		public function internallabel(): string {
			return match($this) {
				ChatOption::CLASSIC			 => "Classic",
				ChatOption::BUBBLE			 => "Bubble",
				ChatOption::BOTH			 => "ClassicAndBubble",
			};
		}
	}
?>