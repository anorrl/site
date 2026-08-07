<?php
	use anorrl\User;

	set_content_type(ARLTYPEJSON);

	$user = null;

	if(isset($_GET['userid'])) {
		$user = User::FromID(intval($_GET['userid']));
	}

	if($user == null) {
		$user = User::FromID(1);
	}

	$thumbsurl = $user->getThumbsUrl();

	die(json_encode([
		"Final" => true,
		"Url" => $thumbsurl,
		"RetryUrl" => $thumbsurl,
	]));

?>