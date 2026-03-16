<?php
	session_start();

	require_once $_SERVER['DOCUMENT_ROOT'].'/core/utilities/userutils.php';
	$user = UserUtils::RetrieveUser();

	if($user == null) {
		die(header("Location: /login"));
	}

	$settings = UserSettings::Get($user);

	if(isset($_POST['ANORRL$Update$Profile$Bio']) &&
	   isset($_POST['ANORRL$Update$Profile$Submit'])) {
		
		$result = $user->UpdateBio(trim($_POST['ANORRL$Update$Profile$Bio']));

		if($result['error']) {
			$_SESSION['ANORRL$Update$ProfileError'] = true;
			$_SESSION['ANORRL$Update$ProfileResult'] = $result['reason'];
			die(header("Location: /my/profile"));
		} else {
			die(header("Location: /users/".$user->id."/profile"));
		}
	}

	if(isset($_POST['ANORRL$Update$Profile$CSS']) &&
	   isset($_POST['ANORRL$Update$Profile$CSS$Submit'])) {
		
		$result = $user->SetUserCSS(trim($_POST['ANORRL$Update$Profile$CSS']));

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

		$result = $user->SetProfilePicture($file);
		
		if($result['error']) {
			$_SESSION['ANORRL$Update$ProfileError'] = true;
			$_SESSION['ANORRL$Update$ProfileResult'] = $result['reason'];
			die(header("Location: /my/profile"));
		} else {
			die(header("Location: /users/".$user->id."/profile"));
		}
	}

	if(isset($_POST['action']) && $_POST['action'] == 'ANORRL$Update$Profile$ResetProfilePicture') {
		$user->ResetProfilePicture();
	}
	
	if(isset($_POST['ANORRL$Update$Settings$Submit'])) {
		$randoms_enabled = isset($_POST['ANORRL$Update$Settings$RandomsEnabled']);
		$teto_enabled = isset($_POST['ANORRL$Update$Settings$TetoEnabled']);
		$emotesounds_enabled = isset($_POST['ANORRL$Update$Settings$EmoteSoundsEnabled']);
		$accessibility_enabled = isset($_POST['ANORRL$Update$Settings$AccessibilityEnabled']);
		$headshots_enabled = isset($_POST['ANORRL$Update$Settings$HeadshotsEnabled']);
		$nightbg_enabled = isset($_POST['ANORRL$Update$Settings$NightBGEnabled']);

		$settings->SetRandomsEnabled($randoms_enabled);
		$settings->SetTetoEnabled($teto_enabled);
		$settings->SetEmoteSoundsEnabled($emotesounds_enabled);
		$settings->SetAccessibilityEnabled($accessibility_enabled);
		$settings->SetHeadshotsEnabled($headshots_enabled);
		$settings->SetNightBGEnabled($nightbg_enabled);

		die(header("Location: /my/profile"));
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Profile - ANORRL</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<link rel="stylesheet" href="/css/new/main.css">
		<link rel="stylesheet" href="/css/new/forms.css">
		<script src="/js/core/jquery.js"></script>
		<script src="/js/main.js?t=1771413807"></script>
		<script>
			function RemovePicture() {
				$.post("/my/profile", {"action": "ANORRL$Update$Profile$ResetProfilePicture"}, function() {
					window.location.reload();
				})
			}

			$(function () {
				$("input[type=file]")[0].onchange = e => { 
					//var file = e.target.files[0];
					$("#PictureForm").submit();
				}
			})
			
		</script>
	</head>
	<body>
		<div id="Container">
			<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/header.php'; ?>
			<div id="Body">
				<div id="BodyContainer">
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
						<div id="DetailsBox">
							<h3>User Profile CSS</h3>
							<div id="FormStuff">
								<span>Ok so this is where you can change your profile stuff... have a go i guess?</span>
								<textarea name="ANORRL$Update$Profile$CSS"><?= $user->GetUserCSS() ?></textarea>
								<input type="submit" value="Update" name="ANORRL$Update$Profile$CSS$Submit">
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
										<td>Emote Sounds (W.I.P/T.B.A)</td>
										<td>
											<input name="ANORRL$Update$Settings$EmoteSoundsEnabled" type="checkbox" <?php if($settings->emotesounds_enabled): ?>checked<?php endif ?>>
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
									<img style="width: 290px;border: 2px solid black;background: #1a1a1a;" src="/thumbs/profile?id=<?= $user->id ?>&sxy=290&nocompress">
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
					
				</div>
				<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/footer.php'; ?>
			</div>
		</div>
	</body>
</html>
<?php
	unset($_SESSION['ANORRL$Update$ProfileError']);
?>
