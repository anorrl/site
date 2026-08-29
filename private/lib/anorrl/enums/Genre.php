<?php

	namespace anorrl\enums;

	enum Genre {
		case ALL;
		case TOWN_AND_CITY;
		case FANTASY;
		case SCI_FI;
		case NINJA;
		case SCARY;
		case PIRATE;
		case ADVENTURE;
		case SPORTS;
		case FUNNY;
		case WILD_WEST;
		case WAR;
		case SKATE_PARK;
		case TUTORIAL;

		public static function index(int $ordinal): Genre {
			return match($ordinal) {
				0  => Genre::ALL,
				1  => Genre::TOWN_AND_CITY,
				2  => Genre::FANTASY,
				3  => Genre::SCI_FI,
				4  => Genre::NINJA,
				5  => Genre::SCARY,
				6  => Genre::PIRATE,
				7  => Genre::ADVENTURE,
				8  => Genre::SPORTS,
				9  => Genre::FUNNY,
				10 => Genre::WILD_WEST,
				11 => Genre::WAR,
				12 => Genre::SKATE_PARK,
				13 => Genre::TUTORIAL,
				default => Genre::ALL
			};
		}

		public function ordinal(): int {
			return match($this) {
				Genre::ALL			 => 0,
				Genre::TOWN_AND_CITY => 1,
				Genre::FANTASY		 => 2,
				Genre::SCI_FI		 => 3,
				Genre::NINJA		 => 4,
				Genre::SCARY		 => 5,
				Genre::PIRATE		 => 6,
				Genre::ADVENTURE	 => 7,
				Genre::SPORTS		 => 8,
				Genre::FUNNY		 => 9,
				Genre::WILD_WEST	 => 10,
				Genre::WAR			 => 11,
				Genre::SKATE_PARK	 => 12,
				Genre::TUTORIAL		 => 13,
			};
		}

		public function label(): string {
			return match($this) {
				Genre::ALL			 => "All",
				Genre::TOWN_AND_CITY => "Town & City",
				Genre::FANTASY		 => "Fantasy",
				Genre::SCI_FI		 => "Sci-Fi",
				Genre::NINJA		 => "Ninja",
				Genre::SCARY		 => "Horror",
				Genre::PIRATE		 => "Pirate",
				Genre::ADVENTURE	 => "Adventure",
				Genre::SPORTS		 => "Sports",
				Genre::FUNNY		 => "Funny",
				Genre::WILD_WEST	 => "Wild West",
				Genre::WAR			 => "War",
				Genre::SKATE_PARK	 => "Skate Park",
				Genre::TUTORIAL		 => "Tutorial",
			};
		}

		public static function values() {
			return [
				Genre::ALL,
				Genre::TOWN_AND_CITY ,
				Genre::FANTASY,
				Genre::SCI_FI,
				Genre::NINJA,
				Genre::SCARY,
				Genre::PIRATE,
				Genre::ADVENTURE,
				Genre::SPORTS,
				Genre::FUNNY,
				Genre::WILD_WEST,
				Genre::WAR,
				Genre::SKATE_PARK,
				Genre::TUTORIAL,
			];
		}
	}
?>