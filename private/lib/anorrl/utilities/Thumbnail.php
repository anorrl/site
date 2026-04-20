<?php
	namespace anorrl\utilities;

	use anorrl\User;

	/* araki, what the fuck am i doing */
	/* paranoia */
	class Thumbnail {
		// https://stackoverflow.com/a/14300703
		private static function IsValidHash($hash) {
			return preg_match('/^[a-f0-9]{32}$/', $hash);
		}

		public static function Exists($hash): bool {
			if(!self::IsValidHash($hash))
				return false;

			return file_exists(self::GetPath($hash));
		}

		public static function GetPath(string $hash, string $service = "renders"): string {
			return $_SERVER['DOCUMENT_ROOT']."/../{$service}/3d/{$hash}.json";
		}

		public static function Generate3D(User $user) {
			$hash = $user->currentoutfitmd5;

			$result_json = self::GetRenderFile($hash);

			if(!$result_json)
				return null;

			return [
				"aabb" => $result_json["AABB"],
				"camera" => $result_json["camera"],
				"hash" => $hash,
			];
		}

		public static function Get3DObj(string $hash) {
			return self::GetFileInRender($hash, "scene.obj");
		}

		public static function Get3DMtl(string $hash) {
			$mtl = self::GetFileInRender($hash, "scene.mtl");

			if($mtl)
				return str_replace("Player1Tex.png", $hash, $mtl);
			else
				return null;
		}

		public static function Get3DTex(string $hash) {
			return self::GetFileInRender($hash, "Player1Tex.png");
		}

		private static function GetRenderFile(string $hash): mixed {
			if(!self::Exists($hash))
				return null;

			$json = json_decode(gzdecode(file_get_contents(self::GetPath($hash))), true);

			if(!$json)
				unlink(self::GetPath($hash)); // scary

			return $json;
		}

		private static function GetFileInRender(string $hash, string $file): mixed {
			$result_json = self::GetRenderFile($hash);

			if(!$result_json)
				return null;

			return base64_decode($result_json["files"][$file]['content']);
		}
	}
?>