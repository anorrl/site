<?php
	// set?placeId=331&key=Highscore2015&&type=sorted&scope=global&target=1&valueLength=1

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

	function stripQuotes($text) {
		$pre_text = $text;

		if(str_starts_with($pre_text, "\"") || str_starts_with($pre_text, "'")) {
			$pre_text = substr($pre_text, 1);
		}

		if(str_ends_with($pre_text, "\"") || str_ends_with($pre_text, "'")) {
			$pre_text = substr($pre_text, 0, strlen($pre_text)-1);
		}
		
		return $pre_text;
	}

	set_content_type(ARLTYPEJSON);

	if(!(ClientDetector::HasAccess()||ClientDetector::IsAClient()))
		dieOff(); // Method Not Allowed

	if(!(
		isset($_GET['placeId']) &&
		isset($_GET['key']) &&
		isset($_GET['type']) &&
		isset($_GET['scope']) &&
		isset($_GET['target']) &&
		isset($_POST['value'])
	))
		dieOff(); // Method Not Allowed

	$key    = $_GET["key"];
	$place  = Place::FromID(intval($_GET["placeId"]));
	$scope  = $_GET["scope"];
	$type   = $_GET["type"];
	$target = $_GET["target"];
	$value  = $_POST["value"];

	if($type != "standard")
		dieOff();

	if(!$place)
		dieOff("Not Found", 404);

	$universe = Universe::FromID($place->universe);
	if(!$universe)
		dieOff("Not Found", 404);

	$ds = new Datastore($universe);

	if (startsWith($_POST["value"], "[{") && endsWith($_POST["value"], "}]")){
		error_log($_POST['value']);
		$postData = json_decode($_POST["value"]);

		if (count($postData) == 1){
			if (isset($postData['0']->Scope) && isset($postData['0']->Key) && isset($postData['0']->Value)){
				$value = $postData['0']->Value;
			}
		}
	} else {
		$value = stripQuotes($value);
	}

	$result = $ds->set($key, $value, $target, $scope);

	if(!$result)
		dieOff();
	else
		exit(json_encode(["data" => []]));
?>
