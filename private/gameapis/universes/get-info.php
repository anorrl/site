<?php
use anorrl\Asset;
header('Content-type: application/json');
$assetid = intval($_GET['universeId']);
$asset = Asset::FromID($assetid);

if($asset != null) {

	echo json_encode([
		"TargetId" => $assetid,
		"ProductType" => "User Product",
		"AssetId" => $assetid,
		"ProductId" => $assetid,
		"Name" => $asset->name,
		"Description" => $asset->description,
		"AssetTypeId" => $asset->type->ordinal(),
		"CreatorId" => $asset->creator->id,
		"CreatorName" => $asset->creator->id,
		"IconImageAssetId" => $assetid,
		"Created" => "2015-06-25T20:07:49.147Z",
		"Updated" => "2015-07-11T20:07:51.863Z",
		"PriceInRobux" => 0,
		"PriceInTickets" => 0,
		"Sales" => 0,
		"IsNew" => true,
		"IsForSale" => true,
		"IsPublicDomain" => $asset->public,
		"IsLimited" => false,
		"IsLimitedUnique" => false,
		"Remaining" => null,
		"MinimumMembershipLevel" => 0,
		"ContentRatingTypeId" => 0,
		"GameId" => $asset->id,
		"UniverseId" => $asset->id,
		"PlaceId" => $asset->id,
		"openGameFromPlaceId" => $asset->id,
		"updateFromPlaceId" => $asset->id,
	]);

} else {
	echo "{}";
}

?>