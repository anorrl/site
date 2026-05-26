<?php
	// lifted from pixie - by parakeet

	define('CONFIG', json_decode(file_get_contents(__DIR__."/../settings.json")));

	define("ARLTYPEJSON", "application/json");
	define("ARLTYPEXML", "application/xml");

	define("ARLTYPEPLAIN", "text/plain");
	define("ARLTYPECSS", "text/css");

	define("ARLTYPEPNG", "image/png");
	define("ARLTYPEWEBP", "image/webp");

	require __DIR__ . "/vendor/autoload.php";

	use anorrl\utilities\UserUtils;
	use anorrl\Session;
	
	if(isset(CONFIG->secret)) {
		if(isset($_GET[CONFIG->secret->partone]) && $_GET[CONFIG->secret->partone] == CONFIG->secret->parttwo) {
			setcookie('ANORRL$Hidden$Cookie$yaya', CONFIG->secret->token, time() + (460800* 30), "/", CONFIG->domain);
			redirect("/register");
		}
	}
	
	$session_user = UserUtils::RetrieveUser();

	if(session_status() != PHP_SESSION_ACTIVE) {
		session_start();
	}

	if($session_user != null) {
		define('SESSION', new Session($session_user));
	} else {
		define('SESSION', false);
	}

	/**
	 * Macro for header()
	 * @param string $name
	 * @param string $value
	 * @param bool $replace
	 * @return void
	 */
	function set_header(string $name, string $value, bool $replace = true) {
		header("$name: $value", $replace);
	}

	/**
	 * Macro for header("Content-Type: {$type}")
	 * @param string $type
	 * @return void
	 */
	function set_content_type(string $type) {
		set_header("Content-Type", $type);
	}

	/**
	 * Macro for setting no caching via headers.
	 * @return void
	 */
	function disable_cache() {
		set_header("Cache-Control", "no-store, no-cache, must-revalidate, max-age=0");
		set_header("Cache-Control", "post-check=0, pre-check=0", false);
		set_header("Pragma", "no-cache");
	}

	/**
	 * Macro for exiting with a Location header
	 * @param string $path
	 * @return never
	 */
	function redirect(string $path) {
		die(set_header("Location", $path));
	}

	function set_encoding(string $type) {
		set_header("Content-Encoding", $type);
	}

	function set_attachment(string $filename) {
		set_header("Content-Disposition", "attachment; filename=\"$filename\"");
	}

	function get_header(string $name) {
		$headers = getallheaders();
		return $headers[$name] ?? null;
	}

	require_once __DIR__ . "/router.php";

	exit();
?>
