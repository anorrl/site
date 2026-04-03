<?php
	header("Content-Type: application/json");
	
	use anorrl\utilities\SlurUtils;

	try {
		if (isset($_REQUEST['text'])) {
		$filtered = SlurUtils::ProcessText($_REQUEST['text']);
			$response = [
				"success" => true,
				"data" => [
					"white" => $filtered,
					"black" => $filtered
				]
			];
		} else {
			$response = [
				"success" => false,
				"data" => [
					"white" => "ERROR",
					"black" => "ERROR"
				]
			];
		}

		echo json_encode($response);
	} catch (Throwable $e) {
		$errorResponse = [
			"success" => false,
			"data" => [
				"white" => "ERROR",
				"black" => "ERROR"
			]
		];

		echo json_encode($errorResponse);
		exit();
	}
?>
