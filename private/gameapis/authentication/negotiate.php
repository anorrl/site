<?php
	use anorrl\Session;

    if(isset($_GET['suggest'])) {
		$key = base64_decode($_GET['suggest']);
        Session::setCookies($key);
    }
?>