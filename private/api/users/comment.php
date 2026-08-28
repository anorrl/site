<?php
	use anorrl\User;
	use anorrl\Comment;

	$session = SESSION ? SESSION->user : null;

	set_content_type(ARLTYPEJSON);

	$result = ["success" => false, "reason" => "Request failed."];

	if(!$session || !isset($id) || !isset($_POST['ANORRL$Comment$Contents']) || !isset($_SESSION['ANORRL$Profile$ID']))
		die(json_encode($result));

	if(intval($_SESSION['ANORRL$Profile$ID']) != $id)
		die(json_encode($result));

	$user = User::FromID($id);

	if(!$user) {
		$result['reason'] = "Failed to find user.";
		die(json_encode($result));
	}

	die(json_encode(Comment::Post($user, trim($_POST['ANORRL$Comment$Contents']))));
?>
