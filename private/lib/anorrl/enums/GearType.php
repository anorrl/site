<?php

	namespace anorrl\enums;

	enum GearType {
		case ALL;
		case MELEE_WEAPONS;
		case RANGED_WEAPONS;
		case EXPLOSIVES;
		case POWER_UPS;
		case NAVIGATION_ENHANCERS;
		case MUSICAL_INSTRUMENTS;
		case SOCIAL_ITEMS;
		case BUILDING_TOOLS;
		case PERSONAL_TRANSPORT;
		case NONE;

		public static function index(int $ordinal): GearType {
			return match($ordinal) {
				0  => GearType::ALL,
				1  => GearType::MELEE_WEAPONS,
				2  => GearType::RANGED_WEAPONS,
				3  => GearType::EXPLOSIVES,
				4  => GearType::POWER_UPS,
				5  => GearType::NAVIGATION_ENHANCERS,
				6  => GearType::MUSICAL_INSTRUMENTS,
				7  => GearType::SOCIAL_ITEMS,
				8  => GearType::BUILDING_TOOLS,
				9  => GearType::PERSONAL_TRANSPORT,
				10 => GearType::NONE,
			};
		}

		public function ordinal(): int {
			return match($this) {
				GearType::ALL			 		=> 0,
				GearType::MELEE_WEAPONS	 		=> 1,
				GearType::RANGED_WEAPONS 		=> 2,
				GearType::EXPLOSIVES			=> 3,
				GearType::POWER_UPS 			=> 4,
				GearType::NAVIGATION_ENHANCERS 	=> 5,
				GearType::MUSICAL_INSTRUMENTS 	=> 6,
				GearType::SOCIAL_ITEMS 			=> 7,
				GearType::BUILDING_TOOLS 		=> 8,
				GearType::PERSONAL_TRANSPORT 	=> 9,
				GearType::NONE 	=> 10,
			};
		}

		public function label(): string {
			return match($this) {
				GearType::ALL	 				=> "All",
				GearType::MELEE_WEAPONS	 		=> "Melee",
				GearType::RANGED_WEAPONS 		=> "Ranged",
				GearType::EXPLOSIVES			=> "Explosives",
				GearType::POWER_UPS 			=> "Power Ups",
				GearType::NAVIGATION_ENHANCERS 	=> "Navigational",
				GearType::MUSICAL_INSTRUMENTS 	=> "Musical",
				GearType::SOCIAL_ITEMS 			=> "Social",
				GearType::BUILDING_TOOLS 		=> "Building",
				GearType::PERSONAL_TRANSPORT 	=> "Transport",
				GearType::NONE 					=> "None",
			};
		}

		public static function values(): array {
			return [
				GearType::ALL,
				GearType::MELEE_WEAPONS,
				GearType::RANGED_WEAPONS,
				GearType::EXPLOSIVES,
				GearType::POWER_UPS,
				GearType::NAVIGATION_ENHANCERS,
				GearType::MUSICAL_INSTRUMENTS,
				GearType::SOCIAL_ITEMS,
				GearType::BUILDING_TOOLS,
				GearType::PERSONAL_TRANSPORT,
				GearType::NONE,
			];
		}
	}
?>