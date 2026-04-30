<?php
	// lifted from pixie - by parakeet

	define('CONFIG', json_decode(file_get_contents(__DIR__."/../settings.json")));

	require __DIR__ . "/vendor/autoload.php";

	use anorrl\utilities\UserUtils;
	use anorrl\Session;
	
	if(isset(CONFIG->secret)) {
		if(isset($_GET[CONFIG->secret->partone]) && $_GET[CONFIG->secret->partone] == CONFIG->secret->parttwo) {
			setcookie('ANORRL$Hidden$Cookie$yaya', CONFIG->secret->token, time() + (460800* 30), "/", CONFIG->domain);
			die(header("Location: /register"));
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
	
	require_once __DIR__ . "/router.php";

	exit();
?>