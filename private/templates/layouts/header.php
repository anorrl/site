<?php
	use anorrl\UserSettings;
	use anorrl\utilities\ClientDetector;
	use anorrl\utilities\Splasher;
	use anorrl\utilities\FileSplasher;
	use anorrl\utilities\UtilUtils;

	$header_check_user = SESSION ? SESSION->user : null;

	$rand_pic = new Splasher(UtilUtils::GetFilesArray("/public/images/randoms/"), false, "RandomImages")->getRandomSplash();
	
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
	</head>
	<body>
		<div id="header" <?= !SESSION ? "" : 'style="border-width: 2px;"' ?>>
			<div id="logo" style="float: left;">
				<a href="/">
					<img src="/public/images/header/logo_small.png">
				</a>
			</div>
			<?php if(SESSION): ?>
			<div style="float: right;margin-top: 6px;margin-right: 6px;">
				<a class="header-link" href="/api/logout?redirect=/" title="You sure you want to do this?">Logout</a>
			</div>
			<?php endif ?>
			<div id="container">
				<div id="links">
					<a class="header-link" href="/my/home">Home</a>
					<a class="header-link" href="/games">Games</a>
					<a class="header-link" href="/catalog">Catalog</a>
					<a class="header-link" href="/vandals">Vandals</a>
					<a class="header-link" href="/about">About</a>
				</div>
			</div>
		</div>
		<?php if(SESSION): ?>
		<div id="submenu" style="position: relative">
			<div id="container">
				<a href="/users/<?= SESSION->user->id ?>/profile"   <?php if($this->internal_name == "user_profile"	 ):?>selected<?php endif ?>>Profile</a>
				<a href="/my/profile"   <?php if($this->internal_name == "my/profile"	 ):?>selected<?php endif ?>>Account</a>
				<a href="/my/character" <?php if($this->internal_name == "my/character"	 ):?>selected<?php endif ?>>Character</a>
				<a href="/my/friends"   <?php if($this->internal_name == "my/friends"	 ):?>selected<?php endif ?>>Friends</a>
				<a href="/create/"      <?php if($this->internal_name == "my/create"	 ):?>selected<?php endif ?>>Create</a>
				<a href="/my/stuff"     <?php if($this->internal_name == "my/stuff"		 ):?>selected<?php endif ?>>Stuff</a>
				<a href="/download"     <?php if($this->internal_name == "download/index"):?>selected<?php endif ?>>Download</a>
			</div>
			<div id="billboard">
				<div id="container">
					<div style="padding: 5px">
						<table id="profile">
							<tbody>
								<tr>
									<td width="40" title="Hey! That's you!"><img src="/thumbs/player?id=1&amp;sxy=42"></td>
									<td title="Hey! That's you!"><a href="/users/1/profile"><?= "OnlyTwentyCharacters" ?></a></td>
								</tr>
							</tbody>
						</table>
						<hr>
						<table style="text-align: center">
							<tbody>
								<tr>
									<td>
										<a href="/my/trade-requests" title="Your incoming requests"><img src="/public/images/icons/messages.png">10K</a>
									</td>
									<td>
										<a href="/my/friends" title="Your friends"><img src="/public/images/icons/friends.png">10K</a>
									</td>
									<td>
										<a href="/my/balance" title="Your balance"><img src="/public/images/icons/traffic_cone.png">10K</a>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		

		<?php endif ?>
		<div id="body">
			<div id="container">