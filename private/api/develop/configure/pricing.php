<?php
	use anorrl\Asset;
	
	set_content_type(ARLTYPEJSON);

	$result = ["success" => false, "reason" => "Request failed."];

	if(!ARLAUTH || !isset($id) || !isset($_SESSION['ANORRL$Asset$ID']) || !isset($_POST['ANORRL$EditItem$Price']))
		die(json_encode($result));

	if(intval($_SESSION['ANORRL$Asset$ID']) != $id)
		die(json_encode($result));

	$asset = Asset::FromID($id);

	if(!$asset) {
		$result['reason'] = "Failed to retrieve asset.";
		die(json_encode($result));
	}

	if(!$asset->isOwner(SESSION->user)) {
		$result['reason'] = "You are not authorised to perform this action.";
		die(json_encode($result));
	}

	$price = intval($_POST['ANORRL$EditItem$Price']);
	if($price < 0) {
		$result['reason'] = "Can't have a negative price!!!!";
		die(json_encode($result));
	}

	if($price > 99999) {
		$result['reason'] = "Can't be higher than 99999!!!!";
		die(json_encode($result));
	}

	$on_sale = isset($_POST['ANORRL$EditItem$OnSale']);

	$result = $asset->update([
		"price" => $price,
		"onsale" => $on_sale
	]);

	die(json_encode(["success" => $result]));
?>