<?php
	use anorrl\User;
	use anorrl\utilities\Thumbnail;
	
	set_content_type(ARLTYPEJSON);

	if(!isset($_GET['for']))
		exit_http(500);

	$user = User::FromID(intval($_GET['for']));

	if(!$user)
		exit_http(500);

	$generated_result = Thumbnail::Generate3D($user);

	if(!$generated_result)
		exit_http(500);

	exit(json_encode($generated_result));
?>