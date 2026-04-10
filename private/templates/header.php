<?php
	use anorrl\utilities\Splasher;

	$header_check_user = SESSION ? SESSION->user : null;
	
	// 99999999 max
	//very true -skyler
	
	function getImagesList() {
		$array = [
			"2behdamned",
			"chokinghamster",
			"horse",
			"mario",
			"satoru",
			"twinfantasy",
			"soretrojak",
			"deimos",
			"xendiscord",
			"sanford",
			"flclcanti",
			"hankblender",
			"jermafwoomp",
			"jermathe",
			"sanfordhappy",
			"sanfordthumbsup",
			"weeeeh",
			"neuroangel",
			"iscream",
			"neuroqueen",
			"tenisbol"
		];
		shuffle($array);

		return $array;
	}

	function rollImage() {
		$pictures = $_SESSION['ANORRL$UserPage$RandomImages'];
		
		if(count($pictures) == 0) {
			$_SESSION['ANORRL$UserPage$RandomImages'] = getImagesList();
			$pictures = $_SESSION['ANORRL$UserPage$RandomImages'];
		}
		
		if(count($pictures) != 1) {
			$rand_pic_name = $pictures[0];
			array_splice($_SESSION['ANORRL$UserPage$RandomImages'], 0, length: 1);
		} else {
			$rand_pic_name = end($pictures);
			$_SESSION['ANORRL$UserPage$RandomImages'] = getImagesList();
		}

		return $rand_pic_name;
	}

	if(!isset($_SESSION['ANORRL$UserPage$RandomImages'])) {
		$_SESSION['ANORRL$UserPage$RandomImages'] = getImagesList();
	}

	$rand_pic = rollImage();

	$randomsignsplash = new Splasher("sign")->getRandomSplash();

    //this is so that if the user ever sets 'background:' on the profile css it'll not apply the night background
    //because the night background can override the user's background
	$hasBackground = false;
	/*if (isset($get_user)) {
   		$userCss = $header_data->getUserCSS();
    	if (!empty($userCss) && preg_match('/background\s*:/i', $userCss)) {
        	$hasBackground = true;
    	}
	}*/
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?= $this->title ?> - ANORRL</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<?php foreach($this->scripts as $script): ?>
		<script src="<?= $script ?>"></script>
		<?php endforeach ?>
		<?php foreach($this->stylesheets as $stylesheet): ?>
		<link rel="stylesheet" href="<?= $stylesheet ?>">
		<?php endforeach ?>

		<?php if(count($this->metas) != 0): ?>
			<?php foreach($this->metas as $meta): ?>
			<meta property="<?= $meta['type'] ?>" content="<?= $meta['contents'] ?>">
			<?php endforeach ?>
		<?php endif ?>
	</head>
	<body <?= $this->settings->nightbg_enabled ? "night" : "" ?>>
		<?php if($this->bad_apple): ?>
		<style>
			body {
				background: url('/images/badapple.gif') !important;
			}
		</style>
		<?php endif ?>
		<?php if($this->settings->randoms_enabled): ?>
		<img src="/images/randoms/<?= $rand_pic ?>.png" style="position: fixed;bottom: 0px;left: 0px;width: 250px;z-index: 9999;">
		<?php endif ?>
		<?php if($this->settings->teto_enabled): ?>
		<div id="TetoContainer">
			<div id="TetoSplashContainer">
				<p id="TetoSplash"><?= new Splasher("teto")->getRandomSplash(); ?></p>
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
						<span title="Your friends"><a href="/my/friends"><img src="/images/icons/friends.png"> <?= $header_check_user->GetFriendsCount() ?></a></span>
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
					<a href="/my/home"      <?php if($_SERVER['SCRIPT_NAME'] == "/private/views/my/home.php"     		 ):?>selected<?php endif ?>>Home</a>
					<a href="/my/profile"   <?php if($_SERVER['SCRIPT_NAME'] == "/private/views/my/profile.php"  		 ):?>selected<?php endif ?>>Account</a>
					<a href="/my/character" <?php if($_SERVER['SCRIPT_NAME'] == "/private/views/my/character.php"		 ):?>selected<?php endif ?>>Character</a>
					<a href="/my/friends"   <?php if($_SERVER['SCRIPT_NAME'] == "/private/views/my/friends.php"		     ):?>selected<?php endif ?>>Friends</a>
					<a href="/create/"      <?php if($_SERVER['SCRIPT_NAME'] == "/private/views/create.php" 		     ):?>selected<?php endif ?>>Create</a>
					<a href="/my/stuff"     <?php if($_SERVER['SCRIPT_NAME'] == "/private/views/my/stuff.php"    		 ):?>selected<?php endif ?>>Stuff</a>
					<a href="/download"     <?php if($_SERVER['SCRIPT_NAME'] == "/private/views/download/index.php"      ):?>selected<?php endif ?>>Download</a>
				</div>
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
					