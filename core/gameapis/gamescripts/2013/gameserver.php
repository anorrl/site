<?php ob_start() ?>
-- Start Game Script Arguments
local placeId, port, url, access, jobID = ...

-- REQUIRES: StartGanmeSharedArgs.txt
-- REQUIRES: MonitorGameStatus.txt

-----------------------------------"CUSTOM" SHARED CODE----------------------------------

pcall(function() settings().Network.UseInstancePacketCache = true end)
pcall(function() settings().Network.UsePhysicsPacketCache = true end)
pcall(function() settings()["Task Scheduler"].PriorityMethod = Enum.PriorityMethod.AccumulatedError end)

settings().Network.PhysicsSend = Enum.PhysicsSendMethod.TopNErrors
settings().Network.ExperimentalPhysicsEnabled = true
settings().Network.WaitingForCharacterLogRate = 100
pcall(function() settings().Diagnostics:LegacyScriptMode() end)

-----------------------------------START GAME SHARED SCRIPT------------------------------

local assetId = placeId -- might be able to remove this now

local scriptContext = game:GetService('ScriptContext')
scriptContext.ScriptsDisabled = true

game:SetPlaceID(assetId, true)
game:GetService("ChangeHistoryService"):SetEnabled(false)

-- establish this peer as the Server
local ns = game:GetService("NetworkServer")

if url~=nil then
	pcall(function() game:GetService("Players"):SetAbuseReportUrl(url .. "/AbuseReport/InGameChatHandler.ashx") end)
	pcall(function() game:GetService("ScriptInformationProvider"):SetAssetUrl(url .. "/Asset/") end)
	pcall(function() game:GetService("ContentProvider"):SetBaseUrl(url .. "/") end)
	--pcall(function() game:GetService("Players"):SetChatFilterUrl(url .. "/Game/ChatFilter.ashx") end)

	game:GetService("BadgeService"):SetPlaceId(placeId)
	if access~=nil then
		game:GetService("BadgeService"):SetAwardBadgeUrl(url .. "/Game/Badge/AwardBadge.ashx?UserID=%d&BadgeID=%d&PlaceID=%d&access=" .. access)
		game:GetService("BadgeService"):SetHasBadgeUrl(url .. "/Game/Badge/HasBadge.ashx?UserID=%d&BadgeID=%d&access=" .. access)
		game:GetService("BadgeService"):SetIsBadgeDisabledUrl(url .. "/Game/Badge/IsBadgeDisabled.ashx?BadgeID=%d&PlaceID=%d&access=" .. access)

		game:GetService("FriendService"):SetMakeFriendUrl(url .. "/Game/CreateFriend?firstUserId=%d&secondUserId=%d")
		game:GetService("FriendService"):SetBreakFriendUrl(url .. "/Game/BreakFriend?firstUserId=%d&secondUserId=%d")
		game:GetService("FriendService"):SetGetFriendsUrl(url .. "/Game/AreFriends?userId=%d")
	end
	game:GetService("BadgeService"):SetIsBadgeLegalUrl("")
	game:GetService("InsertService"):SetBaseSetsUrl(url .. "/Game/Tools/InsertAsset.ashx?nsets=10&type=base")
	game:GetService("InsertService"):SetUserSetsUrl(url .. "/Game/Tools/InsertAsset.ashx?nsets=20&type=user&userid=%d")
	game:GetService("InsertService"):SetCollectionUrl(url .. "/Game/Tools/InsertAsset.ashx?sid=%d")
	game:GetService("InsertService"):SetAssetUrl(url .. "/Asset/?id=%d")
	game:GetService("InsertService"):SetAssetVersionUrl(url .. "/Asset/?assetversionid=%d")
	game:GetService("InsertService"):SetTrustLevel(0) -- i dont know what this does... it just works...
	
	--pcall(function() loadfile(url .. "/Game/LoadPlaceInfo.ashx?PlaceId=" .. placeId)() end)
	
	pcall(function() 
		if access then
			--loadfile(url .. "/Game/PlaceSpecificScript.ashx?PlaceId=" .. placeId .. "&access=" .. access)()
		end
	end)
end

pcall(function() game:GetService("NetworkServer"):SetIsPlayerAuthenticationRequired(false) end)
settings().Diagnostics.LuaRamLimit = 0

local shouldCountDown = true
local countdownTimer = 60

local commands = {";ec", ";cock", ";raymonf", ";gage", ";minecraft", ";suicide", ";energycell", ";cancer", ";bleach", ";sex", ";kms", ";death", ";robloxsuckingpenis"}

local ecSounds = {
	1991,
	1993,
	1995
}

function onChatted(msg, speaker)
	source = string.lower(speaker.Name)
	msg = string.lower(msg)
	for i=1,#commands do
		if msg == commands[i] and speaker.Character.Humanoid.Health > 0 then
			speaker.Character.Humanoid.Health = 0
			local sound = Instance.new("Sound")
			sound.Parent = game.Workspace:FindFirstChild(speaker.Name).Head
			sound.SoundId = "http://arl.lambda.cam/asset/?id=" .. ecSounds[math.random(1, #ecSounds)]
			wait(0.2)
			sound:Play()
		end
	end
end

game:GetService("Players").PlayerAdded:connect(function(player)
	print("Player " .. player.userId .. " added")
	shouldCountDown = false

	if jobID ~= nil and url ~= nil and access ~= nil then
		local playerResult = game:HttpGet(url .. "/api/gameservers/validateplayer?jobID="..jobID .. "&access=" .. access .."&userID=" .. tostring(player.userId), true)

		if playerResult ~= "OK" then
			player:Kick("Hey wait something ain't right here...")
		end
	end

	player.Chatted:connect(function(msg)
		onChatted(msg, player)
	end)
end)


game:GetService("Players").PlayerRemoving:connect(function(player)
	print("Player " .. player.userId .. " leaving")	

	if jobID ~= nil and url ~= nil and access ~= nil then
		game:HttpGet(url .. "/api/gameservers/removeplayer?jobID="..jobID .. "&access="..access.."&userID=" .. tostring(player.userId))

		if #game:GetService("Players"):GetPlayers() == 0 then
			print("CLOSING THE SERVER.")
			game:HttpGet(url .. "/api/gameservers/close?jobID="..jobID .. "&access="..access)
		end
	end
end)

if placeId~=nil and url~=nil then
	-- yield so that file load happens in the heartbeat thread
	wait()
	
	-- load the game
	game:Load(url .. "/asset/?id=" .. placeId .. "&access=" .. access .. "&t=<?= time() ?>")
end

-- Now start the connection
ns:Start(port, 15) 

scriptContext:SetTimeout(10)
scriptContext.ScriptsDisabled = false

------------------------------END START GAME SHARED SCRIPT--------------------------

-- StartGame -- 
game:GetService("RunService"):Run()

if placeId and url and access and jobID then
	while wait(1) do
		if shouldCountDown then
			countdownTimer = countdownTimer - 1

			if shouldCountDown and countdownTimer <= 0 then
				print("CLOSING THE SERVER.")
				game:HttpGet(url .. "/api/gameservers/close?jobID="..jobID .. "&access="..access)
				break
			end
		else
			break
		end
	end
end
<?php
	function get_signature($script) {
		$signature = "";
		openssl_sign($script, $signature, file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/../PrivateKey.pem"), OPENSSL_ALGO_SHA1);
		return base64_encode($signature);
	}    
	header("Content-Type: text/plain");

	$script = "\r\n" . ob_get_clean();
	$signature = get_signature($script);

	die("%". $signature . "%" . $script);
?>