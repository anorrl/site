<?php
	use anorrl\User;
	use anorrl\utilities\UtilUtils;

	if(!UtilUtils::HasBeenRewritten()) {
		redirect("/my/home");
	}

	// No id parameter? GET OUT!
	if(!isset($id)) {
		redirect("/my/home");
	}

	$get_user = User::FromID(intval($id));

	if($get_user == null) {
		redirect("/my/home");
	}

	$header_data = $get_user;

	set_content_type(ARLTYPECSS);
	
	if(UtilUtils::IsValidCSS(SESSION->settings->css) || isset($_GET['force'])) {
		die(SESSION->settings->css);
	}

	die();
?>
