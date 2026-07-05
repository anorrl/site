<?php
	use anorrl\utilities\Thumbnail;
	
	set_content_type(ARLTYPEPNG);

	if(!isset($hash) || !isset($image))
		exit_http(500);


	$data = Thumbnail::Get3DTex($hash, $image, false);

	if(!$data)
		exit_http(500);

	exit($data);
?>