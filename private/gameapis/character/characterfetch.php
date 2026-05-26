<?php 
	use anorrl\User;

    set_content_type(ARLTYPEPLAIN); 
    // dont cache this shit!
    disable_cache();

	$domain = CONFIG->domain;

    if(isset($_GET['assetId'])): ?>
http://<?= $domain ?>/Asset/BodyColors.ashx?clothing;http://<?= $domain ?>/asset/?id=<?= $_GET['assetId'] ?>
<?php else: 

$userId = intval($_GET['userId']) ?? 1;

$user = User::FromID($userId);

if($user == null) {
    $user = User::FromID(1);
    $userId = 1;
}

die($user->getCharacterAppearance());
endif ?>