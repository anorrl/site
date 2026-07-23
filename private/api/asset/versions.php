<?php 

	use anorrl\Asset;

	$user = SESSION ? SESSION->user : null;

	set_content_type(ARLTYPEJSON);

	$response = ["success" => false, "reason" => "Request failed."];

	function die_out(string $reason = "") {
		global $response;

		if($reason != "") {
			$response["reason"] = $reason;
		}

		die(json_encode($response));
	}

	$page = 1;

	if(isset($_GET['p'])) {
		$page = intval($_GET['p']);
	}

	if($page < 1 || !isset($id)) {
		die_out();
	}

	$asset = Asset::FromID(intval($id));

	if(!$asset) {
		die_out("Failed to retrieve asset.");
	}

	if(!$asset->isOwner($user)) {
		die_out("You are not authorised to perform this action.");
	}

	$pre_total_pages = $asset->getAllVersionsCount()/12;

	$uhmbullshitcalc = ((float)((int) $pre_total_pages))-$pre_total_pages;
	if($uhmbullshitcalc < 0.5 && $uhmbullshitcalc != 0) {
		$pre_total_pages += 0.5;
	}

	$total_pages = round($pre_total_pages);

	if($total_pages < 1) {
		$total_pages++;
	}
	
	else {
		if(count($asset->getAllVersions($total_pages, 10)) == 0) {
			$total_pages--;
		}
	}

	if($total_pages < $page && $page != 1) {
		//redirect("/api/catalog?c=$type&q=$query&p=1");
	}

	$versions = $asset->getAllVersions($page);

	$result = [];

	if(count($versions) != 0) {
		foreach($versions as $version) {
			if($version instanceof anorrl\AssetVersion) {
				$result[] = [
					"id" => $version->id,
					"sub_id" => $version->sub_id,
					"date" => $version->publish_date->format("d/m/Y H:i:s A"),
					"current" => $version->sub_id == $asset->current_version
				];
			}
		}
	}

	unset($response['reason']);

	$response['success'] = true;
	$response['versions'] = $result;
	$response['page'] = $page;
	$response['total_pages'] = $total_pages;

	die(json_encode($response));
?>