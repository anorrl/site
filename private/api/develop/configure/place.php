<?php
	use anorrl\Place;
	use anorrl\enums\ChatOption;
	use anorrl\enums\Genre;
	use anorrl\enums\GearType;

	set_content_type(ARLTYPEJSON);

	$result = ["success" => false, "reason" => "Request failed."];

	if(!ARLAUTH || !isset($id) || !isset($_SESSION['ANORRL$Asset$ID']) || 
		!isset($_POST['ANORRL$EditPlace$ServerSize']) || 
		!isset($_POST['ANORRL$EditPlace$Genre']) || 
		!isset($_POST['ANORRL$EditPlace$GearType']) || 
		!isset($_POST['ANORRL$EditPlace$ChatOption']))
		die(json_encode($result));

	if(intval($_SESSION['ANORRL$Asset$ID']) != $id)
		die(json_encode($result));

	$place = Place::FromID($id);

	if(!$place) {
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
		$uploaded_thumbs = $place->setThumbnail($_FILES['ANORRL$EditPlace$ThumbnailFile']);
	}

	$result = ["success" => $place->update([
		"copylocked" => $copylocked,
		"serversize" => $server_size,
		"chat_option" => $chat_type,
		"genre" => $genre,
		"gears_allowed" => $gear_type,
	])];
	

	if(isset($_FILES['ANORRL$EditPlace$ThumbnailFile']) && !$uploaded_thumbs) {
		$result["reason"] = "Something went wrong with setting the thumbnail and thus was not updated.";
	}

	
	die(json_encode($result));
?>