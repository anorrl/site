<?php

use anorrl\User;
	header("Content-Type: application/json");

	use anorrl\enums\AssetType;
	use anorrl\Asset;
	use anorrl\utilities\Renderer;

	$user = SESSION ? SESSION->user : null;

	function performRender(User $user, string $mediadir, string $charactermd5, bool $headshot = false, bool $is3D = false) {
		$render = Renderer::RenderUser($user->id, false, $is3D);
		if($render != null) {
			$data = base64_decode($render);

			if(!$is3D) {
				$render_image = imagecreatefromstring($data);
				imagesavealpha($render_image, true);
				imagepng($render_image, "$mediadir/$charactermd5.png");
			} else {
				file_put_contents("$mediadir/$charactermd5.json", gzcompress($data));
			}
			
			$user->updateOutfitHash();

			if($headshot && !$is3D) {
				$render = Renderer::RenderUser($user->id, true);

				if($render != null) {
					$data = "data:image/png;base64,$render";
					list($type, $data) = explode(';', $data);
					list(, $data)      = explode(',', $data);
					$data = base64_decode($data);

					$render_image = imagecreatefromstring($data);
					imagesavealpha($render_image, true);
					imagepng($render_image, "$mediadir/headshot_$charactermd5.png");
				}
			}
			
		}
	}

	function sanitizeBodyColourID($rawcolour) {
		$colour = intval($_POST[$rawcolour]);

		$colours = [
			1,
			2,
			3,
			5,
			6,
			9,
			11,
			12,
			18,
			21,
			22,
			23,
			24,
			25,
			26,
			27,
			28,
			29,
			36,
			37,
			38,
			39,
			41,
			42,
			43,
			44,
			45,
			47,
			48,
			49,
			50,
			100,
			101,
			102,
			103,
			104,
			105,
			106,
			107,
			108,
			110,
			111,
			112,
			113,
			115,
			116,
			118,
			119,
			120,
			121,
			123,
			124,
			125,
			126,
			127,
			128,
			131,
			133,
			134,
			135,
			136,
			137,
			138,
			140,
			141,
			143,
			145,
			146,
			147,
			148,
			149,
			150,
			151,
			153,
			154,
			157,
			158,
			168,
			176,
			178,
			179,
			180,
			190,
			191,
			192,
			193,
			194,
			195,
			196,
			198,
			199,
			200,
			208,
			209,
			210,
			211,
			212,
			213,
			217,
			218,
			219,
			220,
			221,
			222,
			223,
			224,
			225,
			226,
			232,
			268,
			321,
			333,
			1001,
			1002,
			1003,
			1004,
			1005,
			1006,
			1007,
			1008,
			1009,
			1010,
			1011,
			1012,
			1013,
			1014,
			1015,
			1016,
			1017,
			1018,
			1019,
			1020,
			1021,
			1022,
			1023,
			1024,
			1025,
			1026,
			1027,
			1028,
			1029,
			1030,
			1031,
			1032
		];
		
		if(!in_array($colour, $colours)) {
			return 24;
		} else {
			return $colour;
		}
	}


	if($user != null) {
		if(isset($_GET['r'])) {
			$request = $_GET['r'];
			if($request == "getwardrobe") {
				$type = AssetType::HAT->ordinal();
				if(isset($_GET['c'])) {
					if($_GET['c'] != "outfits") {
						$type = intval($_GET['c']);
					}
				}
				$page = 1;
				if(isset($_GET['p'])) {
					$page = intval($_GET['p']);
				}

				if($page < 1) {
					die(header("Location: /api/character?r=getwardrobe&c=$type&p=1"));
				}

				// REWRITE.
				if($_GET['c'] != "outfits") {
					$wearing_array = $user->getWearingArray();

					$total_assets = $user->getOwnedAssetsCount(AssetType::index($type), "", false, true, $wearing_array);
					$total_pages = floor($total_assets/8)+1;

					if(count($user->getOwnedAssets(AssetType::index($type), "", false, true, $wearing_array, $total_pages, 8)) == 0 && $page != 1) {
						$total_pages--;
					}

					if($total_pages < $page) {
						die(header("Location: /api/character?r=getwardrobe&c=$type&p=1"));
					}

					$assets = $user->getOwnedAssets(AssetType::index($type), "", false, true, $wearing_array, $page, 8);

					$assets_raw = [];

					if(count($assets) != 0) {
						foreach($assets as $asset) {
							if($asset instanceof anorrl\Asset) {
								$assets_raw[] = [
									"id" => $asset->id,
									"name" => $asset->name,
									"creator" => [
										"id" => $asset->creator->id,
										"name" => $asset->creator->name
									],
									"thumbnail" => $asset->getThumbsUrl(130)
								];
							}
						}
					}
					die(json_encode(["assets" => $assets_raw, "page" => $page, "total_pages" => $total_pages]));
				} else {
					die(json_encode(["assets" => [], "page" => 1, "total_pages" => 1, "comment"=> "Hi, outfits haven't been added yet (congrats on finding this lol)"]));
				}
			} else if($request == "search") {
				// coded by skylerclock
				// rewritten by grace (18/03/2026)
				$query = isset($_GET['q']) ? trim($_GET['q']) : "";
				$category = AssetType::index(intval($_GET['c'])) ?? AssetType::HAT;
				$page = isset($_GET['p']) ? intval($_GET['p']) : 1;
				if($page < 1) $page = 1;
				
				$wearing_array = $user->getWearingArray();
				$all_assets = $user->getOwnedAssets($category, $query, false, true, $wearing_array, $page, 8);
				$total_pages = floor($user->getOwnedAssetsCount($category, $query, false, true, $wearing_array)/8);
				$assets_raw = [];

				foreach($all_assets as $asset) {
					$assets_raw = [
						"id" => $asset->id,
						"name" => $asset->name,
						"creator" => [
							"id" => $asset->creator->id,
							"name" => $asset->creator->name
						],
						"thumbnail" => $asset->getThumbsUrl(130)
					];
				}
				die(json_encode([
					"assets" => $assets_raw,
					"page" => $page,
					"total_pages" => $total_pages
				]));
			} else if($request == "wear" && isset($_POST['assetid'])) {
				$asset = Asset::FromID(intval($_POST['assetid']));

				if($asset != null && $user->owns($asset)) {
					die(json_encode($user->wear($asset)));
				}				
			} else if($request == "remove" && isset($_POST['assetid'])) {
				$asset = Asset::FromID(intval($_POST['assetid']));

				if($asset != null && $user->owns($asset)) {
					die(json_encode($user->takeOff($asset)));
				}				
			} else if($request == "getwearing") {
				$items = $user->getWearing();

				$assets = [];

				foreach($items as $asset) {
					if($asset instanceof anorrl\Asset) {
						$assets[] = [
							"id" => $asset->id,
							"name" => $asset->name,
							"creator" => [
								"id" => $asset->creator->id,
								"name" => $asset->creator->name
							],
							"thumbnail" => $asset->getThumbsUrl(130)
						];
					}
				}

				die(json_encode(["assets" => $assets]));
			} else if($request == "getbodycolours") {
				die(json_encode(["colours" => $user->getBodyColours()]));
			} else if($request == "setbodycolours" &&
				isset($_POST['head']) &&
				isset($_POST['torso']) &&
				isset($_POST['leftarm']) &&
				isset($_POST['rightarm']) &&
				isset($_POST['leftleg']) &&
				isset($_POST['rightleg'])
			) {
				$head = sanitizeBodyColourID('head');
				$torso = sanitizeBodyColourID('torso');
				$leftarm = sanitizeBodyColourID('leftarm');
				$rightarm = sanitizeBodyColourID('rightarm');
				$leftleg = sanitizeBodyColourID('leftleg');
				$rightleg = sanitizeBodyColourID('rightleg');

				$user->setBodyColours($head, $torso, $leftarm, $rightarm, $leftleg, $rightleg);
				die(json_encode(["error" => false]));
			} else if($request == "rendercharacter") {
				
				$mediadir = $_SERVER['DOCUMENT_ROOT']."/../renders/";

				$charactermd5 = $user->getCharacterAppearanceHash();
				
				if(file_exists("$mediadir/$charactermd5.png")) {
					$user->updateOutfitHash();
					die(json_encode(["error" => false]));
				}

				performRender($user, $mediadir, $charactermd5, true);
				performRender($user, $mediadir."3d/", $charactermd5, false, false);
				
				die(json_encode(["error" => false, "reason" => "Wow we rendered!"]));
			} else if($request == "rerendercharacter") {
				$mediadir = $_SERVER['DOCUMENT_ROOT']."/../renders/";

				$charactermd5 = $user->getCharacterAppearanceHash();
				
				performRender($user, $mediadir, $charactermd5, true);
				performRender($user, $mediadir."3d/", $charactermd5, false, false);

				
				die(json_encode(["error" => false, "reason" => "Wow we rendered!"]));
			}
		}
		die(json_encode(["error" => true, "reason" => "Invalid request."]));
	} else {
		die(json_encode(["error" => true, "reason" => "User not logged in."]));
	}
	
?>
