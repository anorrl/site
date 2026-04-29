<?php
	use anorrl\Script;

	header("Content-Type: text/plain");

	$username = "Player";
	$userid = 1;
	$userage = 0;
	
	if(SESSION) {
		$user = SESSION->user;
		$username = $user->name;
		$userid = $user->id;
		$userage = $user->getAccountAge();
	}

	die(new Script("visit")->sign(
	[
		"userid" => $userid,
		"username" => $username,
		"accountage" => $userage
	]));
?>
