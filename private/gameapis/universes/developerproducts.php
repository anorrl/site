<?php
	header("Content-Type: application/json");
	
	die(json_encode([
		"DeveloperProducts" => [],
		"FinalPage" => true,
		"PageSize" => 50
	]));
?>