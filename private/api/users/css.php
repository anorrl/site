<?php
	set_content_type(ARLTYPECSS);

	use anorrl\User;
	use anorrl\utilities\Utilities;

	if(!isset($id))
		die();

	$get_user = User::FromID(intval($id));

	if($get_user == null || ($user && $user->isBanned()))
		die();

	$settings = $get_user->getSettings();
	
	if(Utilities::IsValidCSS($settings->css) || isset($_GET['force']))
		die($settings->css);

?>
