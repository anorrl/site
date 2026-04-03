<?php
	header("Content-Type: application/json");
	http_response_code(501);
	exit(json_encode(["error"=>"Not Implemented"]));
?>
