<?php
	set_content_type(ARLTYPEJSON);

	use anorrl\utilities\UserUtils;
	use anorrl\User;

	$page = 1;
	if(isset($_GET['p'])) {
		$page = intval($_GET['p']);
	}

	$query = "";
	if(isset($_GET['q'])) {
		$query = $_GET['q'];
	}

	if($page < 1) {
		redirect("/api/vandals?q=$query&p=1");
		
	}

	$total_pages = floor((count(UserUtils::GetAllUsers($query))/10) + 0.5)+1;

	if(count(UserUtils::GetAllUsersPaged($total_pages, 10, $query)) == 0) {
		$total_pages--;
	}

	if($total_pages <= 0)
		$total_pages = 1;

	if($total_pages < $page && $total_pages > 0) {
		redirect("/api/vandals?q=$query&p=1");
	}

	$users = UserUtils::GetAllUsersPaged($page, 10, $query);

	$users_raw = [];


	if(count($users) != 0) {
		foreach($users as $user) {
			if($user instanceof User) {
				$status = $user->getLatestStatus();
				$blurb = "";
				if($status)
					$blurb = $status->content;
				$users_raw[] = [
					"id" => $user->id,
					"name" => $user->name,
					"blurb" => htmlspecialchars($blurb, ENT_QUOTES),
					"online" => $user->isOnline(),
					"status" => $user->getOnlineActivity(),
					"thumbnail" => $user->getThumbsUrl(64)
				];
			}
		}
	}

	die(json_encode(["users" => $users_raw, "page" => $page, "total_pages" => $total_pages]));

?>
