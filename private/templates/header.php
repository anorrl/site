<?php
	use anorrl\UserSettings;
	use anorrl\utilities\ClientDetector;
	use anorrl\utilities\Splasher;
	use anorrl\utilities\FileSplasher;
	use anorrl\utilities\Utilities;

	$header_check_user = SESSION ? SESSION->user : null;

	$rand_pic = new Splasher(Utilities::GetFilesArray("public/images/randoms/"), false, "RandomImages")->getRandomSplash();

	$randomsignsplash = new FileSplasher("sign")->getRandomSplash();

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
		
		<?php if(false): ?>
		<style>
			#LoadingScreen {
				inset: 0;
				position: fixed;
				width: 100vw;
				height: 100vh;
				background: linear-gradient(#33333399, #00000099);
				z-index: 10000;
				color: white;
				text-align: center;
				display:flex;
				font-size: 16px;
				justify-content: center;
				align-items: center;
				backdrop-filter: blur(10px);
				opacity: 0;
			}

			#LoadingScreen p[caption] {
				margin-top: 3px;
				margin-bottom: 25px;
				font-size: 14px;
				letter-spacing: 0px;
				font-style:italic;
				font-weight: bold;
			}

			#LoadingScreen img[splash] {
				border-radius: 5px;
				border: 3px solid black;
			}

			#LoadingScreen img[loading] {
				width: 100px;
			}
		</style>
		<script>
			const wait = (delay = 0) =>	new Promise(resolve => setTimeout(resolve, delay));

			function setVisible(element, visible) {
				if(element == "#LoadingScreen")
					$(element).css("opacity", visible ? 1 : 0);
			}

			// do loading screen if the page hasn't loaded in a second.

			var hasLoaded = false;
			var initiateLoading = false;

			$(window).load(function() {
				hasLoaded = true;
				$("#LoadingScreen").css("transition", "opacity 0.75s");
				if(initiateLoading) {
					// mom im a genius
					wait(200).then(() => {
						setVisible("#LoadingScreen", false);
						$("#LoadingScreen").css("pointer-events", "none");
						
					});
					wait(1500).then(() => {
						$("#LoadingScreen").remove();
					});
				} else {
					$("#LoadingScreen").remove();
				}
			});

			wait(500).then(() => {
				if(!hasLoaded) {
					$("#LoadingScreen").css("transition", "opacity 0.25s");
					setVisible('#LoadingScreen', true);
					initiateLoading = true;
				}
			})

			
		</script>
		<?php endif ?>
	</head>
	<body <?= $this->settings->nightbg && !$hasBackground ? "night" : "" ?>>
		<?php if(false): ?>
		<div id="LoadingScreen">
			<div>
				<img src="/public/images/splashes/<?= $rand_splash_pic ?>" splash>
				<p caption><?= $splashscreencaption?></p>
				<p id="LoadingText">Loading <?= $this->title ?>...</p>
				<img src="/public/images/spinner100x100_white.gif" loading>
			</div>
		</div>
		<?php endif ?>
		<?php if($this->bad_apple): ?>
		<style>
			body {
				background: url('/public/images/badapple.gif') !important;
			}
		</style>
		<?php endif ?>
		<?php if($this->settings->randoms): ?>
		<img src="/public/images/randoms/<?= $rand_pic ?>" style="position: fixed;bottom: 0px;left: 0px;width: 250px;z-index: 9999;pointer-events: none;">
		<?php endif ?>
		<?php if($this->settings->teto): ?>
		<div id="TetoContainer">
			<div id="TetoSplashContainer">
				<p id="TetoSplash"><?= new FileSplasher("teto")->getRandomSplash(); ?></p>
			</div>
			<img id="Teto" src="/public/images/tetospeech.png">
		</div>
		<?php endif ?>
		<?php if($this->settings->accessibility): ?>
		<style>
			@font-face {
				font-family: 'punk';
				src: url('/public/css/SplendidB.ttf');
			}
		</style>
		<?php endif ?>
		<div id="Container">
			<div id="Header">
				
				<?php if($header_check_user != null): ?>
				<div id="Links">
					<a href="/my/home">GO HOME</a>
					
				</div>
				<?php else: ?>
				<div id="Links"></div>
				<?php endif ?>
			</div>
			<?php if(!ClientDetector::IsAClient()): ?>
			<div class="DisplayMobileWarning" style="display: none">
				<div id="MobileWarningText">
					<h1>HEADS UP!</h1>
					<p>This isn't optimised for mobile devices, best to use a pc (as this was designed for that)</p>
					<button onclick="ANORRL.HideMobileWarning()">Continue anyways...</button>
				</div>
			</div>
			<?php endif ?>
			<div id="Body">
				<div id="BodyContainer">
					<h1>this page is BROKEN on purpose because i haven't gotten to it</h1>
