<?php
	use anorrl\utilities\UserUtils;

    if(SESSION && isset($_GET['suggest'])) {
		$key = base64_decode($_GET['suggest']);
        UserUtils::SetCookies($key);
    }
?>