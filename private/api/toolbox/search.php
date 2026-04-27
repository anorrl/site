<?php

	use anorrl\enums\AssetType;
	use anorrl\enums\CatalogFilter;
	use anorrl\utilities\AssetUtils;

	//?keyword=&type=FreeModels&sortBy=Relevance&startOffset=0&resultSize=26&_=1770476003587

	header("Content-Type: application/json");

	$result = [];

	if(
		isset($_GET['keyword']) &&
		isset($_GET['type']) &&
		isset($_GET['sortBy']) &&
		isset($_GET['startOffset']) &&
		isset($_GET['resultSize'])
	) {
		$query = trim($_GET['keyword']);
		$type = $_GET['type'];
		$sort = $_GET['sortBy'];
		$startOffset = intval(trim($_GET['startOffset']));
		$resultSize = intval(trim($_GET['resultSize']));

		$page = $startOffset/$resultSize;

		$filter = match($sort) {
			"Relevance" => CatalogFilter::RecentlyUploaded,
			"MostTaken" => CatalogFilter::MostSold,
			"Favorites" => CatalogFilter::MostFavourited,
			"Updated" => CatalogFilter::RecentlyUpdated,
		};

		$assets = [];

		if($type == "FreeModels") {
			$assets = AssetUtils::GetFiltered($filter, AssetType::MODEL, $query, $page, $resultSize);
		} elseif($type == "FreeDecals") {
			$assets = AssetUtils::GetFiltered($filter, AssetType::DECAL, $query, $page, $resultSize);
		}

		foreach($assets as $asset) {
			if($asset instanceof anorrl\Asset) {
				$result[] = [
					"ID" => $asset->id,
					"AssetID" => $asset->id,
					"Name" => $asset->name,
					"IsVotable" => false
				];
			}
		}
		
	}

	die(json_encode($result));
?>
