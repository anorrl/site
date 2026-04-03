<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . "/core/utilities/userutils.php";
	header("Content-Type: application/json");
	
	if(isset($_GET['username'])) {
		$user = User::FromName($_GET['username']);
		if($user != null) {
			die(json_encode([
				"Id" => $user->id
			]));
		}
		
	}
	echo "{}";
?>