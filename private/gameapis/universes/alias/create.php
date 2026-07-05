<?php

	use anorrl\Alias;
	use anorrl\Universe;
	use anorrl\Asset;

	if(!SESSION || !isset($_GET['universeId']))
		exit_http(403);

	$universe = Universe::FromID(intval($_GET['universeId']));

	if(!$universe)
		exit_http(503);

	if(!$universe->hasAccess(SESSION->user))
		exit_http(503);

	$jsonstuff = json_decode(file_get_contents("php://input"));

	if(!$jsonstuff)
		exit_http(500);

	$assetid = $jsonstuff->AssetId;

	$asset = Asset::FromID($assetid);

	if(!$asset)
		exit_http(500);

	if(!$asset->isOwner(SESSION->user))
		exit_http(503);

	$asset->setUniverse($universe);

	$alias_name = str_contains($jsonstuff->Name, "%") ? urldecode($jsonstuff->Name) : $jsonstuff->Name;
	
	if($asset->getAssetIDSafe() == $asset->id)
		$new_asset = $asset;
	else
		$new_asset = Asset::FromID($asset->getAssetIDSafe());
	

	Alias::Create($universe, $new_asset, $alias_name);
?>