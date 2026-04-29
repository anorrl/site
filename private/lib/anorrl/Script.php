<?php
	namespace anorrl;

	class Script {
		public string $script;

		function __construct(string $path) {
			$this->script = $this->loadScript($path);
		}

		function replacePlaceholder(string $valname, mixed $val) {
			$this->script = str_replace("{".$valname."}", strval($val), $this->script);
		}

		function sign(array $variables = [], string $header = "arlsig") {
			foreach($variables as $key => $value) {
				$this->replacePlaceholder($key, $value);
			}
			
			$this->replacePlaceholder("domain", \CONFIG->domain);

			$signed_script = "\r\n".$this->script;
			$signature = $this->getSignature($signed_script);
			
			return "--{$header}%{$signature}%{$signed_script}";
		}

		private function getSignature(mixed $data) {
			$signature = "";
			openssl_sign($data, $signature, file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/../PrivateKey.pem"), OPENSSL_ALGO_SHA1);
			return base64_encode($signature);
		}

		private function loadScript($path) {
			return file_get_contents($_SERVER["DOCUMENT_ROOT"]."/private/scripts/$path.lua");
		}

		private static function ReplacePlaceholderStatic(mixed $data, string $valname, mixed $val) {
			return str_replace("{".$valname."}", strval($val), $data);
		}

		public static function SignNonScript($data, string $header = "arlsig") {
			$data = self::ReplacePlaceholderStatic($data, "domain", \CONFIG->domain);
		
			$signed_data = "\r\n".$data;

			$signature = "";
			openssl_sign($signed_data, $signature, file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/../PrivateKey.pem"), OPENSSL_ALGO_SHA1);
			$signature = base64_encode($signature);

			return "--{$header}%{$signature}%{$signed_data}";
		}
	}
?>
