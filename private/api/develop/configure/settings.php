<?php

	// handle basic settings requests
	// e.g `/develop/545/configure/settings`
	
	
	/*
	ANORRL$EditItem$Name	"BULLSHITLAND"
	ANORRL$EditItem$Description	"Place where I put scripts I made."
	ANORRL$EditItem$PublicBox	"on"
	ANORRL$EditItem$CommentsBox	"on"
	*/

	use anorrl\Asset;
	
	set_content_type(ARLTYPEJSON);

	if(!ARLAUTH || !isset($id) || !isset($_SESSION['ANORRL$Asset$ID']))
		die(json_encode($result));

	if(intval($_SESSION['ANORRL$Asset$ID']) != $id)
		die(json_encode($result));

	$asset = Asset::FromID($id);

	if(!$asset) {
		$result['reason'] = "Failed to retrieve asset.";
		die(json_encode($result));
	}
?>