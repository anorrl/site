<?php
	use anorrl\User;

	$user = null;

	if(isset($_GET['userid'])) {
		$user = User::FromID(intval($_GET['userid']));
	}

	if($user == null) {
		$user = User::FromID(1);
	}

	if($user->setprofilepicture) {
		die(json_encode([
			"Final" => true,
			"Url" => "http://arl.lambda.cam/thumbs/profile?id=".$user->id."&nocompress",
			"RetryUrl" => "http://arl.lambda.cam/thumbs/profile?id=".$user->id."&nocompress",
		]));
	} else {
		die(json_encode([
			"Final" => true,
			"Url" => "http://arl.lambda.cam/thumbs/headshot?id=".$user->id."&nocompress",
			"RetryUrl" => "http://arl.lambda.cam/thumbs/headshot?id=".$user->id."&nocompress",
		]));
	}

?>