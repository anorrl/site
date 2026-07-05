<?php
	use anorrl\utilities\Thumbnail;
	
	set_content_type(ARLTYPEPLAIN);

	if(!isset($hash))
		exit_http(500);

	$data = Thumbnail::Get3DObj($hash);

	if(!$data)
		exit_http(500);

	exit($data);
?>