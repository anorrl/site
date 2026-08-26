<?php
	use anorrl\User;
	use anorrl\Page;
	use anorrl\UserSettings;
	use anorrl\enums\AssetType;

	use Michelf\MarkdownExtra;

	// No id parameter? GET OUT!
	if(!isset($id)) {
		redirect("/my/home");
	}

	$user = User::FromID(intval($id));

	if(!$user) {
		redirect("/my/home");
	}

	$bgm = $user->getSettings()->background_music;
	$owner = false;
	$online = $user->isOnline();
	$items = $user->getWearing(null, true);
	$friends = $user->getFriends();
	$friends_count = count($friends);
	$followers_count = $user->getFollowersCount();
	$following_count = $user->getFollowingCount();

	if(ARLAUTH) {
		$settings = SESSION->settings;
		$owner = $user->id == SESSION->user->id;
	} else {
		$settings = UserSettings::Get();
	}

	if($bgm && !$bgm->isUsable() || !$settings->profile_music)
		$bgm = null;

	

	//str_replace("\n", "<br>", UtilUtils::RecurseRemove(UtilUtils::TurnUrlIntoHyperlink($user->blurb), "\r\n\r\n\r\n", "\n\n"))

	$bio = MarkdownExtra::defaultTransform($user->blurb);

	$page = new Page($owner ? "Your Profile" : "{$user->name}'s Profile", $owner ? "user_profile" : null);
	if($user->has_pfp_set)
		$page->setIcon($user->getThumbsUrlProfile());
	$page->addScript("/wimpy/wimpy.js");
	$page->addStylesheet("/css/cropper.min.css");
	$page->addScript("/js/core/cropper.min.js");
	$page->addScript("/js/core/jquery-cropper.min.js");
	$page->addScript("/js/core/jquery-modal.js");
	$page->addScript("/js/user.js");
	$page->addStylesheet("/css/comments.css");
	$page->addStylesheet("/css/profile.css");
	$page->addScript("/js/comments.js");
	
	$page->load3DScripts();

	$page->addStylesheet($user->getTypedURL("css?t=".time()), false);

	$page->addValue("profile", $user->id);

	$page->loadHeader();

	$owner_look = "it's you.";

	if($owner) {
		if(!isset($_SESSION['ANORRL$Owner$StopLooking']))
			$_SESSION['ANORRL$Owner$StopLooking'] = 0;

		$_SESSION['ANORRL$Owner$StopLooking']++;

		if($_SESSION['ANORRL$Owner$StopLooking'] > 10)
			$owner_look = "still you...";
		
		if($_SESSION['ANORRL$Owner$StopLooking'] > 20)
			$owner_look = "you must really like yourself...";
	}

	$profile_badges = $user->getProfileBadges();
	$badges = $user->getUserBadges();
	$badges_count = $user->getUserBadgesTotalCount();
	$favourited_places = $user->getFavouritedAssets(AssetType::PLACE, '', false, 1, 7);
	$fav_places_count = count($favourited_places);

	$friends_upper_limit = 7;
	$friends = $user->getFriends();
	$friends_count = count($friends);
	$has_friends = $friends_count > 0;
	$too_many_friends = $friends_count > $friends_upper_limit;

	shuffle($friends);
	
	if(count($friends) > $friends_upper_limit) {
		$new_friends = [];
		for($i = 1; $i <= $friends_upper_limit; $i++) {
			$new_friends[] = $friends[count($friends)-$i];
		}

		$friends = $new_friends;
	}
?>
<?php $page->loadTemplate("layouts/comments"); ?>

<div id="crop-modal" class="box" modal>
	<h2>crop yo shit!</h2>
	<img id="cropper-img">
	<div style="margin-top: 5px;">
		<!-- evil -->
		<a href="#" rel="modal:close" style="color:white">
			<button class="button">cancel</button>
			<button class="button" rel="save">save</button>
		</a>
	</div>
</div>
<div id="image-modal" class="box" modal>
	<h2>viewing <?= $user->name ?>!</h2>
	<a href="<?= $user->getThumbsUrl()?>" target="__blank"><img src="<?= $user->getThumbsUrl()?>" width="420" height="420"></a>
	<div style="margin-top: 5px;">
		<!-- evil -->
		<a href="#" rel="modal:close" style="color:white">
			<button class="button">close</button>
		</a>
	</div>
</div>
<input type="file" hidden accept="image/*"/>

<div id="profile-container" style="background-image: url('<?= $user->getThumbsUrlBanner() ?>');">
	<div style="padding: 30px; display: flex;">
		<div id="profile-picture">
			<?php if($owner): ?>
			<div id="controls">
				<button class="button" data-method="upload-pfp">change</button>
				<button class="button" data-method="remove-pfp">delete</button>
			</div>
			<?php endif ?>
			<a title="<?= $owner ? "your" : "{$user->name}'s" ?> profile pic!" href="open-modal" target="__blank"><img src="<?= $user->getThumbsUrl(161)?>"></a>
		</div>

		<div id="profile-stats"> 
			<div id="profile-name">
				<img <?php if(!$owner): ?>title="this means they're <?= $online ? "online" : "offline" ?>!"<?php endif ?> src="/public/images/OnlineStatusIndicator_Is<?= $online ? "Online" : "Offline" ?>.png" width="12">
					<?= $user->name ?>
					<?php if($user->admin): ?>
					<img src="/public/images/icons/shield.png" style="height: 18px; margin-bottom: -3px;" title="this fellas is an admin!!!">
					<?php endif ?>
				</div>
			<?php if($user->getLatestStatus()): ?>
			<div style="padding-top: 5px; font-style: italic">
				<span class="quote">"</span><?= $user->getLatestStatus()->content ?><span class="quote">"</span>
			</div>
			<?php endif ?>
			<div style="padding-top: 5px;">
				<a href="<?= $user->getTypedURL("friends")   ?>"><b><?= $friends_count   ?></b> Friend<?=   $friends_count   == 1 ? "" : "s"  ?></a> |
				<a href="<?= $user->getTypedURL("followers") ?>"><b><?= $followers_count ?></b> Follower<?= $followers_count == 1 ? "" : "s"  ?></a> |
				<a href="<?= $user->getTypedURL("following") ?>"><b><?= $following_count ?></b> Following</a>
			</div>
			
			<div style="margin-top: 5px;">
				<?php if($owner): ?>
					<button class="button"><?= $owner_look ?></button>
				<?php else: ?>
					<button class="button">follow</button>
					<button class="button">friend</button>
					<button class="button">block</button>
				<?php endif ?>
			</div>
			<?php if($owner): ?>
			<div id="banner-controls">
				<button class="button" data-method="upload-banner">change</button>
				<button class="button" data-method="remove-banner">delete</button>
			</div>
			<?php endif ?>
		</div>	
		
	</div>
</div>


<div class="box" id="buttons">
	<a class="button" href="#about" data-tab="about">about</a>
	<a class="button" href="#creations" data-tab="creations">creations</a>
</div>

<div data-tab="about">
	<div style="display: flex; gap: 10px;">
		<?php if($user->blurb != ""): ?>
		<div style="flex: 1;">
			<h4 class="page-title">.about</h4>
			<div class="box" id="profile-bio" <?php if(!$bgm): ?>style="max-width:910px"<?php endif ?>><?= $bio ?></div>
		</div>
		<?php endif ?>
		<?php if($bgm): ?>
		<div style="margin: 0 auto;">
			<h4 class="page-title">.music</h4>
			<div style="margin-bottom: 5px;">
				<div style="width: 300px; white-space: nowrap;">
					<div class="box" style="margin-left:-2px; text-align: center;">
						<a href="<?= $bgm->getURL() ?>">
							<div style="border-bottom: 1px solid var(--lighter-border-color); padding: 5px 0px;">
								<img src="<?= $bgm->getThumbsUrl(); ?>" style="width: 206px;">
							</div>
							<div >
								<h4 style="margin: 5px 0px; margin-bottom: 3px;"><?= $bgm->name ?></h4>
							</div>
						</a>
					</div>
					<div 
						data-wimpyplayer
						data-skin="/public/wimpy/skins/Slick_modified.tsv"
						data-loop="2"
						data-disablecontrols="next,playlist,rewind,getid3"
						style="text-align: center; margin-top: 5px; width: 100%;"
						data-media="/asset/?id=<?=$bgm->id?>.mp3"
						data-volume="0.4"
					></div>
				</div>
			</div>
		</div>
		<?php endif ?>
	</div>
	<?php if($has_friends): ?>
	<h4 class="page-title">.friends (<?= $friends_count ?>) <a href="<?= $user->getTypedURL("friends") ?>">(show more)</a></h4>
	<div class="box">
		<ul class="horizontal" style="height: auto;">
	<?php foreach($friends as $user): ?>
			<li>
				<div class="badge" title="<?= $user->name ?>">
					<a href="<?=$user->getURL() ?>">
						<img src="<?= $user->getThumbsURL() ?>">
						<div><?= $user->name ?></div>
					</a>
				</div>
			</li>
	<?php endforeach ?>
		</ul>
	</div>
	<?php endif ?>
	<?php if($fav_places_count > 0): ?>
	<h4 class="page-title">.favourite_games (<?= $fav_places_count ?>) <a href="<?= $user->getTypedURL("favourites#places") ?>">(show more)</a></h4>
	<div class="box">
		<ul class="horizontal" style="height: auto;">
	<?php foreach($favourited_places as $place): ?>
			<li>
				<div class="game" title="<?= $place->name ?>">
					<a href="<?=$place->getURL() ?>">
						<img src="<?= $place->getThumbsURL() ?>">
						<div id="name"><?= $place->name ?></div>
					</a>
					<div>by <a href="<?= $place->creator->getURL() ?>"><?= $place->creator->name ?></a></div>
				</div>
			</li>
	<?php endforeach ?>
		</ul>
	</div>
	<?php endif ?>
	<div class="multi-titles">
		<div>
			<h4 class="page-title">.character</h4>
		</div>
		<div>
			<h4 class="page-title">.items</h4>
		</div>
	</div>
	<div class="box" id="character-container" style="z-index: 2">
		<div style="text-align: center;">
			<div class="thumbnail-holder" width="300" height="300">
				<button id="thumbnail-switcher" data-3d></button>
				<span class="thumbnail-span" data-3d-url="/thumbnail/get?user=<?= $user->id ?>" style="width:300px;height:300px"></span>
				<img src="<?= $user->getThumbsUrlAvatar() ?>" width="300">
			</div>
		</div>
		<?php if(count($items) > 0): ?>
		<div id="character-items">
			<?php foreach($items as $item): ?>
				<div class="accoutrement-item">
					<a href="<?= $item->getURL() ?>" title="<?= $item->name ?>">
						<img src="<?= $item->getThumbsUrl() ?>" width="100" height="100">
					</a>
				</div>
			<?php endforeach ?>
		</div>
		<?php else :?>
		<div id="no-character-items">
			<b><?= $user->name ?> has no items!</b>
		</div>
		<?php endif ?>
	</div>
	
	<?php if(count($profile_badges) > 0): ?>
	<h4 class="page-title">.profile_badges (<?= count($profile_badges) ?>) <?php if(count($profile_badges) > 7): ?><a href="ANORRL.Users.ToggleProfileBadges()">(show more)</a><?php endif ?></h4>
	<div class="box">
		<ul class="horizontal">
	<?php foreach($profile_badges as $badge): ?>
			<li>
				<div class="badge">
					<a href="/badges#badge<?=$badge->ordinal() ?>">
						<img src="/public/images/badges/<?= str_replace(" ", "", $badge->name()) ?>.png">
						<div><?= strtolower($badge->name()) ?></div>
					</a>
				</div>
			</li>
	<?php endforeach ?>
		</ul>
	</div>
	<?php endif ?>

	<?php if($badges_count > 0): ?>
	<h4 class="page-title">.badges (<?= $badges_count ?>) <?php if($badges_count > 7): ?><a href="<?= $user->getTypedURL("inventory#badges") ?>">(show more)</a><?php endif ?></h4>
	<div class="box">
		<ul class="horizontal">
	<?php foreach($badges as $badge): ?>
			<li>
				<div class="badge">
					<a href="<?=$badge->getURL() ?>">
						<img src="<?= $badge->getThumbsUrl() ?>">
						<div><?= $badge->name ?></div>
					</a>
				</div>
			</li>
	<?php endforeach ?>
		</ul>
	</div>
	<?php endif ?>
	<h4 class="page-title">.statistics</h4>
	<div class="box" id="statistics">
		<table>
			<tr>
				<td>
					<div class="stat-title">.join_date</div>
					<div><?= $user->join_date->format("d/m/Y"); ?>
				</td>
				<td>
					<div class="stat-title">.place_visits</div>
					<div><?= $user->getAllPlaceVisits(); ?></div>
				</td>
				<td>
					<div class="stat-title">.knockouts</div>
					<div><?= $user->getKnockouts() ?></div>
				</td>
				<td>
					<div class="stat-title">.wipeouts</div>
					<div><?= $user->getWipeouts() ?></div>
				</td>
			</tr>
		</table>
	</div>
</div>
<div data-tab="creations">

</div>
<?php $page->loadTemplate("layouts/comment_poster"); ?>
<div id="report-banner" class="box">
	got something to report about this user? <a href="<?= $user->getTypedURL("report#profile") ?>">click here!</a>
</div>
<?php
	$page->loadFooter();
?>
