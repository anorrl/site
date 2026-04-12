<?php
	use anorrl\User;

	header("Content-Type: application/json");

	$user = null;

	if(isset($_GET['userid'])) {
		$user = User::FromID(intval($_GET['userid']));
	}

	if($user == null) {
		$user = User::FromID(1);
	}

	$domain = CONFIG->domain;
	$thumbsurl = $user->getThumbsUrlService($user->setprofilepicture ? "profile" : "headshot");

	die(json_encode([
		"Final" => true,
		"Url" => "http://{$domain}{$thumbsurl}&nocompress",
		"RetryUrl" => "http://{$domain}{$thumbsurl}&nocompress",
	]));

?>