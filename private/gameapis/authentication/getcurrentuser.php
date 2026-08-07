<?php
	set_content_type(ARLTYPEJSON);

	$response = [];

	if(ARLAUTH) {
		$response = [
			"UserId" => SESSION->user->id,
			"Name" => SESSION->user->name
		];
	}

	die(json_encode($response));

?>