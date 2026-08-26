<?php
	use anorrl\Asset;
	use anorrl\Comment;
	use anorrl\utilities\UtilUtils;

	set_content_type(ARLTYPEJSON);

	$result = ["success" => false, "reason" => "Request failed."];

	if(!isset($id))
		die(json_encode($result));

	$asset = Asset::FromID($id);

	if(!$asset) {
		$result['reason'] = "Failed to retrieve asset.";
		die(json_encode($result));
	}

	$page = $_GET['p'] ?? 1;
	$page = intval($page);

	if($page < 1)
		$page = 1;

	$pre_total_pages = Comment::GetCommentCountOn($asset)/5;

	$uhmbullshitcalc = ((float)((int) $pre_total_pages))-$pre_total_pages;
	if($uhmbullshitcalc < 0.5 && $uhmbullshitcalc != 0) {
		$pre_total_pages += 0.5;
	}

	if($uhmbullshitcalc == 0) {
		$pre_total_pages++;
	}

	$total_pages = round($pre_total_pages);

	if($total_pages < 1) {
		$total_pages++;
	}
	
	else {
		if(count(Comment::GetCommentsOn($asset, $total_pages)) == 0) {
			$total_pages--;
		}
	}

	if($total_pages < $page && $page != 1) {
		//redirect("/api/catalog?c=$type&q=$query&p=1");
	}


	$comments = Comment::GetCommentsOn($asset, $page, 5);
	$result = [];
	if(count($comments) != 0) {
		foreach($comments as $comment) {
			if($comment instanceof anorrl\Comment) {
				$response = $comment->getJSON();
				$response["creator"] = $asset->isOwner($this->poster, true);
				$result[] = $response;
			}
		}

	}

	$response = [
		"success" => true,
		"comments" => $result,
		"page" => $page,
		"total_pages" => $total_pages,
		"bullshit" => $uhmbullshitcalc
	];
	die(json_encode($response));

?>
