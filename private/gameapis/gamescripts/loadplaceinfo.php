<?php ob_start(); ?>
pcall(function() game:SetCreatorID({creator}, Enum.CreatorType.User) end)

pcall(function() game:GetService("SocialService"):SetFriendUrl("http://arl.lambda.cam/Game/LuaWebService/HandleSocialRequest.ashx?method=IsFriendsWith&playerid=%d&userid=%d") end)
pcall(function() game:GetService("SocialService"):SetBestFriendUrl("http://arl.lambda.cam/Game/LuaWebService/HandleSocialRequest.ashx?method=IsBestFriendsWith&playerid=%d&userid=%d") end)
pcall(function() game:GetService("SocialService"):SetGroupUrl("http://arl.lambda.cam/Game/LuaWebService/HandleSocialRequest.ashx?method=IsInGroup&playerid=%d&groupid=%d") end)
pcall(function() game:GetService("SocialService"):SetGroupRankUrl("http://arl.lambda.cam/Game/LuaWebService/HandleSocialRequest.ashx?method=GetGroupRank&playerid=%d&groupid=%d") end)
pcall(function() game:GetService("SocialService"):SetGroupRoleUrl("http://arl.lambda.cam/Game/LuaWebService/HandleSocialRequest.ashx?method=GetGroupRole&playerid=%d&groupid=%d") end)
pcall(function() game:GetService("GamePassService"):SetPlayerHasPassUrl("http://arl.lambda.cam/Game/GamePass/GamePassHandler.ashx?Action=HasPass&UserID=%d&PassID=%d") end)
<?php


	function get_signature($script) {
		$signature = "";
		openssl_sign($script, $signature, file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/../PrivateKey.pem"), OPENSSL_ALGO_SHA1);
		return base64_encode($signature);
	}

	header("Content-Type: text/plain");

	require_once $_SERVER['DOCUMENT_ROOT'].'/core/utilities/assetutils.php';

	if(isset($_GET['PlaceId'])) {
		$place = Place::FromID(intval($_GET['PlaceId']));

		if($place != null && $place instanceof Place) {
			$script = "\r\n" . ob_get_clean();
			$script = str_replace("{creator}", $place->creator->id, $script);
			$signature = get_signature($script);
	
			echo "--rbxsig%". $signature . "%" . $script;
		} else {
			$script = "\r\nprint(\"Not a place hellooooo - Grace\")";
			$signature = get_signature($script);
	
			echo "--rbxsig%". $signature . "%" . $script;
		}
	} else {
		$script = "\r\nprint(\"What were you even trying to do?? - Grace\")";
		$signature = get_signature($script);

		echo "--rbxsig%". $signature . "%" . $script;
	}
	

	
?>