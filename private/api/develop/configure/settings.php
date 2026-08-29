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
	use anorrl\utilities\UtilUtils;
	use anorrl\utilities\AssetUploader;

	set_content_type(ARLTYPEJSON);

	if(!ARLAUTH || !isset($id) || !isset($_SESSION['ANORRL$Asset$ID']) || !isset($_POST['ANORRL$EditItem$Name']) || !isset($_POST['ANORRL$EditItem$Description']))
		die(json_encode($result));

	if(intval($_SESSION['ANORRL$Asset$ID']) != $id)
		die(json_encode($result));

	$asset = Asset::FromID($id);

	if(!$asset) {
		$result['reason'] = "Failed to retrieve asset.";
		die(json_encode($result));
	}

	$name = UtilUtils::StripUnicode($_POST['ANORRL$EditItem$Name']);
	$description = UtilUtils::StripUnicode($_POST['ANORRL$EditItem$Description']);
	$public = isset($_POST['ANORRL$EditItem$PublicBox']);
	$comments_enabled = isset($_POST['ANORRL$EditItem$CommentsBox']);
	//$on_sale = isset($_POST['ANORRL$EditItem$OnSaleBox']);

	die(json_encode(AssetUploader::EditAsset($asset, $name, $description, $public, $asset->onsale, $comments_enabled)));
?>