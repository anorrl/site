<?php
	use anorrl\User;
	use anorrl\Asset;

	if(!isset($_GET['user']) && !isset($_GET['asset']))
		exit_http(500);
	
	$data = isset($_GET['user']) ? User::FromID(intval($_GET['user'])) : Asset::FromID(intval($_GET['asset']));

	if(!$data)
		exit_http(500);

	set_content_type(ARLTYPEJSON);

	$api = $data instanceof User ? "avatar" : "asset";

	echo json_encode([
		"Final" => true,
		"Url" => "/thumbnail/{$api}/generate?for={$data->id}"
	])
?>