<?php 

	use anorrl\User;

	header("Content-Type: application/xml;charset=utf-8");
	
	if ($_GET["method"] == "IsBestFriendsWith" || $_GET["method"] == "IsFriendsWith") {
		
		$uID = (int)$_GET['playerid'];
		$friendId = (int)$_GET['userid'];

		$user = User::FromID(intval($_GET['playerid']));
		$userToCheckFriendsWith = User::FromID(intval($_GET['userid']));

		if($user != null && $userToCheckFriendsWith != null && $user->IsFriendsWith($userToCheckFriendsWith)) {
			echo "<Value Type=\"boolean\">true</Value>";
		}else{
			echo '<Value Type="boolean">false</Value>';
		}
	}
	else if ($_GET["method"] == "IsInGroup") {
		$value = 'false';
		if ($_GET['groupid'] == "1200769") {
			
			$user = User::FromID(intval($_GET['playerid']));

			if($user != null) {
				$value = $user->IsAdmin() ? "true" : "false";
			}
		}
		echo '<Value Type="boolean">'.$value.'</Value>';
	} 
	else if ($_GET["method"] == "GetGroupRole") {
		echo 'Guest';
	}
	else if ($_GET["method"] == "GetGroupRank") {
		echo '<Value Type="integer">1</Value>';
	}
?>