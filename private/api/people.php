<?php
	header("Content-Type: application/json");

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
		die(header("Location: /api/people?q=$query&p=1"));
	}

	$total_pages = floor((count(UserUtils::GetAllUsers($query))/10) + 0.5)+1;

	if(count(UserUtils::GetAllUsersPaged($total_pages, 10, $query)) == 0) {
		$total_pages--;
	}

	if($total_pages < $page) {
		die(header("Location: /api/people?q=$query&p=1"));
	}

	$users = UserUtils::GetAllUsersPaged($page, 10, $query);

	$users_raw = [];


	if(count($users) != 0) {
		foreach($users as $user) {
			if($user instanceof User) {
				$profile = ($user->setprofilepicture ? "profile":"headshot");

				if(SESSION->settings->headshots_enabled) {
					$profile = "headshot";
				}

				array_push($users_raw, [
					"id" => $user->id,
					"name" => $user->name,
					"blurb" => htmlspecialchars($user->blurb, ENT_QUOTES),
					"online" => $user->IsOnline(),
					"status" => $user->GetOnlineActivity(),
					"thumbnail" => "/thumbs/$profile?id=".$user->id."&sxy=64"
				]);
			}
		}
	}

	die(json_encode(["users" => $users_raw, "page" => $page, "total_pages" => $total_pages]));

?>