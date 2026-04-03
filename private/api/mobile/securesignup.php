<?php

/*
	[userName] => grace2
	[password] => BALLS123
	[gender] => Female
	[dateOfBirth] => 12/31/2008
	[email] => penis@lambda.cam
*/

	use anorrl\utilities\UserUtils;
	use anorrl\utilities\SlurUtils;

	if(
		isset($_POST['userName']) &&
		isset($_POST['password']) &&
		isset($_POST['gender']) &&
		isset($_POST['dateOfBirth']) &&
		isset($_POST['email'])
	) {
		$username = trim($_POST['userName']);
		$password = trim($_POST['password']);
		$email = trim($_POST['email']); // not actually email but yeah

		if(!str_ends_with($email, "@lambda.cam")) {
			die(json_encode([
				"Status" => "Invalid Characters Used"
			]));
		} else {
			if(UserUtils::IsUsernameValid($username)) {
				$result = UserUtils::RegisterUser($username, $password, $password, str_replace("@lambda.cam", "", $email));

				if($result == "success") {
					die(json_encode([
						"Status" => "OK"
					]));
				} else {
					if(!UserUtils::IsUsernameAvailable($username)) {
						die(json_encode([
							"Status" => "Already Taken"
						]));
					} else if(isset($result['token'])) {
						die(json_encode([
							"Status" => "Invalid Characters Used"
						]));
					}
				}
			} else {
				if(str_contains($username, " ")) {
					die(json_encode([
						"Status" => "Username Cannot Contain Spaces"
					]));
				} else if(str_contains(SlurUtils::ProcessText($username), "#")) {
					die(json_encode([
						"Status" => "Invalid username"
					]));
				} else {
					die(json_encode([
						"Status" => "Invalid Characters Used"
					]));
				}
				
			}

			
		}
	}

?>