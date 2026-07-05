<?php
	use anorrl\User;
	use anorrl\Asset;

	if(!isset($_GET['userid']) && !isset($_GET['assetid']))
		exit_http(500);
	
	$data = isset($_GET['userid']) ? User::FromID(intval($_GET['userid'])) : Asset::FromID(intval($_GET['assetid']));

	if(!$data)
		exit_http(500);

	set_content_type(ARLTYPEJSON);

	$api = $data instanceof User ? "avatar" : "asset";

	echo json_encode([
		"Final" => true,
		"Url" => "/thumbnail/{$api}/generate?for={$data->id}"
	])
?>