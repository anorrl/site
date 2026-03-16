<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/core/utilities/userutils.php';
	require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/comment.php';

	function IsRewrite() {
		if(!empty($_SERVER['IIS_WasUrlRewritten']))
			return true;
		else if(array_key_exists('HTTP_MOD_REWRITE',$_SERVER))
			return true;
		else if( array_key_exists('REDIRECT_URL', $_SERVER))
			return true;
		else
			return false;
	}

	if(!IsRewrite()) {
		die(header("Location: /my/home"));
	}

	// No id parameter? GET OUT!
	if(!isset($_GET['id'])) {
		die(header("Location: /my/home"));
	}

	$get_user = User::FromID(intval($_GET['id']));

	if($get_user == null) {
		die(header("Location: /my/home"));
	}

	if(isset($_GET['redirect']) && $_GET['redirect'] == "true") {
		die(header("Location: /users/".$get_user->id."/profile"));
	}
	
	$user = UserUtils::RetrieveUser($get_user);

	if($user == null) {
		die(header("Location: /login"));
	}

	$header_data = $get_user;

	$games = $get_user->GetAllOwnedAssetsOfType(AssetType::PLACE, false);

	if(
		isset($_POST['ANORRL$Comment$Post$Contents']) &&
		isset($_POST['ANORRL$Comment$Post$Submit'])
	) {
		$result = Comment::Post($get_user, $_POST['ANORRL$Comment$Post$Contents']);
		
		if($result['error']) {
			$_SESSION['ANORRL$Comment$Post$Error'] = $result['reason'];
		}

		die(header("Location: /users/".$get_user->id."/profile"));
	}

	$comments = Comment::GetCommentsOn($get_user);
	$comments_count = count($comments);

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?= $get_user->name ?> - ANORRL</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<link rel="stylesheet" href="/css/new/comments.css?v=1">
		<link rel="stylesheet" href="/css/new/stuff.css?v=1">
		<link rel="stylesheet" href="/css/new/my/profile.css?v=11">
		<link rel="stylesheet" href="/css/new/main.css?v=6">
		<link rel="stylesheet" href="/users/<?= $get_user->id ?>/css?t=<?= time() ?>">
	
	
		<script src="/js/core/jquery.js"></script>
		<script src="/js/main.js?t=1771413807"></script>
		<script src="/js/placelauncher.js?t=1771413807"></script>
		<script src="/js/user.js?t=1771413807"></script>
		<script>
			$(function(){
				//ANORRL.User.GrabFeed(<?= $get_user->id ?>);
			});
			var render = true;
			function flipRenders() {
				render = !render;

				if(render) {
					$("#AvatarRenderYeah").attr("src", "/thumbs/player?id=<?= $get_user->id ?>&sxy=200");
				} else {
					$("#AvatarRenderYeah").attr("src", "/thumbs/headshot?id=<?= $get_user->id ?>&sxy=200");
				}
			}
		</script>
	</head>
	<body>
		<div class="Badge" template><a href=""><img src=""><span></span></a></div>
		<div id="Container">
			<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/header.php'; ?>
			<div id="WrapperBody">
				<div id="Body">
					<div id="UserInfoContainer">
						<div id="PaddingContainer">
							<h2 style="margin: 5px 0px; width: 825px;"><?= $get_user->name ?>'s Profile</h2>
							<div id="ProfileImage">
								<div id="ImageContainer">
									<img id="ProfilePictureYeah" src="/thumbs/<?= $get_user->setprofilepicture ? "profile" : "headshot" ?>?id=<?= $get_user->id ?>&nocompress">
								</div>
								
								<div id="Controls">
									<?php if($user != null): ?>
										<?php if($user->id != $get_user->id): ?>
											
											<?php
												$friend_button_label = "Add Friend";
												$follow_label = $user->IsFollowing($get_user) ? "Unfollow" : "Follow";

												if($user->IsFriendsWith($get_user)) {
													$friend_button_label = "Unfriend :[";
												}
												else {
													if($user->IsPendingFriendsReq($get_user)) {
														$friend_button_label = "Cancel Req.";
													} else {
														if($user->IsIncomingFriendsReq($get_user)) {
															$friend_button_label = "Accept Req.";
														}
													}
												}
											?>

											<button style="width: 107px;" onclick="ANORRL.User.Friend(<?= $get_user->id ?>)"><?= $friend_button_label ?></button>
											<button style="width: 70px;margin-left: 2px;" onclick="ANORRL.User.Follow(<?= $get_user->id ?>);"><?= $follow_label ?></button><br>
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
											<b id="Numbers"><?= $get_user->GetFriendsCount() ?></b> <span>Friends</span>
										</a> | 
										<a href="/users/<?= $get_user->id ?>/followers">
											<b id="Numbers"><?= $get_user->GetFollowersCount() ?></b> <span>Followers</span>
										</a> | 
										<a href="/users/<?= $get_user->id ?>/following">
											<b id="Numbers"><?= $get_user->GetFollowingCount() ?></b> <span>Following</span>
										</a>
									</div>
									<div id="OnlineStatusArea">
										<?php $profile_status = $get_user->IsOnline() ? "Online" : "Offline"; ?>										
										<span class="<?= $profile_status ?>"><b><?= $profile_status ?></b> - <?= $get_user->GetOnlineActivity() ?></span>

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
					<div id="UserAvatarContainer">
						<h3><?= $get_user->name ?>'s Character</h3>
						<div id="UserAvatarPane">
							<ul id="AvatarItems">
								<?php if(count($get_user->GetWearingArray()) == 0): ?>
								<li>
									<div id="NoItemsOn">
										<?= $get_user->name ?> does not have any items on!
									</div>
								</li>
								<?php else: ?>
								<?php 
									$items = $get_user->GetWearingArray();
									foreach($items as $item) {
										$asset = Asset::FromID($item);

										if($asset instanceof Asset) {
											$asset_id = $asset->id;
											$asset_urlname = $asset->GetURLTitle();
											$asset_name = $asset->name;
											$asset_creator_id = $asset->creator->id;
											$asset_creator_name = $asset->creator->name;
											echo <<<EOT
											<li>
												<div class="Asset">
													<a id="NameAndThumbs" href="/$asset_urlname-item?id=$asset_id">
														<img src="/thumbs/?id=$asset_id&sxy=130">
														<span>$asset_name</span>
													</a>
													<a id="Creator" href="/users/$asset_creator_id/profile"><span>$asset_creator_name</span></a>
												</div>
											</li>
											EOT;
										}
									}
								?>
								
								<?php endif ?>
							</ul>
							<div id="AvatarRender">
								<a href="javascript:flipRenders()" style="position: absolute;z-index: 2;bottom: 5px;right: 5px;"><img src="/images/icons/switch.png" style="width: 30px;image-rendering: pixelated;"></a>
									
								<img id="AvatarRenderYeah" src="/thumbs/player?id=<?= $get_user->id ?>&sxy=200&nocompress">
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
											<a data-placeid="$game_id"><img src="/thumbs/?id=$game_id&sx=227&sy=128"></a>
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
										$profilebadges = $get_user->GetProfileBadges();
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

												if(!($badge instanceof ProfileBadge)) {
													continue;
												}

												$badgeid = $badge->id->ordinal();
												$badgename = $badge->name;
												$badgenamefile = str_replace(" ", "", $badge->name);
												$badgedesc = $badge->description;

												echo <<<EOT
												<td>
													<div class="Badge">
														<a href="/badges#badge$badgeid" title="$badgedesc">
															<img src="/images/Badges/$badgenamefile.png?v=1" title="icon made by ignisole">
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
						<h3>Comments (<?= $comments_count ?>)</h3>
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
					</div>
					<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/footer.php'; ?>
				</div>
				
			</div>
		</div>
	</body>
</html>
