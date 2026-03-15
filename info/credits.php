<?php
	session_start();

	require_once $_SERVER['DOCUMENT_ROOT'].'/core/utilities/userutils.php';
	$user = UserUtils::RetrieveUser();

	$header_check_user = $user;

	$excludelist = [
		1
	];

	function CreateProfile(int $id, string $description) {
		$profileUser = User::FromID($id);
		
		$name = $profileUser->name;
		$online = $profileUser->IsOnline();
		$thumbs = $profileUser->GetAutoThumbsUrl();

		if($profileUser != null) {
			global $excludelist;
			array_push($excludelist, $id);
			echo <<<EOT
			<td>
				<div>
					<a href="/users/$id/profile">
						<img src="$thumbs&sxy=128" width="128" height="128">
						<span>$name</span>
					</a>
				</div>
				<div style="text-align: center; border: 2px solid black; background: #1a1a1a; padding: 10px;">
					$description
				</div>
			</td>
			EOT;
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>The Contributors! - ANORRL</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<link rel="stylesheet" href="/css/new/main.css">
		<link rel="stylesheet" href="/css/new/credits.css">
		<script src="/js/core/jquery.js"></script>
		<script src="/js/main.js?t=1771413807"></script>
		<style>
			table tr {
				vertical-align: top;
			}
		</style>
	</head>
	<body>
		<div id="Container">
		<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/header.php'; ?>
			<div id="Body">
				<div id="BodyContainer">
					<h2>Credits!</h2>
					<div id="CreditsContainer">
						<div class="Note">This is a page dedicated to the people who has ever contributed to this projects (AND MAKING IT COOLER!)</div>
						<hr>
						<table>
							<tr>
								<?php CreateProfile(
									41,
									"Created the ANORRL studio icons you see!!! (and also other things like corescripts and stuff)"
								); ?>

								<?php CreateProfile(
									43,
									"Helped me find and fix up some vulnerabilities, along with supplying help for datastores!!! They also created the arbiter program (for 2016 only) so like thank them for that too..."
								); ?>

								<?php CreateProfile(
									2,
									"Helped me sanity check this when I was first testing this and also created most of the splash screens with me!"
								); ?>
							</tr>
							<tr>
								<?php CreateProfile(
									22,
									"Created splash screen #19 for the studio!"
								); ?>
								<?php CreateProfile(
									17,
									"Created splash screen #18 for the studio!"
								); ?>
								<?php CreateProfile(
									46,
									"Created most of the client icons that aren't 2016 !!!!!"
								); ?>
							</tr>
							<tr>
								<?php CreateProfile(
									5,
									"Created the emote music for dywec, californiagurls and caramelldansen!!!"
								); ?>
								<?php CreateProfile(
									48,
									"Created the badge icons you see on the site!"
								); ?>
								<?php CreateProfile(
									72,
									"Created the topbar icons you see in the client!"
								); ?>
							</tr>
							<tr>
								<?php CreateProfile(
									60,
									"Contributed to the website's development!"
								); ?>
							</tr>
						</table>
						<hr>
						<p>But these aren't the only ones, no.</p>
						<p>Thank you everyone (in this list below) for playing on/participating in the community for this project!</p>
						<div style="text-align: center; word-spacing: 10px;">
						<?php
							foreach(UserUtils::GetAllUsers() as $user) {
								if(in_array($user->id, $excludelist)) {
									continue;
								}
								$userid = $user->id;
								$username = $user->name;

								echo <<<EOT
								<a href="/users/$userid/profile">$username,</a> 
								EOT;
							}
						?>
						</div>
						<p>Even if you don't say much, even if you don't play that much. Just knowing you like and play on this at all means enough to me :]</p>
					</div>
				</div>
				<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/footer.php'; ?>
			</div>
		</div>
	</body>
</html>
