<?php 

	ini_set("default_socket_timeout", 60);

	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/userutils.php";

	$directory = $_SERVER['DOCUMENT_ROOT']."/core/Assemblies/Roblox/Grid/Rcc/";
	$scanned_directory = array_diff(scandir($directory), array('..', '.'));

	foreach($scanned_directory as $file) {
		if(str_contains($file, "wsdl")) {
			continue;
		}
		require $directory.$file;
	}

	$settings = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../settings.env", true);
	


	class TheFuckingRenderer {

		public static string $arbiter_ip = "";
		public static string $arbiter_token = "";
		public static bool $cantuserenderer = false;

		private static function RequestA(string $endpoint, array $data): ?string {
			self::UpdateAndSetConfig();
			$arb_ip = self::$arbiter_ip;
			$arb_token = self::$arbiter_token;
			$ch = curl_init("http://$arb_ip" . $endpoint);//37.114.46.52
			error_log("http://$arb_ip" . $endpoint);

			curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => json_encode($data),
				CURLOPT_HTTPHEADER => [
					"Authorization: Bearer $arb_token",
					"Content-Type: application/json",
					"User-Agent: ANORRL/1.0"
				],
				CURLOPT_TIMEOUT => 60
			]);

			$response = curl_exec($ch);

			if ($response === false) {
				curl_close($ch);
				return null;
			}

			curl_close($ch);

			$json = json_decode($response, true);

			if (!$json || !isset($json["base64"])) {
				return null;
			}

			return $json["base64"];
		}

		private static function UpdateAndSetConfig() {
			$settings = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../settings.env", true);
			$renderer_settings = $settings['renderer'];
			if(self::$cantuserenderer != boolval($renderer_settings['DISABLED'])) {
				self::$cantuserenderer = boolval($renderer_settings['DISABLED']);
			}

			if(self::$arbiter_ip != $settings['arbiter']['LOC']) {
				self::$arbiter_ip = $settings['arbiter']['LOC'];
			}

			if(self::$arbiter_token != $settings['arbiter']['token']) {
				self::$arbiter_token = $settings['arbiter']['token'];
			}
		}

		public static function RenderPlayer(int $id = 0) {
			
			self::UpdateAndSetConfig();

			if(self::$cantuserenderer) {
				return null;
			}
			
			$data = self::RequestA("/api/v1/avatar-render", ["UserId" => $id, "IsHeadshot" => false, "IsClothing" => true]);

			if(!$data) {
				return null;
			}

			return $data;
		}

		public static function RenderUser(int $id = 0, bool $headshot = false) {
			if($id == 0) {
				return null;
			}
			
			$user = User::FromID($id);

			if($user == null) {
				return null;
			}

			self::UpdateAndSetConfig();

			if(self::$cantuserenderer) {
				return null;
			}

			$data = self::RequestA("/api/v1/avatar-render", ["UserId" => $id, "IsHeadshot" => $headshot, "IsClothing" => false]);

			return $data;
		}

		public static function RenderMesh(int $id = 0) {
			self::UpdateAndSetConfig();
			
			if(self::$cantuserenderer) {
				return null;
			}

			$data = self::RequestA("/api/v1/mesh-render", ["MeshId" => $id]);

			if(!$data) {
				return null;
			}

			return $data;
		}

		public static function RenderPlace(int $id = 0) {
			self::UpdateAndSetConfig();

			if(self::$cantuserenderer) {
				return null;
			}

			$data = self::RequestA("/api/v1/place-render", ["PlaceId" => $id]);

			if(!$data) {
				return null;
			}

			return $data;
		}

		public static function RenderModel(int $id = 0) {
			self::UpdateAndSetConfig();

			if(self::$cantuserenderer) {
				return null;
			}

			$data = self::RequestA("/api/v1/model-render", ["AssetId" => $id]);

			if(!$data) {
				return null;
			}

			return $data;
		}
	}
?>