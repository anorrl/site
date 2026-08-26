<?php
	set_content_type(ARLTYPEJSON);

	use anorrl\enums\AssetType;

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
	
	if(isset($_GET['r']) && SESSION) {
		
		$request = $_GET['r'];
		$user = SESSION->user;
		
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
				redirect("/api/character?r=getwardrobe&c=$type&p=1");
			}

			// REWRITE.
			if($_GET['c'] != "outfits") {
				$wearing_array = $user->getWearingArray();

				$pre_total_pages_v2 = $user->getOwnedAssetsCount(AssetType::index($type), "", false, true, $wearing_array)/8;
				$pre_total_pages = $pre_total_pages_v2;
				$uhmbullshitcalc = ((float)((int) $pre_total_pages))-$pre_total_pages;
				if($uhmbullshitcalc < 0.5 && $uhmbullshitcalc != 0) {
					$pre_total_pages += 0.5;
				}

				$total_pages = round($pre_total_pages);

				if($total_pages < 1) {
					$total_pages++;
				}

				if($total_pages < $page) {
					redirect("/api/character?r=getwardrobe&c=$type&p=1");
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
				die(json_encode([
					"assets" => $assets_raw,
					"page" => $page,
					"total_pages" => $total_pages
				]));
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

			$pre_total_pages = $user->getOwnedAssetsCount($category, $query, false, true, $wearing_array)/8;

			if(((int) $pre_total_pages)-$pre_total_pages < 0.5) {
				$pre_total_pages += 0.5;
			}

			$total_pages = floor($pre_total_pages);
			
			if($total_pages < 1) {
				$total_pages++;
			}

			$assets_raw = [];

			foreach($all_assets as $asset) {
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
			die(json_encode([
				"assets" => $assets_raw,
				"page" => $page,
				"total_pages" => $total_pages
			]));
		} else if($request == "wear" && isset($_POST['assetid'])) {
			$asset = intval($_POST['assetid']);

			if($user->owns($asset)) {
				die(json_encode($user->wear($asset)));
			}				
		} else if($request == "remove" && isset($_POST['assetid'])) {
			$asset = intval($_POST['assetid']);

			if($user->owns($asset)) {
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
			die(json_encode(["success" => true]));
		} else if(str_ends_with($request, "rendercharacter")) {
			
			if(!str_starts_with($request, "rere") && get_user_render_path($user->getCharacterAppearanceHash(), ARLRENDER)) {
				$user->updateOutfitHash();
				die(json_encode(["success" => true]));
			}
			
			$user->render(false);
			
			die(json_encode(["success" => true, "reason" => "Wow we rendered!"]));
		}
	}
	die(json_encode(["success" => false, "reason" => "Invalid request."]));
?>
