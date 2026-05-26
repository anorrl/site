<?php
	use anorrl\Universe;

	set_content_type(ARLTYPEJSON);

	// dont cache this shit!
	disable_cache();

	$universe = Universe::FromID(intval($universeId));


	if($universe != null) {
		echo json_encode([
			"enabled" => $universe->teamcreate
		]);
	} else {
		echo "{}";
	}
?>