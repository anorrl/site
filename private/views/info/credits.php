<?php
	
	use anorrl\utilities\UserUtils;
	use anorrl\Page;
	use anorrl\User;

	$user = SESSION->user;

	$excludelist = [
		1
	];

	function createProfile(int $id, string $description) {
		$profileUser = User::FromID($id);

		if(!$profileUser) {
			return;
		}
		
		$name = $profileUser->name;
		$thumbs = $profileUser->getThumbsUrl();

		if($profileUser != null) {
			global $excludelist;
			$excludelist[] = $id;
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

	$page = new Page("The Contributors!");
	$page->loadHeader();
?>
<style>
	table tr {
		vertical-align: top;
	}
</style>
<h2>Credits!</h2>
<div id="CreditsContainer">
	<div class="Note">This is a page dedicated to the people who has ever contributed to this projects (AND MAKING IT COOLER!)</div>
	<hr>
	<?php if(false): ?>
	<table>
		<tr>
			<?php createProfile(
				41,
				"Created the ANORRL studio icons you see!!! (and also other things like corescripts and stuff)"
			); ?>

			<?php createProfile(
				43,
				"Helped me find and fix up some vulnerabilities, along with supplying help for datastores!!! They also created the arbiter program (for 2016 only) so like thank them for that too..."
			); ?>

			<?php createProfile(
				2,
				"Helped me sanity check this when I was first testing this and also created most of the splash screens with me!"
			); ?>
		</tr>
		<tr>
			<?php createProfile(
				22,
				"Created splash screen #19 for the studio!"
			); ?>
			<?php createProfile(
				17,
				"Created splash screen #18 for the studio!"
			); ?>
			<?php createProfile(
				46,
				"Created most of the client icons that aren't 2016 !!!!!"
			); ?>
		</tr>
		<tr>
			<?php createProfile(
				5,
				"Created the emote music for dywec, californiagurls and caramelldansen!!!"
			); ?>
			<?php createProfile(
				48,
				"Created the badge icons you see on the site!"
			); ?>
			<?php createProfile(
				72,
				"Created the topbar icons you see in the client!"
			); ?>
		</tr>
		<tr>
			<?php createProfile(
				60,
				"Contributed to the website's development!"
			); ?>
			<?php createProfile(
				53,
				"Added a few funny and silly stuff to the site!"
			); ?>
		</tr>
	</table>
	<?php endif ?>
	<hr>
	<p>But these aren't the only ones, no.</p>
	<p>Thank you everyone (in this list below) for playing on/participating in the community for this project!</p>
	<div style="text-align: center; word-spacing: 10px;">
	<?php
		foreach(UserUtils::GetAllUsers() as $user) {
			if(in_array($user->id, $excludelist)) {
				continue;
			}
			
			echo <<<EOT
			<a href="{$user->getURL()}">{$user->name},</a> 
			EOT;
		}
	?>
	</div>
	<p>Even if you don't say much, even if you don't play that much. Just knowing you like and play on this at all means enough to me :]</p>
</div>
<?php $page->loadFooter(); ?>