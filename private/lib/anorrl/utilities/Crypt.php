<?php
	namespace anorrl\utilities;

	use Exception;

	class Crypt {

		private static string $privatekey = "";
		private static string $publickey = "";
		
		public static function decrypt($data) {
			if(strlen(self::$privatekey) == 0) {
				self::$privatekey = file_get_contents(get_path_file("PrivateKey.pem"));
			}
			if(!openssl_private_decrypt($data, $result, self::$privatekey))
				throw new Exception("Unable to decrypt data!");

			return $result;
		}

		public static function encrypt($data) {
			if(strlen(self::$privatekey) == 0) {
				self::$privatekey = file_get_contents(get_path_file("PrivateKey.pem"));
			}
			if(strlen(self::$publickey) == 0) {
				// https://stackoverflow.com/a/29277171
				if(!file_exists(get_path_file("PublicKey.pem"))) {
					file_put_contents(get_path_file("PublicKey.pem"), openssl_pkey_get_details(openssl_pkey_get_private(self::$privatekey))['key']);
				}
				self::$publickey = file_get_contents(get_path_file("PublicKey.pem"));
			}
				
			if(!openssl_public_encrypt($data, $result, self::$publickey))
				throw new Exception("Unable to encrypt data!");

			return $result;

		}

	}
?>