<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/core/utilities/userutils.php';
	if(!isset($header_data)) {
		$header_data = null;
	}
	$header_check_user = UserUtils::RetrieveUser($header_data);

	$header_user_settings = UserSettings::Get($header_check_user);

	// 99999999 max
	//very true -skyler
	
	$signsplashes = [
		"Thank you for trying this!",
		"Thank you for using ANORRL!",
		"Thank you for playing!",
		"Thank you for your support!"
	];
	
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
			"iscream"
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

	if(session_status() != PHP_SESSION_ACTIVE) {
		session_start();
	}

	if(!isset($_SESSION['ANORRL$UserPage$RandomImages'])) {
		$_SESSION['ANORRL$UserPage$RandomImages'] = getImagesList();
	}

	$randomNumber = rand(0, 100000);

	$badAppled = $randomNumber > 6500 && $randomNumber < 6515;

	$rand_pic = rollImage();

	function GetRandomSplash(): string {
		$splashes = file($_SERVER["DOCUMENT_ROOT"]."/core/splashes.txt");
		return $splashes[array_rand($splashes)];
	}
	$randomsignsplash = $signsplashes[array_rand($signsplashes)];
?>
<?php if($badAppled): ?>
<style>
	body {
		background: url('/images/badapple.gif') !important;
	}
</style>
<?php endif ?>
<?php if($header_user_settings->nightbg_enabled): ?>
<style>
	body {
		background: url('/images/bkg_night.png') !important;
	}
</style>
<?php endif ?>
<?php if($header_user_settings->randoms_enabled): ?>
<img src="/images/randoms/<?= $rand_pic ?>.png" style="position: fixed;bottom: 0px;left: 0px;width: 250px;z-index: 9999;">
<?php endif ?>
<?php if($header_user_settings->teto_enabled): ?>
<div style="position: fixed;bottom: 0px;right: 10px;width: 250px;z-index: 9999;">
	<div style="width: 210px;background: white;padding: 10px;height: 100px;margin: 0 auto;margin-bottom: -93px;border: 6px solid black;">
		<p style="text-align: center;display: table-cell;vertical-align: middle;width: 210px;height: 100px;"><?= GetRandomSplash() ?></p>
	</div>
	<img style="position: relative;width: 250px;" src="/images/tetospeech.png">
</div>
<?php endif ?>
<?php if($header_user_settings->accessibility_enabled): ?>
<style>
	@font-face {
		font-family: 'punk';
		src: url('/css/SplendidB.ttf');
		*src: url('/css/TransportMedium.eot')\9;
	}
</style>
<?php endif ?>
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
		<a href="/my/home"      <?php if($_SERVER['SCRIPT_NAME'] == "/my/home.php"     		 ):?>selected<?php endif ?>>Home</a>
		<a href="/my/profile"   <?php if($_SERVER['SCRIPT_NAME'] == "/my/profile.php"  		 ):?>selected<?php endif ?>>Account</a>
		<a href="/my/character" <?php if($_SERVER['SCRIPT_NAME'] == "/my/character.php"		 ):?>selected<?php endif ?>>Character</a>
		<a href="/my/friends"   <?php if($_SERVER['SCRIPT_NAME'] == "/my/friends.php"		 ):?>selected<?php endif ?>>Friends</a>
		<a href="/create/"      <?php if($_SERVER['SCRIPT_NAME'] == "/core/create.php" 		 ):?>selected<?php endif ?>>Create</a>
		<a href="/my/stuff"     <?php if($_SERVER['SCRIPT_NAME'] == "/my/stuff.php"    		 ):?>selected<?php endif ?>>Stuff</a>
		<a href="/download"     <?php if($_SERVER['SCRIPT_NAME'] == "/download/index.php"    ):?>selected<?php endif ?>>Download</a>
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
