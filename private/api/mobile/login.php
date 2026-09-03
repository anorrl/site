
<?php 
	use anorrl\Session;
	
	set_content_type(ARLTYPEJSON); 
	
	if(isset($_POST['username']) && isset($_POST['password'])) {
		$result = Session::login($_POST['username'], $_POST['password']);
		$user = Session::retrieveUser();	

		$domain = CONFIG->domain;
		$scheme = CONFIG->prefer_https ? "https" : "http";

		if($result["success"]) {
			echo json_encode([
				"Status" => "OK", 
				"UserInfo" => [
					"UserID" => $user->id,
					"UserName" => trim($user->name),
					"RobuxBalance" => 69,
					"TicketsBalance" => 420,
					"IsAnyBuildersClubMember" => false,
					"ThumbnailUrl" => "{$scheme}://{$domain}{$user->getThumbsUrlService("player")}"
				]
			]);
		} else {
			echo json_encode(["Status" => print_r($result)]);
		}
	}

?>