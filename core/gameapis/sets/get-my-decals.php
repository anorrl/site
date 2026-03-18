<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/assetutils.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/userutils.php";

	$user = UserUtils::RetrieveUser();

	$result = [];

	if($user != null) {
		$decals = $user->GetOwnedAssets(AssetType::DECAL, "", true);
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