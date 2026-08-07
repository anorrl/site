<?php
	use anorrl\User;
	use anorrl\Comment;

	$user = SESSION ? SESSION->user : null;

	set_content_type(ARLTYPEJSON);

	$result = ["success" => false, "reason" => "Request failed."];

	if(!$user || !isset($id) || !isset($_POST['ANORRL$Comments$Contents']))
		die(json_encode($result));

	$user = User::FromID($id);

	if(!$user) {
		$result['reason'] = "Failed to find user.";
		die(json_encode($result));
	}

	die(json_encode(Comment::Post($user, trim($_POST['ANORRL$Comments$Contents']))));
?>
