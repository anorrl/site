<?php
	set_content_type(ARLTYPEJSON);

	use anorrl\Place;
	use anorrl\enums\AssetType;

	$user = SESSION ? SESSION->user : null;

	if($user != null) {
		if(!isset($_GET['i'])) {
			die(json_encode(["error" => true, "reason" => "No place given."]));
		}

		$place = Place::FromID(intval($_GET['i']));

		if(!$place || ($place && !$place->isOwner($user))) {
			die(json_encode(["error" => true, "reason" => "No place given."]));
		}
		
		$type = AssetType::BADGE->ordinal();
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
			redirect("/api/placestuff?c=$type&p=1&i={$place->id}");
		}

		$asset_type = AssetType::index($type);
		if(!$asset_type) {
			$asset_type = AssetType::BADGE;
		}

		$total_pages = 1;
		$asset = [];
		
		if($asset_type == AssetType::BADGE) {
			$total_pages = floor(count($place->getBadges())/12)+1;
		}

		if($total_pages < $page) {
			redirect("/api/placestuff?c=$type&p=1&q=$query&i={$place->id}");
		}

		if($asset_type == AssetType::BADGE) {
			$assets = $place->getBadges($page, 12);
		}

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
						"thumbnail" => $asset->getThumbsUrl(130),
						"url" => "{$asset->getURL()}"
					];
				}
			}
		}
		
		die(json_encode(["assets" => $assets_raw, "page" => $page, "total_pages" => $total_pages]));
	} else {
		die(json_encode(["error" => true, "reason" => "User not logged in."]));
	}
	
?>