<?php

	// handle basic settings requests
	// e.g `/develop/545/configure/settings`
	
	
	/*
	ANORRL$EditItem$Name	"BULLSHITLAND"
	ANORRL$EditItem$Description	"Place where I put scripts I made."
	ANORRL$EditItem$PublicBox	"on"
	ANORRL$EditItem$CommentsBox	"on"
	*/

	use anorrl\Place;
	use anorrl\enums\ChatOption;
	use anorrl\enums\Genre;
	use anorrl\enums\GearType;

	set_content_type(ARLTYPEJSON);

	if(!ARLAUTH || !isset($id) || !isset($_SESSION['ANORRL$Asset$ID']) || 
		!isset($_POST['ANORRL$EditPlace$ServerSize']) || 
		!isset($_POST['ANORRL$EditPlace$Genre']) || 
		!isset($_POST['ANORRL$EditPlace$GearType']) || 
		!isset($_POST['ANORRL$EditPlace$ChatOption']))
		die(json_encode($result));

	if(intval($_SESSION['ANORRL$Asset$ID']) != $id)
		die(json_encode($result));

	$asset = Place::FromID($id);

	if(!$asset) {
		$result['reason'] = "Failed to retrieve asset.";
		die(json_encode($result));
	}

	$server_size = intval($_POST['ANORRL$EditPlace$ServerSize']);
	$genre = Genre::index(intval($_POST['ANORRL$EditPlace$Genre']));
	$gear_type = GearType::index(intval($_POST['ANORRL$EditPlace$GearType']));
	$chat_type = ChatOption::index(intval($_POST['ANORRL$EditPlace$ChatOption']));
	$copylocked = isset($_POST['ANORRL$EditPlace$Copylocked']);
	$uploaded_thumbs = false;

	if(isset($_FILES['ANORRL$EditPlace$ThumbnailFile'])) {
		$uploaded_thumbs = $asset->setThumbnail($_FILES['ANORRL$EditPlace$ThumbnailFile']);
	}

	$result = ["success" => true];
	
	$asset->update($copylocked, $server_size, $chat_type, $genre, $gear_type);

	if(isset($_FILES['ANORRL$EditPlace$ThumbnailFile']) && !$uploaded_thumbs) {
		$result["reason"] = "Something went wrong with settings the thumbnail and thus was not updated.";
	}

	
	die(json_encode($result));
?>