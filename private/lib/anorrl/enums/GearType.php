<?php

	namespace anorrl\enums;

	enum GearType {
		case MELEE_WEAPONS;
		case RANGED_WEAPONS;
		case EXPLOSIVES;
		case POWER_UPS;
		case NAVIGATION_ENHANCERS;
		case MUSICAL_INSTRUMENTS;
		case SOCIAL_ITEMS;
		case BUILDING_TOOLS;
		case PERSONAL_TRANSPORT;

		public static function index(int $ordinal): GearType {
			return match($ordinal) {
				0  => GearType::MELEE_WEAPONS,
				1  => GearType::RANGED_WEAPONS,
				2  => GearType::EXPLOSIVES,
				3  => GearType::POWER_UPS,
				4  => GearType::NAVIGATION_ENHANCERS,
				5  => GearType::MUSICAL_INSTRUMENTS,
				6  => GearType::SOCIAL_ITEMS,
				7  => GearType::BUILDING_TOOLS,
				8  => GearType::PERSONAL_TRANSPORT,
			};
		}

		public function ordinal(): int {
			return match($this) {
				GearType::MELEE_WEAPONS	 		=> 0,
				GearType::RANGED_WEAPONS 		=> 1,
				GearType::EXPLOSIVES			=> 2,
				GearType::POWER_UPS 			=> 3,
				GearType::NAVIGATION_ENHANCERS 	=> 4,
				GearType::MUSICAL_INSTRUMENTS 	=> 5,
				GearType::SOCIAL_ITEMS 			=> 6,
				GearType::BUILDING_TOOLS 		=> 7,
				GearType::PERSONAL_TRANSPORT 	=> 8,
			};
		}

		public function label(): string {
			return match($this) {
				GearType::MELEE_WEAPONS	 		=> "Melee",
				GearType::RANGED_WEAPONS 		=> "Ranged",
				GearType::EXPLOSIVES			=> "Explosives",
				GearType::POWER_UPS 			=> "Power Ups",
				GearType::NAVIGATION_ENHANCERS 	=> "Navigational",
				GearType::MUSICAL_INSTRUMENTS 	=> "Musical",
				GearType::SOCIAL_ITEMS 			=> "Social",
				GearType::BUILDING_TOOLS 		=> "Building",
				GearType::PERSONAL_TRANSPORT 	=> "Transport",
			};
		}
	}
?>