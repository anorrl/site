<?php
	use anorrl\Asset;
	use anorrl\Place;
	use anorrl\enums\AssetType;
	use anorrl\utilities\MeshConverter;

	if(!isset($_GET['id']) && !isset($_GET['ID']) && !isset($_GET['Id'])) {
		die(http_response_code(500));
	}

	if(isset($_GET['id'])) {
		$id = intval($_GET["id"]);
	} else if(isset($_GET['ID'])) {
		$id = intval($_GET["ID"]);
	} else if(isset($_GET['Id'])) {
		$id = intval($_GET["Id"]);
	}

	function checkMimeType($contents) {
		$file_info = new finfo(FILEINFO_MIME_TYPE);
		return $file_info->buffer($contents);
	}

	$access = CONFIG->asset->key;
	$domain = CONFIG->domain;
	
	$user = SESSION ? SESSION->user : null;

	$asset = Asset::FromID($id);
	if($asset != null) {
		$version = isset($_GET['version']) ? intval($_GET['version']) : -1;
		$contents = $asset->getFileContents($version);

		if($contents != null) {
			$md5hash = md5($contents);
			if($asset->type == AssetType::PLACE) {
				$place = Place::FromID($asset->id);
				
				if($place->copylocked) {
					$error = false;
					if($user == null && !isset($_GET['access'])) {
						$error = true;
					} 
					
					if(!$error && (isset($_GET['access']) && trim($_GET['access']) != $access)) {
						$error = true;
					}

					if(!$error && $user != null && $place->creator->id != $user->id && !$user->isAdmin()) {
						$error = true;
					}

					if($error) {
						if(!($_SERVER['HTTP_USER_AGENT'] == "Roblox/WinInet" || $_SERVER['HTTP_USER_AGENT'] == "Roblox/WinHttp"))
							die(http_response_code(503));
					}
				}
			} else{
				if (isset($_GET['serverplaceid'])) {
					$serverplace = Place::FromID(intval($_GET['serverplaceid']));
					
					if ($serverplace == null && intval($_GET['serverplaceid']) != 0) {
						http_response_code(400);
						die("Bad Request");
					}

					if(!$serverplace->gears_enabled && $asset->type == AssetType::GEAR && intval($_GET['serverplaceid']) != 0) {
						die(file_get_contents($_SERVER['DOCUMENT_ROOT']."/private/templates/assets/nothing.rbxm"));
					}
					
					$blacklist = ["MeshId", "Script", "Remote", "Service", "Model"];
					$whitelist = ["Keyframe", "Animation"];
					
					/*foreach($whitelist as $white) {
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

			header("Content-Type: application/octet-stream");
			Header("Content-Disposition: attachment; filename=\"$md5hash\"");
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
				
				$mimetype = checkMimeType($output);
				
				if($mimetype == "application/gzip") {
					$output = gzdecode($output);
					$mimetype = checkMimeType($contents);
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
					header("Content-Type: $mimetype");

					$contents = str_replace("www.roblox.com", $domain, $output);

					if(str_starts_with($contents, "version "))
						$mesh_result = MeshConverter::Convert($contents);
						if(!$mesh_result['error'])
							$contents = $mesh_result['mesh'];

					if(!isset($_GET['version'])) {
						file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id, $contents);
					} else {
						file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id."_".$version, $contents);
					}
				}
				
			} else {
				if($id > 10420) {
					$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id.(isset($_GET['version']) ?  "_".$version : ""));
					$mimetype = checkMimeType($contents);
					
					if($mimetype == "application/gzip") {
						$contents = gzdecode($contents);
						$mimetype = checkMimeType($contents);
					}
					header("Content-Type: $mimetype");
					if(str_contains(checkMimeType($contents), "json")) {
						echo "Unauthorised access to this roblox asset!";
						file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id.(isset($_GET['version']) ?  "_".$version : ""), "");
						die(http_response_code(500));
					}
				} else {
					http_response_code(404);
					die("Asset not found!");
				}
				
			}

			Header('Content-Disposition: attachment; filename="rbx_'.$id.'"');
			echo $contents;	
		
		} else {
			http_response_code(404);
			die("Asset not found!");
		}

		
	}
?>
