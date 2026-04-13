<?php

	use anorrl\User;
	use anorrl\Asset;
	use anorrl\Place;
	use anorrl\enums\AssetType;
	use anorrl\utilities\AssetUploader;
	
	$access = CONFIG->asset->key;
	
	$user = SESSION->user;

	function FunnyStrToBool(string $value): bool {
		return $value == "True";
	}

	/* thank you weeg <3 */
	function ValidateRoblox_XML(string $XML_Data): bool {
		//FIND BETTER WAY TO DO THIS
		$xml = new DOMDocument();
		$xml->loadXML($XML_Data);

		if(!@$xml->schemaValidate($_SERVER['DOCUMENT_ROOT']."/roblox.xsd")){
			//throw new Exception("Invalid LEGACY ROBLOX XML Format file");
			return false;
		}else{
			//echo "Valid XML File<br>";
			return true;
		}
	}

	if($user == null) {
		if(isset($_GET['security'])) {
			$user = User::FromSecurityKey(urldecode($_GET['security']));
		}
	}

	if($user != null || (isset($_GET['access']) && $_GET['access'] == $access)) {
		if(isset($_GET['assetid'])) {
			$assetid = intval($_GET['assetid']);

			if($assetid == 0 && $user != null) {
				// Publish new item

				$timer = 31;
				if($user->getLatestAssetUploaded() != null) {
					$difference = (time()-($user->getLatestAssetUploaded()->created_at->getTimestamp()-3600));
					$timer = $difference;
				}

				if($timer < 30) {
					http_response_code(501);
					die("You are uploading too many assets! Wait a bit!");
				}

				/*
					type
					name
					description
					ispublic
					commentsenabled
					serversize
					iscopylocked
				*/

				if(
					isset($_GET['type']) &&
					isset($_GET['name']) &&
					isset($_GET['description']) &&
					isset($_GET['ispublic']) &&
					isset($_GET['commentsenabled'])
				) {
					$type = $_GET['type'];
					$name = urldecode($_GET['name']);
					$description = urldecode($_GET['description']);
					$public = FunnyStrToBool($_GET['ispublic']);
					$comments_enabled = FunnyStrToBool($_GET['commentsenabled']);

					$recieveddata = file_get_contents("php://input");
					//echo "parsed:".$recieveddata;
					if(strlen(gzdecode($recieveddata)) != 0) {
						$recieveddata = gzdecode($recieveddata);
						echo "decoding using gz\n";
					}
					die(http_response_code(502));
					
				} else {
					die(http_response_code(502));
				}

			} else {
				$asset = Asset::FromID(intval($assetid));

				if($asset != null) {
					$recieveddata = file_get_contents("php://input");
					if(is_bool($recieveddata)) {
						http_response_code(500);
						error_log("Something went wrong idfk what complain to grace until she says something");
						die("Something went wrong idfk what complain to grace until she says something");
					}
					if(strlen(gzdecode($recieveddata)) != 0) {
						$recieveddata = gzdecode($recieveddata);
						if(is_bool($recieveddata)) {
							http_response_code(500);
							error_log("You can't just publish an empty place you dumb eejit!");
							die("You can't just publish an empty place you dumb eejit!");
						}
						error_log("decoding using gz for ".$asset->id);
					}

					if($asset->type == AssetType::PLACE) {
						$place = Place::FromID(intval($assetid));

						if(($user != null && $asset->creator->id == $user->id) || ($place->teamcreate_enabled && (($user != null && $place->isCloudEditor($user))  || (isset($_GET['access']) && $_GET['access'] == $access)))) {
							// If the user owns this asset, then allow publishing.
							$result = AssetUploader::UpdateAsset($asset, $recieveddata, $asset->creator);

							if($result['error']) {
								http_response_code(500);
								error_log($result['reason']);
								die($result['reason']);
							}
							http_response_code(200);
							die("Uploaded successfully!");
						} else {
							http_response_code(500);
							error_log("So like you don't own this asset so can you not");
							die("So like you don't own this asset so can you not");
						}
						
					}
					
				}
			}
		}
	} else {
		http_response_code(503);
		die("Action failed.");
	}

	http_response_code(500);
	error_log("Action failed.");
	die("Action failed.");

?>