<?php
	use anorrl\Alias;
	use anorrl\Asset;
	use anorrl\Place;
	use anorrl\Universe;
	use anorrl\enums\AssetType;
	use anorrl\utilities\ClientDetector;

	if(!isset($_GET['id']) && !isset($_GET['ID']) && !isset($_GET['Id']) && !isset($_GET['assetName']) && !isset($_GET['universeId'])) {
		exit_http(500);
	}

	if(isset($_GET['id'])) {
		$id = intval($_GET["id"]);
	} else if(isset($_GET['ID'])) {
		$id = intval($_GET["ID"]);
	} else if(isset($_GET['Id'])) {
		$id = intval($_GET["Id"]);
	} else {
		$id = null;
	}

	if(isset($_GET['assetName']) && isset($_GET['universeId'])) {
		$universe = Universe::FromID(intval($_GET['universeId']));
		$name = urldecode($_GET['assetName']);
	} else {
		$name = null;
		$universe = null;
	}

	$domain = CONFIG->domain;
	
	$user = SESSION ? SESSION->user : null;

	if($id != null) {
		$asset = Asset::FromID($id);
	} else {
		if($universe && $name) {
			$alias = Alias::FromName($universe, $name);

			if($alias)
				$asset = $alias->asset;
		}
	}

	if($asset != null) {
		$version = isset($_GET['version']) ? intval($_GET['version']) : -1;
		$contents = $asset->getFileContents($version);

		if($contents != null) {
			$md5hash = md5($contents);
			if($asset->type == AssetType::PLACE) {
				$place = Place::FromID($asset->id);
				
				if($place && $place->copylocked) {
					$error = false;

					if($user == null || (!ClientDetector::HasAccess() && $user != null && !$place->isOwner($user))) {
						$error = true;
					}

					if($error) {
						if(!ClientDetector::HasAccess())
							exit_http(403, "Unauthorized");
					}
				}
			} else{
				// might break who knows
				if (isset($_GET['serverplaceid']) && isset($_GET['clientinsert'])) {
					$serverplace = Place::FromID(intval($_GET['serverplaceid']));
					
					if ($serverplace == null && intval($_GET['serverplaceid']) != 0) {
						exit_http(400, "Bad Request");
					}

					if(intval($_GET['serverplaceid']) != 0 && !$serverplace->gears_enabled && $asset->type == AssetType::GEAR) {
						die(file_get_contents($_SERVER['DOCUMENT_ROOT']."/private/templates/assets/nothing.arlm"));
					}
					
					/*$blacklist = ["MeshId", "Script", "Remote", "Service", "Model"];
					$whitelist = ["Keyframe", "Animation"];
					
					foreach($whitelist as $white) {
						if(strpos($contents, $white) !== false) {
							foreach($blacklist as $black) {
								if(strpos($contents, $black) !== false && (intval($_GET['serverplaceid']) != 0 && $asset->type != AssetType::HAT && $asset->type != AssetType::MODEL && !(intval($_GET['serverplaceid']) == 0 && $asset->type == AssetType::GEAR))) { // hope that model whitelist aint gonna bite my ass
									http_response_code(405);
									die("Method Not Allowed");
								}
							}
						}
					}*/
				}
			}

			set_content_type("application/octet-stream");
			set_attachment($md5hash);
			die($contents);
			
		} else {
			exit_http(404, "Asset not found!");
		}
	} else {
		exit_http(404, "Asset not found!");
	}
?>
