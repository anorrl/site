<?php
	// getSortedValues?placeId=331&type=sorted&scope=global&key=Highscore2015&pageSize=10&ascending=False

	use anorrl\Datastore;
	use anorrl\Universe;
	use anorrl\Place;
	use anorrl\utilities\ClientDetector;

	function dieOff(string $message = "Method Not Allowed", int $response_code = 500) {
		http_response_code($response_code);
		exit(json_encode(["error"=>$message]));
	}

	function startsWith ($string, $startString) {
		$len = strlen($startString);
		return (substr($string, 0, $len) === $startString);
	}

	function endsWith($string, $endString) {
		$len = strlen($endString);
		if ($len == 0) {
			return true;
		}
		return (substr($string, -$len) === $endString);
	}

	set_content_type(ARLTYPEJSON);

	if(!(ClientDetector::HasAccess()||ClientDetector::IsAClient()))
		dieOff(); // Method Not Allowed


	/// getSortedValues?placeId=768&type=sorted&scope=global&key=Test&pageSize=10&ascending=True
	if(!(
		isset($_GET['placeId']) &&
		isset($_GET['key']) &&
		isset($_GET['type']) &&
		isset($_GET['scope']) &&
		isset($_GET['pageSize']) &&
		isset($_GET['ascending'])
	))
		dieOff(); // Method Not Allowed

	$key    = $_GET["key"];
	$place  = Place::FromID(intval($_GET["placeId"]));
	$scope  = $_GET["scope"];
	$type   = $_GET["type"];
	$page_size = intval($_GET['pageSize']);
	$ascending = $_GET['ascending'] == "True";

	if(!$place)
		dieOff("Not Found", 404);

	$universe = Universe::FromID($place->universe);
	if(!$universe)
		dieOff("Not Found", 404);

	$ds = new Datastore($universe);

	//$result = $ds->getordered($key, $target, $scope, $type, $page_size, $ascending);

	$result = $ds->getordered($key, $scope, "sorted", $page_size, $ascending);

	exit(json_encode([
		"data" => [
			"Entries" => $result
		]
	]));
?>
