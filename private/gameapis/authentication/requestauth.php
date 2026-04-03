<?php
    $user = SESSION ? SESSION->user : null;

    if($user != null) {
        echo "http://arl.lambda.cam/Login/Negotiate.ashx?suggest=".base64_encode($user->security_key);
    } else {
        die(http_response_code(401));
    }
?>