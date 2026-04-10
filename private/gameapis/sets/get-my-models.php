<?php
	use anorrl\enums\AssetType;

	$user = SESSION ? SESSION->user : null;

	$result = [];

	if($user != null) {
		$models = $user->GetOwnedAssets(AssetType::MODEL, $_GET['query'] ?? "", true);
		foreach($models as $model) {
			array_push($result,
				[
					"AssetSetID" => 1,
					"AssetTypeID" => $model->type->ordinal(),
					"AssetVersionID" => $model->getVersionID(),
					"ID" => $model->id,
					"Name" => $model->name,
					"SortOrder" => 2147483647,
					"NewerVersionAvailable" => "False",
					"AssetID" => $model->id,
					"IsEndorsed" => false,
					"TotalNumAssetsInSet" => 0
				]
			);
		}

	}
	die(json_encode($result));

?>