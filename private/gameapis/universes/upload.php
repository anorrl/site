<?php
	use anorrl\enums\AssetType;
	use anorrl\utilities\ClientDetector;
	use anorrl\utilities\AssetUploader;

	set_content_type(ARLTYPEJSON);

	if(!SESSION)
		exit_http(500);

	$user = SESSION->user;

	//assetType=13&name=Images%2Fballhhhhhhhh&description=madeinstudio

	function isAValidType(int $type) {
		return match($type) {
			13 => true,
			3 => true,
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
		if(!isAValidType($raw_asset_type) || !hasValidName($asset_name))
			die(json_encode(["Success" => false, "Message" => "Any other asset type id has not been implemented yet sorry!"]));

		$contents = file_get_contents("php://input");
		$asset_type = AssetType::index($raw_asset_type);

		if($asset_type == AssetType::DECAL) {
			$image = imagecreatefromstring($contents);

			if(!($image instanceof GdImage))
				die(json_encode([
					"Success" => false,
					"Message" => "That was not an image pal."
				]));
		} else if($asset_type == AssetType::AUDIO) {
			// maybe do filtering here
		}

		$result = AssetUploader::UploadAsset($contents, $asset_type, $asset_name, "madeinstudio", false, false, true);

		if(!$result['error']) {
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
