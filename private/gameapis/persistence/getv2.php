<?php
	// getV2?placeId=331&type=standard&scope=global

	header("Content-Type: application/json");
	http_response_code(501);
	exit(json_encode(["error"=>"Not Implemented"]));
?>
