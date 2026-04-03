<?php
	use anorrl\enums\AssetType;

	$user = SESSION ? SESSION->user : null;

	$result = [];

	if($user != null) {
		$decals = $user->GetOwnedAssets(AssetType::DECAL, $_GET['query'] ?? "", true);
		foreach($decals as $decal) {
			array_push($result,
				[
					"AssetSetID" => 1,
					"AssetTypeID" => $decal->type->ordinal(),
					"AssetVersionID" => $decal->GetVersionID(),
					"ID" => $decal->id,
					"Name" => $decal->name,
					"SortOrder" => 2147483647,
					"NewerVersionAvailable" => "False",
					"AssetID" => $decal->id,
					"IsEndorsed" => false
				]
			);
		}

	}
	die(json_encode($result));

?>