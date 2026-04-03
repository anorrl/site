<?php

	$username = $_GET['usernameToTry'];

	$randomised = rand(1, 10000000000);

	$new_username = $username . strval($randomised);

	if(strlen($new_username) < 20) {
		$new_username = substr($new_username, 0, 20);
	}

	ob_clean();
	echo $new_username;
?>