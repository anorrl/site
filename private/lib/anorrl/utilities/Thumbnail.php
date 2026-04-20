<?php
	namespace anorrl\utilities;

	use anorrl\Asset;
	use anorrl\User;

	/* araki, what the fuck am i doing */
	/* paranoia */
	class Thumbnail {
		// https://stackoverflow.com/a/14300703
		private static function IsValidHash($hash) {
			return preg_match('/^[a-f0-9]{32}$/', $hash);
		}

		public static function Exists($hash, bool $user): bool {
			if(!self::IsValidHash($hash))
				return false;

			return file_exists(self::GetPath($hash, $user ? "renders" : "assets"));
		}

		public static function GetPath(string $hash, string $service = "renders"): string {
			return $_SERVER['DOCUMENT_ROOT']."/../{$service}/3d/{$hash}.json";
		}

		public static function Generate3D(User|Asset $item) {
			if($item instanceof User)
				$hash = $item->currentoutfitmd5;
			else
				$hash = $item->getMD5HashCurrent();
			
			$result_json = self::GetRenderFile($hash, $item instanceof User);

			if(!$result_json)
				return null;

			return [
				"aabb" => $result_json["AABB"],
				"camera" => $result_json["camera"],
				"hash" => $hash,
			];
		}

		public static function Get3DObj(string $hash, bool $user = true) {
			return self::GetFileInRender($hash, "scene.obj", $user);
		}

		public static function Get3DMtl(string $hash, bool $user = true) {
			$mtl = self::GetFileInRender($hash, "scene.mtl", $user);

			if($mtl)
				//return preg_replace("/Player([0-9]+)Tex\.png/i", $hash, $mtl);
				return $mtl;
			else
				return null;
		}

		public static function Get3DTex(string $hash, string $file, bool $user = true) {
			return self::GetFileInRender($hash, $file, $user);
		}

		private static function GetRenderFile(string $hash, bool $user = true): mixed {
			if(!self::Exists($hash, $user))
				return null;

			$json = json_decode((file_get_contents(self::GetPath($hash, $user ? "renders" : "assets"))), true, 1024);

			//if(!$json)
				//unlink(self::GetPath($hash, $user ? "renders" : "assets")); // scary

			return $json;
		}

		private static function GetFileInRender(string $hash, string $file, bool $user): mixed {
			$result_json = self::GetRenderFile($hash, $user);

			if(!$result_json)
				return null;

			return base64_decode($result_json["files"][$file]['content']);
		}
	}
?>