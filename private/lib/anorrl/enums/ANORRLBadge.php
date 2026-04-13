<?php

	namespace anorrl\enums;

	/**
	 *  Core Profile Badges.
	 */
	enum ANORRLBadge {
		case ADMINISTRATOR;
		case FORUM_MOD;
		case IMAGE_MOD;
		case HOMESTEAD;
		case BRICKSMITH;
		case FRIENDSHIP;
		case INVITER;
		case COMBAT_INITIATION;
		case WARRIOR;
		case BLOXXER;
		case VETERAN;
		case TESTER;

		public function ordinal(): int {
			return match($this) {
				ANORRLBadge::ADMINISTRATOR => 1,
				ANORRLBadge::FORUM_MOD => 2,
				ANORRLBadge::IMAGE_MOD => 3,
				ANORRLBadge::HOMESTEAD => 4,
				ANORRLBadge::BRICKSMITH => 5,
				ANORRLBadge::FRIENDSHIP => 6,
				ANORRLBadge::INVITER => 7,
				ANORRLBadge::COMBAT_INITIATION => 8,
				ANORRLBadge::WARRIOR => 9,
				ANORRLBadge::BLOXXER => 10,
				ANORRLBadge::VETERAN => 11,
				ANORRLBadge::TESTER => 12
			};
		}

		public static function index(int $badge): ANORRLBadge {
			return match($badge) {
				1 => ANORRLBadge::ADMINISTRATOR,
				2 => ANORRLBadge::FORUM_MOD,
				3 => ANORRLBadge::IMAGE_MOD,
				4 => ANORRLBadge::HOMESTEAD,
				5 => ANORRLBadge::BRICKSMITH,
				6 => ANORRLBadge::FRIENDSHIP,
				7 => ANORRLBadge::INVITER,
				8 => ANORRLBadge::COMBAT_INITIATION,
				9 => ANORRLBadge::WARRIOR,
				10 => ANORRLBadge::BLOXXER,
				11 => ANORRLBadge::VETERAN,
				12 => ANORRLBadge::TESTER,
			};
		}

		function name(): string {
			return match($this) {
				ANORRLBadge::ADMINISTRATOR => "Administrator",
				ANORRLBadge::FORUM_MOD => "Forum Moderator",
				ANORRLBadge::IMAGE_MOD => "Image Moderator",
				ANORRLBadge::HOMESTEAD => "Homestead",
				ANORRLBadge::BRICKSMITH => "Bricksmith",
				ANORRLBadge::FRIENDSHIP => "Friendship",
				ANORRLBadge::INVITER => "Inviter",
				ANORRLBadge::COMBAT_INITIATION => "Combat Initiation",
				ANORRLBadge::WARRIOR => "Warrior",
				ANORRLBadge::BLOXXER => "Bloxxer",
				ANORRLBadge::VETERAN => "Veteran",
				ANORRLBadge::TESTER => "Tester",
			};
		}

		function description(): string {
			return match($this) {
				ANORRLBadge::ADMINISTRATOR => "This badge identifies an account as belonging to a ANORRL administrator. Only official ANORRL administrators will possess this badge. If someone claims to be an admin, but does not have this badge, they are potentially trying to mislead you. If this happens, please report abuse and we will delete the imposter's account.",
				ANORRLBadge::FORUM_MOD => "Users with this badge are forum moderators. They have special powers on the ANORRL forum and are able to delete threads that violate the Community Guidelines. Users who are exemplary citizens on ANORRL over a long period of time may be invited to be moderators. This badge is granted by invitation only.<",
				ANORRLBadge::IMAGE_MOD => "Users with this badge are image moderators. Image moderators have special powers on ANORRL that allow them to approve or disapprove images that other users upload. Rejected images are immediately banished from the site. Users who are exemplary citizens on ANORRL over a long period of time may be invited to be moderators. This badge is granted by invitation only.",
				ANORRLBadge::HOMESTEAD => "The homestead badge is earned by having your personal place visited 100 times. Players who achieve this have demonstrated their ability to build cool things that other Vandals were interested enough in to check out. Get a jump-start on earning this reward by inviting people to come visit your place.",
				ANORRLBadge::BRICKSMITH => "The Bricksmith badge is earned by having a popular personal place. Once your place has been visited 1000 times, you will receive this award. Vandals with Bricksmith badges are accomplished builders who were able to create a place that people wanted to explore a thousand times. They no doubt know a thing or two about putting bricks together.",
				ANORRLBadge::FRIENDSHIP => "This badge is given to players who have embraced the ANORRL community and have made at least 20 friends. People who have this badge are good people to know and can probably help you out if you are having trouble.",
				ANORRLBadge::INVITER => "ANORRL is a vast uncharted realm, as large as the imagination. Individuals who invite others to join in the effort of mapping this mysterious region are honored in ANORRL society. Citizens who successfully recruit three or more fellow explorers via the Share ANORRL with a Friend mechanism are awarded with this badge.",
				ANORRLBadge::COMBAT_INITIATION => "This badge is given to any player who has proven his or her combat abilities by accumulating 10 victories in battle. Players who have this badge are not complete newbies and probably know how to handle their weapons.",
				ANORRLBadge::WARRIOR => "This badge is given to the warriors of ANORRL, who have time and time again overwhelmed their foes in battle. To earn this badge, you must rack up 100 knockouts. Anyone with this badge knows what to do in a fight!",
				ANORRLBadge::BLOXXER => "Anyone who has earned this badge is a very dangerous player indeed. Those Vandals who excel at combat can one day hope to achieve this honor, the Bloxxer Badge. It is given to the warrior who has bloxxed at least 250 enemies and who has tasted victory more times than he or she has suffered defeat. Salute!",
				ANORRLBadge::VETERAN => "This decoration is awarded to all citizens who have played ANORRL for at least a year. It recognizes stalwart community members who have stuck with us over countless releases and have helped shape ANORRL into the game that it is today. These medalists are the true steel, the core of the ANORRL history ... and its future.",
				ANORRLBadge::TESTER => "These fellas helped make ANORRL better during its gametest period! Thank you all!"
			};
		}
	}

?>