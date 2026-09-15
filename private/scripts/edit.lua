local success, err = pcall(function()
    message.Text = "Loading Place. Please wait..." 
    coroutine.yield() 
    game:Load("arlassetid://{placeid}") 
    visit:SetUploadUrl("{uploadurl}")
end)

if success then
	message.Parent = nil
else
	print(err)
	wait(5)
	message.Text = "Error on edit: " .. err
end