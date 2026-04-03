<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/core/utilities/userutils.php';

	function IsRewrite() {
		if(!empty($_SERVER['IIS_WasUrlRewritten']))
			return true;
		else if(array_key_exists('HTTP_MOD_REWRITE',$_SERVER))
			return true;
		else if( array_key_exists('REDIRECT_URL', $_SERVER))
			return true;
		else
			return false;
	}

	header("Content-Type: application/json");

	if(!IsRewrite()) {
		die("{}");
	}

	// No id parameter? GET OUT!
	if(!isset($_GET['id'])) {
		die("{}");
	}

	$get_user = User::FromID(intval($_GET['id']));

	if($get_user == null) {
		die("{}");
	}

	die(json_encode([
		"Id" => $get_user->id,
		"Username" => $get_user->name
	]));
?>