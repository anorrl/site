<?php
	namespace anorrl\enums;

	enum CharacterMeshType {
		case HEAD;
		case TORSO;
		case RIGHTARM;
		case LEFTARM;
		case LEFTLEG;
		case RIGHTLEG;

		public static function index(int $ordinal): CharacterMeshType|null {
			return match($ordinal) {
				0 => CharacterMeshType::HEAD,
				1 => CharacterMeshType::TORSO,
				2 => CharacterMeshType::LEFTARM,
				3 => CharacterMeshType::RIGHTARM,
				4 => CharacterMeshType::LEFTLEG,
				5 => CharacterMeshType::RIGHTLEG,
				default => null
			};
		}

		public function ordinal(): int {
			return match($this) {
				CharacterMeshType::HEAD     => 0,
				CharacterMeshType::TORSO 	=> 1,
				CharacterMeshType::LEFTARM 	=> 2,
				CharacterMeshType::RIGHTARM => 3,
				CharacterMeshType::LEFTLEG 	=> 4,
				CharacterMeshType::RIGHTLEG => 5,
			};
		}

		public function assettype(): AssetType {
			return match($this) {
				CharacterMeshType::HEAD 	    => AssetType::HEAD,
				CharacterMeshType::TORSO 		=> AssetType::TORSO,
				CharacterMeshType::RIGHTARM 	=> AssetType::RIGHTARM,
				CharacterMeshType::LEFTARM 		=> AssetType::LEFTARM,
				CharacterMeshType::LEFTLEG 		=> AssetType::LEFTLEG,
				CharacterMeshType::RIGHTLEG 	=> AssetType::RIGHTLEG,
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

		public static function all(): array {
			return [
				CharacterMeshType::HEAD,
				CharacterMeshType::TORSO,
				CharacterMeshType::LEFTARM,
				CharacterMeshType::RIGHTARM,
				CharacterMeshType::LEFTLEG,
				CharacterMeshType::RIGHTLEG,
			];
		}
	}
?>