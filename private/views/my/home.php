<?php
	use anorrl\Page;
	use anorrl\Status;
	use anorrl\utilities\FileSplasher;
	use anorrl\utilities\UtilUtils;

	$user = SESSION->user;

	$rand_status = new FileSplasher("home/statuses", false, "Home\$Random\$Status")->getRandomSplash();
	$hello = new FileSplasher("home/hello", false, "Home\$Random\$Hello")->getRandomSplash();

	$rand_status = str_replace("\"", "&quot;", $rand_status);

	if(isset($_POST['ANORRL$Home$Status$Text']) &&
	   isset($_POST['ANORRL$Home$Status$Submit'])) {
		$text = trim($_POST['ANORRL$Home$Status$Text']);
		$result = Status::Send($user->id, trim($_POST['ANORRL$Home$Status$Text']));

		if(!$result['success']) {
			$_SESSION['ANORRL$Home$StatusResult'] = [
				"success" => false,
				"reason" => $result['reason'],
				"text" => trim($_POST['ANORRL$Home$Status$Text'])
			];
		} else {
			$_SESSION['ANORRL$Home$StatusResult'] = [
				"success" => true
			];
		}

		redirect("/my/home");
	}

	$recentlyplayed = $user->getRecentlyPlayedGames(2);

	$friends_upper_limit = 4;

	$friends = $user->getFriends();
	$friends_count = count($friends);
	$has_friends = $friends_count != 0;
	$too_many_friends = $friends_count > $friends_upper_limit;

	shuffle($friends);
	
	if(count($friends) > $friends_upper_limit) {
		$new_friends = [];
		for($i = 1; $i <= $friends_upper_limit; $i++) {
			$new_friends[] = $friends[count($friends)-$i];
		}

		$friends = $new_friends;
	}

	$page = new Page("Home", "my/home");
	$page->clearAll();
	$page->addScript("/js/core/jquery.js"); // remove this after revamping all pages
	$page->addStylesheet("/css/home.css");
	$page->addScript("/js/home.js?t=1776011774");

	$page->loadHeader2();
?>
<table class="feed-item" template style="display: none">
	<td id="user">
		<a href="">
			<img src="">
		</a>
	</td>
	<td id="content">
		<a href=""><div id="name"></div></a>
		<code></code>
		<img id="scroll-arrow" src="/public/images/icons/feed-dropdown_arrow.png" title="This has more content! Scroll!">
		<div id="date-posted">posted <span id="date">DD/MM/YYYY</span><a href="" id="report">.report</a></div>
	</td>
</table>
<h2 class="page-title">.home</h2>
<div class="box" style="margin-bottom: 5px;padding: 20px;">
	<h1 style="margin: 0px;"><?= trim($hello) ?>, <?= $user->name ?>...</h1>
</div>
<div class="box" style="margin-bottom: 5px;">
	<h2 style="margin-bottom: 5px; margin-left: 25px;">.add_status</h2>
	
	<form method="POST" style="margin: 0px 25px; margin-bottom: 20px;">
		<?php // this lowk ass but it works so whatever ?>
		<?php if(isset($_SESSION['ANORRL$Home$StatusResult'])):
			$result = $_SESSION['ANORRL$Home$StatusResult']; ?>
			<?php if(!$result['success']): ?>
				<div class="error-text"><?= $result['reason'] ?></div>
			<?php else: ?>
				<div class="success-text">Success!</div>
			<?php endif ?>
		<?php endif ?>
		<textarea class="box input" name="ANORRL$Home$Status$Text" type="text" minlength="4" maxlength="256" placeholder="<?= $rand_status ?>"><?= (isset($_SESSION['ANORRL$Home$StatusResult']) && !$_SESSION['ANORRL$Home$StatusResult']["success"]) ? $_SESSION['ANORRL$Home$StatusResult']["text"] : "" ?></textarea>
		<input style="margin-top: 5px" class="button" name="ANORRL$Home$Status$Submit" type="submit" value="submit_status">
	</form>
</div>
<div style="display: flex; gap: 5px; align-items: flex-start;">
	<div class="box" style="flex: 1; padding: 0px 25px">
		<h2 style="margin-left: -8px;margin-bottom: 8px;">.feed</h2>
		<div id="feeds">
			
		</div>
		<hr style="margin-bottom: 0px;">
		<div id="pager" style="display:none">
			<a href="javascript:ANORRL.Home.DeadvanceFeed()" id="back-pager">&lt;&lt; back</a>
			<span id="page-counter">1 of 1</span>
			<a href="javascript:ANORRL.Home.AdvanceFeed()" id="next-pager">next &gt;&gt;</a>
		</div>
	</div>
	<div class="box" style="flex: 0.75; padding: 5px 25px; max-width: 345px;">
		<div>
			<div id="recently-played">
				<h2>.recently_played</h2>
				<table style="">
					<?php foreach($recentlyplayed as $game): ?>
					<td>
						<div class="game">
							<a href="/<?= $game->getUrl() ?>">
								<img src="<?= $game->getThumbsUrl(160, 90) ?>" width="160" height="90">
								<div id="name"><?= $game->name ?></div>
							</a>
							<div id="creator"><a href="/users/<?= $game->creator->id?>/profile"><?= $game->creator->name?></a></div>
							<div id="played"><?= UtilUtils::GetTimeAgo($game->getLastVisited($user)) ?></div>
						</div>
					</td>
					<?php endforeach ?>
				</table>
			</div>
			<br style="clear: both;">
			<hr style="margin: 5px -25px;margin-top: 15px;clear: both;">
			<div id="friends">
				<h2>.friends <div><a href="/my/friends">[manage]</a></div></h2>
				<div style="margin: 0px -5px;">
					<?php foreach($friends as $friend): 
						$status = $friend->getOnlineActivity(); ?>
						<table class="friend">
							<td id="user">
								<a href="/users/<?= $friend->id ?>/profile">
									<img src="<?= $friend->getThumbsUrl() ?>&amp;sxy=50">
								</a>
							</td>
							<td id="content" style="vertical-align: top;">
								<div id="name"><a href="/users/<?= $friend->id ?>/profile"><?= $friend->name ?></a></div>
								<?php if($friend->isOnline()): ?>
								<div id="activity" online><?= str_contains($status, "[") ? $status : "[ $status ]" ?></div>
								<?php else: ?>
								<div id="activity" offline>[ Offline ]</div>
								<?php endif ?>
								<div id="status"><?= $friend->getLatestStatus()->content ?? '' ?></div>
							</td>
						</table>
					<?php endforeach ?>
				</div>
				<hr>
				<?php if($too_many_friends): ?>
					<div id="view-all"><a href="/my/friends">view all (<?= $friends_count ?>)</a></div>
				<?php endif ?>
			</div>
		</div>
		
	</div>
</div>
<?php
	$page->loadFooter2();
	unset($_SESSION['ANORRL$Home$StatusResult']);
?>