<?php
	header("Content-Type: application/json");

	use anorrl\enums\AssetType;

	$user = SESSION ? SESSION->user : null;

	if($user != null) {

		
		$type = AssetType::HAT->ordinal();
		if(isset($_GET['c'])) {
			$type = intval($_GET['c']);
		}
		$page = 1;
		if(isset($_GET['p'])) {
			$page = intval($_GET['p']);
		}

		$query = "";

		if(isset($_GET['q'])) {
			$query = trim($_GET['q']);
		}

		if($page < 1) {
			die(header("Location: /api/stuff?c=$type&p=1"));
		}

		$showcreatoronly = false;

		if(isset($_GET['showcreatoronly'])) {
			$showcreatoronly = true;
		}

		$total_pages = floor($user->getOwnedAssetsCount(AssetType::index($type), $query, $showcreatoronly)/12)+1;

		if($total_pages < $page) {
			die(header("Location: /api/stuff?c=$type&p=1&q=$query"));
		}

		$assets = $user->getOwnedAssets(AssetType::index($type), $query, $showcreatoronly, true, [], $page, 12);

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
		die(json_encode(["error" => true, "reason" => "User not logged in."]));
	}
	
?>