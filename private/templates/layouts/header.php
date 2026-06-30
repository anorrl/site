<?php
	use anorrl\UserSettings;
	use anorrl\utilities\ClientDetector;
	use anorrl\utilities\Splasher;
	use anorrl\utilities\FileSplasher;
	use anorrl\utilities\UtilUtils;

	$header_check_user = SESSION ? SESSION->user : null;

	$rand_pic = new Splasher(UtilUtils::GetFilesArray("/public/images/randoms/"), false, "RandomImages")->getRandomSplash();
	$rand_splash_pic = new Splasher(UtilUtils::GetFilesArray("/public/images/splashes/"), false, "SplashScreen")->getRandomSplash();

	$randomsignsplash = new FileSplasher("sign")->getRandomSplash();

	$splashscreencaptions = file($_SERVER["DOCUMENT_ROOT"]."/private/splashes/screens.txt");
	$splashscreencaption = $splashscreencaptions[str_replace(["ANORRLStudioSplash-", ".png"], "", $rand_splash_pic)-1];
	
	if(session_status() == PHP_SESSION_NONE)
		session_start();

	if(isset($_SESSION['ANORRL$UserPage$RandomImages']))
		unset($_SESSION['ANORRL$UserPage$RandomImages']);

	//this is so that if the user ever sets 'background:' on the profile css it'll not apply the night background
	//because the night background can override the user's background
	$hasBackground = false;

	$userCSS = isset($get_user) ? UserSettings::Get($get_user)->css : (SESSION ? SESSION->settings->css : "");
	if (!empty($userCSS) && preg_match('/background\s*:/i', $userCSS)) {
		$hasBackground = true;
	}

	/*
	$hasBackground = false;
	if (isset($get_user)) {
   		$userCss = $header_data->GetUserCSS();
    	if (!empty($userCss) && preg_match('/background\s*:/i', $userCss)) {
        	$hasBackground = true;
    	}
	}
	*/
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?= $this->title ?><?php if(!str_contains($this->title, "ANORRL")): ?> - ANORRL<?php endif ?></title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		
		<?php foreach($this->scripts as $script): ?>
		<script src="<?= $script ?>"></script>
		<?php endforeach ?>
		
		<?php foreach($this->stylesheets as $stylesheet): ?>
		<link rel="stylesheet" href="<?= $stylesheet ?>">
		<?php endforeach ?>

		<?php foreach($this->metas as $meta): ?>
		<meta property="<?= $meta['type'] ?>" content="<?= $meta['contents'] ?>">
		<?php endforeach ?>
	</head>
	<body>
		<div id="header">
			<div id="logo" style="float: left;">
				<a href="/">
					<img src="/public/images/header/logo2.png">
				</a>
			</div>
			<div id="container">
				<div id="links">
					<a href="/my/home">Home</a>
					<a href="/games">Games</a>
					<a href="/catalog">Catalog</a>
					<a href="/vandals">Vandals</a>
					<a href="/about">About</a>
				</div>
			</div>
			
		</div>
		<div id="body">
			<div id="container">