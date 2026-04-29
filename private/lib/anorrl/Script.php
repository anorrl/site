<?php
	namespace anorrl;

	class Script {
		string $script;

		function __construct(string $path) {
			$this->script = $this->loadScript($path);
		}

		function replacePlaceholder(string $valname, mixed $val) {
			$this->script = str_replace("{$valname}", strval($val), $this->script);
		}

		function sign(array $variables, string $header = "arlsig") {
			$this->replacePlaceholder("domain", \CONFIG->domain);

			foreach($variables as $key => $value) {
				$this->replacePlaceholder($key, $value);
			}

			$signed_script = "\r\n".$this->script;
			return "--{$header}%".getSignature($signed_script)."%".$signed_script;
		}

        	private function getSignature($data) {
                	$signature = "";
                	openssl_sign($script, $signature, file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/../PrivateKey.pem"), OPENSSL_ALGO_SHA1);
                	return base64_encode($signature);
        	}

		private function loadScript($path) {
			return file_get_contents($_SERVER["DOCUMENT_ROOT"]."/private/scripts/$path.lua";
		}
	}
?>
