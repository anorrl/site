-- Prepended to Edit.lua and Visit.lua and Studio.lua and PlaySolo.lua--

-- maybe try find the placeid and creatorid?

local placeID = 0
local creatorID = 0

pcall(function() game:SetPlaceID(placeID) end)

visit = game:GetService("Visit")

local message = Instance.new("Message")
message.Parent = workspace
message.archivable = false

game:GetService("ScriptInformationProvider"):SetAssetUrl("http://{domain}/Asset/")
game:GetService("ContentProvider"):SetThreadPool(16)
pcall(function() game:GetService("InsertService"):SetFreeModelUrl("http://{domain}/Game/Tools/InsertAsset.ashx?type=fm&q=%s&pg=%d&rs=%d") end) -- Used for free model search (insert tool)
pcall(function() game:GetService("InsertService"):SetFreeDecalUrl("http://{domain}/Game/Tools/InsertAsset.ashx?type=fd&q=%s&pg=%d&rs=%d") end) -- Used for free decal search (insert tool)

settings().Diagnostics:LegacyScriptMode()

game:GetService("InsertService"):SetBaseSetsUrl("http://{domain}/Game/Tools/InsertAsset.ashx?nsets=10&type=base")
game:GetService("InsertService"):SetUserSetsUrl("http://{domain}/Game/Tools/InsertAsset.ashx?nsets=20&type=user&userid=%d")
game:GetService("InsertService"):SetCollectionUrl("http://{domain}/Game/Tools/InsertAsset.ashx?sid=%d")
game:GetService("InsertService"):SetAssetUrl("http://{domain}/Asset/?id=%d")
game:GetService("InsertService"):SetAssetVersionUrl("http://{domain}/Asset/?assetversionid=%d")

pcall(function() game:GetService("SocialService"):SetFriendUrl("http://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=IsFriendsWith&playerid=%d&userid=%d") end)
pcall(function() game:GetService("SocialService"):SetBestFriendUrl("http://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=IsBestFriendsWith&playerid=%d&userid=%d") end)
pcall(function() game:GetService("SocialService"):SetGroupUrl("http://{domain}/Game/LuaWebService/HandleSocialRequest.ashx?method=IsInGroup&playerid=%d&groupid=%d") end)
pcall(function() game:SetCreatorID(creatorID, Enum.CreatorType.User) end)

pcall(function() game:SetScreenshotInfo("") end)
pcall(function() game:SetVideoInfo("") end)

pcall(function() settings().Rendering.EnableFRM = true end)
pcall(function() settings()["Task Scheduler"].PriorityMethod = Enum.PriorityMethod.AccumulatedError end)

game:GetService("ChangeHistoryService"):SetEnabled(false)
pcall(function() game:GetService("Players"):SetBuildUserPermissionsUrl("http://{domain}//Game/BuildActionPermissionCheck.ashx?assetId=0&userId=%d&isSolo=true") end)
pcall(function() game:GetService("Players"):SetChatStyle(Enum.ChatStyle.ClassicAndBubble) end)

workspace:SetPhysicsThrottleEnabled(true)

local addedBuildTools = false
local screenGui = game:GetService("CoreGui"):FindFirstChild("RobloxGui")

function doVisit()
	message.Text = "Loading Game"
	pcall(function() visit:SetUploadUrl("") end)

	message.Text = "Running"
	game:GetService("RunService"):Run()

	message.Text = "Creating Player"
	player = game:GetService("Players"):CreateLocalPlayer({userid})
	pcall(function() player.Name = "{username}" end)
	player.CharacterAppearance = "http://{domain}/Asset/CharacterFetch.ashx?userId={userid}&placeId=0"
	local propExists, canAutoLoadChar = false
	propExists = pcall(function()  canAutoLoadChar = game.Players.CharacterAutoLoads end)
	
	if (propExists and canAutoLoadChar) or (not propExists) then
		player:LoadCharacter()
	end

	message.Text = "Setting GUI"
	player:SetSuperSafeChat(false)
	pcall(function() player:SetMembershipType(Enum.MembershipType.None) end)
	pcall(function() player:SetAccountAge({accountage}) end)
end

success, err = pcall(doVisit)

if not addedBuildTools then
	local playerName = Instance.new("StringValue")
	playerName.Name = "PlayerName"
	playerName.Value = player.Name
	playerName.RobloxLocked = true
	playerName.Parent = screenGui
				
	pcall(function() game:GetService("ScriptContext"):AddCoreScript(59431535,screenGui,"BuildToolsScript") end)
	addedBuildTools = true
end

if success then
	pcall(function() warn("PLEASE DON'T USE THIS FOR ACTUAL TESTING BECAUSE THIS ISN'T FUCKING ACCURATE! USE THE SERVER/CLIENT TESTING WAY!!!!!") end)
	message.Parent = nil
else
	print(err)
	wait(5)
	message.Text = "Error on visit: " .. err
end
