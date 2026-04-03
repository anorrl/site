<?php
	
	use anorrl\utilities\UserUtils;
	
	$user = UserUtils::RetrieveUser();
	if($user != null) {
		UserUtils::RemoveCookies();
		session_destroy();
		echo "Logged out yay";
	} else {
		echo "Why even perform this when you aren't even logged in??";
	}
	
?>