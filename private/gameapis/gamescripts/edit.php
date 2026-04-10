<?php 
	use anorrl\Place;
	ob_start(); 
?>
-- Prepended to Edit.lua and Visit.lua and Studio.lua--

pcall(function() game:SetPlaceID({placeid}) end)

visit = game:GetService("Visit")

local message = Instance.new("Message")
message.Parent = workspace
message.archivable = false

pcall(function() game:SetUniverseId({placeid}) end)

game:GetService("ContentProvider"):SetThreadPool(16)
pcall(function() game:GetService("InsertService"):SetFreeModelUrl("http://{domain}/Game/Tools/InsertAsset.ashx?type=fm&q=%s&pg=%d&rs=%d") end) -- Used for free model search (insert tool)
pcall(function() game:GetService("InsertService"):SetFreeDecalUrl("http://{domain}/Game/Tools/InsertAsset.ashx?type=fd&q=%s&pg=%d&rs=%d") end) -- Used for free decal search (insert tool)

settings().Diagnostics:LegacyScriptMode()

game:GetService("InsertService"):SetBaseSetsUrl("http://{domain}/Game/Tools/InsertAsset.ashx?nsets=10&type=base")
game:GetService("InsertService"):SetUserSetsUrl("http://{domain}/Game/Tools/InsertAsset.ashx?nsets=20&type=user&userid=%d")
game:GetService("InsertService"):SetCollectionUrl("http://{domain}/Game/Tools/InsertAsset.ashx?sid=%d")
game:GetService("InsertService"):SetAssetUrl("http://{domain}/Asset/?id=%d")
game:GetService("InsertService"):SetAssetVersionUrl("http://{domain}/Asset/?assetversionid=%d")

-- TODO: move this to a text file to be included with other scripts
pcall(function() game:GetService("SocialService"):SetFriendUrl("http://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=IsFriendsWith&playerid=%d&userid=%d") end)
pcall(function() game:GetService("SocialService"):SetBestFriendUrl("http://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=IsBestFriendsWith&playerid=%d&userid=%d") end)
pcall(function() game:GetService("SocialService"):SetGroupUrl("http://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=IsInGroup&playerid=%d&groupid=%d") end)
pcall(function() game:GetService("SocialService"):SetGroupRankUrl("http://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=GetGroupRank&playerid=%d&groupid=%d") end)
pcall(function() game:GetService("SocialService"):SetGroupRoleUrl("http://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=GetGroupRole&playerid=%d&groupid=%d") end)
pcall(function() game:GetService("GamePassService"):SetPlayerHasPassUrl("https://{domain}/Game/GamePass/GamePassHandler.ashx?Action=HasPass&UserID=%d&PassID=%d") end)
pcall(function() game:GetService("MarketplaceService"):SetProductInfoUrl("https://{domain}/marketplace/productinfo?assetId=%d") end)
pcall(function() game:GetService("MarketplaceService"):SetDevProductInfoUrl("https://{domain}/marketplace/productDetails?productId=%d") end)
pcall(function() game:GetService("MarketplaceService"):SetPlayerOwnsAssetUrl("https://{domain}/ownership/hasasset?userId=%d&assetId=%d") end)
pcall(function() game:SetCreatorID({creatorid}, Enum.CreatorType.User) end)

pcall(function() game:SetScreenshotInfo("") end)
pcall(function() game:SetVideoInfo("") end)



message.Text = "Loading Place. Please wait..." 
coroutine.yield() 
game:Load("http://{domain}/Asset/?id={placeid}") 
visit:SetUploadUrl("{uploadurl}")



message.Parent = nil

game:GetService("ChangeHistoryService"):SetEnabled(true)
<?php
	/*
	--visit:SetPing("http://{domain}/Game/ClientPresence.ashx?version=old&PlaceID=1818&LocationType=Studio", 120)
	--game:HttpGet("http://{domain}/Game/Statistics.ashx?UserID=0&AssociatedCreatorID=0&AssociatedCreatorType=User&AssociatedPlaceID=1818")
	*/
	function get_signature($script)
	{
		$signature = "";
		openssl_sign($script, $signature, file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/../PrivateKey.pem"), OPENSSL_ALGO_SHA1);
		return base64_encode($signature);
	}

	header("Content-Type: text/plain");

	$user = SESSION ? SESSION->user : null;
	$domain = CONFIG->domain;

	if($user != null) {
		if(isset($_GET['placeId'])) {
			$place = Place::FromID(intval($_GET['placeId']));

			if($place != null) {
				if($place->creator->id == $user->id || $user->isAdmin() || !$place->copylocked || ($place->teamcreate_enabled && $place->IsCloudEditor($user))) {	
					$script = "\r\n" . ob_get_clean();
				
					$uploadurl = "http://{$domain}/Data/Upload.ashx?assetid=".$place->id;
					if(!$place->copylocked && $place->creator->id != $user->id) {
						$uploadurl = "";
					}

					$script = str_replace("{domain}", $domain, $script);
					$script = str_replace("{placeid}", "".intval($_GET['placeId']), $script);
					$script = str_replace("{uploadurl}", $uploadurl, $script);
					$script = str_replace("{creatorid}", "".$place->creator->id, $script);
					$signature = get_signature($script);
					die("--rbxsig%". $signature . "%" . $script);
					
				}
			}
			
		}
		
	}

	// if like nothing then just not output anything duh.
	ob_clean(); die();
?>