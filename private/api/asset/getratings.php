<?php
	set_content_type(ARLTYPEJSON);

	die(json_encode([
		"positives" => 0,
		"negatives" => 0,
		"can_vote" => ARLAUTH,
		"has_voted" => false
	]));
?>