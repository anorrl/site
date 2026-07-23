<?php
	use anorrl\User;
	use anorrl\Page;
	use anorrl\utilities\UtilUtils;

	if(!UtilUtils::HasBeenRewritten()) {
		redirect("/my/home");
	}
	
	// No id parameter? GET OUT!
	
	if(!isset($id)) {
		redirect("/my/home");
	}

	$user = User::FromID(intval($id));

	if($user == null) {
		redirect("/my/home");
	}

	$settings = SESSION->settings;
	$bgm = null;

	$owner = $user->id == SESSION->user->id;

	$page = new Page($owner ? "Your Profile" : "{$user->name}'s Profile", $owner ? "user_profile" : null);
	
	$page->loadHeader2();

	if($settings->profile_music) {
		$bgm = $user->getSettings()->background_music;
		if($bgm && !$bgm->isUsable()) {
			$bgm = null;
		}

		if($bgm)
			$page->loadWimpy("/asset/?id={$bgm->id}", $bgm->name, "", $bgm->getURL());
	}
?>
<script src="/public/wimpy/wimpy.js"></script>
<style>
	#profile-container {
		position:relative;
		width: 970px;
		height: 220px;
		background-color:black;
		background-image: url('/api/background?t=<?= time() ?>');
		border: 2px solid var(--border-color);
	}

	#profile-picture {
		width: 161px;
		height: 161px;
		position: relative;
	}

	#profile-picture #controls {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		gap: 5px;

		position: absolute;
		background-color: rgba(0,0,0, 0.3);
		transition: opacity 0.25s;
		opacity: 0;
		inset: 0px;
		right: -6px;
		bottom: -6px;
	}

	#profile-picture:hover #controls {
		opacity: 1;
		cursor: pointer;
	}

	#profile-picture img {
		border-image: repeating-linear-gradient(
			-55deg,
			#000,
			#000 10px,
			#ffb101 10px,
			#ffb101 20px
		) 10;
		border-style: solid;
		width: 100%;
	}

	#profile-stats {
		user-select: none;
		display: flex;
		justify-content: center;
		flex-direction: column;
		gap: 3px;
		margin-left: 15px;
	}

	#profile-stats div {
		background: rgba(0,0,0, 0.6);
		font-family: "Fira Mono";
		letter-spacing: 2px;
		padding: 5px 10px;
		width:fit-content;
	}

	#profile-stats a {
		color: white;
		transition: 0.5s font-size;
	}

	#profile-stats a:hover {
		font-weight: bold;
		*text-decoration: none;
		font-size: 14px;
	}

	#profile-stats #profile-name {
		font-size: 18px;
		font-style: italic;
		font-weight: bold;
	}

	.quote {
		font-weight: bold;
		font-size: 15px;
		margin-top: -5px;
		display: inline-block;
	}

	#banner-controls {
		position: absolute;display: flex; flex-direction: column;top:15px;right:15px; gap: 5px;
		opacity: 0;
		transition: opacity 0.25s;
	}

	#profile-container:hover #banner-controls {
		opacity: 1;
	}

	#report-banner {
		font-size: 11px; text-align: center; padding: 8px 16px; 
		width: fit-content;
		margin: 0 auto;
		letter-spacing: 2px;
		font-weight: bolder;
		font-style: italic;
		color: #ddd;
		margin-top:5px;
	}

	.page-title {
		margin-top: 5px; font-size: 13px
	}

	#character-container {
		padding: 5px; display: flex;
	}

	#character-container > div {
		flex: 1;
		padding: 10px;
	}
</style>
<div id="profile-container">
	<div style="padding: 30px; display: flex;">
		<div id="profile-picture">
			<?php if($owner): ?>
			<div id="controls">
				<button class="button">change</button>
				<button class="button">delete</button>
			</div>
			<?php endif ?>
			<img src="<?= $user->getThumbsUrl(161)?>">
		</div>

		<div id="profile-stats"> 
			<div id="profile-name"><?= $user->name ?></div>
			<?php if($user->getLatestStatus()): ?>
			<div style="padding-top: 5px; font-style: italic">
				<span class="quote">"</span><?= $user->getLatestStatus()->content ?><span class="quote">"</span>
			</div>
			<?php endif ?>
			<div style="padding-top: 5px;">
				<a href="/users/<?= $user->id ?>/friends"><b><?= $user->getFriendsCount() ?></b> Friends</a> |
				<a href="/users/<?= $user->id ?>/followers"><b><?= $user->getFollowersCount() ?></b> Followers</a> |
				<a href="/users/<?= $user->id ?>/following"><b><?= $user->getFollowingCount() ?></b> Following</a>
			</div>
			
			<div style="*background: none; *padding: 0px; margin-top: 5px;">
				<button class="button">follow</button>
				<button class="button">friend</button>
				<button class="button">block</button>
			</div>
			<?php if($owner): ?>
			<div id="banner-controls">
				<button class="button">change</button>
				<button class="button">delete</button>
			</div>
			<?php endif ?>
		</div>	
		
	</div>
</div>

<?php if($user->blurb != ""): ?>
<h4 class="page-title">.about</h4>
<div class="box" style="padding: 15px 30px; font-size: 13px;  font-family: 'Fira Mono';" >
	<?= UtilUtils::TurnUrlIntoHyperlink($user->blurb) ?>
</div>
<?php endif ?>

<h4 class="page-title">.character</h4>
<div class="box" id="character-container">
	<div>
		hi
	</div>
	<div style="border-left: 1px solid var(--lighter-border-color)">
		hi
	</div>
</div>

<div id="report-banner" class="box">
	got something to report about this user? <a href="/report?userid=<?= $user->id ?>">click here!</a>
</div>

<?php
	$page->loadFooter2();
?>
