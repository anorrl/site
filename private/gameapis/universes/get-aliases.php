<?php
	header("Content-Type: application/json");
	
	echo json_encode([
		"FinalPage" => true,
		"Aliases" => [],
		"PageSize" => 1
	])
?>