<?php 
	require_once $_SERVER["DOCUMENT_ROOT"]."/core/utilities/assetutils.php";
	require_once $_SERVER["DOCUMENT_ROOT"]."/core/utilities/userutils.php";
	require_once $_SERVER["DOCUMENT_ROOT"]."/core/utilities/transactionutils.php";

	$user = UserUtils::RetrieveUser();
	header("Content-Type: application/json");
	if($user != null && !$user->IsBanned() && isset($_POST['asset_id']) && isset($_POST['typatransaction'])) {

		$type = strtolower(trim($_POST['typatransaction']));
		$result = TransactionUtils::BuyItem($_POST['asset_id']);
		if($result != "yay") {
			echo "{ \"error\" : true, \"message\":\"$result\"}";
		} else {
			echo "{ \"error\" : false, \"message\":\"Success!\"}";
		}

		
		die();
	} else {
		echo "{ \"error\" : true, \"message\":\"User is not logged in.\"}";
	}
?>