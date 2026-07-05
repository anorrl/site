<?php
	use anorrl\Session;
	
	$user = SESSION ? SESSION->user : null;
	
	if($user != null) {
		Session::removeCookies();
		session_destroy();
		if(isset($_GET['redirect']))
			redirect($_GET['redirect']);
		
		echo "Logged out yay";
	} else {
		echo "Why even perform this when you aren't even logged in??";
	}
	
?>