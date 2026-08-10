<?php
	use anorrl\Badge;
	use anorrl\Universe;
	
	if(!SESSION || !isset($universeId) || !isset($_GET['badgeId']))
		exit_http(403);

	$universe = Universe::FromID(intval($universeId));
	$badge = Badge::FromID(intval($_GET['badgeId']));

	if(!$universe || !$badge)
		exit_http(503);

	if(!$universe->isOwner(SESSION->user) || !$badge->isOwner(SESSION->user) || $badge->universe != $universe->id)
		exit_http(403);

	$badge->delete();
?>
