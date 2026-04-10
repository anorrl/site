<?php

	// REMOVE THIS AND LIKE KILL EVERYONE

	include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
	
	use anorrl\utilities\Renderer;
	use anorrl\enums\AssetType;
	use anorrl\Asset;
	use anorrl\AssetVersion;

	$user = SESSION ? SESSION->user : null;
	
	$directory = $_SERVER['DOCUMENT_ROOT'];
	$assetsdir = "$directory/../assets/";

	
	if(isset($_POST['type'])) {
		if(isset($_POST['id'])) {
			$asset = Asset::FromID(intval($_POST['id']));

			if($asset != null && ($asset->creator->id == $user->id || $user->isAdmin())) {
				if($_POST['type'] == "delete") {
					$asset->delete();
					$message = "Success!";
				} else if($_POST['type'] == "render") {
					$asset->render();
					$message = "Success!";
				}

			}
		}
	}

	$message = "You are not authorised to use this.";
		
	

	die($message);
?>
