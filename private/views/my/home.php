<?php
	use anorrl\Page;
	use anorrl\Status;
	use anorrl\utilities\FileSplasher;
	use anorrl\utilities\UtilUtils;

	$user = SESSION->user;

	$rand_status = new FileSplasher("home_statuses", false, "Home\$Random\$Status")->getRandomSplash();

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

	$page = new Page("Home", "my/home");

	$page->loadHeader2();
?>
<table class="feed-item" template style="display: none">
	<td id="user">
		<a href="">
			<img src="">
		</a>
	</td>
	<td id="content">
		<a href=""><div id="name">Name here</div></a>
		<code>Content content</code>
		<div id="date-posted">Posted <span id="date">DD/MM/YYYY</span><!-- <a href="">Report abuse</a>--></div>
	</td>
</table>
<script src="/public/js/home.js?t=1776011774"></script>
<style>
	textarea.box.input {
		width: 890px; height: 52px; max-width: 890px;min-width: 200px; min-height: 20px; max-height: 52px;
	}

	h2 {
		margin-left: 25px;
	}

	hr {
		color: rgb(141, 29, 216);
	}

	.error-text {
		color: red;
		font-weight: bold;
		margin-bottom: 5px;
		font-style: italic;
	}

	.success-text {
		color: rgb(242, 83, 255);
		font-weight: bold;
		margin-bottom: 5px;
		font-style: italic;
	}
</style>
<div class="box" style="margin-bottom: 5px;">
	<h2 style="margin-bottom: 5px;">.add_status</h2>
	
	<form method="POST" style="margin: 0px 25px; margin-bottom: 20px;">
		<?php // this lowk ass but it works so whatever ?>
		<?php if(isset($_SESSION['ANORRL$Home$StatusResult'])):
			$result = $_SESSION['ANORRL$Home$StatusResult'];
		?>
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
		<h2 style="margin-left: -5px;">.feed</h2>
		<style>
			.feed-item {
				*margin-left: 25px;
			}

			#pager {
				padding: 15px;
				text-align: center;
			}


			.feed-item #content #name {
				font-weight: bold;
				font-size: 12px;
				letter-spacing: 2px;
			}

			.feed-item #content #date-posted {
				color: rgb(159, 124, 184);
				font-style: italic;
			}
			.feed-item #user img {
				margin-right: 5px;
			}
		</style>
		<div id="feeds">
			
		</div>
		<hr style="margin-bottom: 0px;">
		<div id="pager" style="display:none">
			<a href="javascript:ANORRL.Home.DeadvanceFeed()" id="back-pager">&lt;&lt; Back</a>
			<span id="page-counter">Page 1 of 1</span>
			<a href="javascript:ANORRL.Home.AdvanceFeed()" id="next-pager">Next &gt;&gt;</a>
		</div>
	</div>
	<div class="box" style="flex: 0.75;">
		<div>
			<h2>.friends</h2>
		</div>
		<hr style="margin: 5px -5px">
		<div>
			<h2>.recently_played</h2>
		</div>
	</div>
</div>
<?php
	$page->loadFooter2();
	unset($_SESSION['ANORRL$Home$StatusResult']);
?>