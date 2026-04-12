<?php
	header("Content-Type: application/json");

	use anorrl\Status;
	use anorrl\utilities\UtilUtils;

	$user = SESSION ? SESSION->user : null;

	if($user != null) {

		$page = 1;
		if(isset($_GET['p'])) {
			$page = intval($_GET['p']);
		}

		if($page < 1) {
			die(header("Location: /api/feeds?p=1"));
		}

		$statuses = Status::GetLatestFeedsPaged($page, 5);

		$statuses_raw = [];

		if(count($statuses) != 0) {
			foreach($statuses as $status) {
				if($status instanceof Status) {
					$statuses_raw[] = [
						"id" => $status->id,
						"poster" => [
							"id" => $status->poster->id,
							"name" => $status->poster->name,
							"thumbnail" => $status->poster->getThumbsUrl(90)
						],
						"content" => $status->content,
						"time_posted" => $status->time_posted->getTimestamp(),
						"time_posted_label" => UtilUtils::GetTimeAgo($status->time_posted)
					];
				}
			}
		}

		die(json_encode(["feed" => $statuses_raw, "page" => $page, "total_pages" => floor(Status::GetLatestFeedsCount()/5)+1]));
	} else {
		die(json_encode(["error" => true, "reason" => "User not logged in."]));
	}
?>