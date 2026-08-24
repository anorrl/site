<?php

	namespace anorrl\utilities;

	class ClientDetector {
		
		public static function IsAClient(): bool {
			if(!isset(\CONFIG->studioUA))
				throw new \Exception("Set a user agent or something BAKA!");

			return strcmp($_SERVER['HTTP_USER_AGENT'], \CONFIG->studioUA) == 0 || 
				str_contains(strtolower($_SERVER['HTTP_USER_AGENT']), "anorrl/wininet") ||
				str_contains(strtolower($_SERVER['HTTP_USER_AGENT']), "anorrl/winhttp");
		}

		public static function HasAccess(): bool {
			$REQaccessKey = $_SERVER["HTTP_ACCESSKEY"] ?? null;
			return !($REQaccessKey !== \CONFIG->arbiter->key);
		}

	}
?>