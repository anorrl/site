<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/assetutils.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/userutils.php";

	$user = UserUtils::RetrieveUser();

	$result = [];

	if($user != null) {
		$models = $user->GetOwnedAssets(AssetType::MODEL, "", true);
		foreach($models as $model) {
			array_push($result,
				[
					"AssetSetID" => 1,
					"AssetTypeID" => $model->type->ordinal(),
					"AssetVersionID" => $model->GetVersionID(),
					"ID" => $model->id,
					"Name" => $model->name,
					"SortOrder" => 2147483647,
					"NewerVersionAvailable" => "False",
					"AssetID" => $model->id,
					"IsEndorsed" => false
				]
			);
		}

	}
	die(json_encode($result));

?>