<?php
	use anorrl\Asset;
	use anorrl\Place;
	use anorrl\enums\AssetType;

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

	$settings = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../settings.env", true);

	$access = $settings['asset']['ACCESSKEY'];
	$roblosec = $settings['asset']['ROBLOSEC'];
	
	$user = SESSION ? SESSION->user : null;

	$asset = Asset::FromID($id);
	if($asset != null) {
		$version = isset($_GET['version']) ? intval($_GET['version']) : -1;
		$contents = $asset->GetFileContents($version);

		if($contents != null) {
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

					if(!$error && $user != null && $place->creator->id != $user->id && !$user->IsAdmin()) {
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
			die($contents);
			
		} else {
			die(http_response_code(404));
		}
	} else {
		
		if(CONFIG->asset->canforward) {

			if(isset($_GET['version'])) {
				$version = intval($_GET['version']);
			}

			if(!file_exists($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id.(isset($_GET['version']) ?  "_".$version : ""))) {
				$url = "https://assetdelivery.roblox.com/v1/asset/?id=".$id.(isset($_GET['version']) ? '&version='.$version : "");
				$ch = curl_init ($url);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: .ROBLOSECURITY=$roblosec"));
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				$output = curl_exec($ch);
				curl_close($ch);

				if(strlen(gzdecode($output)) != 0) {
					$output = gzdecode($output);
				}

				$mimetype = checkMimeType($output);

				if(str_contains($mimetype, "json")) {
					$contents = "";

					if(!isset($_GET['version'])) {
						file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id, $contents);
					} else {
						file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id."_".$version, $contents);
					}

					die(http_response_code(500));
				} else {
					header("Content-Type: $mimetype");

					$contents = str_replace("www.roblox.com", "arl.lambda.cam",$output);

					if(!isset($_GET['version'])) {
						file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id, $contents);
					} else {
						file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id."_".$version, $contents);
					}
				}
				
			} else {
				$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id.(isset($_GET['version']) ?  "_".$version : ""));
				header("Content-Type: ".checkMimeType($contents));
				if(str_contains(checkMimeType($contents), "json")) {
					echo "Unauthorised access to this roblox asset!";
					file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/rbx_".$id.(isset($_GET['version']) ?  "_".$version : ""), "");
					die(http_response_code(500));
				}
			}
		
		}

		echo $contents;	
	}
?>
