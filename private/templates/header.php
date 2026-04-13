<?php
	use anorrl\UserSettings;
	use anorrl\utilities\Splasher;
	use anorrl\utilities\FileSplasher;
	use anorrl\utilities\UtilUtils;

	$header_check_user = SESSION ? SESSION->user : null;

	$rand_pic = new Splasher(UtilUtils::GetFilesArray("/images/randoms/"), false, "RandomImages")->getRandomSplash();
	$rand_splash_pic = new Splasher(UtilUtils::GetFilesArray("/images/splashes/"), false, "SplashScreen")->getRandomSplash();

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
		
		<?php if($this->settings->loadingscreens_enabled): ?>
		<style>
			#LoadingScreen {
				inset: 0;
				position: fixed;
				width: 100vw;
				height: 100vh;
				background: linear-gradient(#33333366, #00000066);
				z-index: 10000;
				color: white;
				text-align: center;
				display:flex;
				font-size: 16px;
				justify-content: center;
				align-items: center;
				transition: opacity 0.75s;
				backdrop-filter: blur(10px);
				display: none;
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
					document.querySelector(element).style.display = visible ? "flex" : "none";
				else
					document.querySelector(element).style.display = visible ? "block" : "none";
			}

			// do loading screen if the page hasn't loaded in a second.

			var hasLoaded = false;
			var initiateLoading = false;
			setVisible("#Container", false);

			document.addEventListener('DOMContentLoaded', function() {
				hasLoaded = true;
				setVisible("#Container", true);
				if(initiateLoading) {
					// mom im a genius
					wait(200).then(() => {
						$("#LoadingScreen").css("opacity", "0");
						$("#LoadingScreen").css("pointer-events", "none");
						
					});
					wait(1500).then(() => {
						setVisible('#LoadingScreen', false);
					});
				}
			});

			wait(1000).then(() => {
				if(!hasLoaded) {
					setVisible('#LoadingScreen', true);
					initiateLoading = true;
				}
			})

			
		</script>
		<?php endif ?>
	</head>
	<body <?= $this->settings->nightbg_enabled && !$hasBackground ? "night" : "" ?>>
		<?php if($this->settings->loadingscreens_enabled): ?>
		<div id="LoadingScreen">
			<div>
				<img src="/images/splashes/<?= $rand_splash_pic ?>" splash>
				<p id="LoadingText">Loading <?= $this->title ?>...</p>
				<img src="/images/ProgressIndicator4White.gif" loading>
			</div>
		</div>
		<?php endif ?>
		<?php if($this->bad_apple): ?>
		<style>
			body {
				background: url('/images/badapple.gif') !important;
			}
		</style>
		<?php endif ?>
		<?php if($this->settings->randoms_enabled): ?>
		<img src="/images/randoms/<?= $rand_pic ?>" style="position: fixed;bottom: 0px;left: 0px;width: 250px;z-index: 9999;pointer-events: none;">
		<?php endif ?>
		<?php if($this->settings->teto_enabled): ?>
		<div id="TetoContainer">
			<div id="TetoSplashContainer">
				<p id="TetoSplash"><?= new FileSplasher("teto")->getRandomSplash(); ?></p>
			</div>
			<img id="Teto" src="/images/tetospeech.png">
		</div>
		<?php endif ?>
		<?php if($this->settings->accessibility_enabled): ?>
		<style>
			@font-face {
				font-family: 'punk';
				src: url('/css/SplendidB.ttf');
			}
		</style>
		<?php endif ?>
		<div id="Container">
			<div id="Header">
				<?php if($header_check_user != null): 
					$pendingreqscount = $header_check_user->GetPendingFriendRequestsCount();	
				?>
				<div id="ProfileSign" logged="true">
					<img id="background" src="/images/header/signs/profile.png"> <!-- DO NOT FUCKING REMOVE -->
					<div id="UsernameRow">
						YOU ARE: <br>
						<a href="/users/<?= $header_check_user->id ?>/profile"><?= $header_check_user->name ?></a>
					</div>
					<hr>
					<div id="CreditsRow">
						<span title="Traffic Cones (ROBUX)"><img src="/images/icons/traffic_cone.png"> <?= $header_check_user->getNetCones() ?></span> <span class="Separator">|</span>
						<span title="Traffic Lights (TIX)"><img src="/images/icons/traffic_light.png"> <?= $header_check_user->getNetLights() ?></span>

						<hr>
						<span title="Your pending requests"><a href="/my/friends"><img src="/images/icons/messages<?= $pendingreqscount == 0 ? "" : "_notify" ?>.png"> <?= $pendingreqscount ?></a></span> <span class="Separator">|</span>
						<span title="Your friends"><a href="/my/friends"><img src="/images/icons/friends.png"> <?= $header_check_user->getFriendsCount() ?></a></span>
						<hr>
						<span title="Message" style="width:auto"><?= $randomsignsplash ?><a href="/images/anorrl-smile.png" target="_blank" style="display: block;"><img src="/images/anorrl-smile.png" style="width: 42px;margin: 2px 0px;"></a></span>
					</div>
				</div>
				<a id="LogoutSign" href="javascript:ANORRL.Logout()">LOGOUT</a>
				<?php else: ?>
				<div id="ProfileSign" logged="false">
					<img id="background" src="/images/header/signs/profile.png"> <!-- DO NOT FUCKING REMOVE -->
					<a href="/register" id="RegisterSign">Register</a>
					<img src="/images/sign_2way.png" style="width: 72px;padding: 10px 0;padding-top: 30px;padding-bottom:5px;z-index: 2;position: relative;">
					<a href="/login" id="LoginSign">Login</a>
				</div>
				<?php endif ?>
				<div id="Logo">
					<a href="/">
						<img src="/images/header/logo.png">
					</a>
				</div>
				
				<?php if($header_check_user != null): ?>
				<div id="Links">
					<a href="/users/<?= $header_check_user->id ?>/profile">Profile</a>
					<a href="/games">Games</a>
					<a href="/catalog">Catalog</a>
					<a href="/vandals">Vandals</a>
				</div>
				<div id="UserLinks" >
					<a href="/my/home"      <?php if($this->internal_name == "my/home"		 ):?>selected<?php endif ?>>Home</a>
					<a href="/my/profile"   <?php if($this->internal_name == "my/profile"	 ):?>selected<?php endif ?>>Account</a>
					<a href="/my/character" <?php if($this->internal_name == "my/character"	 ):?>selected<?php endif ?>>Character</a>
					<a href="/my/friends"   <?php if($this->internal_name == "my/friends"	 ):?>selected<?php endif ?>>Friends</a>
					<a href="/create/"      <?php if($this->internal_name == "my/create"	 ):?>selected<?php endif ?>>Create</a>
					<a href="/my/stuff"     <?php if($this->internal_name == "my/stuff"		 ):?>selected<?php endif ?>>Stuff</a>
					<a href="/download"     <?php if($this->internal_name == "download/index"):?>selected<?php endif ?>>Download</a>
				</div>
				<?php if($header_check_user->pendingStipend()): ?>
				<div id="StipendThingy">
					<span style="font-size: 13px">Yoo bitch you got a paycheck incoming!!!</span>
					<a href="javascript:ANORRL.CollectStipend()">Collect</a>
				</div>
				<?php endif ?>
				<?php else: ?>
				<div id="Links"></div>
				<?php endif ?>
			</div>
			<div class="DisplayMobileWarning" style="display: none">
				<div id="MobileWarningText">
					<h1>HEADS UP!</h1>
					<p>This isn't optimised for mobile devices, best to use a pc (as this was designed for that)</p>
					<button onclick="ANORRL.HideMobileWarning()">Continue anyways...</button>
				</div>
			</div>
			<div id="Body">
				<div id="BodyContainer">
					