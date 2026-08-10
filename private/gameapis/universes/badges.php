<?php
	use anorrl\Universe;
	
	set_content_type(ARLTYPEJSON);

	$universe = Universe::FromID(intval($universeId));
	$user = SESSION->user;

	if($universe && $user && $universe->isOwner($user)) {
		$badges = [];

		foreach($universe->getBadges() as $badge) {
			$badges[] = [
				"BadgeId" => $badge->id,
				"Name" => $badge->name,
				"Secret" => $badge->secret,
				"Thumbnail" => [
					"Url" => $badge->getThumbsUrl()
				]
			];
		}

		// thanks cubp
		die(json_encode([
			"GameBadges" => $badges,
			"FinalPage" => true,
		]));
	}
?>
