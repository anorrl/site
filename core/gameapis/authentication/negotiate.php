<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/core/utilities/userutils.php';
	$user = UserUtils::RetrieveUser();

    if($user == null && isset($_GET['suggest'])) {
        $key = base64_decode($_GET['suggest']);

        UserUtils::SetCookies($key);
    }
?>