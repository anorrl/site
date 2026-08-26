<?php
	set_content_type(ARLTYPEJSON);

	use anorrl\Status;

	$user = SESSION ? SESSION->user : null;
	
	if(isset($_POST['ANORRL$Home$Status$Text']) && $user)
		die(json_encode(Status::Send($user->id, trim($_POST['ANORRL$Home$Status$Text']))));

	die(json_encode([
		"success" => false,
		"reason" => "Request failed."
	]));
?>