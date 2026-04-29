game:GetService("Players"):SetSaveDataUrl("http://{domain}/Persistence/SetBlob.ashx?placeid={id}&userid=%d&access={access}")
game:GetService("Players"):SetLoadDataUrl("http://{domain}/Persistence/GetBlob.ashx?placeid={id}&userid=%d&access={access}")

game:GetService("Players").PlayerAdded:connectFirst(function(player)
	player:LoadData()
end)

game:GetService("Players").PlayerRemoving:connectLast(function(player)
	player:SaveData()
end)
