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

		redirect("/my/home");
	}

	$recentlyplayed = $user->getRecentlyPlayedGames(2);

	$page = new Page("Home", "my/home");

	$page->loadHeader2();
?>
<div class="box" style="margin-bottom: 5px;">
	<h2 style="margin-left: 25px;margin-bottom: 5px;">.add_status</h2>
</div>
<div style="display: flex; gap: 5px;">
	<div class="box" style="flex: 1;">
		<h2 style="margin-left: 25px;">.feed</h2>
	</div>
	<div class="box" style="flex: 0.75;">
		<div>
			<h2 style="margin-left: 25px;">.friends</h2>

		</div>
		<hr style="color: rgb(141, 29, 216); margin: 5px -5px">
		<div>
			<h2 style="margin-left: 25px;">.recently_played</h2>
		</div>
	</div>
</div>
<?php
	$page->loadFooter2();
	unset($_SESSION['ANORRL$Home$StatusError']);
	unset($_SESSION['ANORRL$Home$StatusResult']);
?>