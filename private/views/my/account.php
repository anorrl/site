<?php
	use anorrl\Page;

	$user = SESSION->user;
	$settings = SESSION->settings;

	// todo: move this to api and javascript it

	if(isset($_POST['ANORRL$Update$Profile$BGM']) &&
	   isset($_POST['ANORRL$Update$Profile$BGM$Submit'])) {
		
		$settings->setBackgroundMusic(intval(trim($_POST['ANORRL$Update$Profile$BGM'])));

		redirect("/my/account");
	}
	
	if(isset($_POST['ANORRL$Update$Profile$PLIcon']) &&
	   isset($_POST['ANORRL$Update$Profile$PLIcon$Submit'])) {
		
		$settings->setPlayerListIcon(intval(trim($_POST['ANORRL$Update$Profile$PLIcon'])));

		redirect("/my/account");
	}

	if(isset($_POST['ANORRL$Update$Profile$CSS']) &&
	   isset($_POST['ANORRL$Update$Profile$CSS$Submit'])) {
		
		$result = $settings->setCSS(trim($_POST['ANORRL$Update$Profile$CSS']));

		if(!$result) {
			$_SESSION['ANORRL$Update$ProfileError'] = true;
			$_SESSION['ANORRL$Update$ProfileResult'] = "That was invalid css!";
			redirect("/my/account");
		} else {
			redirect($user->getURL());
		}
	}

	if(isset($_POST['action']) && $_POST['action'] == 'ANORRL$Update$Profile$resetProfilePicture') {
		$user->resetProfilePicture();
	}
	
	if(isset($_POST['ANORRL$Update$Settings$Submit']) && isset($_POST['ANORRL$Update$Settings$Username'])) {
		$headshots = isset($_POST['ANORRL$Update$Settings$HeadshotsEnabled']);
		$nightbg = isset($_POST['ANORRL$Update$Settings$NightBGEnabled']);
		$profile_music = isset($_POST['ANORRL$Update$Settings$ProfileMusicEnabled']);

		$settings->setHeadshotsEnabled($headshots);
		$settings->setNightBGEnabled($nightbg);
		$settings->setProfileMusicEnabled($profile_music);

		$result = $user->updateUsername($_POST['ANORRL$Update$Settings$Username']);
		if(!$result['success']) {
			$_SESSION['ANORRL$Update$ProfileError'] = true;
			$_SESSION['ANORRL$Update$ProfileResult'] = $result['reason'];
		}

		redirect("/my/account");
	}

	$bgm = $settings->background_music;
	$plicon = $settings->playerlisticon;

	$page = new Page("Profile", "my/profile");
	$page->loadHeader();
?>
<script>
	function RemovePicture() {
		$.post("", {"action": "ANORRL$Update$Profile$resetProfilePicture"}, function() {
			window.location.reload();
		})
	}
</script>
<style>
	h3 {
		margin-bottom:0px;
		background: black;
		padding: 5px 10px;
		padding-left: 5px;
		width:fit-content;
		margin-left:  5px;
		margin-top:   8px;
	}
</style>
<?php if(isset($_SESSION['ANORRL$Update$ProfileError']) && $_SESSION['ANORRL$Update$ProfileError']): ?>
<div class="ErrorTime" style="margin: 5px; border: 2px solid black;">Error: <?= $_SESSION['ANORRL$Update$ProfileResult'] ?></div>
<?php endif ?>
<div style="display: flex;gap: 10px;justify-content: center;">
	<div style="width:350px; text-align: center" >
		<h3>.settings</h3>
		<div class="box" style="padding: 10px;">
			<table class="box input" width="200" style="text-align: left; margin: 0 auto;">
				<tr title="Night time!">
					<td>Night Background</td>
					<td style="text-align: center">
						<input name="ANORRL$Update$Settings$NightBGEnabled" type="checkbox" <?php if($settings->nightbg): ?>checked<?php endif ?>>
					</td>
				</tr>
				<tr title="Shows headshots instead of profile pictures when available.">
					<td>Headshots Only!</td>
					<td style="text-align: center">
						<input name="ANORRL$Update$Settings$HeadshotsEnabled" type="checkbox" <?php if($settings->headshots): ?>checked<?php endif ?>>
					</td>
				</tr>
				<tr title="Do you want to hear other peoples' music? No? You're boring.">
					<td>Enable Profile Music</td>
					<td style="text-align: center">
						<input name="ANORRL$Update$Settings$ProfileMusicEnabled" type="checkbox" <?php if($settings->profile_music): ?>checked<?php endif ?>>
					</td>
				</tr>
			</table>
			<hr>
			<div style="margin-top: 5px;">
				<input class="button" type="submit" value="update" name="ANORRL$Update$Settings$Submit">
			</div>
		</div>
		<h3>.change_your_name</h3>
		<div class="box"  style="padding: 10px;">
			<input class="box input" name="ANORRL$Update$Settings$Username" style="text-align: center;width: 182px;" minlength="3" maxlength="20" value="<?= $user->name ?>">
			<input class="button" type="submit" value="change">
		</div>
	</div>
	<div style="width: 350px; text-align: center">
		<h3>.profile_css</h3>
		<div class="box">
			<textarea style="width: 300px;height: 150px;resize: vertical;overflow: scroll;" class="box input" name="ANORRL$Update$Profile$CSS" placeholder="remember: this will apply to the whole site and on your profile for other users."><?= $settings->css; ?></textarea>
			<hr>
			<input class="button" type="submit" value="style!" name="ANORRL$Update$Profile$CSS$Submit">
		</div>
	</div>
</div>
<hr>
<div style="display: flex;gap: 10px;justify-content: center;">
	<div style="width: 350px; text-align: center">
		<h3>.profile_music</h3>
		<?php if($bgm): ?>
		<div class="box assetholder">
			<a href="<?= $bgm->getURL() ?>">
				<div id="img-container"><img data-src="<?= $bgm->getThumbsUrl() ?>"></div>
				<h4 id="name"><?= $bgm->name ?></h4>
			</a>
		</div>
		<?php else: ?>
		<div class="box assetholder">
			<a style="color: white; text-decoration: none">
				<div id="img-container"><img src="/public/images/noassets.png" style="height: 120px;object-fit: contain;"></div>
				<h4 id="name">you should set some music on your profile!</h4>
			</a>
		</div>
		<?php endif ?>
		<div class="box" style="padding: 10px;margin-top: 5px;">
			<input name="ANORRL$Update$Profile$BGM" style="text-align: center" class="box input" value="<?= $bgm ? $bgm->id : "" ?>">
			<input class="button" type="submit" value="update" name="ANORRL$Update$Profile$BGM$Submit">
		</div>
	</div>

	<div style="width: 350px; text-align: center">
		<h3>.playerlist_icon</h3>
		<?php if($plicon): ?>
		<div class="box assetholder">
			<a href="<?= $plicon->getURL() ?>">
				<div id="img-container"><img data-src="<?= $plicon->getThumbsUrl() ?>"></div>
				<h4 id="name"><?= $plicon->name ?></h4>
			</a>
		</div>
		<?php else: ?>
		<div class="box assetholder">
			<a style="color: white; text-decoration: none">
				<div id="img-container"><img src="/public/images/noassets.png" style="height: 120px;object-fit: contain;"></div>
				<h4 id="name">you should set a playerlist icon!</h4>
			</a>
		</div>
		<?php endif ?>
		<div class="box" style="padding: 10px;margin-top: 5px;">
			<input name="ANORRL$Update$Profile$BGM" style="text-align: center" class="box input" value="<?= $plicon ? $plicon->id : "" ?>">
			<input class="button" type="submit" value="update" name="ANORRL$Update$Profile$BGM$Submit">
		</div>
	</div>
</div>

<?php
	$page->loadFooter();
	unset($_SESSION['ANORRL$Update$ProfileError']);
?>
