<?php 
	use anorrl\User;

    set_content_type(ARLTYPEPLAIN); 
    // dont cache this shit!
    disable_cache();

	$domain = CONFIG->domain;
	$scheme = CONFIG->prefer_https ? "https" : "http";

    if(isset($_GET['assetId'])): ?>
<?= $scheme ?>://<?= $domain ?>/Asset/BodyColors.ashx?clothing;<?= $scheme ?>://<?= $domain ?>/asset/?id=<?= intval($_GET['assetId']) ?>
<?php else: 

$userId = intval($_GET['userId']) ?? 1;

$user = User::FromID($userId);

if($user == null) {
    $user = User::FromID(1);
    $userId = 1;
}

die($user->getCharacterAppearance());
endif ?>