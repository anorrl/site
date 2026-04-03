<?php

	namespace anorrl\utilities;

	class ClientDetector {
		
		public static function IsAClient(): bool {
			return str_contains($_SERVER['HTTP_USER_AGENT'], "ANORRLStudio") || 
				str_contains(strtolower($_SERVER['HTTP_USER_AGENT']), "anorrl/wininet") ||
				str_contains(strtolower($_SERVER['HTTP_USER_AGENT']), "anorrl/winhttp");
		}

	}
?>