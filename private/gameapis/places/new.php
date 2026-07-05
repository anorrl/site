<?php
	use anorrl\Universe;
	use anorrl\utilities\AssetUploader;
	
	if(!SESSION || !isset($_GET['universeId']))
		exit_http(403);

	$universe = Universe::FromID(intval($_GET['universeId']));

	if(!$universe)
		exit_http(503);

	if(!$universe->isOwner(SESSION->user))
		exit_http(403);

	$result = AssetUploader::CreateSubPlace($universe);

	if($result['error']) {
		http_response_code(500);
		error_log($result['reason']);
		die($result['reason']);
	}
?>
