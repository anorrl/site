<?php
	namespace anorrl\enums;

	enum CharacterMeshType {
		case HEAD;
		case TORSO;
		case RIGHTARM;
		case LEFTARM;
		case LEFTLEG;
		case RIGHTLEG;

		public static function index(int $ordinal): CharacterMeshType {
			return match($ordinal) {
				0 => CharacterMeshType::HEAD,
				1 => CharacterMeshType::TORSO,
				2 => CharacterMeshType::LEFTARM,
				3 => CharacterMeshType::RIGHTARM,
				4 => CharacterMeshType::LEFTLEG,
				5 => CharacterMeshType::RIGHTLEG,
			};
		}

		public function ordinal(): int {
			return match($this) {
				CharacterMeshType::HEAD 	    => 0,
				CharacterMeshType::TORSO 		=> 1,
				CharacterMeshType::LEFTARM 		=> 2,
				CharacterMeshType::RIGHTARM 	=> 3,
				CharacterMeshType::LEFTLEG 		=> 4,			
				CharacterMeshType::RIGHTLEG 	=> 5,
			};
		}

		public function assettype(): AssetType {
			return match($this) {
				CharacterMeshType::HEAD 	    => AssetType::HEAD,
				CharacterMeshType::TORSO 		=> AssetType::HEAD,
				CharacterMeshType::RIGHTARM 	=> AssetType::HEAD,
				CharacterMeshType::LEFTARM 		=> AssetType::HEAD,
				CharacterMeshType::LEFTLEG 		=> AssetType::HEAD,
				CharacterMeshType::RIGHTLEG 	=> AssetType::HEAD,
				default => false
			};
		}

		public function label(): string {
			return match($this) {
				CharacterMeshType::HEAD 	    => "Head",
				CharacterMeshType::TORSO 		=> "Torso",
				CharacterMeshType::RIGHTARM 	=> "Right Arm",
				CharacterMeshType::LEFTARM 		=> "Left Arm",
				CharacterMeshType::LEFTLEG 		=> "Left Leg",
				CharacterMeshType::RIGHTLEG 	=> "Right Leg",
			};
		}
	}
?>