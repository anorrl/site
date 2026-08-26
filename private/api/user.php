<?php
	use anorrl\enums\AssetType;
	use anorrl\User;

	set_content_type(ARLTYPEJSON);

	if(isset($_GET['id']) && isset($_GET['request'])) {
		$user = User::FromID(intval($_GET['id']));
		$selfuser = false;
		if($user == null) {
			$user = SESSION ? SESSION->user : null;
			$selfuser = true;
		}

		if($user != null) {
			if($_GET['request'] == "getuserbadges") {
				$page = 1;
				if(isset($_GET['p'])) {
					$page = intval($_GET['p']);
				}
				
				if($page < 1) {
					$id = $user->id;
					redirect("/api/user?id=$id&request=getuserbadges&p=1");
				}
				
				$badges = $user->getOwnedAssets(AssetType::BADGE, "", false, true, [], $page, 12);
				$badges_raw = [];
		
				if(count($badges) != 0) {
					foreach($badges as $asset) {
						if($asset instanceof anorrl\Asset) {
							$badges_raw[] = [
								"id" => $asset->id,
								"name" => $asset->name
							];
						}
					}
				}
		
				die(json_encode(["badges" => $badges_raw, "page" => $page, "total_pages" => floor(count($user->getOwnedAssets(AssetType::BADGE))/12)]));
			}
			else if($_GET['request'] == "isadmin") {
				die(json_encode(["success" => true, 'isadmin' => $user->isAdmin()]));
			}
			
			else {
				die(json_encode(["success" => false, "reason" => "Invalid request"]));
			}
			
		} else {
			die(json_encode(["success" => false, "reason" => "User not found."]));
		}
	} else if(isset($_POST['id']) && isset($_POST['request'])) {
		$user = User::FromID(intval($_POST['id']));
		$selfuser = false;
		if($user == null) {
			$user = SESSION ? SESSION->user : null;
			$selfuser = true;
		}


		if($user != null) {
			
			if($_POST['request'] == "follow" && !$selfuser) {
				$founduser = SESSION ? SESSION->user : null;

				if($founduser != null) {
					if($founduser->id != $user->id) {
						if(!$founduser->isFollowing($user)) {
							$founduser->follow($user);
						} else {
							$founduser->unfollow($user);
						}
						
						die(json_encode(["success" => true]));
					}
				}
			} else if($_POST['request'] == "friend" && !$selfuser) {
				$founduser = SESSION ? SESSION->user : null;

				if($founduser != null) {
					if($founduser->id != $user->id) {
						if($founduser->isFriendsWith($user)) {
							$founduser->unfriend($user);
						} else {
							$founduser->friend($user);
						}
						
						die(json_encode(["success" => true]));
					}
				}
			} else if($_POST['request'] == "unfriend" && !$selfuser) {
				$founduser = SESSION ? SESSION->user : null;

				if($founduser != null) {
					if($founduser->id != $user->id) {
						$founduser->unfriend($user);
						
						die(json_encode(["success" => true]));
					}
				}
			}
		} else {
			die(json_encode(["success" => false, "reason" => "User not found."]));
		}
		 
	}
	
	die(json_encode(["success" => false, "reason" => "Invalid request"]));
?>