<?php 
	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/userutils.php";

	$user = UserUtils::RetrieveUser();

	header("Content-Type: application/json");

	if($user != null) {

		if(isset($_POST['create'])) {
			
		}
		else {

		}


	}
	else {
		die(json_encode(
			[
				"error" => true,
				"reason" => "User is not authorised!"
			]
		));
	}

?>