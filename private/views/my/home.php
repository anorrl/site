<?php
	use anorrl\Page;
	use anorrl\Status;

	$user = SESSION->user;

	if(isset($_POST['ANORRL$Home$Status$Text']) &&
	   isset($_POST['ANORRL$Home$Status$Submit'])) {
		$result = Status::Send($user->id, trim($_POST['ANORRL$Home$Status$Text']));

		if($result['error']) {
			$_SESSION['ANORRL$Home$StatusError'] = true;
			$_SESSION['ANORRL$Home$StatusResult'] = $result['reason'];
		} else {
			$_SESSION['ANORRL$Home$StatusError'] = false;
			$_SESSION['ANORRL$Home$StatusResult'] = "Success!";
		}

		die(header("Location: /my/home"));
	}

	$recentlyplayed = $user->getRecentlyPlayedGames(2);

	$page = new Page("Home", "my/home");

	$page->addStylesheet("/css/new/my/home.css?v=2");
	$page->addScript("/js/home.js?t=1776011774");

	$page->loadHeader();
?>
<table id="FeedItem" template>
	<td class="User">
		<a href="">
			<img src="">
			<div id="Name">Name here</div>
		</a>
	</td>
	<td id="Content">
		<code>Content content</code>
		<div id="DatePosted">Posted <span id="Date">DD/MM/YYYY</span><!-- <a href="">Report abuse</a>--></div>
	</td>
</table>
<div id="HomePage">
	<div id="HelloStuff">
		<h1 id="Hello" style="width: 850px">
			<marquee scrollamount="15" direction="right" behavior="alternate"><?= $user->name ?></marquee>
			<marquee scrollamount="15" behavior="alternate" style="display: block;margin-top: -33px;z-index: 9;" direction="left"><?= $user->name ?></marquee>
		</h1>
		<div id="UserProfile">
			<a href="/users/<?= $user->id ?>/profile"><img id="ProfilePicture" src="<?= $user->getThumbsUrlService($user->setprofilepicture ? "profile" : "player", 200) ?>"></a>
			<div id="StatusContainer">
			<?php 
				if($user->getLatestStatus() != null) {
					$status = $user->getLatestStatus()->content;
					echo <<<EOT
						<span id="Quotation" style="top: 4px;left: 7px;">&quot;</span>
							<span id="Status">$status</span>
						<span id="Quotation" style="bottom: -10px;right: 7px;">&quot;</span>								
					EOT;
				} else {
					echo <<<EOT
						<span id="NoStatus">Seems like you have no status... Try sending one!</span>
					EOT;
				}
			?>
			
		</div>
		</div>
		<div id="FriendsContainer">
			<h3>Friends<?php if($user->getFriendsCount() > 5): ?> <a href="/my/friends" style="font-size: 12px;">(See all)</a><?php endif ?></h3>
			<?php if($user->getFriendsCount() != 0): ?>
			<ul id="Friends">
			<?php 
				$friends = $user->getFriends();
				shuffle($friends);
				
				if(count($friends) > 5) {
					$new_friends = [];
					for($i = 1; $i <= 5; $i++) {
						$new_friends[] = $friends[count($friends)-$i];
					}

					$friends = $new_friends;
				}

				foreach($friends as $friend): ?>

					<li class="Friend">
						<a id="ProfileLink" href="/users/<?= $friend->id ?>/profile">
							<img id="Profile" src="<?= $friend->getThumbsUrl(100) ?>">
							<div id="Name"><?= $friend->name ?></div>
						</a>
					</li>

				<?php endforeach ?>
			</ul>
			<?php else: ?>
			<ul id="Friends" style="display: table">
				<div id="NoFriends">You don't have any friends!</div>
			</ul>
			<?php endif ?>
		</div>
		<br style="clear: both">
		<div id="FeedAndGames">
			<div id="ProfileGames">
				<div id="RecentlyPlayed">
					<h3>Recently Played</h3>
					<div id="Games">
						<!--<div class="Game">
							<a href="/game/1000" title="Whats up guys im gonna rob a store">
								<img src="/public/images/review-pending.png">
								<span id="Name">aaaaaaaaaaaaaaaaaaaaa...</span>
							</a>
							<div id="Stats">
								<div id="OnlinePlayers">Couple ppl online</div>
								<div id="Created">Creator: <a href="/profile/1">Creator</a></div>
							</div>
						</div>-->
						<?php if(count($recentlyplayed) == 0):?>
							<span id="NoTagline">No recently played games yet!</span>
						<?php else: ?>
							<?php foreach($recentlyplayed as $recentlyplayedplace): ?>
							<div class="Game">
								<a href="/game/<?= $recentlyplayedplace->id ?>" title="<?= $recentlyplayedplace->name ?>">
									<img src="/public/images/review-pending.png">
									<span id="Name"><?= $recentlyplayedplace->name ?></span>
								</a>
								<div id="Stats">
									<div id="OnlinePlayers">Couple ppl online</div>
									<div id="Created">Creator: <a href="/users/<?= $recentlyplayedplace->creator->id ?>/profile"><?= $recentlyplayedplace->creator->name ?></a></div>
								</div>
							</div>
							<?php endforeach ?>
						<?php endif ?>

						
					</div>
				</div>
				<div id="Favourites">
					<h3>Favourites</h3>
					<div id="Games">
						<span id="NoTagline">No favourites yet!</span>
					</div>
				</div>
				
			</div>
			<div id="FeedsContainer">
				<h2>Your feed</h2>
				<div id="Submit">
					<?php if(isset($_SESSION['ANORRL$Home$StatusError'])): ?>
						<?php if($_SESSION['ANORRL$Home$StatusError']): ?>
							<div class="Error"><?= $_SESSION['ANORRL$Home$StatusResult'] ?></div>
						<?php else: ?>
							<div class="Success">Success!</div>
						<?php endif ?>
					<?php endif ?>
					<form method="POST">
						<input name="ANORRL$Home$Status$Text" type="text" minlength="4" maxlength="64" placeholder="What are you feeling today?">
						<input name="ANORRL$Home$Status$Submit" type="submit" value="Submit Status">
					</form>
				</div>
				<div id="Feeds">
					
				</div>
				<div id="Pager" style="display:none">
					<a href="javascript:ANORRL.Home.DeadvanceFeed()" id="BackPager">&lt;&lt; Back</a> <span id="PageCounter">Page 1 of 1</span> <a href="javascript:ANORRL.Home.AdvanceFeed()" id="NextPager">Next &gt;&gt;</a>
				</div>	
			</div>
		</div>
	</div>
</div>
<div id="Clearer"></div>
<?php
	$page->loadFooter();
	unset($_SESSION['ANORRL$Home$StatusError']);
	unset($_SESSION['ANORRL$Home$StatusResult']);
?>