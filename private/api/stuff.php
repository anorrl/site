<?php
	set_content_type(ARLTYPEJSON);

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
		$limit = 12;
		if(isset($_GET['l'])) {
			$limit = intval($_GET['l']);
		}

		$query = "";

		if(isset($_GET['q'])) {
			$query = trim($_GET['q']);
		}

		if($page < 1) {
			redirect("/api/stuff?c=$type&p=1");
		}

		$showcreatoronly = false;

		if(isset($_GET['showcreatoronly'])) {
			$showcreatoronly = true;
		}

		$total_pages = floor($user->getOwnedAssetsCount(AssetType::index($type), $query, $showcreatoronly)/$limit)+1;

		if($total_pages < $page) {
			redirect("/api/stuff?c=$type&p=1&q=$query&l=$limit".($showcreatoronly ? "&showcreatoronly" : ""));
		}

		$assets = $user->getOwnedAssets(AssetType::index($type), $query, $showcreatoronly, true, [], $page, $limit);

		$assets_raw = [];

		if(count($assets) != 0) {
			foreach($assets as $asset) {
				$assets_raw[] = $asset->getStuffResponse();
			}
		}
		
		die(json_encode(["assets" => $assets_raw, "page" => $page, "total_pages" => $total_pages]));
	}
	
?>