<?php
	set_content_type(ARLTYPEJSON);

	$result = ["success" => false, "reason" => "Request failed."];

	$user = SESSION ? SESSION->user : null;

	if(!$user || !isset($_POST['ANORRL$Update$Profile$Bio']))
		die(json_encode($result));

	die(json_encode($user->updateBio(trim($_POST['ANORRL$Update$Profile$Bio']))));

?>