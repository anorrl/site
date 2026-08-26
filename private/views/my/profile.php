<?php
	use anorrl\Page;

	$user = SESSION->user;
	$settings = SESSION->settings;

	if(isset($_POST['ANORRL$Update$Profile$Bio']) &&
	   isset($_POST['ANORRL$Update$Profile$Submit'])) {
		
		$result = $user->updateBio(trim($_POST['ANORRL$Update$Profile$Bio']));

		if(!$result['success']) {
			$_SESSION['ANORRL$Update$ProfileError'] = true;
			$_SESSION['ANORRL$Update$ProfileResult'] = $result['reason'];
			redirect("/my/profile");
		} else {
			redirect($user->getUrl());
		}
	}

	if(isset($_POST['ANORRL$Update$Profile$BGM']) &&
	   isset($_POST['ANORRL$Update$Profile$BGM$Submit'])) {
		
		SESSION->settings->setBackgroundMusic(intval(trim($_POST['ANORRL$Update$Profile$BGM'])));

		redirect("/my/profile");
	}
	
	if(isset($_POST['ANORRL$Update$Profile$PLIcon']) &&
	   isset($_POST['ANORRL$Update$Profile$PLIcon$Submit'])) {
		
		SESSION->settings->setPlayerListIcon(intval(trim($_POST['ANORRL$Update$Profile$PLIcon'])));

		redirect("/my/profile");
	}

	if(isset($_POST['ANORRL$Update$Profile$CSS']) &&
	   isset($_POST['ANORRL$Update$Profile$CSS$Submit'])) {
		
		$result = SESSION->settings->setCSS(trim($_POST['ANORRL$Update$Profile$CSS']));

		if(!$result) {
			$_SESSION['ANORRL$Update$ProfileError'] = true;
			$_SESSION['ANORRL$Update$ProfileResult'] = "That was invalid css!";
			redirect("/my/profile");
		} else {
			redirect($user->getURL());
		}
	}

	if(isset($_FILES['ANORRL$Update$Profile$Picture'])) {
		$file = $_FILES['ANORRL$Update$Profile$Picture'];

		$result = $user->setProfilePicture($file);
		
		if(!$result['success']) {
			$_SESSION['ANORRL$Update$ProfileError'] = true;
			$_SESSION['ANORRL$Update$ProfileResult'] = $result['reason'];
			redirect("/my/profile");
		} else {
			redirect($user->getURL());
		}
	}

	if(isset($_POST['action']) && $_POST['action'] == 'ANORRL$Update$Profile$resetProfilePicture') {
		$user->resetProfilePicture();
	}
	
	if(isset($_POST['ANORRL$Update$Settings$Submit']) && isset($_POST['ANORRL$Update$Settings$Username'])) {
		$randoms = isset($_POST['ANORRL$Update$Settings$RandomsEnabled']);
		$teto = isset($_POST['ANORRL$Update$Settings$TetoEnabled']);
		$accessibility = isset($_POST['ANORRL$Update$Settings$AccessibilityEnabled']);
		$headshots = isset($_POST['ANORRL$Update$Settings$HeadshotsEnabled']);
		$nightbg = isset($_POST['ANORRL$Update$Settings$NightBGEnabled']);
		$loadingscreens = isset($_POST['ANORRL$Update$Settings$LoadingScreensEnabled']);
		$profile_music = isset($_POST['ANORRL$Update$Settings$ProfileMusicEnabled']);

		$settings->setRandomsEnabled($randoms);
		$settings->setTetoEnabled($teto);
		$settings->setAccessibilityEnabled($accessibility);
		$settings->setHeadshotsEnabled($headshots);
		$settings->setNightBGEnabled($nightbg);
		$settings->setLoadingScreensEnabled($loadingscreens);
		$settings->setProfileMusicEnabled($profile_music);

		$result = $user->updateUsername($_POST['ANORRL$Update$Settings$Username']);

		if(!$result['success']) {
			$_SESSION['ANORRL$Update$ProfileError'] = true;
			$_SESSION['ANORRL$Update$ProfileResult'] = $result['reason'];
		}

		redirect("/my/profile");
	}

	$bgm = $settings->background_music;
	$plicon = $settings->playerlisticon;

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
<style>
	#DetailsBox {
		margin-top: 5px !important;
	}
</style>
<?php if(isset($_SESSION['ANORRL$Update$ProfileError']) && $_SESSION['ANORRL$Update$ProfileError']): ?>
<div class="ErrorTime" style="margin: 5px; border: 2px solid black;">Error: <?= $_SESSION['ANORRL$Update$ProfileResult'] ?></div>
<?php endif ?>
<div class="FormBox">
	<div id="DetailsBox">
		<h3>Your Settings</h3>
		<div id="FormStuff">
			<form method="POST" style="float: left;margin: 15px;margin-top: 75px;">
				<table width="200" >
					<tr title="I love my random images, do you?">
						<td>Random Images</td>
						<td>
							<input name="ANORRL$Update$Settings$RandomsEnabled" type="checkbox" <?php if($settings->randoms): ?>checked<?php endif ?>>
						</td>
					</tr>
					<tr title="Fatass Teto">
						<td>Fatass Teto</td>
						<td>
							<input name="ANORRL$Update$Settings$TetoEnabled" type="checkbox" <?php if($settings->teto): ?>checked<?php endif ?>>
						</td>
					</tr>
					<tr id="Changes the punk font to a cleaner version">
						<td>Accessibility</td>
						<td>
							<input name="ANORRL$Update$Settings$AccessibilityEnabled" type="checkbox" <?php if($settings->accessibility): ?>checked<?php endif ?>>
						</td>
					</tr>
					<tr title="Shows headshots instead of profile pictures when available.">
						<td>Headshots</td>
						<td>
							<input name="ANORRL$Update$Settings$HeadshotsEnabled" type="checkbox" <?php if($settings->headshots): ?>checked<?php endif ?>>
						</td>
					</tr>
					<tr title="Night time!">
						<td>Night Background</td>
						<td>
							<input name="ANORRL$Update$Settings$NightBGEnabled" type="checkbox" <?php if($settings->nightbg): ?>checked<?php endif ?>>
						</td>
					</tr>
					<tr title="Fun little splash screens!">
						<td>Loading Screens</td>
						<td>
							<input name="ANORRL$Update$Settings$LoadingScreensEnabled" type="checkbox" <?php if($settings->loadingscreens): ?>checked<?php endif ?>>
						</td>
					</tr>
					<tr title="Do you want to hear other peoples' music? No? You're boring.">
						<td>Profile Music</td>
						<td>
							<input name="ANORRL$Update$Settings$ProfileMusicEnabled" type="checkbox" <?php if($settings->profile_music): ?>checked<?php endif ?>>
						</td>
					</tr>
				</table>
				<div style="margin-top: 15px;">
					<h3>Change Username</h3>
					<textarea name="ANORRL$Update$Settings$Username" style="height:16px;resize:none;margin-top: 0px;text-align: center;width: 182px;" minlength="3" maxlength="20"><?= $user->name ?></textarea>
				</div>
				<div style="margin-top: 15px;">
					<input type="submit" value="Update" name="ANORRL$Update$Settings$Submit">
				</div>
			</form>
			
			<!-- Ew, when are you going to un-inline this -->
			<form method="POST" style="width: 400px;float: right;" enctype="multipart/form-data" id="PictureForm">
				<span style="display: block;margin-bottom: 10px;font-size: 10px;color: #999;font-style: italic;">Thanks gamma for the template and letting my ass scrutinise it :sob:</span>
				<div style="width:294px;margin: 0 auto;">
					<h4 style="margin: 0;width: 254px;">This what you look like right now...</h4>
					<img style="width: 290px;border: 2px solid black;background: #1a1a1a;" src="<?= $user->getThumbsUrlProfile(290) ?>">
					<div class="FilePicker" style="display: block;margin-top: 10px;">
						<label for="thumbfiles">Choose file</label>
						<input id="thumbfiles" type="file" name="ANORRL$Update$Profile$Picture" accept="image/*">
						<label id="thumbfilename">No file chosen</label>
						<a href="javascript:RemovePicture()">Remove...</a>
					</div>
				</div>
			</form>
			<div style="clear:both"></div>
			
		</div>
	</div>
</div>
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
	<div id="DetailsBox">
		<h3>User Profile CSS</h3>
		<div id="FormStuff">
			<span>Ok so this is where you can change your profile stuff... have a go i guess?</span>
			<textarea name="ANORRL$Update$Profile$CSS"><?= SESSION->settings->css; ?></textarea>
			<input type="submit" value="Update" name="ANORRL$Update$Profile$CSS$Submit">
		</div>
	</div>
</form>
<?php if($settings->profile_music): ?>
<form method="POST" class="FormBox">
	<div id="DetailsBox">
		<h3>Profile Music</h3>
		<div id="FormStuff">
			<span>Here you can input the id of a sound asset and it'll just play when someone views your profile ig</span>
			<?php if($bgm): ?>
			<div style="border: 2px solid black; margin: 10px auto; width: 320px; text-align: center;">
				<img src="<?= $bgm->getThumbsUrl(320) ?>">
				<div style="padding: 5px; background: #333;">
					<a href="<?= $bgm->getURL() ?>"><?= $bgm->name ?></a>
				</div>
			</div>
			<?php endif ?>
			<textarea name="ANORRL$Update$Profile$BGM" style="height:16px;resize:none;margin-top: 10px;text-align: center"><?= $bgm ? $bgm->id : "" ?></textarea>
			<input type="submit" value="Update" name="ANORRL$Update$Profile$BGM$Submit">
		</div>
	</div>
</form>
<?php endif ?>
<form method="POST" class="FormBox">
	<div id="DetailsBox">
		<h3>Player List Icon</h3>
		<div id="FormStuff">
			<span>you can input the id of an image asset and it'll be applied to the player list on every game you join.</span>
			<?php if($plicon): ?>
			<div style="border: 2px solid black; margin: 10px auto; width: 320px; text-align: center;">
				<img src="<?= $plicon->getThumbsUrl(320) ?>">
				<div style="padding: 5px; background: #333;">
					<a href="<?= $plicon->getURL() ?>"><?= $plicon->name ?></a>
				</div>
			</div>
			<?php endif ?>
			<textarea name="ANORRL$Update$Profile$PLIcon" style="height:16px;resize:none;margin-top: 10px;text-align: center"><?= $plicon ? $plicon->id : "" ?></textarea>
			<input type="submit" value="Update" name="ANORRL$Update$Profile$PLIcon$Submit">
		</div>
	</div>
</form>
<?php
	$page->loadFooter();
	unset($_SESSION['ANORRL$Update$ProfileError']);
?>
