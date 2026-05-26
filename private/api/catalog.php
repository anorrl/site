<?php
	set_content_type(ARLTYPEJSON);

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
		redirect("/api/catalog?c=$type&q=$query&p=1");
	}

	$catalog_filter = CatalogFilter::index($filter);
	$asset_type = AssetType::index($type);

	$pre_total_pages = AssetUtils::GetFilteredCount($catalog_filter, $asset_type, $query)/12;

	$uhmbullshitcalc = ((float)((int) $pre_total_pages))-$pre_total_pages;
	if($uhmbullshitcalc < 0.5 && $uhmbullshitcalc != 0) {
		$pre_total_pages += 0.5;
	}

	$total_pages = round($pre_total_pages);

	if($total_pages < 1) {
		$total_pages++;
	}
	
	else {
		if(AssetUtils::GetFilteredCount($catalog_filter, $asset_type, $query, $total_pages, 12) == 0) {
			$total_pages--;
		}
	}

	if($total_pages < $page && $page != 1) {
		redirect("/api/catalog?c=$type&q=$query&p=1");
	}

	$assets = AssetUtils::GetFiltered($catalog_filter, $asset_type, $query, $page, 12);

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
					"onsale" => $asset->onsale,
					"favourites" => $asset->favourites_count,
					"sales_count" => $asset->sales_count,
					"thumbnail" => $asset->getThumbsUrl(130)					
				];
			}
		}
	}
		
	set_encoding("gzip");
	ob_start("ob_gzhandler");
	echo (json_encode(["assets" => $assets_raw, "page" => $page, "total_pages" => $total_pages]));
	ob_end_flush();
?>
