<?php
	namespace anorrl\utilities;

	use anorrl\enums\AssetType;

	class AssetTypeUtils {

		public static function IsRBX(AssetType $type) {
			return match($type) {
				AssetType::GEAR => true,
				AssetType::HAT => true,
				AssetType::MODEL => true,
				AssetType::PLACE => true,
				AssetType::ANIMATION => true,
				default => false,
			};
		}

		public static function IsRenderable(AssetType $type) {
			return match($type) {
				/** Accessories */
				AssetType::GEAR => true,
				AssetType::HAT => true,
				AssetType::SHIRT => true,
				AssetType::PANTS => true,
				/** Development */
				AssetType::MODEL => true,
				AssetType::PLACE => true,
				AssetType::MESH => true,
				/** Avatar */
				AssetType::HEAD => true,
				AssetType::TORSO => true,
				AssetType::LEFTARM => true,
				AssetType::RIGHTARM => true,
				AssetType::LEFTLEG => true,
				AssetType::RIGHTLEG => true,
				default => false,
			};
		}

		public static function IsHidden(AssetType $type) {
			return match($type) {
				AssetType::IMAGE => true,
				AssetType::LUA => true,
				AssetType::BADGE => true,
				default => false,
			};
		}

		public static function IsImage(AssetType $type) {
			return match($type) {
				AssetType::IMAGE => true,
				AssetType::DECAL => true,
				AssetType::TSHIRT => true,
				AssetType::SHIRT => true,
				AssetType::PANTS => true,
				AssetType::FACE => true,
				default => false,
			};
		}

		public static function IsSellable(AssetType $type) {
			return match($type) {
				AssetType::PLACE => false,
				AssetType::IMAGE => false,
				AssetType::LUA => false,
				default => true,
			};
		}

		public static function IsUpdateable(AssetType $type) {
			return match($type) {
				AssetType::PLACE => true,
				AssetType::MESH => true,
				AssetType::MODEL => true,
				AssetType::LUA => true,
				AssetType::HAT => true,
				AssetType::GEAR => true,
				AssetType::ANIMATION => true,
				default => false,
			};
		}

		private static function GetTemplate(string $filename): string {
			$file = file_get_contents($_SERVER['DOCUMENT_ROOT']."/private/templates/assets/$filename.rbxm");
			return self::Replace("domain", \CONFIG->domain, $file);
		}

		public static function Replace(string $var, mixed $val, string $data) {
			return str_replace("{".$var."}", $val, $data);
		}

		public static function GenerateDecalRBXM(int $id, bool $face = false): string {
			$data = self::GetTemplate("decal");
			if($face) {
				$data = str_replace("{name}", "face", $data);
			}

			return self::Replace("assetid", $id, $data);
		}

		public static function GenerateFaceRBXM(int $id): string {
			return self::GenerateDecalRBXM($id, true);
		}

		public static function GenerateTShirtRBXM(int $id): string {
			return self::Replace("assetid", $id, self::GetTemplate("tshirt"));
		}
		
		public static function GenerateShirtRBXM(int $id): string {
			return self::Replace("assetid", $id, self::GetTemplate("shirt"));
		}

		public static function GeneratePantsRBXM(int $id): string {
			return self::Replace("assetid", $id, self::GetTemplate("pants"));
		}

		public static function GenerateCharacterMeshRBXM(int $id, AssetType $type): string {
			$meshrbxm = self::Replace("assetid", $id, self::GetTemplate("charactermesh"));
			return self::Replace("bodypart", $type->tocharactermesh()->ordinal(), $meshrbxm);
		}
	}
?>