<?php

	use anorrl\Alias;
	use anorrl\Universe;

	if(!SESSION || !isset($_GET['universeId']) || !isset($_GET['name']))
		exit_http(403);

	$universe = Universe::FromID(intval($_GET['universeId']));

	if(!$universe)
		exit_http(503);

	if(!$universe->hasAccess(SESSION->user))
		exit_http(503);

	if(!str_contains($_GET['name'], "/"))
		exit_http(500);

	$name = $_GET['name']; //urldecode($_GET['name']);

	$alias = Alias::FromName($universe, $name);

	if(!$alias)
		exit_http(500);

	$alias->delete();
?>