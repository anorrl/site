<?php
	// lifted from pixie - by parakeet

	define('CONFIG', json_decode(file_get_contents(__DIR__."/../settings.json")));

	define("ARLTYPEJSON", "application/json");
	define("ARLTYPEXML", "application/xml");

	define("ARLTYPEPLAIN", "text/plain");
	define("ARLTYPECSS", "text/css");

	define("ARLTYPEPNG", "image/png");
	define("ARLTYPEWEBP", "image/webp");

	define("ARLRENDER", 0);
	define("ARLRENDER3D", 1);
	define("ARLRENDERHEADSHOT", 2);

	// probably should just put this in php.ini ...
	date_default_timezone_set('Europe/London');
	error_reporting(E_ALL ^ E_DEPRECATED);

	require __DIR__ . "/vendor/autoload.php";

	use anorrl\Session;
	use anorrl\Database;

	if(Database::singleton()->run("SELECT `id` FROM `users`")->rowCount() == 0)
		Session::registerAdmin("ANORRL", md5(rand()));
		

	$session_user = Session::retrieveUser();

	if(session_status() != PHP_SESSION_ACTIVE) {
		session_start();
	}

	if($session_user != null) {
		define('SESSION', new Session($session_user));
	} else {
		define('SESSION', false);
	}

	/**
	 * ARLAUTH = false/true
	 * false = logged out
	 * true  = logged in
	 */
	define('ARLAUTH', SESSION != false);

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

	function exit_http(int $http_response_code, string $message = "") {
		http_response_code($http_response_code);
		die($message);
	}

	function create_folder(string $path) {
		if(file_exists(__DIR__."/{$path}"))
			return;

		if(!mkdir(__DIR__."/{$path}", 0777, true))
			throw new Exception("Can't create folders!");
	}

	function get_path_sitefile(string $path) {
		return $_SERVER['DOCUMENT_ROOT']."/{$path}";
	}

	function get_path_file(string $path) {
		return get_path_sitefile("../{$path}");
	}

	function get_user_profile_path(int $id) {
		return get_path_file("users/profiles/{$id}.png");
	}

	function get_user_banner_path(int $id) {
		return get_path_file("users/banners/{$id}");
	}

	function get_user_render_path(string $md5, int $type) {
		$path = get_path_file("users/renders/{$md5}");
		switch($type) {
			case ARLRENDER:
				return "$path/image.png";
			case ARLRENDER3D:
				return "$path/scene.json";
			case ARLRENDERHEADSHOT:
				return "$path/headshot.png";
		}

		throw new Exception("Something went wrong.");
	}

	function get_asset(string $path = "") {
		return get_path_file("assets/{$path}");
	}

	function get_asset_thumbs($id) {
		return get_asset("thumbs/{$id}");
	}

	create_folder("../assets/thumbs");
	create_folder("../assets/3d");
	create_folder("../users/profiles");
	create_folder("../users/banners");
	create_folder("../users/renders");

	require_once __DIR__ . "/router.php";

	exit();
?>
