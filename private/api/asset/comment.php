<?php
	use anorrl\Asset;
	use anorrl\Comment;

	set_content_type(ARLTYPEJSON);

	$result = ["success" => false, "reason" => "Request failed."];

	if(!ARLAUTH || !isset($id) || !isset($_POST['ANORRL$Comment$Contents']) || !isset($_SESSION['ANORRL$Asset$ID']))
		die(json_encode($result));

	if(intval($_SESSION['ANORRL$Asset$ID']) != $id)
		die(json_encode($result));

	$asset = Asset::FromID($id);

	if(!$asset) {
		$result['reason'] = "Failed to retrieve asset.";
		die(json_encode($result));
	}

	die(json_encode(Comment::Post($asset, trim($_POST['ANORRL$Comment$Contents']))));
?>
