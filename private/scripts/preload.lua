-- Prepended to Edit.lua and Visit.lua --
game:SetPlaceID({placeid})
game:SetUniverseId({universeid})

visit = game:GetService("Visit")

local message = Instance.new("Message")
message.Parent = workspace
message.Archivable = false

settings()["Task Scheduler"].PriorityMethod = Enum.PriorityMethod.AccumulatedError

game:GetService("ScriptInformationProvider"):SetAssetUrl("{scheme}://{domain}/asset/")
game:GetService("ContentProvider"):SetThreadPool(16)
game:GetService("InsertService"):SetFreeModelUrl("{scheme}://{domain}/Game/Tools/InsertAsset.ashx?type=fm&q=%s&pg=%d&rs=%d") -- Used for free model search (insert tool)
game:GetService("InsertService"):SetFreeDecalUrl("{scheme}://{domain}/Game/Tools/InsertAsset.ashx?type=fd&q=%s&pg=%d&rs=%d") -- Used for free decal search (insert tool)

game:GetService("InsertService"):SetBaseSetsUrl("{scheme}://{domain}/Game/Tools/InsertAsset.ashx?nsets=10&type=base")
game:GetService("InsertService"):SetUserSetsUrl("{scheme}://{domain}/Game/Tools/InsertAsset.ashx?nsets=20&type=user&userid=%d")
game:GetService("InsertService"):SetCollectionUrl("{scheme}://{domain}/Game/Tools/InsertAsset.ashx?sid=%d")
game:GetService("InsertService"):SetAssetUrl("{scheme}://{domain}/asset/?id=%d")
game:GetService("InsertService"):SetAssetVersionUrl("{scheme}://{domain}/asset/?assetversionid=%d")

game:GetService("SocialService"):SetFriendUrl("{scheme}://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=IsFriendsWith&playerid=%d&userid=%d")
game:GetService("SocialService"):SetBestFriendUrl("{scheme}://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=IsBestFriendsWith&playerid=%d&userid=%d")
game:GetService("SocialService"):SetGroupUrl("{scheme}://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=IsInGroup&playerid=%d&groupid=%d")
game:GetService("SocialService"):SetGroupRankUrl("{scheme}://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=GetGroupRank&playerid=%d&groupid=%d")
game:GetService("SocialService"):SetGroupRoleUrl("{scheme}://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=GetGroupRole&playerid=%d&groupid=%d")
game:GetService("GamePassService"):SetPlayerHasPassUrl("{scheme}://{domain}/Game/GamePass/GamePassHandler.ashx?Action=HasPass&UserID=%d&PassID=%d")
game:GetService("MarketplaceService"):SetProductInfoUrl("{scheme}://{domain}/marketplace/productinfo?assetId=%d")
game:GetService("MarketplaceService"):SetDevProductInfoUrl("{scheme}://{domain}/marketplace/productDetails?productId=%d")
game:GetService("MarketplaceService"):SetPlayerOwnsAssetUrl("{scheme}://{domain}/ownership/hasasset?userId=%d&assetId=%d")
game:SetCreatorID({creatorid}, Enum.CreatorType.User)
-- workspace:SetPhysicsThrottleEnabled(true)

game:GetService("ChangeHistoryService"):SetEnabled({changehistory})
-- false on visit, true on edit