<?php

	require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/comment.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/userutils.php";

	$user = UserUtils::RetrieveUser();

	if(session_start() != PHP_SESSION_ACTIVE) {
		session_start();
	}

	if(
		isset($_POST['ANORRL$Comment$Post$Contents']) &&
		isset($_POST['ANORRL$Comment$Post$Submit']) &&
		$user != null
	) {
		if(isset($_SESSION['ANORRL$Comment$Post$AssetID'])) {
			Comment::Post(Asset::FromID(intval($_SESSION['ANORRL$Comment$Post$AssetID'])), $_POST['ANORRL$Comment$Post$Contents']);
		}
		else if(isset($_SESSION['ANORRL$Comment$Post$ProfileID'])) {
			Comment::Post(User::FromID(intval($_SESSION['ANORRL$Comment$Post$ProfileID'])), $_POST['ANORRL$Comment$Post$Contents']);
		} 
		else {
			die(json_encode([
				"error" => true,
				"reason" => "Invalid request!"
			]));
		}
	}
	else {
		die(json_encode([
			"error" => true,
			"reason" => "User is not authorised to perform this action"
		]));
	}

?>