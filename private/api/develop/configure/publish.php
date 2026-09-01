<?php
	use anorrl\Asset;
	use anorrl\utilities\AssetUploader;

	set_content_type(ARLTYPEJSON);

	$result = ["success" => false, "reason" => "Request failed."];

	if(!ARLAUTH || !isset($id) || !isset($_FILES['ANORRL$EditItem$Version$File']))
		die(json_encode($result));

	if(intval($_SESSION['ANORRL$Asset$ID']) != $id)
		die(json_encode($result));

	$asset = Asset::FromID($id);

	if(!$asset) {
		$result['reason'] = "Failed to retrieve asset.";
		die(json_encode($result));
	}

	if(!$asset->isOwner(SESSION->user)) {
		$result['reason'] = "You are not authorised to perform this action.";
		die(json_encode($result));
	}

	die(json_encode(AssetUploader::UpdateAsset($asset, $_FILES['ANORRL$EditItem$Version$File'])));
?>