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

	$domain = CONFIG->domain;
	$thumbsurl = $user->getThumbsUrl();

	if(str_starts_with($thumbsurl, "/"))
		$thumbsurl .= "http://".CONFIG->domain.$thumbsurl;

	die(json_encode([
		"Final" => true,
		"Url" => "http://{$domain}{$thumbsurl}",
		"RetryUrl" => "http://{$domain}{$thumbsurl}",
	]));

?>