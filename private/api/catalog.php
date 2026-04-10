<?php
	header("Content-Type: application/json");

	use anorrl\utilities\UserUtils;
	use anorrl\utilities\AssetUtils;
	use anorrl\enums\AssetType;
	use anorrl\enums\CatalogFilter;

	$user = UserUtils::RetrieveUser();
	$type = AssetType::HAT->ordinal();
	$filter = CatalogFilter::MostSold->ordinal();
	$query = "";
	$page = 1;

	if(isset($_GET['f'])) {
		$filter = intval($_GET['f']);
	}

	if(isset($_GET['c'])) {
		$type = intval($_GET['c']);
	}

	if(isset($_GET['q'])) {
		$query = $_GET['q'];
	}
	
	if(isset($_GET['p'])) {
		$page = intval($_GET['p']);
	}

	if($page < 1) {
		die(header("Location: /api/catalog?c=$type&q=$query&p=1"));
	}

	$catalog_filter = CatalogFilter::index($filter);
	$asset_type = AssetType::index($type);

	$total_pages = floor((AssetUtils::GetFilteredCount($catalog_filter, $asset_type, $query)/12) + 0.5)+1;

	if(AssetUtils::GetFilteredCount($catalog_filter, $asset_type, $query, $total_pages, 12) == 0) {
		$total_pages--;
	}

	if($total_pages < $page && $page != 1) {
		die(header("Location: /api/catalog?c=$type&q=$query&p=1"));
	}

	$assets = AssetUtils::GetFiltered($catalog_filter, $asset_type, $query, $page, 12);

	$assets_raw = [];

	if(count($assets) != 0) {
		foreach($assets as $asset) {
			if($asset instanceof anorrl\Asset) {
				array_push($assets_raw, [
					"id" => $asset->id,
					"name" => $asset->name,
					"creator" => [
						"id" => $asset->creator->id,
						"name" => $asset->creator->name
					],
					"onsale" => $asset->onsale,
					"favourites" => $asset->favourites_count,
					"sales_count" => $asset->sales_count,
					"cost" => [
						"cones" => $asset->cones,
						"lights" => $asset->lights
					]
					
				]);
			}
		}
	}
		
	header("Content-Encoding: gzip");
	ob_start("ob_gzhandler");
	echo (json_encode(["assets" => $assets_raw, "page" => $page, "total_pages" => $total_pages]));
	ob_end_flush();
?>