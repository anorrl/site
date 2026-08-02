<?php
	set_content_type(ARLTYPEJSON);

	use anorrl\Place;
	use anorrl\Universe;
	use anorrl\utilities\AssetUtils;
	use anorrl\enums\AssetType;
	use anorrl\enums\CatalogFilter;

	Place::UpdateAllPlaces();

	if(!isset($_GET['placeid'])) {
		$original = false;
		$filter = CatalogFilter::MostPopular->ordinal();
		$page = 1;
		$query = "";
		
		if(isset($_GET['f'])) {
			$filter = intval($_GET['f']);
		}

		if(isset($_GET['p'])) {
			$page = intval($_GET['p']);
		}

		if(isset($_GET['q'])) {
			$query = $_GET['q'];
		}

		if(isset($_GET['o'])) {
			$original = boolval($_GET['o']);
		}

		if($page < 1) {
			redirect("/api/games?q=$query&p=1");
		}

		$catalog_filter = CatalogFilter::index($filter);

		$_SESSION['ANORRL$Games$OriginalOnly'] = $original;

		$retrievedassets = AssetUtils::GetFiltered($catalog_filter, AssetType::PLACE, $query, $page, 9);

		$assets = [];

		if(count($retrievedassets) != 0) {
			foreach($retrievedassets as $asset) {
				if($asset instanceof anorrl\Place) {
					$assets[] = [
						"id" => $asset->id,
						"creator" => [
							"id" => $asset->creator->id,
							"name" => $asset->creator->name
						],
						"name" => $asset->name,
						"url" => $asset->getURL(),
						"favouritescount" => $asset->favourites_count,
						"activeplayers" => $asset->current_playing_count,
						"visits" => $asset->visit_count,
						"original" => Universe::FromID($asset->universe)->original,
						"thumbnail" => $asset->getThumbsUrl(189, 106)
					];
				}
			}
		}
		
		$pre_total_pages = AssetUtils::GetFilteredCount($catalog_filter, AssetType::PLACE, $query)/9;

		if($pre_total_pages < 0.5) {
			$pre_total_pages += 0.5;
		}

		$total_pages = floor($pre_total_pages);

		if($total_pages == 0) {
			$total_pages++;
		}
		else {
			if(AssetUtils::GetFilteredCount($catalog_filter, AssetType::PLACE, $query, $total_pages, 12) == 0) {
				$total_pages--;
			}
		}

		if($total_pages < $page && $total_pages != $page && $page != 1) {
			redirect("/api/games?q=$query&p=1");
		}
		unset($_SESSION['ANORRL$Games$OriginalOnly']);
		echo (json_encode(["games" => $assets, "page" => $page, "total_pages" => $total_pages]));
	} else {
		unset($_SESSION['ANORRL$Games$OriginalOnly']);
		$placeid = intval($_GET['placeid']);

		$place = Place::FromID($placeid);
		if($place == null) {
			die(json_encode(["error" => true, "reason" => "Place not found!"]));
		} else {
			die(json_encode([
				"error" => false,
				"place" => [
					"id" => $place->id,
					"name" => $place->name,
					"description" => $place->description,
					"thumbnail" => $place->getThumbsUrl(300, 169)
				]
			]));
		}
	}	
?>
