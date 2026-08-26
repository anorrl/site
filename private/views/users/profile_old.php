<?php
	use anorrl\User;
	use anorrl\enums\AssetType;
	use anorrl\Comment;
	use anorrl\Page;
	use anorrl\utilities\UtilUtils;

	if(!UtilUtils::HasBeenRewritten()) {
		redirect("/my/home");
	}
	
	// No id parameter? GET OUT!
	
	if(!isset($id)) {
		redirect("/my/home");
	}

	$get_user = User::FromID(intval($id));

	if($get_user == null) {
		redirect("/my/home");
	}

	$user = SESSION->user;

	$header_data = $get_user;

	$games = $get_user->getOwnedAssets(AssetType::PLACE, "", true, $get_user->id == $user->id);

	if(
		isset($_POST['ANORRL$Comment$Post$Contents']) &&
		isset($_POST['ANORRL$Comment$Post$Submit'])
	) {
		$result = Comment::Post($get_user, $_POST['ANORRL$Comment$Post$Contents']);
		
		if(!$result['success']) {
			$_SESSION['ANORRL$Comment$Post$Error'] = $result['reason'];
		}

		redirect($get_user->getURL());
	}

	$comments = Comment::GetCommentsOn($get_user);
	$comments_count = count($comments);

	$settings = SESSION->settings;

    $bgm = $get_user->getSettings()->background_music;
	if($bgm && !$bgm->isUsable()) {
		$bgm = null;
	}

	$page = new Page($get_user->id == $user->id ? "Your Profile" : "{$get_user->name}'s Profile");

	$page->addStylesheet("/css/new/stuff.css?v=2");
	$page->addStylesheet("/css/new/comments.css");
	$page->addStylesheet("/css/new/my/profile.css?v=15");
	$page->addStylesheet("/users/{$get_user->id}/css?t=".time(), false);
	$page->addStylesheet("/css/new/window.css");
	$page->addStylesheet("/css/new/placelauncher.css?");
	$page->addStylesheet("/css/new/thumbnail.css");
	$page->addStylesheet("/css/new/my/home.css?v=2");

	$page->addScript("/js/placelauncher.js?t=1776506477");
	$page->addScript("/js/user.js?t=1776712536");

	$page->loadHeader();

	if(!$settings->profile_music)
		$bgm = null;

	if($bgm)
		$page->loadWimpy("/asset/?id={$bgm->id}", $bgm->name, $bgm->getThumbsUrl(298, 298, true), $bgm->getURL());
?>
<script src="/public/js/3D/ThumbnailView.js"></script>
<script src="/public/js/3D/ThreeDeeThumbnails.js?v=3"></script>
<script src="/public/js/3D/three.min.js"></script>
<script src="/public/js/3D/MTLLoader.js?v=1"></script>
<script src="/public/js/3D/OBJMTLLoader.js?v=1"></script>
<script src="/public/js/3D/tween.js"></script>
<script src="/public/js/3D/PolygonOrbitControls.js"></script>
<script>
	$(function(){
		//ANORRL.User.GrabFeed(<?= $get_user->id ?>);
	});
	var render = false;
	function flipRenders() {
		render = !render;

		if(render) {
			$("#ProfilePictureYeah").attr("src", "<?= $get_user->getThumbsUrlService("headshot", 200) ?>&nocompress");
		} else {
			$("#ProfilePictureYeah").attr("src", "<?= $get_user->getThumbsUrlService("profile", 200) ?>&nocompress");
		}
	}
</script>
<div id="LaunchingGameContainer">
	<div class="Window">
		<div id="Name">ANORRL</div>
		<div id="Contents" style="padding: 20px;">
			<div id="LoadingAreaContainer">
				<div id="RunningGuy">
					<img src="/public/images/ProgressIndicator4White.gif" width="100">
				</div>
				<p id="LaunchingTextContainer">
					<span id="LaunchingText">ANORRL is launching!</span>
					<img src="/public/images/spinner16x16.gif">
				</p>
				<p id="LauncherQuote">Have you checked the oven recently?</p>
			</div>
			<div id="DownloadClientContainer" style="display: none">
				<img src="/public/images/download/client.png" width="100">
				<p>You should probably <a href="/download">download</a> the client if you haven't already...</p>
			</div>
			<div id="DownloadStudioContainer" style="display: none">
				<img src="/public/images/download/studio.png" width="100">
				<p>You should probably <a href="/download">download</a> the studio if you haven't already...</p>
			</div>
		</div>
	</div>
</div>
<div class="Badge" template><a href=""><img src=""><span></span></a></div>
<div id="UserInfoContainer">
<div id="PaddingContainer">
	<h2 style="margin: 5px 0px; width: 825px;"><?= $get_user->name ?>'s Profile</h2>
	<div id="ProfileImage">
		<div id="ImageContainer">
			<a href="javascript:flipRenders()" style="position: absolute;z-index: 2;bottom: 5px;right: 5px;"><img src="/public/images/icons/switch.png" style="width: 30px;image-rendering: pixelated;"></a>
			<img id="ProfilePictureYeah" src="<?= $get_user->getThumbsUrlService($get_user->setprofilepicture ? "profile" : "headshot") ?>&nocompress">
		</div>
		
		<div id="Controls">
			<?php if($user != null): ?>
				<?php if($user->id != $get_user->id): ?>
					
					<?php
						$friend_button_label = "Add Friend";
						$follow_label = $user->isFollowing($get_user) ? "Unfollow" : "Follow";

						if($user->isFriendsWith($get_user)) {
							$friend_button_label = "Unfriend :[";
						}
						else {
							if($user->isPendingFriendsReq($get_user)) {
								$friend_button_label = "Cancel Req.";
							} else {
								if($user->isIncomingFriendsReq($get_user)) {
									$friend_button_label = "Accept Req.";
								}
							}
						}
					?>

					<button style="width: 107px;" onclick="ANORRL.User.Friend(<?= $get_user->id ?>)"><?= $friend_button_label ?></button>
					<button style="width: 70px;margin-left: 2px;" onclick="ANORRL.User.follow(<?= $get_user->id ?>);"><?= $follow_label ?></button><br>
				<?php else: ?>
				<button style="width: 74px;">It's you.</button>
				<?php endif ?>
			<?php endif ?>
		</div>
	</div>
	<div id="ProfileInfo">
		<div id="Stats">
			<div id="FollowFriendsWhatever">
				<a href="/users/<?= $get_user->id ?>/friends">
					<b id="Numbers"><?= $get_user->getFriendsCount() ?></b> <span>Friends</span>
				</a> |
				<a href="/users/<?= $get_user->id ?>/followers">
					<b id="Numbers"><?= $get_user->getFollowersCount() ?></b> <span>Followers</span>
				</a> |
				<a href="/users/<?= $get_user->id ?>/following">
					<b id="Numbers"><?= $get_user->getFollowingCount() ?></b> <span>Following</span>
				</a>
			</div>
			<div id="OnlineStatusArea">
				<?php $profile_status = $get_user->isOnline() ? "Online" : "Offline"; ?>
				<span class="<?= $profile_status ?>"><b><?= $profile_status ?></b> - <?= $get_user->getOnlineActivity() ?></span>

			</div>
			<div id="OnlineStatusArea" style="padding-top:0px; margin-top:-5px;">
				<span><b>Joined</b>: <?= $get_user->join_date->format('F dS, Y') ?></span>
			</div>
			<div id="Blurb">
				<?php
					if(strlen($get_user->blurb) == 0) {
						echo "<b>This user has no blurb!</b>";
					} else {
						echo str_replace(" ","&nbsp;",str_replace(PHP_EOL, "<br>", $get_user->blurb));
					}
				?>
			</div>
		</div>
	</div>
	<br clear="all">
</div>
</div>
<hr>
<div id="CommentsContainer" style="margin: 10px">
	<h3 style="margin-bottom: 0px;"><?= $get_user->name ?>'s Friends<?php if($get_user->getFriendsCount() > 6): ?> <a href="/users/<?= $get_user->id ?>/friends" style="font-size: 12px;">(See all)</a><?php endif ?></h3>
	<div id="CommentSection" style="background: #111;">
		<?php if($get_user->getFriendsCount() > 0): ?>
			<div id="FriendsContainer">
				<ul id="Friends" style="width: 848px;border: 0px;background: none;padding: 0px; text-align: center;">
				<?php 
					$friends = $get_user->getFriends();
					shuffle($friends);
					
					if(count($friends) > 6) {
						$new_friends = [];
						for($i = 1; $i <= 6; $i++) {
							$new_friends[] = $friends[count($friends)-$i];
						}

						$friends = $new_friends;
					}

					foreach($friends as $friend): ?>

						<li class="Friend">
							<a id="ProfileLink" href="<?= $friend->getURL() ?>">
								<img id="Profile" src="<?= $friend->getThumbsUrl(100) ?>">
								<div id="Name"><?= $friend->name ?></div>
							</a>
						</li>

					<?php endforeach ?>
				</ul>
				
			
			</div>
		<?php else: ?>
			<div id="CommentsDisabled">Aw man! <?= $get_user->name ?> has no friends... :(</div>	
		<?php endif ?>
	</div>
</div>
<hr>
<div id="UserAvatarContainer">
<h3><?= $get_user->name ?>'s Character</h3>
<div id="UserAvatarPane">
	<ul id="AvatarItems">
		<?php if(count($get_user->getWearingArray()) == 0): ?>
		<li>
			<div id="NoItemsOn">
				<?= $get_user->name ?> does not have any items on!
			</div>
		</li>
		<?php else:
			$items = $get_user->getWearing();
			foreach($items as $asset) {
				if($asset instanceof anorrl\Asset) {
					echo <<<EOT
					<li>
						<div class="Asset">
							<a id="NameAndThumbs" href="{$asset->getURL()}">
								<img src="{$asset->getThumbsUrl(130)}">
								<span>{$asset->name}</span>
							</a>
							<a id="Creator" href="{$asset->creator->getURL()}"><span>{$asset->creator->name}</span></a>
						</div>
					</li>
					EOT;
				}
			}
		?>
		
		<?php endif ?>
	</ul>
	<div class="thumbnail-holder" id="AvatarRender">
		<button id="ThumbnailSwitcher" data-3d="false"></button>
		<span class="thumbnail-span" data-3d-url="/thumbnail/get?userid=<?= $get_user->id ?>" style="display: none;"></span>
		<img id="AvatarRenderYeah" src="<?= $get_user->getThumbsUrlService("player") ?>&nocompress">
	</div>
	<br id="Clearer">
</div>
</div>
<?php if(count($games) != 0): ?>
<hr>
<div id="UserGamesContainer">
<h3><?= $get_user->name ?>'s Games</h3>
<table id="ProfileGamesBox">
	<td class="ProfileGame">
		<table>
			<td id="ShowcaseBigImages">
				<div id="NameAndCreator"><a href="" id="Name">Game Name</a></div>
				<img src="">
				<a id="Play" href="javascript:ANORRL.User.JoinTheGame()" data-placejoinid=""></a>
			</td>
			<td id="ShowcaseDetails">
				<code>
					Description hi hihi
				</code>
			</td>
		</table>
	</td>
	<td id="ProfileGames">
		<div style="height: 265px;overflow-x: hidden;overflow-y: scroll;width:244px;padding: 9px;">
			<?php
				foreach($games as $game) {
					$game_id = $game->id;

					if(!$game->public) {
						continue;
					}

					echo <<<EOT
					<a data-placeid="$game_id"><img src="{$game->getThumbsUrl(227, 128)}"></a>
					EOT;
				}
			?>
		</div>
	</td>
</table>
</div>
<?php endif ?>
<hr>
<div id="UserStatsContainer">
<div id="LeftContainer">
	<div id="ProfileBadgesContainer">
		<h3>ANORRL Badges</h3>
		<table id="BadgesPane">
			<?php 
				$profilebadges = $get_user->getProfileBadges();
				$count = count($profilebadges);
				$iteration_countfull = 0;
				$iteration_count = 0;
				
				if($count != 0) {
					foreach($profilebadges as $badge) {
						if($iteration_count == 0) {
							echo <<<EOT
							<tr>
							EOT;
						}

						if(!($badge instanceof anorrl\enums\ANORRLBadge)) {
							continue;
						}

						$badgeid = $badge->ordinal();
						$badgename = $badge->name();
						$badgenamefile = str_replace(" ", "", $badge->name());
						$badgedesc = $badge->description();

						echo <<<EOT
						<td>
							<div class="Badge">
								<a href="/badges#Badge$badgeid" title="$badgedesc">
									<img src="/public/images/badges/$badgenamefile.png?v=2" title="$badgename">
									<span>$badgename</span>
								</a>
							</div>
						</td>
						EOT;

						$iteration_countfull++;
						$iteration_count = $iteration_countfull % 4;

						if($iteration_count < 4 && count($profilebadges) == $iteration_countfull) {
							for($i = 0; $i < 4-$iteration_count; $i++) {
								echo <<<EOT
								<td><div class="Badge" style="background: none;border: none;margin: 2px;"></div></td>
								EOT;
							}
						}

						if($iteration_count == 4 || count($profilebadges) == $iteration_countfull) {
							echo <<<EOT
							</tr>
							EOT;
						}
					}
				}
				
			if($count == 0): ?>
			<tr>
				<td class="Loading"><?= $get_user->name ?> has no badges!</td>
			</tr>
			<?php endif ?>
		</table>
	</div>
</div>
<div id="RightContainer">
	<div id="PlayerBadgesContainer">
		<h3>Player Badges</h3>
		<table id="BadgesPane">
			<tr>
				<td class="Loading">No badges yet...</td>
			</tr>
		</table>
	</div>
</div>
<br clear="all">
</div>
<div id="CommentsContainer" style="margin: 10px">
<?php if($user == null): ?>
<h3 style="margin-bottom: 0px">Comments</h3>
<div id="CommentSection">
	<div id="CommentsDisabled">You need to be logged in to comment on this profile!</div>
</div>
<?php else: ?>
<h3 style="margin-bottom: 0px">Comments (<?= $comments_count ?>)</h3>
<div id="CommentPostArea">
	<?php if(isset($_SESSION['ANORRL$Comment$Post$Error'])): ?>
	<div class="Error">Error: <?= $_SESSION['ANORRL$Comment$Post$Error'] ?></div>
	<?php endif ?>
	<form method="POST">
		<h4 style="margin: 0; letter-spacing: 5px;">Post a comment or something</h4>
		<textarea placeholder="Write a nice comment about <?= $get_user->name ?>!" name="ANORRL$Comment$Post$Contents" maxlength="256" minlength="4"></textarea>
		<input type="submit" value="Submit!" name="ANORRL$Comment$Post$Submit">
	</form>
</div>
<div id="CommentSection">
	<?php if($comments_count != 0):
		foreach($comments as $comment) {
			if($comment instanceof Comment) {
				$comment->PrintComment();
			}
		}
	else: ?>
	<div id="CommentsDisabled">It's pretty empty in here... :<</div>
	<?php endif ?>
</div>
<?php endif ?>
<?php
	$page->loadFooter();
?>
