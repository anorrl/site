<?php
	use anorrl\User;
	use anorrl\Comment;
	use anorrl\utilities\UtilUtils;

	set_content_type(ARLTYPEJSON);

	$result = ["success" => false, "reason" => "Request failed."];

	if(!isset($id))
		die(json_encode($result));

	$user = User::FromID($id);

	if(!$user) {
		$result['reason'] = "Failed to find user.";
		die(json_encode($result));
	}

	$page = $_GET['p'] ?? 1;
	$page = intval($page);

	if($page < 1)
		$page = 1;

	$pre_total_pages = Comment::GetCommentCountOn($user)/10;

	$uhmbullshitcalc = ((float)((int) $pre_total_pages))-$pre_total_pages;
	if($uhmbullshitcalc < 0.5 && $uhmbullshitcalc != 0) {
		$pre_total_pages += 0.5;
	}

	$total_pages = round($pre_total_pages);

	if($total_pages < 1) {
		$total_pages++;
	}
	
	else {
		if(count(Comment::GetCommentsOn($user, $total_pages)) == 0) {
			$total_pages--;
		}
	}

	if($total_pages < $page && $page != 1) {
		//redirect("/api/catalog?c=$type&q=$query&p=1");
	}


	$comments = Comment::GetCommentsOn($user, $page);
	$result = [];
	
	if(count($comments) != 0) {
		foreach($comments as $comment) {
			if($comment instanceof anorrl\Comment) {
				$result[] = [
					"id" => $comment->id,
					"creator" => [
						"id" => $comment->poster->id,
						"name" => $comment->poster->name,
					],
					"contents" => $comment->contents,
					"date" => UtilUtils::GetTimeAgo($comment->postdate)
				];
			}
		}
	}

	$response = [
		"success" => true,
		"comments" => $result,
		"page" => $page,
		"total_pages" => $total_pages
	];
	die(json_encode($response));

?>
