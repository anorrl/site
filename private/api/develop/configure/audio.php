<?php
	use anorrl\Asset;
	use anorrl\enums\AssetType;

	set_content_type(ARLTYPEJSON);

	$result = ["success" => false, "reason" => "Request failed."];

	if(!ARLAUTH || !isset($id) || !isset($_POST['ANORRL$EditAudio$Metadata$AlbumID']))
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

	$album_id = trim($_POST['ANORRL$EditAudio$Metadata$AlbumID']);

	if(strlen($album_id) == 0 || intval($album_id) <= 0) {
		$asset->resetThumbnail();
	}
	else {
		$thumbasset = Asset::FromID(intval($album_id));

		if(!$thumbasset) {
			$result['reason'] = "Failed to retrieve cover asset.";
			die(json_encode($result));
		}

		if($thumbasset->type != AssetType::DECAL && $thumbasset->type != AssetType::IMAGE) {
			$result['reason'] = "Cover asset must either be a decal or image!";
			die(json_encode($result));
		}

		$asset->setThumbnailTo($thumbasset);
	}

	die(json_encode([
		"success" => true
	]));
?>