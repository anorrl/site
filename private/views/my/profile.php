<?php
	use anorrl\Page;

	$user = SESSION->user;
	$settings = SESSION->settings;

	if(isset($_POST['ANORRL$Update$Profile$Bio']) &&
	   isset($_POST['ANORRL$Update$Profile$Submit'])) {
		
		$result = $user->updateBio(trim($_POST['ANORRL$Update$Profile$Bio']));

		if($result['error']) {
			$_SESSION['ANORRL$Update$ProfileError'] = true;
			$_SESSION['ANORRL$Update$ProfileResult'] = $result['reason'];
			die(header("Location: /my/profile"));
		} else {
			die(header("Location: /users/".$user->id."/profile"));
		}
	}

	if(isset($_POST['ANORRL$Update$Profile$BGM']) &&
	   isset($_POST['ANORRL$Update$Profile$BGM$Submit'])) {
		
		SESSION->settings->setBackgroundMusic(intval(trim($_POST['ANORRL$Update$Profile$BGM'])));

		die(header("Location: /my/profile"));
	}

	if(isset($_POST['ANORRL$Update$Profile$CSS']) &&
	   isset($_POST['ANORRL$Update$Profile$CSS$Submit'])) {
		
		$result = SESSION->settings->setCSS(trim($_POST['ANORRL$Update$Profile$CSS']));

		if(!$result) {
			$_SESSION['ANORRL$Update$ProfileError'] = true;
			$_SESSION['ANORRL$Update$ProfileResult'] = "That was invalid css!";
			die(header("Location: /my/profile"));
		} else {
			die(header("Location: /users/".$user->id."/profile"));
		}
	}

	if(isset($_FILES['ANORRL$Update$Profile$Picture'])) {
		$file = $_FILES['ANORRL$Update$Profile$Picture'];

		$result = $user->setProfilePicture($file);
		
		if($result['error']) {
			$_SESSION['ANORRL$Update$ProfileError'] = true;
			$_SESSION['ANORRL$Update$ProfileResult'] = $result['reason'];
			die(header("Location: /my/profile"));
		} else {
			die(header("Location: /users/".$user->id."/profile"));
		}
	}

	if(isset($_POST['action']) && $_POST['action'] == 'ANORRL$Update$Profile$resetProfilePicture') {
		$user->resetProfilePicture();
	}
	
	if(isset($_POST['ANORRL$Update$Settings$Submit'])) {
		$randoms_enabled = isset($_POST['ANORRL$Update$Settings$RandomsEnabled']);
		$teto_enabled = isset($_POST['ANORRL$Update$Settings$TetoEnabled']);
		$accessibility_enabled = isset($_POST['ANORRL$Update$Settings$AccessibilityEnabled']);
		$headshots_enabled = isset($_POST['ANORRL$Update$Settings$HeadshotsEnabled']);
		$nightbg_enabled = isset($_POST['ANORRL$Update$Settings$NightBGEnabled']);
		$loadingscreens_enabled = isset($_POST['ANORRL$Update$Settings$LoadingScreensEnabled']);

		$settings->setRandomsEnabled($randoms_enabled);
		$settings->setTetoEnabled($teto_enabled);
		$settings->setAccessibilityEnabled($accessibility_enabled);
		$settings->setHeadshotsEnabled($headshots_enabled);
		$settings->setNightBGEnabled($nightbg_enabled);
		$settings->setLoadingScreensEnabled($loadingscreens_enabled);

		die(header("Location: /my/profile"));
	}

	$bgm = SESSION->settings->background_music;

	if($bgm && !$bgm->isUsable()) {
		$bgm = null;
	}

	$page = new Page("Profile", "my/profile");
	$page->addStylesheet("/css/new/forms.css");

	$page->loadHeader();
?>
<script>
	function RemovePicture() {
		$.post("/my/profile", {"action": "ANORRL$Update$Profile$resetProfilePicture"}, function() {
			window.location.reload();
		})
	}

	$(function () {
		$("input[type=file]")[0].onchange = e => { 
			$("#PictureForm").submit();
		}
	})
</script>
<?php if(isset($_SESSION['ANORRL$Update$ProfileError']) && $_SESSION['ANORRL$Update$ProfileError']): ?>
<div class="ErrorTime" style="margin: 5px; border: 2px solid black;">Error: <?= $_SESSION['ANORRL$Update$ProfileResult'] ?></div>
<?php endif ?>
<form method="POST" class="FormBox">
	<div id="DetailsBox">
		<h3>About yourself</h3>
		<div id="FormStuff">
			<span>Who are you? What do you like etc etc</span>
			<textarea name="ANORRL$Update$Profile$Bio"><?= $user->blurb ?></textarea>
			<input type="submit" value="Update" name="ANORRL$Update$Profile$Submit">
		</div>
	</div>
</form>
<form method="POST" class="FormBox">
	<div id="DetailsBox" style="margin-top: 5px;">
		<h3>User Profile CSS</h3>
		<div id="FormStuff">
			<span>Ok so this is where you can change your profile stuff... have a go i guess?</span>
			<textarea name="ANORRL$Update$Profile$CSS"><?= SESSION->settings->css; ?></textarea>
			<input type="submit" value="Update" name="ANORRL$Update$Profile$CSS$Submit">
		</div>
	</div>
</form>
<form method="POST" class="FormBox">
	<div id="DetailsBox" style="margin-top: 5px;">
		<h3>Profile Music</h3>
		<div id="FormStuff">
			<span>Here you can input the id of a sound asset and it'll just play when someone views your profile ig</span>
			<?php if($bgm): ?>
			<div style="border: 2px solid black; margin: 10px auto; width: 320px; text-align: center;">
				<img src="<?= $bgm->getThumbsUrl(320) ?>">
				<div style="padding: 5px; background: #333;">
					<a href="<?= $bgm->getUrl() ?>"><?= $bgm->name ?></a>
				</div>
			</div>
			<?php endif ?>
			<textarea name="ANORRL$Update$Profile$BGM" style="height:16px;resize:none;margin-top: 0px;text-align: center"><?= $bgm ? SESSION->settings->background_music : "" ?></textarea>
			<input type="submit" value="Update" name="ANORRL$Update$Profile$BGM$Submit">
		</div>
	</div>
</form>
<form method="POST" class="FormBox">
	<div id="DetailsBox" style="margin-top: 5px;">
		<h3>Your Settings</h3>
		<div id="FormStuff">
			<table width="200" style="margin: 10px auto;">
				<tr>
					<td>Random Images</td>
					<td>
						<input name="ANORRL$Update$Settings$RandomsEnabled" type="checkbox" <?php if($settings->randoms_enabled): ?>checked<?php endif ?>>
					</td>
				</tr>
				<tr>
					<td>Fatass Teto</td>
					<td>
						<input name="ANORRL$Update$Settings$TetoEnabled" type="checkbox" <?php if($settings->teto_enabled): ?>checked<?php endif ?>>
					</td>
				</tr>
				<tr>
					<td>Accessibility</td>
					<td>
						<input name="ANORRL$Update$Settings$AccessibilityEnabled" type="checkbox" <?php if($settings->accessibility_enabled): ?>checked<?php endif ?>>
					</td>
				</tr>
				<tr>
					<td>Headshots</td>
					<td>
						<input name="ANORRL$Update$Settings$HeadshotsEnabled" type="checkbox" <?php if($settings->headshots_enabled): ?>checked<?php endif ?>>
					</td>
				</tr>
				<tr>
					<td>Night Background</td>
					<td>
						<input name="ANORRL$Update$Settings$NightBGEnabled" type="checkbox" <?php if($settings->nightbg_enabled): ?>checked<?php endif ?>>
					</td>
				</tr>
				<tr>
					<td>Loading Screens</td>
					<td>
						<input name="ANORRL$Update$Settings$LoadingScreensEnabled" type="checkbox" <?php if($settings->loadingscreens_enabled): ?>checked<?php endif ?>>
					</td>
				</tr>
			</table>

			<input type="submit" value="Update" name="ANORRL$Update$Settings$Submit">
		</div>
	</div>
</form>
<form method="POST" class="FormBox" id="PictureForm" enctype="multipart/form-data">
	<div id="DetailsBox" style="margin-top: 5px;">
		<h3>Get a look!</h3>
		<div id="FormStuff">
			<span style="display: block;margin-bottom: 10px;font-size: 10px;color: #999;font-style: italic;">Thanks gamma for the template and letting my ass scrutinise it :sob:</span>
			<div style="width:294px;margin: 0 auto;">
				<h4 style="margin: 0;width: 254px;">This what you look like right now...</h4>
				<img style="width: 290px;border: 2px solid black;background: #1a1a1a;" src="<?= $user->getThumbsUrlService("profile", 290) ?>&nocompress">
				<div class="FilePicker" style="display: block;margin-top: 10px;">
					<label for="thumbfiles">Choose file</label>
					<input id="thumbfiles" type="file" name="ANORRL$Update$Profile$Picture" accept="image/*">
					<label id="thumbfilename">No file chosen</label>
					<a href="javascript:RemovePicture()">Remove...</a>
				</div>
			</div>
		</div>
	</div>
</form>
<?php
	$page->loadFooter();
	unset($_SESSION['ANORRL$Update$ProfileError']);
?>
