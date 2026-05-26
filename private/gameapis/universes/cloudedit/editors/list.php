<?php 
	use anorrl\Universe;
	use anorrl\User;
	
	set_content_type(ARLTYPEJSON);
	// dont cache this shit!
	disable_cache();

	if(isset($universeId)) {
		$universe = Universe::FromID(intval($universeId));

		if($universe != null && $universe->teamcreate) {
			$editorusers = $universe->getCloudEditors();

			$editors = [];

			foreach($editorusers as $user) {
				if($user instanceof anorrl\User) {
					if(!$user->isBanned()) {
						$editors[] = [
							"userId" => $user->id,
							"isAdmin" => $universe->isOwner($user)
						];
					}
				}
			}

			die(json_encode([
				"finalPage" => true,
				"users" => $editors
			]));
		}
	}

	echo "{}";
?>