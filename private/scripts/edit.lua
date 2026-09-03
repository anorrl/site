-- Prepended to Edit.lua and Visit.lua and Studio.lua--

pcall(function() game:SetPlaceID({placeid}) end)

visit = game:GetService("Visit")

local message = Instance.new("Message")
message.Parent = workspace
message.archivable = false

pcall(function() game:SetUniverseId({universeid}) end)

game:GetService("ContentProvider"):SetThreadPool(16)
pcall(function() game:GetService("InsertService"):SetFreeModelUrl("{scheme}://{domain}/Game/Tools/InsertAsset.ashx?type=fm&q=%s&pg=%d&rs=%d") end) -- Used for free model search (insert tool)
pcall(function() game:GetService("InsertService"):SetFreeDecalUrl("{scheme}://{domain}/Game/Tools/InsertAsset.ashx?type=fd&q=%s&pg=%d&rs=%d") end) -- Used for free decal search (insert tool)

settings().Diagnostics:LegacyScriptMode()

game:GetService("InsertService"):SetBaseSetsUrl("{scheme}://{domain}/Game/Tools/InsertAsset.ashx?nsets=10&type=base")
game:GetService("InsertService"):SetUserSetsUrl("{scheme}://{domain}/Game/Tools/InsertAsset.ashx?nsets=20&type=user&userid=%d")
game:GetService("InsertService"):SetCollectionUrl("{scheme}://{domain}/Game/Tools/InsertAsset.ashx?sid=%d")
game:GetService("InsertService"):SetAssetUrl("{scheme}://{domain}/asset/?id=%d")
game:GetService("InsertService"):SetAssetVersionUrl("{scheme}://{domain}/asset/?assetversionid=%d")

-- TODO: move this to a text file to be included with other scripts
pcall(function() game:GetService("SocialService"):SetFriendUrl("{scheme}://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=IsFriendsWith&playerid=%d&userid=%d") end)
pcall(function() game:GetService("SocialService"):SetBestFriendUrl("{scheme}://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=IsBestFriendsWith&playerid=%d&userid=%d") end)
pcall(function() game:GetService("SocialService"):SetGroupUrl("{scheme}://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=IsInGroup&playerid=%d&groupid=%d") end)
pcall(function() game:GetService("SocialService"):SetGroupRankUrl("{scheme}://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=GetGroupRank&playerid=%d&groupid=%d") end)
pcall(function() game:GetService("SocialService"):SetGroupRoleUrl("{scheme}://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=GetGroupRole&playerid=%d&groupid=%d") end)
pcall(function() game:GetService("GamePassService"):SetPlayerHasPassUrl("{scheme}://{domain}/Game/GamePass/GamePassHandler.ashx?Action=HasPass&UserID=%d&PassID=%d") end)
pcall(function() game:GetService("MarketplaceService"):SetProductInfoUrl("{scheme}://{domain}/marketplace/productinfo?assetId=%d") end)
pcall(function() game:GetService("MarketplaceService"):SetDevProductInfoUrl("{scheme}://{domain}/marketplace/productDetails?productId=%d") end)
pcall(function() game:GetService("MarketplaceService"):SetPlayerOwnsAssetUrl("{scheme}://{domain}/ownership/hasasset?userId=%d&assetId=%d") end)
pcall(function() game:SetCreatorID({creatorid}, Enum.CreatorType.User) end)

pcall(function() game:SetScreenshotInfo("") end)
pcall(function() game:SetVideoInfo("") end)

message.Text = "Loading Place. Please wait..." 
coroutine.yield() 
game:Load("arlassetid://{placeid}") 
visit:SetUploadUrl("{uploadurl}")

message.Parent = nil

game:GetService("ChangeHistoryService"):SetEnabled(true)