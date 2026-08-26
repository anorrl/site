<?php
	use anorrl\User;
	use anorrl\utilities\UtilUtils;

	set_content_type(ARLTYPEJSON);

	if(!UtilUtils::HasBeenRewritten()) {
		die("{}");
	}

	// No id parameter? GET OUT!
	if(!isset($userId)) {
		die("{}");
	}

	$get_user = User::FromID(intval($userId));

	if($get_user == null) {
		die("{}");
	}

	die(json_encode([
		"Id" => $get_user->id,
		"Username" => $get_user->name
	]));
?>