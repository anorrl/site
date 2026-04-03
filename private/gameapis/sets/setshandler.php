<?php
	header("Content-Type: application/json");

	//?maxsets=10&rqtype=getrobloxsets

	if(isset($_GET['rqtype'])) {
		$type = $_GET['rqtype'];

		if($type == "getrobloxsets") {
			die(include($_SERVER['DOCUMENT_ROOT']."/core/gameapis/sets/get-roblox-sets.php"));
		}
		else if($type == "getsetinfo") {
			die(include($_SERVER['DOCUMENT_ROOT']."/core/gameapis/sets/get-set-info.php"));
		}
		else if($type == "getsetitems") {
			die(include($_SERVER['DOCUMENT_ROOT']."/core/gameapis/sets/get-set-items.php"));
		}
		else if($type == "getmydecals") {
			die(include($_SERVER['DOCUMENT_ROOT']."/core/gameapis/sets/get-my-decals.php"));
		}
		else if($type == "getmymodels") {
			die(include($_SERVER['DOCUMENT_ROOT']."/core/gameapis/sets/get-my-models.php"));
		}
	}

	die("{}");
?>