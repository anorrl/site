<?php

	use anorrl\User;
	use anorrl\Asset;
	use anorrl\Place;
	use anorrl\enums\AssetType;
	use anorrl\utilities\AssetUploader;
	use anorrl\utilities\ClientDetector;
	use anorrl\utilities\UtilUtils;
	
	if(!SESSION)
		$user = null;
	else
		$user = SESSION->user;

	function FunnyStrToBool(string $value): bool {
		return $value == "True";
	}

	/* thank you weeg <3 */
	function ValidateRoblox_XML(string $XML_Data): bool {
		//FIND BETTER WAY TO DO THIS
		$xml = new DOMDocument();
		$xml->loadXML($XML_Data);

		if(!@$xml->schemaValidate($_SERVER['DOCUMENT_ROOT']."/anorrl.xsd")){
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

	if($user != null || ClientDetector::HasAccess()) {
		if(isset($_GET['assetid'])) {
			$assetid = intval($_GET['assetid']);

			if($assetid == 0 && $user != null) {
				// Publish new item
				
				$timer = 31;
				if($user->getLatestAssetUploaded() != null) {
					$difference = UtilUtils::GetSecondsElapsedFrom($user->getLatestAssetUploaded()->created_at);
					$timer = $difference;
				}

				if($timer < 30) {
					exit_http(501, "You are uploading too many assets! Wait a bit!");
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
					exit_http(502);
					
				} else {
					exit_http(502);
				}

			} else {
				$asset = Asset::FromID(intval($assetid));

				if($asset != null) {
					$recieveddata = file_get_contents("php://input");
					if(is_bool($recieveddata)) {
						exit_http(500, "Something went wrong idfk what complain to grace until she says something");
					}
					if(strlen(gzdecode($recieveddata)) != 0) {
						$recieveddata = gzdecode($recieveddata);
						if(is_bool($recieveddata)) {
							exit_http(500, "You can't just publish an empty place you dumb eejit!");
						}
						error_log("decoding using gz for ".$asset->id);
					}

					if($asset->type == AssetType::PLACE) {
						$place = Place::FromID(intval($assetid));

						if(($user != null && $place->isOwner($user)) || ClientDetector::HasAccess()) {
							// If the user owns this asset, then allow publishing.
							$result = AssetUploader::UpdateAsset($asset, $recieveddata, $asset->creator);

							if($result['error']) {
								exit_http(500, $result['reason']);
							}
							die("Uploaded successfully!");
						} else {
							exit_http(500, "So like you don't own this asset so can you not");
						}
						
					}
					
				}
			}
		}
	} else {
		exit_http(503, "Action failed.");
	}

	exit_http(500, "Action failed.");
?>
