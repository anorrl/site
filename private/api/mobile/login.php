
<?php 
	use anorrl\utilities\UserUtils;
	
	header("Content-Type: application/json"); 
	
	$user = UserUtils::RetrieveUser();
	if(isset($_POST['username']) && isset($_POST['password'])) {
		$result = UserUtils::LoginUser($_POST['username'], $_POST['password']);
		$user = UserUtils::RetrieveUser();
	}

	$domain = CONFIG->domain;

	if($result["login"] != "Incorrect details provided!") {
		echo json_encode([
			"Status" => "OK", 
			"UserInfo" => [
				"UserID" => $user->id,
				"UserName" => trim($user->name),
				"RobuxBalance" => 69,
				"TicketsBalance" => 420,
				"IsAnyBuildersClubMember" => false,
				"ThumbnailUrl" => "http://{$domain}{$user->getThumbsUrlService("player")}"
			]
		]);
	} else {
		echo json_encode(["Status" => print_r($result)]);
	}

?>