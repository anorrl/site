<?php 
    $user = SESSION ? SESSION->user : null;

    if($user != null) {
        echo strval($user->id);
    } else {
        echo "1";
    }
?>