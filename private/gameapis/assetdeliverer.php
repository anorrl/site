<?php
	use anorrl\Alias;
	use anorrl\Asset;
	use anorrl\Place;
	use anorrl\Universe;
	use anorrl\enums\AssetType;
	use anorrl\utilities\MeshConverter;
	use anorrl\utilities\ClientDetector;
	use anorrl\utilities\ImageUtils;

	if(!isset($_GET['id']) && !isset($_GET['ID']) && !isset($_GET['Id']) && !isset($_GET['assetName']) && !isset($_GET['universeId'])) {
		die(http_response_code(500));
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
							die(http_response_code(403));
					}
				}
			} else{
				// might break who knows
				if (isset($_GET['serverplaceid']) && isset($_GET['clientinsert'])) {
					$serverplace = Place::FromID(intval($_GET['serverplaceid']));
					
					if ($serverplace == null && intval($_GET['serverplaceid']) != 0) {
						http_response_code(400);
						die("Bad Request");
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
			// quick hack workaround, not gonna be in release.
			if(str_contains($contents, "<roblox")) {
				$contents = str_replace("<roblox", "<anorrl", $contents);
				$contents = str_replace("roblox>", "anorrl>", $contents);
				$contents = str_replace("rbxasset", "arlasset", $contents);
			}

			set_content_type("application/octet-stream");
			set_attachment($md5hash);
			die($contents);
			
		} else {
			http_response_code(404);
			die("Asset not found!");
		}
	} else {
		$roblosec = CONFIG->asset->roblosec;
		if(CONFIG->asset->canforward && strlen(trim($roblosec)) != 0) {
			

			if(isset($_GET['version'])) {
				$version = intval($_GET['version']);
			}

			if(!file_exists($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id.(isset($_GET['version']) ?  "_".$version : ""))) {
				$url = "https://assetdelivery.roblox.com/v1/asset/?id=".$id.(isset($_GET['version']) ? '&version='.$version : "");
				$ch = curl_init ($url);
				curl_setopt($ch, CURLOPT_HTTPHEADER, ["Cookie: .ROBLOSECURITY=$roblosec"]);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				$output = curl_exec($ch);
				curl_close($ch);
				
				$mimetype = ImageUtils::checkMimeType($output);
				
				if($mimetype == "application/gzip") {
					$output = gzdecode($output);
					$mimetype = ImageUtils::checkMimeType($output);
				}
				
				if(str_contains($mimetype, "json")) {
					$contents = "";

					if(!isset($_GET['version'])) {
						file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id, $contents);
					} else {
						file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id."_".$version, $contents);
					}

					echo "Unauthorised access to this roblox asset!";
					die(http_response_code(500));
				} else {
					set_content_type($mimetype);

					$contents = str_replace("www.roblox.com", $domain, $output);

					if(str_starts_with($contents, "version ")) {
						$mesh_result = MeshConverter::Convert($contents);
						if($mesh_result && !$mesh_result['error'])
							$contents = $mesh_result['mesh'];
						// todo: do something with $mesh_result['reason']
					}
					
					if(!isset($_GET['version'])) {
						file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id, $contents);
					} else {
						file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id."_".$version, $contents);
					}
				}
				
			} else {
				if($id > 10420) {
					$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id.(isset($_GET['version']) ?  "_".$version : ""));
					$mimetype = ImageUtils::checkMimeType($contents);
					
					if($mimetype == "application/gzip") {
						$contents = gzdecode($contents);
						$mimetype = ImageUtils::checkMimeType($contents);
					}
					set_content_type($mimetype);
					if(str_contains(ImageUtils::checkMimeType($contents), "json")) {
						echo "Unauthorised access to this roblox asset!";
						file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id.(isset($_GET['version']) ?  "_".$version : ""), "");
						die(http_response_code(500));
					}
				} else {
					http_response_code(404);
					die("Asset not found!");
				}
				
			}

			set_attachment("rbx_{$id}");

			if(str_contains($contents, "<roblox")) {
                                $contents = str_replace("<roblox", "<anorrl", $contents);
                                $contents = str_replace("roblox>", "anorrl>", $contents);
                                $contents = str_replace("rbxasset", "arlasset", $contents);
                        }

			echo $contents;
		
		} else {
			if(!file_exists($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id.(isset($_GET['version']) ?  "_".$version : ""))) {
				http_response_code(404);
				die("Asset not found!");
			} else {
				$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id.(isset($_GET['version']) ?  "_".$version : ""));
				$mimetype = ImageUtils::checkMimeType($contents);
				
				if($mimetype == "application/gzip") {
					$contents = gzdecode($contents);
					$mimetype = ImageUtils::checkMimeType($contents);
				}
				set_content_type($mimetype);
				if(str_contains(ImageUtils::checkMimeType($contents), "json")) {
					echo "Unauthorised access to this roblox asset!";
					file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id.(isset($_GET['version']) ?  "_".$version : ""), "");
					die(http_response_code(500));
				}

				echo $contents;
			}
			
		}

		
	}
?>