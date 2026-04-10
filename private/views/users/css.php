<?php
	use anorrl\User;
	use anorrl\utilities\UtilUtils;

	function IsRewrite() {
		if(!empty($_SERVER['IIS_WasUrlRewritten']))
			return true;
		else if(array_key_exists('HTTP_MOD_REWRITE', $_SERVER))
			return true;
		else if(array_key_exists('REDIRECT_URL', $_SERVER))
			return true;
		else
			return false;
	}

	if(!IsRewrite()) {
		die(header("Location: /my/home"));
	}

	// No id parameter? GET OUT!
	if(!isset($id)) {
		die(header("Location: /my/home"));
	}

	$get_user = User::FromID(intval($id));

	if($get_user == null) {
		die(header("Location: /my/home"));
	}

	$header_data = $get_user;

	header("Content-Type: text/css");
	
	if(UtilUtils::IsValidCSS($get_user->getUserCSS()) || isset($_GET['force'])) {
		die($get_user->getUserCSS());
	}

	die();
?>
