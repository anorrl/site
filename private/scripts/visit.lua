pcall(function() game:GetService("Players"):SetBuildUserPermissionsUrl("{scheme}://{domain}/Game/BuildActionPermissionCheck.ashx?assetId=0&userId=%d&isSolo=true") end)

local addedBuildTools = false
local screenGui = game:GetService("CoreGui"):FindFirstChild("ANORRLGui")

function doVisit()
	message.Text = "Loading Game"
	pcall(function() visit:SetUploadUrl("") end)

	message.Text = "Running"
	game:GetService("RunService"):Run()

	message.Text = "Creating Player"
	player = game:GetService("Players"):CreateLocalPlayer({userid})
	pcall(function() player.Name = "{username}" end)
	player.CharacterAppearance = "{charapp}&placeId=0"
	
	if game:GetService("Players").CharacterAutoLoads then
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
	playerName.ANORRLLocked = true
	playerName.Parent = screenGui
				
	pcall(function() game:GetService("ScriptContext"):AddCoreScript(59431535,screenGui,"BuildToolsScript") end)
	addedBuildTools = true
end

if success then
	pcall(function() warn("Play Solo is inaccurate as it doesn't have FilteringEnabled! Please use the Server/Client testing!") end)
	message.Parent = nil
else
	print(err)
	wait(5)
	message.Text = "Error on visit: " .. err
end
