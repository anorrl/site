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

	$asset = Asset::FromID($jsonstuff->Asset->Id);
	$alias = Alias::FromID($jsonstuff->TargetId);

	if(!$asset || !$alias)
		exit_http(500);

	if(!$asset->isOwner(SESSION->user))
		exit_http(503);

	if($alias->asset->id != $asset->id)
		exit_http(500);

	$name = $jsonstuff->Name;

	if(!str_contains($name, "/"))
		exit_http(500);

	if(strcmp($name, $alias->name) == 0)
		exit_http(500);

	$alias->renameTo($name)

?>