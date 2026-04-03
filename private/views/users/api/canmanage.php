<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/assetutils.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/userutils.php";
	header("Content-Type: application/json");

	if(isset($_GET['userId']) && isset($_GET['placeId'])) {
		$user = User::FromID(intval($_GET['userId']));
		$place = Place::FromID(intval($_GET['placeId']));

		if($place != null && $user != null) {
			die(json_encode([
				"Success" => true,
				"CanManage" => $place->creator->id == $user->id || $user->IsAdmin() 
			]));
		}
		
	}

	die(json_encode([
		"Success" => false,
		"CanManage" => false 
	]));
?>