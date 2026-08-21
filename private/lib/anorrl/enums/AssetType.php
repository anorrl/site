<?php

	namespace anorrl\enums;

	use anorrl\enums\CharacterMeshType;

	enum AssetType {
		case IMAGE;
		case TSHIRT;
		case AUDIO;
		case MESH;
		case LUA;
		case HAT;
		case PLACE;
		case MODEL;
		case SHIRT;
		case PANTS;
		case DECAL;
		case HEAD;
		case FACE;
		case GEAR;
		case BADGE;
		case ANIMATION;
		case TORSO;
		case RIGHTARM;
		case LEFTARM;
		case LEFTLEG;
		case RIGHTLEG;
		case PACKAGE;
		case GAMEPASS;
		case PLUGIN;
		case MESHPART;
		case HAIRACCESSORY;
		case FACEACCESSORY;
		case NECKACCESSORY;
		case SHOULDERACCESSORY;
		case FRONTACCESSORY;
		case BACKACCESSORY;
		case WAISTACCESSORY;
		case EMOTE;
		/**
		 * This is only for stuff api lol
		 */
		case BODYPARTS;
		case GAME;

		public static function index(int $ordinal): AssetType {
			return match($ordinal) {
				1 => AssetType::IMAGE,
				2 => AssetType::TSHIRT,
				3 => AssetType::AUDIO,
				4 => AssetType::MESH,
				5 => AssetType::LUA,
				8 => AssetType::HAT,
				9 => AssetType::PLACE,
				10 => AssetType::MODEL,
				11 => AssetType::SHIRT,
				12 => AssetType::PANTS,
				13 => AssetType::DECAL,
				17 => AssetType::HEAD,
				18 => AssetType::FACE,
				19 => AssetType::GEAR,
				21 => AssetType::BADGE,
				24 => AssetType::ANIMATION,
				27 => AssetType::TORSO,
				28 => AssetType::RIGHTARM,
				29 => AssetType::LEFTARM,
				30 => AssetType::LEFTLEG,
				31 => AssetType::RIGHTLEG,
				32 => AssetType::PACKAGE,
				34 => AssetType::GAMEPASS,
				38 => AssetType::PLUGIN,
				40 => AssetType::MESHPART,
				41 => AssetType::HAIRACCESSORY,
				42 => AssetType::FACEACCESSORY,
				43 => AssetType::NECKACCESSORY,
				44 => AssetType::SHOULDERACCESSORY,
				45 => AssetType::FRONTACCESSORY,
				46 => AssetType::BACKACCESSORY,
				47 => AssetType::WAISTACCESSORY,
				61 => AssetType::EMOTE,
				98 => AssetType::GAME,
				99 => AssetType::BODYPARTS
			};
		}

		public function ordinal(): int {
			return match($this) {
				AssetType::IMAGE 	=> 1,
				AssetType::TSHIRT 	=> 2,
				AssetType::AUDIO	=> 3,
				AssetType::MESH 	=> 4,
				AssetType::LUA 		=> 5,
				AssetType::HAT 		=> 8,
				AssetType::PLACE	=> 9,
				AssetType::MODEL 	=> 10,
				AssetType::SHIRT 	=> 11,
				AssetType::PANTS 	=> 12,
				AssetType::DECAL 	=> 13,
				AssetType::HEAD 	=> 17,
				AssetType::FACE 	=> 18,
				AssetType::GEAR 	=> 19,
				AssetType::BADGE 	=> 21,
				AssetType::ANIMATION 	=> 24,
				AssetType::TORSO 		=> 27,
				AssetType::RIGHTARM 	=> 28,
				AssetType::LEFTARM 		=> 29,
				AssetType::LEFTLEG 		=> 30,
				AssetType::RIGHTLEG 	=> 31,
				AssetType::PACKAGE      => 32,
				AssetType::GAMEPASS     => 34,
				AssetType::PLUGIN		=> 38,
				AssetType::MESHPART		=> 40,
				AssetType::HAIRACCESSORY 	 => 41,
				AssetType::FACEACCESSORY	 => 42,
				AssetType::NECKACCESSORY 	 => 43,
				AssetType::SHOULDERACCESSORY => 44,
				AssetType::FRONTACCESSORY 	 => 45,
				AssetType::BACKACCESSORY 	 => 46,
				AssetType::WAISTACCESSORY 	 => 47,
				AssetType::EMOTE		=> 61,
				AssetType::GAME    		=> 98,
				AssetType::BODYPARTS    => 99,
			};
		}

		public function wearable(): bool {
			return match($this) {
				AssetType::TSHIRT 	=> true,
				AssetType::HAT 		=> true,
				AssetType::SHIRT 	=> true,
				AssetType::PANTS 	=> true,
				AssetType::HEAD 	=> true,
				AssetType::FACE 	=> true,
				AssetType::GEAR 	=> true,
				AssetType::TORSO 		=> true,
				AssetType::RIGHTARM 	=> true,
				AssetType::LEFTARM 		=> true,
				AssetType::LEFTLEG 		=> true,
				AssetType::RIGHTLEG 	=> true,
				AssetType::EMOTE 		=> true,
				AssetType::HAIRACCESSORY 	 => true,
				AssetType::FACEACCESSORY	 => true,
				AssetType::NECKACCESSORY 	 => true,
				AssetType::SHOULDERACCESSORY => true,
				AssetType::FRONTACCESSORY 	 => true,
				AssetType::BACKACCESSORY 	 => true,
				AssetType::WAISTACCESSORY 	 => true,
				default => false
			};
		}

		public function wearone(): bool {
			return match($this) {
				AssetType::TSHIRT 	=> true,
				AssetType::SHIRT 	=> true,
				AssetType::PANTS 	=> true,
				AssetType::HEAD 	=> true,
				AssetType::FACE 	=> true,
				AssetType::TORSO 		=> true,
				AssetType::RIGHTARM 	=> true,
				AssetType::LEFTARM 		=> true,
				AssetType::LEFTLEG 		=> true,
				AssetType::RIGHTLEG 	=> true,
				default => false
			};
		}

		public function label(): string {
			return match($this) {
				AssetType::IMAGE 	=> "Image",
				AssetType::TSHIRT 	=> "T-Shirt",
				AssetType::AUDIO	=> "Audio",
				AssetType::MESH 	=> "Mesh",
				AssetType::LUA 		=> "Script",
				AssetType::HAT 		=> "Hat",
				AssetType::PLACE	=> "Place",
				AssetType::MODEL 	=> "Model",
				AssetType::SHIRT 	=> "Shirt",
				AssetType::PANTS 	=> "Pants",
				AssetType::DECAL 	=> "Decal",
				AssetType::HEAD 	=> "Head",
				AssetType::FACE 	=> "Face",
				AssetType::GEAR 	=> "Gear",
				AssetType::BADGE 	=> "Badge",
				AssetType::ANIMATION 	=> "Animation",
				AssetType::TORSO 		=> "Torso",
				AssetType::RIGHTARM 	=> "Right Arm",
				AssetType::LEFTARM 		=> "Left Arm",
				AssetType::LEFTLEG 		=> "Left Leg",
				AssetType::RIGHTLEG 	=> "Right Leg",
				AssetType::PACKAGE      => "Package",
				AssetType::GAMEPASS     => "Gamepass",
				AssetType::PLUGIN    	=> "Plugin",
				AssetType::MESHPART    	=> "Mesh Part",
				AssetType::HAIRACCESSORY 	 => "Hair Accessory",
				AssetType::FACEACCESSORY	 => "Face Accessory",
				AssetType::NECKACCESSORY 	 => "Neck Accessory",
				AssetType::SHOULDERACCESSORY => "Shoulder Accessory",
				AssetType::FRONTACCESSORY 	 => "Front Accessory",
				AssetType::BACKACCESSORY 	 => "Back Accessory",
				AssetType::WAISTACCESSORY 	 => "Waist Accessory",
				AssetType::EMOTE			 => "Emote",
			};
		}


		public function tocharactermesh(): CharacterMeshType {
			return match($this) {
				AssetType::HEAD 	    => CharacterMeshType::HEAD,
				AssetType::TORSO 		=> CharacterMeshType::TORSO,
				AssetType::RIGHTARM 	=> CharacterMeshType::RIGHTARM,
				AssetType::LEFTARM 		=> CharacterMeshType::LEFTARM,
				AssetType::LEFTLEG 		=> CharacterMeshType::LEFTLEG,
				AssetType::RIGHTLEG 	=> CharacterMeshType::RIGHTLEG
			};
		}
	}
?>