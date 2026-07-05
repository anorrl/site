<?php
	use anorrl\Asset;
	use anorrl\utilities\Thumbnail;
	
	set_content_type(ARLTYPEJSON);

	if(!isset($_GET['for']))
		exit_http(500);

	$asset = Asset::FromID(intval($_GET['for']));
	
	if(!$asset)
		exit_http(500);

	$generated_result = Thumbnail::Generate3D($asset);

	if(!$generated_result)
		exit_http(500);

	exit(json_encode($generated_result));
?>