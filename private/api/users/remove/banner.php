<?php
	set_content_type(ARLTYPEJSON);

	$result = ["error" => true, "reason" => "Request failed."];

	$user = SESSION ? SESSION->user : null;

	if(!$user)
		die(json_encode($result));

	die(json_encode($user->resetBannerPicture()));

?>