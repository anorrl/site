<?php
	// getV2?placeId=331&type=standard&scope=global

	use anorrl\Datastore;
	use anorrl\Universe;
	use anorrl\Place;
	use anorrl\utilities\ClientDetector;

	function dieOff(string $message = "Method Not Allowed", int $response_code = 500) {
		http_response_code($response_code);
		exit(json_encode(["error"=>$message]));
	}
	
	function removeEverythingBefore($in, $before) {
		$pos = strpos($in, $before);
		return $pos !== FALSE
			? substr($in, $pos + strlen($before), strlen($in))
			: "";
	}

	set_content_type(ARLTYPEJSON);

	if(!(ClientDetector::HasAccess()||ClientDetector::IsAClient()))
		dieOff(); // Method Not Allowed
	
	if(!(
		isset($_GET["placeId"]) &&
		isset($_GET["scope"]) &&
		isset($_GET["type"])
	))
		dieOff();
	
	$values = [];
	$input = file_get_contents('php://input');

	if(strlen($input) == 0)
		dieOff();

	$qkeys = explode("&",substr($input, 1));
	$tempTable = array();
	foreach($qkeys as &$val){
		$after = substr($val, 0, strpos($val, "="));
		$tempTable[$after] = removeEverythingBefore($val,"=");
	}
	$qkeys = $tempTable;
	$tempTable = null;

	if(!(isset($qkeys['qkeys[0].key']) && isset($qkeys['qkeys[0].target'])))
		dieOff("Bad Request", 400);

	$key    = urldecode($qkeys['qkeys[0].key']);
	$place  = Place::FromID(intval($_GET["placeId"]));
	$scope  = urldecode($_GET["scope"]);
	$type   = urldecode($_GET["type"]);
	$target = urldecode($qkeys['qkeys[0].target']);

	if($type != "standard")
		dieOff();
	
	if(!$place)
		dieOff("Not Found", 404);

	$universe = Universe::FromID($place->universe);
	if(!$universe)
		dieOff("Not Found", 404);

	$ds = new Datastore($universe);

	exit(json_encode([
		"data" => $ds->get($key, $target, $scope)
	], JSON_NUMERIC_CHECK));
?>
