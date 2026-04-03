<?php

	use anorrl\utilities\UserUtils;
	use anorrl\utilities\SlurUtils;

	$isValid = false;
	$errorCode = 2;
	$errorMessage = "Unknown error!";

	if(isset($_GET['username'])) {
		$username = trim($_GET['username']);

		if(UserUtils::IsUsernameValid($username)) {
			$filtered_username = SlurUtils::ProcessText($username);

			if(str_contains($filtered_username, "#") || !UserUtils::IsUsernameValid($filtered_username)) {
				$errorMessage = "Username not appropriate for ANORRL";
			} else {
				if(UserUtils::IsUsernameAvailable($filtered_username)) {
					$isValid = true;
					$errorCode = 0;
					$errorMessage = "";
				} else {
					$errorCode = 1;
					$errorMessage = "This username is already in use.";
				}
			}
		} else {
			$errorMessage = "Username must be a-z A-Z 0-9 and 3-20 characters only!";
		}	
	}


	die(json_encode([
		"IsValid" => $isValid,
		"ErrorCode" => $errorcode,
		"ErrorMessage" => $errorMessage
	]));

?>