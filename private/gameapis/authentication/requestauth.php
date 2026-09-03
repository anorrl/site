<?php
    
	$domain = CONFIG->domain;
	$scheme = CONFIG->prefer_https ? "https" : "http";

    if(ARLAUTH) {
		$user = SESSION->user;
        exit("{$scheme}://$domain/Login/Negotiate.ashx?suggest=".base64_encode($user->security_key));
    } else {
        exit_http(401);
    }
?>