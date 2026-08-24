<?php 
	set_content_type(ARLTYPEJSON);
	
	echo json_encode([
		"user" => [
			"id" => 1,
			"name" => "kuro"
		],
		"isBanned" => false
	]);
?>