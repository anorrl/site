<?php
	header('Content-Type: application/json');
	
	if(!isset($_REQUEST['productId'])) {
		$productId = rand(0,100000);
	} else {
		$productId = (int)$_REQUEST['productId'];
	}
	echo json_encode(array('success' => 'true', 'status' => 'Bought', 'receipt' => $productId));
?>