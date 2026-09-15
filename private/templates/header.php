<?php
	use anorrl\UserSettings;
	use anorrl\utilities\ClientDetector;
	
	$header_check_user = SESSION ? SESSION->user : null;


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
	<body <?= $this->settings->nightbg && !$hasBackground ? "night" : "" ?>>
		<?php if($this->bad_apple): ?>
		<style>
			body {
				background: url('/public/images/badapple.gif') !important;
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
