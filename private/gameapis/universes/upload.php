<?php
	use anorrl\Asset;
	use anorrl\Universe;
	use anorrl\enums\AssetType;
	use anorrl\utilities\ClientDetector;
	use anorrl\utilities\AssetUploader;
	use anorrl\utilities\ImageUtils;

	set_content_type(ARLTYPEJSON);

	if(!SESSION)
		exit_http(500);

	$user = SESSION->user;

	//assetType=13&name=Images%2Fballhhhhhhhh&description=madeinstudio

	function isAValidType(int $type) {
		return match($type) {
			13 => true,
			3 => true,
			21 => true,
			default => false
		};
	}

	function hasValidName(string $name) {
		return str_starts_with($name, "Images") || str_starts_with($name, "Audio");
	}

	if(
		isset($_GET['assetTypeId']) &&
		isset($_GET['name']) &&
		isset($_GET['description']) &&
		ClientDetector::IsAClient()
	) {
		$raw_asset_type = intval($_GET['assetTypeId']);
		$asset_name = urldecode($_GET['name']);
		$asset_description = urldecode($_GET['description'] ?? "madeinstudio");
		$raw_public = $_GET['secret'] ?? "false";
		$universe = Universe::FromID(intval($_GET['universe'] ?? -1));

		if(!isAValidType($raw_asset_type) || ($raw_asset_type != 21 && !hasValidName($asset_name)))
			die(json_encode(["Success" => false, "Message" => "Any other asset type id has not been implemented yet sorry!"]));

		if($raw_asset_type == 21 && !$universe)
			die(json_encode(["Success" => false]));

		$public = strcmp($raw_public, "true") == 0;

		$contents = file_get_contents("php://input");
		$asset_type = AssetType::index($raw_asset_type);

		if($asset_type == AssetType::DECAL || $asset_type == AssetType::BADGE) {
			$image = imagecreatefromstring($contents);

			if(!($image instanceof GdImage))
				die(json_encode([
					"Success" => false,
					"Message" => "That was not an image pal."
				]));
			
		} else if($asset_type == AssetType::AUDIO) {
			// maybe do filtering here
			$mimetype = ImageUtils::checkMimeType($contents);

			if(str_starts_with($mimetype, "audio/")) {
				die(json_encode([
					"Success" => false,
					"Message" => "That was not an audio file pal."
				]));
			}
		}
		
		if($asset_type == AssetType::BADGE)
			$public = !$public;

		$result = AssetUploader::UploadAsset($contents, $asset_type, $asset_name, $asset_description, $public, false);

		if(!$result['error']) {
			if($asset_type == AssetType::BADGE) {
				Asset::FromID($result['id'])->setUniverse($universe);
			}
			die(json_encode([
				"Success" => true,
				"BackingAssetId" => $result['id']
			]));
		}
		else {
			die(json_encode([
				"Success" => false,
				"Message" => $result['reason']
			]));
		}
		
	}

	die(json_encode([ "Success" => false ]));
?>
