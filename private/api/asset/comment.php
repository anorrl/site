<?php
	use anorrl\Asset;
	use anorrl\Comment;

	$user = SESSION ? SESSION->user : null;

	set_content_type(ARLTYPEJSON);

	$result = ["success" => false, "reason" => "Request failed."];

	if(!$user || !isset($id) || !isset($_POST['ANORRL$Comments$Contents']))
		die(json_encode($result));

	$asset = Asset::FromID($id);

	if(!$asset) {
		$result['reason'] = "Failed to retrieve asset.";
		die(json_encode($result));
	}

	die(json_encode(Comment::Post($asset, trim($_POST['ANORRL$Comments$Contents']))));
?>
